<?php
namespace BretRZaun\StatusPage\Check;

use Exception;
use BretRZaun\StatusPage\Result;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;

class UrlCheck extends AbstractCheck
{

    protected $url;

    /**
     * @var Client
     */
    protected $client;

    /**
     * UrlCheck constructor.
     *
     * @param string $label Label
     * @param string $url URL to be tested
     * @param ClientInterface $client HTTP-Client to use
     */
    public function __construct(string $label, string $url, ClientInterface $client)
    {
        parent::__construct($label);
        $this->url = $url;
        $this->setHttpClient($client);
    }

    /**
     * set http client
     */
    public function setHttpClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Check URL
     *
     * @return Result
     * @throws GuzzleException
     */
    public function checkStatus(): Result
    {
        $result = new Result($this->label);
        try {
            $request = $this->client->createRequest('GET', $this->url);
            $response = $this->client->sendRequest($request);
            if ($response->getStatusCode() !== 200) {
                $result->setError('HTTP status code for '.$this->url.' is '.$response->getStatusCode());
            }
        } catch (ClientExceptionInterface) {
            $result->setError('URL failed: '.$this->url);
        }
        return $result;
    }
}
