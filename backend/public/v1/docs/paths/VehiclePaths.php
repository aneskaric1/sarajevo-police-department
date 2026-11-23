<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/VehicleRoutes.php */
class VehiclePaths
{
    #[OA\Get(
        path: '/vehicles', tags: ['vehicles'], summary: 'List all vehicles',
        description: 'With ?plate= the answer is one Vehicle object instead of a list.',
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'stolen = only vehicles reported stolen', schema: new OA\Schema(type: 'string', enum: ['stolen'])),
            new OA\Parameter(name: 'plate', in: 'query', required: false, description: 'Find one vehicle by its plate (returns one object, 404 if unknown)', example: 'A12-K-345', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of vehicles', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Vehicle'))),
            new OA\Response(response: 400, description: 'Invalid filter value', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getVehicles() {}

    #[OA\Get(
        path: '/vehicles/{id}', tags: ['vehicles'], summary: 'Get one vehicle',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The vehicle', content: new OA\JsonContent(ref: '#/components/schemas/Vehicle')),
            new OA\Response(response: 404, description: 'Vehicle not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getVehiclesById() {}

    #[OA\Post(
        path: '/vehicles', tags: ['vehicles'], summary: 'Create a vehicle',
        description: 'The plate is stored in capitals and must be unique; the owner (citizen_id) must exist.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/VehicleInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created vehicle', content: new OA\JsonContent(ref: '#/components/schemas/Vehicle')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postVehicles() {}

    #[OA\Put(
        path: '/vehicles/{id}', tags: ['vehicles'], summary: 'Update a vehicle',
        description: 'Send only the fields that change; the result is validated as a whole.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/VehicleInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated vehicle', content: new OA\JsonContent(ref: '#/components/schemas/Vehicle')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Vehicle not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putVehicles() {}

    #[OA\Delete(
        path: '/vehicles/{id}', tags: ['vehicles'], summary: 'Delete a vehicle',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Vehicle deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Vehicle not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteVehicles() {}
}
