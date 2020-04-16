<?php
declare(strict_types=1);

namespace App\Img;

use HTTP\Request2;
use App\Config\Env;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;


class ComputerVision {
    protected $url;
    protected $key;
    protected $scoreThreshold = 0.79;

    protected $error500code = [
        'FailedToProcess',
        'Timeout',
        'InternalServerError'
    ];

    public function __construct() {
        $this->url = Env::get('AZURE_COMPUTER_VISION');
        $this->url .= '/vision/v2.0/analyze';
        $this->key = Env::get('AZURE_COMPUTER_VISION_KEY');
    }

    public function getThreshold() {
        return $this->scoreThreshold;
    }

    /**
     * @access public
     * @param string $imageUrl absolute path for image to be analyzed
     * @return string Json-formatted api response
     */
    public function analyze(string $imageUrl): string {
        $postData = [
            "url" => "$imageUrl"
        ];

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
        json_encode($postData)
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Ocp-Apim-Subscription-Key:'.$this->key
        ]);

        $imgJsonDescription = curl_exec($ch);
        curl_close($ch);
        
        return $imgJsonDescription;
    }


    public function handleCvResponse (string $jsonResponse) :array {
        $response = json_decode($jsonResponse, true);

        // errors
        if (isset($response['code'])) {
            if (in_array($response['code'], $this->error500code)) {
                throw new ServiceException($response['code']);
            }
            throw new InvalidArgumentTypeException($response['code']);
        }

        return $response;
    }

    public function getAnalysis(string $imageUrl) :array {
        $jsonResponse = $this->analyze($imageUrl);
        return $this->handleCvResponse($jsonResponse);
    }

}