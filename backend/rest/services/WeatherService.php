<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../utils/exceptions.php";
require_once __DIR__ . "/../../utils/Validator.php";

/*
 * Patrol weather from the external Open-Meteo API (https://open-meteo.com).
 * Our backend calls Open-Meteo; the frontend only calls our GET /weather.
 * If Open-Meteo is slow, down or returns nonsense, we throw
 * ExternalServiceException (503) and the rest of the API keeps working.
 */
class WeatherService
{
    private const UNAVAILABLE = "The weather service is not available right now. Please try again later.";

    // WMO weather codes used by Open-Meteo -> text and a Bootstrap icon
    private const CODES = [
        0  => ["Clear sky", "sun"],
        1  => ["Mainly clear", "cloud-sun"],
        2  => ["Partly cloudy", "cloud-sun"],
        3  => ["Overcast", "clouds"],
        45 => ["Fog", "cloud-fog"],
        48 => ["Freezing fog", "cloud-fog"],
        51 => ["Light drizzle", "cloud-drizzle"],
        53 => ["Drizzle", "cloud-drizzle"],
        55 => ["Heavy drizzle", "cloud-drizzle"],
        56 => ["Freezing drizzle", "cloud-sleet"],
        57 => ["Freezing drizzle", "cloud-sleet"],
        61 => ["Light rain", "cloud-rain"],
        63 => ["Rain", "cloud-rain"],
        65 => ["Heavy rain", "cloud-rain-heavy"],
        66 => ["Freezing rain", "cloud-sleet"],
        67 => ["Freezing rain", "cloud-sleet"],
        71 => ["Light snow", "cloud-snow"],
        73 => ["Snow", "cloud-snow"],
        75 => ["Heavy snow", "cloud-snow"],
        77 => ["Snow grains", "cloud-snow"],
        80 => ["Rain showers", "cloud-rain"],
        81 => ["Rain showers", "cloud-rain"],
        82 => ["Violent rain showers", "cloud-rain-heavy"],
        85 => ["Snow showers", "cloud-snow"],
        86 => ["Snow showers", "cloud-snow"],
        95 => ["Thunderstorm", "cloud-lightning-rain"],
        96 => ["Thunderstorm with hail", "cloud-lightning-rain"],
        99 => ["Thunderstorm with hail", "cloud-lightning-rain"],
    ];

    // GET /weather?days=3 : current weather and a short forecast for the patrol city
    public function get_patrol_weather($days = 3)
    {
        Validator::integer($days, "days", 1);
        if ((int) $days > 7) {
            throw new ValidationException("Field 'days' must be at most 7.");
        }

        $url = Config::OPEN_METEO_FORECAST_URL() . "?" . http_build_query([
            "latitude"      => Config::PATROL_LATITUDE(),
            "longitude"     => Config::PATROL_LONGITUDE(),
            "current"       => "temperature_2m,wind_speed_10m,weather_code",
            "daily"         => "weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max",
            "timezone"      => "Europe/Sarajevo",
            "forecast_days" => (int) $days,
        ]);
        $data = $this->request($url);

        // Check that the parts we use are really there
        if (!isset($data["current"]["temperature_2m"], $data["daily"]["time"]) || !is_array($data["daily"]["time"])) {
            error_log("Open-Meteo: unexpected answer: " . substr(json_encode($data), 0, 300));
            throw new ExternalServiceException(self::UNAVAILABLE);
        }

        return [
            "city"    => Config::PATROL_CITY(),
            "current" => [
                "temperature" => $data["current"]["temperature_2m"],
                "wind_speed"  => $data["current"]["wind_speed_10m"] ?? null,
            ] + $this->describe($data["current"]["weather_code"] ?? null),
            "days"    => $this->shape_days($data["daily"]),
            "source"  => "Open-Meteo",
        ];
    }

    // Open-Meteo sends parallel arrays (time[i], weather_code[i], ...);
    // the frontend gets one object per day instead
    private function shape_days($daily)
    {
        $days = [];
        foreach ($daily["time"] as $i => $date) {
            $days[] = [
                "date"                      => $date,
                "temp_max"                  => $daily["temperature_2m_max"][$i] ?? null,
                "temp_min"                  => $daily["temperature_2m_min"][$i] ?? null,
                "precipitation_probability" => $daily["precipitation_probability_max"][$i] ?? null,
            ] + $this->describe($daily["weather_code"][$i] ?? null);
        }
        return $days;
    }

    private function describe($code)
    {
        [$description, $icon] = self::CODES[$code] ?? ["Unknown", "question-circle"];
        return ["description" => $description, "icon" => $icon];
    }

    // One place for every check of the external call
    private function request($url)
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,                                // 3 s to connect
            CURLOPT_TIMEOUT        => (int) Config::OPEN_METEO_TIMEOUT(), // 5 s in total
            CURLOPT_HTTPHEADER     => ["Accept: application/json"],
        ]);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);

        if ($body === false) {                      // timeout, DNS error, no internet
            error_log("Open-Meteo: request failed: $curl_error");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        if ($status !== 200) {                      // their error
            error_log("Open-Meteo: HTTP $status: " . substr($body, 0, 300));
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        $data = json_decode($body, true);
        if (!is_array($data)) {                     // a 200 with HTML inside is still a failure
            error_log("Open-Meteo: answer is not JSON");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        return $data;
    }
}
