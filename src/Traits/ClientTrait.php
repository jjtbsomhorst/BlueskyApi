<?php

namespace cjrasmussen\BlueskyApi\Traits;

use cjrasmussen\BlueskyApi\Exceptions\ClientException as ClientException;
use cjrasmussen\BlueskyApi\Exceptions\ServerException as ServerException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

trait ClientTrait
{
    use Authentication;

    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';

    private ClientInterface $client;

    private function initClient(string $baseUrl): void
    {
        $config = ['base_uri' => str_ends_with($baseUrl, '/') ? $baseUrl : $baseUrl.'/'];
        $this->client = new Client($config);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws ClientException
     * @throws ServerException
     */
    public function sendRequest(string $method, string $lexicon, array|string|null $body, ?string $content_type = self::CONTENT_TYPE_APPLICATION_JSON, ?array $headers = null, ?array $query = null): ?stdClass
    {
        $request = $this->buildRequest($method, $lexicon, $query, $body, $content_type, $headers);
        $response = $this->client->sendRequest($request);
        $this->validateResponse($response);

        return json_decode($response->getBody(), flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    private function buildRequest(string $method, string $lexicon, ?array $query, array|string|null $body, ?string $content_type, ?array $headers): RequestInterface
    {
        if (isset($query) && count($query)) {
            $lexicon = $lexicon.'?'.http_build_query($query);
        }

        $request = new Request($method, $lexicon);
        if (isset($this->activeSession->accessJwt)) {
            $request = $request->withHeader('Authorization', 'Bearer '.$this->activeSession->accessJwt);
        }

        if ($method === 'POST') {
            $contentType = ! empty($content_type) ? $content_type : self::CONTENT_TYPE_APPLICATION_JSON;
            $request = $request->withHeader('Content-Type', $contentType);

            if (empty($body)) {
                throw new Exception('No body specified?');
            }

            if (strtolower($contentType) === self::CONTENT_TYPE_APPLICATION_JSON) {
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

    /**
     * @throws ServerException
     * @throws ClientException
     */
    private function validateResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            throw new ClientException($response);
        }

        if ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            throw new ServerException($response);
        }
    }
}
