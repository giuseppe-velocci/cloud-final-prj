<?php
declare(strict_types=1);

namespace App\Img;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use App\Config\Env;

class ComputerVision {
    protected $uriBase;
    protected $key;
    protected $requestFactory;

    public function __construct(ServerRequestFactory $requestFactory) {
        $this->uriBase = Env::get('AZURE_COMPUTER_VISION');
        $this->key = Env::get('AZURE_COMPUTER_VISION_KEY');
    }

    /**
     * @access public
     * @param string $imageUrl absolute path for image to be analyzed
     * @return 
     */
    public function analyze(string $imageUrl): string {
        // $request = new \Http_Request2($this->uriBase); // . '/analyze');
        $method = 'POST';
        $uri = $this->uriBase;
        $serverParams = $_SERVER;

        $this->requestFactory->createServerRequest(
            $method, $uri, $serverParams
        );
        
        $headers = [
            // Request headers
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $this->key
        ];
        $request = $request->withHeaders($headers);

        $parameters = [
            // Request parameters
            'visualFeatures' => 'Categories,Description',
            'details' => '',
            'language' => 'en'
        ];
        $url->setQueryParams($parameters);

        $request->setMethod(\HTTP_Request2::METHOD_POST);

        // Request body parameters
        $body = json_encode(array('url' => $imageUrl));

        // Request body
        $request->setBody($body);

       // try {
        $response = $request->send();
        return \json_encode(json_decode($response->getBody()), JSON_PRETTY_PRINT);
   //    } catch (HttpException $ex) {
    //        echo "<pre>" . $ex . "</pre>";
    //    }
    }
}
