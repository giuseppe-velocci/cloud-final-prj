<?php

declare(strict_types=1);

namespace App\Api;
// use Psr\Http\Message\ResponseInterface;

abstract class AbsApi {
    /**
     * int code = status code
     * string message = text message
     */
    protected function setResponse(int $code, string $message) :string {
        // set response code
        // http_response_code($code);
        // display message:
        return json_encode(array("code" => $code, "message" => $message));
    }

    public abstract function execute(string $jsonData) :string;
}