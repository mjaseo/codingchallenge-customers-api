<?php

namespace App\Http;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RandomUserApiClient implements ApiClientInterface
{
    /** @var HttpClientInterface */
    private $httpClient;

    public const URL = 'https://randomuser.me/api';

    private $noOfResults = 100;

    private $nationality = 'AU';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetchApiData()
    {
        try {
            return $this->httpClient->request('GET', self::URL, [
                'query' => [
                    'results' => $this->noOfResults,
                    'nat' => $this->nationality
                ]
            ]);
        } catch (\Exception|TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }

    public function setNoOfResults($noOfResults): RandomUserApiClient
    {
        $this->noOfResults = $noOfResults;
        return $this;
    }

    public function getNoOfResults()
    {
        return $this->noOfResults;
    }

    public function setNationality($nationality): RandomUserApiClient
    {
        $this->nationality = $nationality;
        return $this;
    }

    public function getNationality()
    {
        return $this->nationality;
    }
}