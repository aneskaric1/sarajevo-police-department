<?php
use OpenApi\Attributes as OA;

/* Documentation of rest/routes/TicketRoutes.php */
class TicketPaths
{
    #[OA\Get(
        path: '/tickets', tags: ['tickets'], summary: 'List all tickets',
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'Only tickets with this status', schema: new OA\Schema(type: 'string', enum: ['unpaid', 'paid', 'contested', 'cancelled']))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of tickets', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Ticket'))),
            new OA\Response(response: 400, description: 'Invalid filter value', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTickets() {}

    #[OA\Get(
        path: '/tickets/{id}', tags: ['tickets'], summary: 'Get one ticket',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The ticket', content: new OA\JsonContent(ref: '#/components/schemas/Ticket')),
            new OA\Response(response: 404, description: 'Ticket not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getTicketsById() {}

    #[OA\Post(
        path: '/tickets', tags: ['tickets'], summary: 'Create a ticket',
        description: 'The server sets ticket_number (next free number), status (unpaid), issued_at (now, if not sent) and fine_amount (the standard fine of the violation, if not sent). The officer must be active.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TicketInput')),
        responses: [
            new OA\Response(response: 201, description: 'The created ticket', content: new OA\JsonContent(ref: '#/components/schemas/Ticket')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postTickets() {}

    #[OA\Put(
        path: '/tickets/{id}', tags: ['tickets'], summary: 'Update a ticket',
        description: 'Send only the fields that change. The ticket number cannot be changed.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TicketInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated ticket', content: new OA\JsonContent(ref: '#/components/schemas/Ticket')),
            new OA\Response(response: 400, description: 'Invalid data or a broken rule', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Ticket not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTickets() {}

    #[OA\Delete(
        path: '/tickets/{id}', tags: ['tickets'], summary: 'Delete a ticket',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ticket deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Ticket not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteTickets() {}

    #[OA\Put(
        path: '/tickets/{id}/pay', tags: ['tickets'], summary: 'Mark a ticket as paid',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 6, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The paid ticket', content: new OA\JsonContent(ref: '#/components/schemas/Ticket')),
            new OA\Response(response: 400, description: 'Only an unpaid or contested ticket can be paid', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Ticket not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTicketsByIdPay() {}

    #[OA\Put(
        path: '/tickets/{id}/cancel', tags: ['tickets'], summary: 'Cancel a ticket',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'Id', example: 8, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The cancelled ticket', content: new OA\JsonContent(ref: '#/components/schemas/Ticket')),
            new OA\Response(response: 400, description: 'The ticket is paid or already cancelled', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Ticket not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putTicketsByIdCancel() {}
}
