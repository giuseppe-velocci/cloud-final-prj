<?php
declare(strict_types=1);

namespace App\Helper;

class HttpRequestHelper {
    protected $httpMethods = ['POST', 'GET', 'PUT', 'PATCH', 'DELETE'];

    public function post(
        string $url, 
        array $headers=[], 
        array $data=[]
    ) {
        $url = strpos($url, 'http') === false? 'http://' . $url : $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        if (! empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (! empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close ($ch);

        var_dump($url);
        var_dump($result);
        die();
    }


    public function send(
        string $url, 
        string $method,
        ?array $headers=null, 
        ?array $data=null
    ) {
        $url = strpos($url, 'http') === false? 'http://' . $url : $url;
        $method = strtoupper($method);
        $options = ['http' => []];

        if (! in_array($method, $this->httpMethods)) 
            throw new \InvalidArgumentException(sprintf('Invalid http method.', $method));

        $options['http']['method'] = $method;

        if (! is_null($headers)) {
            $options['http']['header'] = implode("\r\n", $headers);
        }
            
        $options['http']['content'] = '';
        if (! is_null($data)) {
            $options['http']['content'] = http_build_query($data);
        }
var_dump($url);  
var_dump($data);      
var_dump($options);

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
var_dump($http_response_header);
        if ($result === FALSE) { 
            throw new \Exception(sprintf('Http %s error.', $method));
        }
    }
}