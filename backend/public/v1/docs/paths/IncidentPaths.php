<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/IncidentRoutes.php */
class IncidentPaths
{
    #[OA\Get(
        path: '/incidents', tags: ['incidents'], summary: 'List all incidents',
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'Only incidents with this status', schema: new OA\Schema(type: 'string', enum: ['open', 'investigating', 'closed']))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of incidents', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Incident'))),
            new OA\Response(response: 400, description: 'Invalid filter value', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getIncidents() {}

    #[OA\Get(
        path: '/incidents/{id}', tags: ['incidents'], summary: 'Get one incident',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The incident', content: new OA\JsonContent(ref: '#/components/schemas/Incident')),
            new OA\Response(response: 404, description: 'Incident not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getIncidentsById() {}

    #[OA\Post(
        path: '/incidents', tags: ['incidents'], summary: 'Create a incident',
        description: 'The server sets case_number (INC-year-number), reported_at (now, if not sent) and status (open, if not sent).',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/IncidentInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created incident', content: new OA\JsonContent(ref: '#/components/schemas/Incident')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postIncidents() {}

    #[OA\Put(
        path: '/incidents/{id}', tags: ['incidents'], summary: 'Update a incident',
        description: 'Send only the fields that change. The case number cannot be changed.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/IncidentInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated incident', content: new OA\JsonContent(ref: '#/components/schemas/Incident')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Incident not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putIncidents() {}

    #[OA\Delete(
        path: '/incidents/{id}', tags: ['incidents'], summary: 'Delete a incident',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Incident deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Incident not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteIncidents() {}

    #[OA\Get(
        path: '/incidents/stats', tags: ['incidents'], summary: 'Number of incidents of each type',
        description: 'For the dashboard chart Incidents by type.',
        responses: [
            new OA\Response(response: 200, description: 'Count per incident type, most common first', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/IncidentTypeCount')))
        ]
    )]
    public function getIncidentsStats() {}

    #[OA\Get(
        path: '/incidents/{id}/citizens', tags: ['incidents'], summary: 'People involved in one incident',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 7, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Involved citizens', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/CitizenInIncident'))),
            new OA\Response(response: 404, description: 'Incident not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getIncidentsByIdCitizens() {}
}
