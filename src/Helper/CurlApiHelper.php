<?php
declare(strict_types=1);

namespace App\Helper;

class CurlApiHelper {

    /**
     * headers
     */
    protected static function setHeaders(string $jwt) {
        $headers = ['Content-Type: application/json'];
        if (strlen($jwt) > 0) {
            $headers[] = sprintf('Authorization: Bearer %s', $jwt);
        }
        return $headers;
    }

    /**
     * get
     */
    public static function get(string $url, array $queryParams, string $jwt='') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . http_build_query($queryParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = self::setHeaders($jwt);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $jsonResult = curl_exec($ch);
        curl_close($ch);
        
        return $jsonResult;
    }

    /**
     * post
     */
    public static function post(string $url, array $postData, string $jwt=''): string {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($postData)
        );

        $headers = self::setHeaders($jwt);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $jsonResult = curl_exec($ch);
        curl_close($ch);
        
        return $jsonResult;
    }
}