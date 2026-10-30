<?php
// Copy this file to config.php (same folder) and put your own values in it.
// config.php is listed in .gitignore and is never committed to GitHub.
// Environment variables on the server win over the defaults written here.

class Config
{
    public static function DB_HOST()     { return self::get_env("DB_HOST", "127.0.0.1"); }
    public static function DB_PORT()     { return self::get_env("DB_PORT", "3306"); }
    public static function DB_NAME()     { return self::get_env("DB_NAME", "sarajevo_police"); }
    public static function DB_USER()     { return self::get_env("DB_USER", "root"); }
    public static function DB_PASSWORD() { return self::get_env("DB_PASSWORD", ""); }

    // Used from Milestone 3 and 4 on
    public static function JWT_SECRET()      { return self::get_env("JWT_SECRET", "change-me-to-a-long-random-string"); }
    public static function WEATHER_API_KEY() { return self::get_env("WEATHER_API_KEY", "your-openweathermap-key"); }

    private static function get_env($name, $default)
    {
        $value = getenv($name);
        return ($value === false || $value === "") ? $default : $value;
    }
}
