<?php
require_once __DIR__ . "/exceptions.php";

/*
 * Small, reusable checks for the services.
 * Every method throws ValidationException (400) with a message for the user.
 * Values from JSON or the URL may arrive as strings ("25"), so the checks accept them.
 */
class Validator
{
    // Every listed field must be present and not empty
    public static function required($data, $fields)
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === "") {
                throw new ValidationException("Field '$field' is required.");
            }
        }
    }

    public static function length($value, $field, $min, $max)
    {
        $length = mb_strlen(trim((string) $value));
        if ($length < $min || $length > $max) {
            throw new ValidationException("Field '$field' must be between $min and $max characters long.");
        }
    }

    // A whole number, optionally with a minimum (e.g. ids are at least 1)
    public static function integer($value, $field, $min = null)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new ValidationException("Field '$field' must be a whole number.");
        }
        if ($min !== null && (int) $value < $min) {
            throw new ValidationException("Field '$field' must be at least $min.");
        }
    }

    // A number such as 80 or 80.50 that is greater than zero (money)
    public static function positive_number($value, $field)
    {
        if (!is_numeric($value) || (float) $value <= 0) {
            throw new ValidationException("Field '$field' must be a number greater than 0.");
        }
    }

    public static function one_of($value, $field, $allowed)
    {
        if (!in_array($value, $allowed, true)) {
            throw new ValidationException("Field '$field' must be one of: " . implode(", ", $allowed) . ".");
        }
    }

    public static function email($value, $field)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException("Field '$field' must be a valid e-mail address.");
        }
    }

    // A date in the format YYYY-MM-DD
    public static function date($value, $field)
    {
        $date = DateTime::createFromFormat("Y-m-d", (string) $value);
        if (!$date || $date->format("Y-m-d") !== $value) {
            throw new ValidationException("Field '$field' must be a date in the format YYYY-MM-DD.");
        }
    }

    // A date and time in the format YYYY-MM-DD HH:MM:SS
    public static function datetime($value, $field)
    {
        $date = DateTime::createFromFormat("Y-m-d H:i:s", (string) $value);
        if (!$date || $date->format("Y-m-d H:i:s") !== $value) {
            throw new ValidationException("Field '$field' must be a date and time in the format YYYY-MM-DD HH:MM:SS.");
        }
    }

    // Works for dates and for date-times: both formats compare correctly as text
    public static function not_in_future($value, $field)
    {
        $now = strlen((string) $value) === 10 ? date("Y-m-d") : date("Y-m-d H:i:s");
        if ($value > $now) {
            throw new ValidationException("Field '$field' cannot be in the future.");
        }
    }

    public static function pattern($value, $field, $regex, $example)
    {
        if (!preg_match($regex, (string) $value)) {
            throw new ValidationException("Field '$field' has the wrong format (example: $example).");
        }
    }

    // Text with < or > could run as JavaScript in the browser (XSS)
    public static function no_html($value, $field)
    {
        if (preg_match('/[<>]/', (string) $value)) {
            throw new ValidationException("Field '$field' must not contain HTML.");
        }
    }
}
