<?php

namespace components;

/**
 * Class Helpers
 * @package components
 */
class Helpers
{
    public const HTTP_OK = 200;
    public const HTTP_BAD_REQUEST = 400;
    public const PHP_EXTENSION = '.php';

    public const UNDEFINED = 0;
    public const GET = 1;
    public const POST = 2;

    /**
     * @param int          $status
     * @param array|string $error
     * @return bool
     */
    public static function response(int $status = self::HTTP_OK, array | string $error = []): bool
    {
        if (is_string($error)) {
            $error = (array)$error;
        }
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($error);
        return $status === self::HTTP_OK;
    }
}
