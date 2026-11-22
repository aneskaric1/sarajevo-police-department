<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/ViolationTypeRoutes.php */
class ViolationTypePaths
{
    #[OA\Get(
        path: '/violation-types', tags: ['violation-types'], summary: 'List all violation types',
        responses: [
            new OA\Response(response: 200, description: 'List of violation types', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ViolationType')))
        ]
    )]
    public function getViolationTypes() {}

    #[OA\Get(
        path: '/violation-types/{id}', tags: ['violation-types'], summary: 'Get one violation type',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The violation type', content: new OA\JsonContent(ref: '#/components/schemas/ViolationType')),
            new OA\Response(response: 404, description: 'Violation type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getViolationTypesById() {}

    #[OA\Post(
        path: '/violation-types', tags: ['violation-types'], summary: 'Create a violation type',
        description: 'The code (V-01 ... V-99) must be unique; the standard fine is between 0 and 5000.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ViolationTypeInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created violation type', content: new OA\JsonContent(ref: '#/components/schemas/ViolationType')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postViolationTypes() {}

    #[OA\Put(
        path: '/violation-types/{id}', tags: ['violation-types'], summary: 'Update a violation type',
        description: 'Send only the fields that change; the result is validated as a whole.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ViolationTypeInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated violation type', content: new OA\JsonContent(ref: '#/components/schemas/ViolationType')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Violation type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putViolationTypes() {}

    #[OA\Delete(
        path: '/violation-types/{id}', tags: ['violation-types'], summary: 'Delete a violation type',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Violation type deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The violation type is used by tickets', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Violation type not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteViolationTypes() {}
}
