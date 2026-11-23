<?php
/*
 * Exceptions that know their HTTP status.
 * A service throws them; the error handler in index.php turns them into JSON.
 * The message is written for the user, so it can be shown as it is.
 */
class HttpException extends Exception
{
    protected $status = 500;

    public function get_status()
    {
        return $this->status;
    }
}

// 400: missing field, wrong format or a broken business rule
class ValidationException extends HttpException
{
    protected $status = 400;
}

// 404: the requested record does not exist
class NotFoundException extends HttpException
{
    protected $status = 404;
}

// 503: an external API (Open-Meteo) is slow, down or returned nonsense
class ExternalServiceException extends HttpException
{
    protected $status = 503;
}
