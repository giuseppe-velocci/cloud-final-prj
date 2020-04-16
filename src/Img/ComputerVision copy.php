<?php
declare(strict_types=1);

namespace App\Img;

use HTTP\Request2;
use App\Config\Env;

class ComputerVision {
    protected $uriBase;
    protected $key;

    public function __construct() {
        $this->uriBase = Env::get('AZURE_COMPUTER_VISION');
        $this->key = Env::get('AZURE_COMPUTER_VISION_KEY');
    }

    /**
     * @access public
     * @param string $imageUrl absolute path for image to be analyzed
     * @return 
     */
    public function analyze(string $imageUrl): string {
        $request = new \Http_Request2($this->uriBase); // . '/analyze');
        $url = $request->getUrl();

        $headers = array(
            // Request headers
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $this->key
        );
        $request->setHeader($headers);

        $parameters = array(
            // Request parameters
            'visualFeatures' => 'Categories,Description',
            'details' => '',
            'language' => 'en'
        );
        $url->setQueryVariables($parameters);

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
