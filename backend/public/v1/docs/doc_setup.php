<?php
use OpenApi\Attributes as OA;

/* General information about the API and the shapes of the data (schemas).
   The client sends ...Input; the API answers with the full object (Input + what the server adds).
   Comments in these files use a single star on purpose: swagger-php turns doc comments into descriptions. */

#[OA\Info(
    title: 'Sarajevo Police Department API',
    description: 'REST API of the Sarajevo Police Department portal: officers, citizens, vehicles, violation types, tickets and incidents, plus patrol weather from Open-Meteo. IT 2001 Web Programming project.',
    version: '1.0',
    contact: new OA\Contact(name: 'Anes Karic', email: 'anes.karic1@stu.ibu.edu.ba')
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]
#[OA\Tag(name: 'users', description: 'Officers and administrators')]
#[OA\Tag(name: 'citizens', description: 'People known to the department')]
#[OA\Tag(name: 'vehicles', description: 'Registered vehicles and their owners')]
#[OA\Tag(name: 'violation-types', description: 'Catalogue of violations with the standard fine')]
#[OA\Tag(name: 'tickets', description: 'Fines issued by officers')]
#[OA\Tag(name: 'incidents', description: 'Reported cases')]
#[OA\Tag(name: 'incident-citizens', description: 'Who was involved in an incident and how')]
#[OA\Tag(name: 'weather', description: 'External API: patrol weather from Open-Meteo')]
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'error', type: 'string', example: 'Field \'location\' is required.')
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Ticket deleted.')
    ]
)]
#[OA\Schema(
    schema: 'UserInput',
    description: 'password is required when creating, optional when updating (min 8 characters)',
    required: ['badge_number', 'first_name', 'last_name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'badge_number', type: 'string', example: 'P-400'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Lana'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Hodzic'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lana.hodzic@spd.test'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
        new OA\Property(property: 'officer_rank', type: 'string', example: 'Patrol Officer'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], example: 'user'),
        new OA\Property(property: 'status', type: 'string', enum: ['active', 'pending'], example: 'pending')
    ]
)]
#[OA\Schema(
    schema: 'User',
    description: 'The password hash is never returned.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'badge_number', type: 'string', example: 'P-214'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Emir'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Kovac'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'officer@spd.test'),
        new OA\Property(property: 'officer_rank', type: 'string', example: 'Patrol Officer'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'user'], example: 'user'),
        new OA\Property(property: 'status', type: 'string', enum: ['active', 'pending'], example: 'active'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-10-30 01:00:00')
    ]
)]
#[OA\Schema(
    schema: 'CitizenInput',
    required: ['first_name', 'last_name', 'national_id', 'date_of_birth'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Mirza'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Hodzic'),
        new OA\Property(property: 'national_id', type: 'string', example: '0505990170033'),
        new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '1990-05-05'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '061 555 909'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Titova 10, Sarajevo')
    ]
)]
#[OA\Schema(
    schema: 'Citizen',
    allOf: [new OA\Schema(ref: '#/components/schemas/CitizenInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'VehicleInput',
    required: ['plate_number', 'make', 'model', 'citizen_id'],
    properties: [
        new OA\Property(property: 'plate_number', type: 'string', example: 'A99-K-111'),
        new OA\Property(property: 'make', type: 'string', example: 'Skoda'),
        new OA\Property(property: 'model', type: 'string', example: 'Fabia'),
        new OA\Property(property: 'colour', type: 'string', nullable: true, example: 'White'),
        new OA\Property(property: 'status', type: 'string', enum: ['registered', 'stolen'], example: 'registered'),
        new OA\Property(property: 'citizen_id', type: 'integer', example: 2)
    ]
)]
#[OA\Schema(
    schema: 'Vehicle',
    allOf: [new OA\Schema(ref: '#/components/schemas/VehicleInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'owner_name', type: 'string', example: 'Lejla Hadzic')
    ]
)]
#[OA\Schema(
    schema: 'ViolationTypeInput',
    required: ['code', 'name', 'standard_fine'],
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'V-07'),
        new OA\Property(property: 'name', type: 'string', example: 'Driving without a licence'),
        new OA\Property(property: 'standard_fine', type: 'number', format: 'float', example: 200),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Driving without a valid driving licence')
    ]
)]
#[OA\Schema(
    schema: 'ViolationType',
    allOf: [new OA\Schema(ref: '#/components/schemas/ViolationTypeInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'TicketInput',
    description: 'ticket_number and status are set by the server. Without fine_amount the standard fine of the violation is used; without issued_at the current time.',
    required: ['citizen_id', 'violation_type_id', 'officer_id', 'location'],
    properties: [
        new OA\Property(property: 'citizen_id', type: 'integer', example: 1),
        new OA\Property(property: 'vehicle_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'violation_type_id', type: 'integer', example: 1),
        new OA\Property(property: 'officer_id', type: 'integer', example: 2),
        new OA\Property(property: 'location', type: 'string', example: 'Zmaja od Bosne, Marijin Dvor'),
        new OA\Property(property: 'fine_amount', type: 'number', format: 'float', example: 80),
        new OA\Property(property: 'issued_at', type: 'string', example: '2026-11-21 10:30:00')
    ]
)]
#[OA\Schema(
    schema: 'Ticket',
    allOf: [new OA\Schema(ref: '#/components/schemas/TicketInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'ticket_number', type: 'string', example: 'TCK-88231'),
        new OA\Property(property: 'status', type: 'string', enum: ['unpaid', 'paid', 'contested', 'cancelled'], example: 'unpaid'),
        new OA\Property(property: 'citizen_name', type: 'string', example: 'Adnan Mujic'),
        new OA\Property(property: 'plate_number', type: 'string', nullable: true, example: 'A12-K-345'),
        new OA\Property(property: 'violation_code', type: 'string', example: 'V-01'),
        new OA\Property(property: 'violation_name', type: 'string', example: 'Speeding'),
        new OA\Property(property: 'officer_badge', type: 'string', example: 'P-214'),
        new OA\Property(property: 'officer_name', type: 'string', example: 'Emir Kovac')
    ]
)]
#[OA\Schema(
    schema: 'IncidentInput',
    description: 'case_number is set by the server. An incident with status investigating needs an officer_id.',
    required: ['title', 'type', 'location'],
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Bicycle stolen'),
        new OA\Property(property: 'type', type: 'string', enum: ['Theft', 'Traffic accident', 'Disturbance', 'Vandalism', 'Assault', 'Vehicle theft', 'Other'], example: 'Theft'),
        new OA\Property(property: 'location', type: 'string', example: 'Ferhadija 5'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'A bicycle was taken from the bike rack.'),
        new OA\Property(property: 'reported_at', type: 'string', example: '2026-11-21 13:00:00'),
        new OA\Property(property: 'status', type: 'string', enum: ['open', 'investigating', 'closed'], example: 'open'),
        new OA\Property(property: 'officer_id', type: 'integer', nullable: true, example: 2)
    ]
)]
#[OA\Schema(
    schema: 'Incident',
    allOf: [new OA\Schema(ref: '#/components/schemas/IncidentInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'case_number', type: 'string', example: 'INC-2026-0412'),
        new OA\Property(property: 'officer_badge', type: 'string', nullable: true, example: 'P-214'),
        new OA\Property(property: 'officer_name', type: 'string', nullable: true, example: 'Emir Kovac')
    ]
)]
#[OA\Schema(
    schema: 'IncidentTypeCount',
    properties: [
        new OA\Property(property: 'type', type: 'string', example: 'Theft'),
        new OA\Property(property: 'total', type: 'integer', example: 3)
    ]
)]
#[OA\Schema(
    schema: 'IncidentCitizenInput',
    required: ['incident_id', 'citizen_id', 'involvement'],
    properties: [
        new OA\Property(property: 'incident_id', type: 'integer', example: 8),
        new OA\Property(property: 'citizen_id', type: 'integer', example: 1),
        new OA\Property(property: 'involvement', type: 'string', enum: ['victim', 'witness', 'suspect'], example: 'witness')
    ]
)]
#[OA\Schema(
    schema: 'IncidentCitizen',
    allOf: [new OA\Schema(ref: '#/components/schemas/IncidentCitizenInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'CitizenInIncident',
    description: 'An involvement record with the name of the citizen',
    allOf: [new OA\Schema(ref: '#/components/schemas/IncidentCitizen')],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Lejla'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Hadzic'),
        new OA\Property(property: 'national_id', type: 'string', example: '1505985175034')
    ]
)]
#[OA\Schema(
    schema: 'IncidentOfCitizen',
    description: 'An involvement record with the incident it belongs to',
    allOf: [new OA\Schema(ref: '#/components/schemas/IncidentCitizen')],
    properties: [
        new OA\Property(property: 'case_number', type: 'string', example: 'INC-2026-0412'),
        new OA\Property(property: 'title', type: 'string', example: 'Phone stolen at the market'),
        new OA\Property(property: 'incident_status', type: 'string', example: 'investigating')
    ]
)]
#[OA\Schema(
    schema: 'WeatherDay',
    properties: [
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2026-11-22'),
        new OA\Property(property: 'temp_max', type: 'number', example: 8.5),
        new OA\Property(property: 'temp_min', type: 'number', example: 1.2),
        new OA\Property(property: 'precipitation_probability', type: 'integer', example: 70),
        new OA\Property(property: 'description', type: 'string', example: 'Light rain'),
        new OA\Property(property: 'icon', type: 'string', example: 'cloud-rain')
    ]
)]
#[OA\Schema(
    schema: 'Weather',
    properties: [
        new OA\Property(property: 'city', type: 'string', example: 'Sarajevo'),
        new OA\Property(property: 'current', type: 'object', properties: [
            new OA\Property(property: 'temperature', type: 'number', example: 7.4),
            new OA\Property(property: 'wind_speed', type: 'number', example: 12.1),
            new OA\Property(property: 'description', type: 'string', example: 'Light rain'),
            new OA\Property(property: 'icon', type: 'string', example: 'cloud-rain')
        ]),
        new OA\Property(property: 'days', type: 'array', items: new OA\Items(ref: '#/components/schemas/WeatherDay')),
        new OA\Property(property: 'source', type: 'string', example: 'Open-Meteo')
    ]
)]
class OpenApiSpec {}
