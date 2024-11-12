<?php

namespace cjrasmussen\BlueskyApi\Exceptions;

use Psr\Http\Message\ResponseInterface;

class ServerException extends \Exception
{

    private \StdClass $responseData;

    public function __construct(ResponseInterface $response){
        try {
            $this->responseData = json_decode($response->getBody(), flags: JSON_THROW_ON_ERROR);
            parent::__construct($this->responseData?->message, $this->responseData->code);
        } catch (\JsonException $e) {
            parent::__construct($e->getMessage(), $e->getCode(), $e);
        }
    }
}