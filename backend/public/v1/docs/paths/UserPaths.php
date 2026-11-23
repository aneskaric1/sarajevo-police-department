<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/UserRoutes.php */
class UserPaths
{
    #[OA\Get(
        path: '/users', tags: ['users'], summary: 'List all users',
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'Only accounts with this status (pending = waiting for an admin)', schema: new OA\Schema(type: 'string', enum: ['active', 'pending']))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of users', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/User'))),
            new OA\Response(response: 400, description: 'Invalid filter value', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getUsers() {}

    #[OA\Get(
        path: '/users/{id}', tags: ['users'], summary: 'Get one officer',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The officer', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 404, description: 'Officer not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getUsersById() {}

    #[OA\Post(
        path: '/users', tags: ['users'], summary: 'Create a officer',
        description: 'The password is stored only as a bcrypt hash. Badge number and e-mail must be unique.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created officer', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postUsers() {}

    #[OA\Put(
        path: '/users/{id}', tags: ['users'], summary: 'Update a officer',
        description: 'Send only the fields that change; the result is validated as a whole.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UserInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated officer', content: new OA\JsonContent(ref: '#/components/schemas/User')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Officer not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putUsers() {}

    #[OA\Delete(
        path: '/users/{id}', tags: ['users'], summary: 'Delete a officer',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Officer deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Officer not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteUsers() {}

    #[OA\Get(
        path: '/users/{id}/tickets', tags: ['users'], summary: 'Tickets issued by one officer',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Tickets of the officer', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Ticket'))),
            new OA\Response(response: 404, description: 'Officer not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getUsersByIdTickets() {}

    #[OA\Get(
        path: '/users/{id}/incidents', tags: ['users'], summary: 'Incidents handled by one officer',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Incidents of the officer', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Incident'))),
            new OA\Response(response: 404, description: 'Officer not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getUsersByIdIncidents() {}
}
