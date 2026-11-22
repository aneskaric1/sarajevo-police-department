<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/IncidentCitizenRoutes.php */
class IncidentCitizenPaths
{
    #[OA\Get(
        path: '/incident-citizens', tags: ['incident-citizens'], summary: 'List all incident citizens',
        responses: [
            new OA\Response(response: 200, description: 'List of incident citizens', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/IncidentCitizen')))
        ]
    )]
    public function getIncidentCitizens() {}

    #[OA\Get(
        path: '/incident-citizens/{id}', tags: ['incident-citizens'], summary: 'Get one involvement record',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The involvement record', content: new OA\JsonContent(ref: '#/components/schemas/IncidentCitizen')),
            new OA\Response(response: 404, description: 'Involvement record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getIncidentCitizensById() {}

    #[OA\Post(
        path: '/incident-citizens', tags: ['incident-citizens'], summary: 'Create a involvement record',
        description: 'A citizen can be recorded only once per incident, and nobody can be added to a closed incident.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/IncidentCitizenInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created involvement record', content: new OA\JsonContent(ref: '#/components/schemas/IncidentCitizen')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postIncidentCitizens() {}

    #[OA\Put(
        path: '/incident-citizens/{id}', tags: ['incident-citizens'], summary: 'Update a involvement record',
        description: 'Send only the fields that change; the result is validated as a whole.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/IncidentCitizenInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated involvement record', content: new OA\JsonContent(ref: '#/components/schemas/IncidentCitizen')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Involvement record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putIncidentCitizens() {}

    #[OA\Delete(
        path: '/incident-citizens/{id}', tags: ['incident-citizens'], summary: 'Delete a involvement record',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Involvement record deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Involvement record not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteIncidentCitizens() {}
}
