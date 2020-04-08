<?php
declare(strict_types=1);

namespace App\Helper;

use Psr\Http\Message\ResponseInterface;

class ResponseOutputHelper {

    // https://stackoverflow.com/questions/33304790/emit-a-response-with-psr-7
    public static function printResponse(ResponseInterface $response) {
        if (headers_sent()) {
            throw new \RuntimeException('Headers were already sent. The response could not be emitted!');
        }

        $statusLine = sprintf(
            'HTTP/%s %s %s'
            , $response->getProtocolVersion()
            , $response->getStatusCode()
            , $response->getReasonPhrase()
        );
        header($statusLine, TRUE); /* The header replaces a previous similar header. */
        
        // Step 2: Send the response headers from the headers list.
        foreach ($response->getHeaders() as $name => $values) {
            $responseHeader = sprintf(
                '%s: %s'
                , $name
                , $response->getHeaderLine($name)
            );
            header($responseHeader, FALSE); /* The header doesn't replace a previous similar header. */
        }
        
        // Step 3: Output the message body.
        echo $response->getBody();
        exit;
    }
}