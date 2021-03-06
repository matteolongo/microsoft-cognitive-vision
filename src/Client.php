<?php

namespace Hymns\MicrosoftCognitiveVision;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Hymns\MicrosoftCognitiveVision\Model\Vision;

class Client
{
    private const BASE_URL = 'https://%s.api.cognitive.microsoft.com/vision/v2.0/';

    private $guzzleClient;

    public function __construct(string $key, string $region = 'australiaeast')
    {
        $this->guzzleClient = new \GuzzleHttp\Client([
            'base_uri' => sprintf(self::BASE_URL, $region),
            'headers'  => [
                'Ocp-Apim-Subscription-Key' => $key,
                'Content-Type'              => 'application/json',
                'User-Agent'                => 'hymns/microsoft-cognitive-vision/1.0'
            ]
        ]);
    }

    /**
     * @param string     $method
     * @param string     $uri
     * @param array|null $formParameters
     * @param array|null $bodyParameters
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws Exception\ClientException
     */
    public function request(string $method, string $uri, array $bodyParameters = null, array $formParameters = null)
    {
        if (\is_array($bodyParameters)) 
        {
            $parameters = [
                \GuzzleHttp\RequestOptions::BODY => json_encode($bodyParameters)
            ];
        } 
        else 
        {
            $parameters = array();
        }

        $responseUri = $uri;
        
        if (\is_array($formParameters)) 
        {
            $params = array();

            foreach ($formParameters as $key => $value) 
            {
                $params[] = $key . '=' . $value;
            }

            if ($params !== array()) 
            {
                $responseUri .= '?' . implode($params, '&');
            }
        }

        try 
        {
            return $this->guzzleClient->request($method, $responseUri, $parameters);
        } 
        catch (GuzzleException $e) 
        {
            /**
             * @var $e RequestException
             */
            throw new Exception\ClientException((string)$e->getResponse()->getBody(), $e->getCode(), $e);
        }
    }

    /**
     * Vision instance model
     * 
     * @return mixed
     */
    public function vision(): Vision
    {
        return new Vision($this);
    }
}
