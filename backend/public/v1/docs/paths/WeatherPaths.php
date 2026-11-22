<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/WeatherRoutes.php */
class WeatherPaths
{
    #[OA\Get(
        path: '/weather', tags: ['weather'], summary: 'Patrol weather for Sarajevo',
        description: 'Our backend calls the external Open-Meteo API (timeout 5 s) and returns only the fields the dashboard needs. If Open-Meteo does not answer, the answer is 503 and the rest of the API keeps working.',
        parameters: [
            new OA\Parameter(name: 'days', in: 'query', required: false, description: 'Number of forecast days (1 to 7, default 3)', example: 3, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Current weather and the forecast', content: new OA\JsonContent(ref: '#/components/schemas/Weather')),
            new OA\Response(response: 400, description: 'days is not a whole number from 1 to 7', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'Open-Meteo is not available (timeout or error)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWeather() {}
}
