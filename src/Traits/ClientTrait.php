<?php

namespace cjrasmussen\BlueskyApi\Traits\Traits;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait ClientTrait
{
    use Authentication;
    private ClientInterface $client;

    private function initClient(string $baseUrl): void
    {
        $config = ['base_uri' => str_ends_with($baseUrl, "/") ? $baseUrl : $baseUrl . "/"];
        $this->client = new Client($config);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function sendRequest(string $method, string $lexicon, array|string $body, ?string $content_type = 'application/json', ?array $headers = null, array $query = null): ResponseInterface
    {
        $request = $this->buildRequest($method, $lexicon, $query, $body, $content_type, $headers);
        return $this->client->sendRequest($request);
    }
    /**
     * @throws JsonException
     * @throws Exception
     */
    private function buildRequest(string $method, string $lexicon, ?array $query, array|string $body, ?string $content_type, ?array $headers): RequestInterface
    {
        if (count($query)) {
            $lexicon = $lexicon .'?'.http_build_query($query);
        }

        $request = new Request($method, $lexicon);
        if (isset($this->apiToken)) {
            $request = $request->withHeader('Authorization', 'Bearer ' . $this->apiToken);
        }

        if ($method === 'POST') {
            $contentType = !empty($content_type) ? $content_type : 'application/json';
            $request = $request->withHeader('Content-Type', $contentType);

            if (empty($body)) {
                throw new Exception('No body specified?');
            }

            if (strtolower($contentType) === 'application/json') {
                $streamInterface = is_string($body) ? Utils::streamFor($body) : Utils::streamFor(json_encode($body, JSON_THROW_ON_ERROR));
                $request = $request->withBody($streamInterface);
            } else {
                $request = $request->withBody(Utils::streamFor($body));
            }
        }

        if ($headers) {
            foreach ($headers as $key => $value) {
                $request = $request->withHeader($key, $value);
            }
        }

        return $request;
    }
}