<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/CitizenRoutes.php */
class CitizenPaths
{
    #[OA\Get(
        path: '/citizens', tags: ['citizens'], summary: 'List all citizens',
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Part of the first or last name (2 to 50 characters)', example: 'muj', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of citizens', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Citizen'))),
            new OA\Response(response: 400, description: 'Invalid filter value', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCitizens() {}

    #[OA\Get(
        path: '/citizens/{id}', tags: ['citizens'], summary: 'Get one citizen',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The citizen', content: new OA\JsonContent(ref: '#/components/schemas/Citizen')),
            new OA\Response(response: 404, description: 'Citizen not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCitizensById() {}

    #[OA\Post(
        path: '/citizens', tags: ['citizens'], summary: 'Create a citizen',
        description: 'The national ID (13 digits) must be unique; the date of birth cannot be in the future.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CitizenInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created citizen', content: new OA\JsonContent(ref: '#/components/schemas/Citizen')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postCitizens() {}

    #[OA\Put(
        path: '/citizens/{id}', tags: ['citizens'], summary: 'Update a citizen',
        description: 'Send only the fields that change; the result is validated as a whole.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CitizenInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated citizen', content: new OA\JsonContent(ref: '#/components/schemas/Citizen')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Citizen not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putCitizens() {}

    #[OA\Delete(
        path: '/citizens/{id}', tags: ['citizens'], summary: 'Delete a citizen',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Citizen deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The citizen still has vehicles, tickets or incident records', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Citizen not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteCitizens() {}

    #[OA\Get(
        path: '/citizens/{id}/vehicles', tags: ['citizens'], summary: 'Vehicles owned by one citizen',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Vehicles of the citizen', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Vehicle'))),
            new OA\Response(response: 404, description: 'Citizen not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCitizensByIdVehicles() {}

    #[OA\Get(
        path: '/citizens/{id}/tickets', tags: ['citizens'], summary: 'Tickets of one citizen',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Tickets of the citizen', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Ticket'))),
            new OA\Response(response: 404, description: 'Citizen not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCitizensByIdTickets() {}

    #[OA\Get(
        path: '/citizens/{id}/incidents', tags: ['citizens'], summary: 'Incidents one citizen was involved in',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 7, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Incident records of the citizen', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/IncidentOfCitizen'))),
            new OA\Response(response: 404, description: 'Citizen not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCitizensByIdIncidents() {}
}
