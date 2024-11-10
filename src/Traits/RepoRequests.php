<?php

namespace cjrasmussen\BlueskyApi\Traits;

use Carbon\Carbon;
use cjrasmussen\BlueskyApi\Traits\Traits\ClientTrait;
use DateTimeInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait RepoRequests
{
    use ClientTrait;
    const CREATE_RECORD = 'com.atproto.repo.createRecord';
    const LIST_RECORD = 'com.atproto.repo.listRecords';
    const DELETE_RECORD = 'com.atproto.repo.deleteRecord';
    const UPDATE_RECORD = 'com.atproto.repo.putRecord';
    const GET_RECORD = 'com.atproto.repo.getRecord';
    const UPLOAD_BLOB = 'com.atproto.repo.uploadBlob';
    const COLLECTION_BSKY_FEED_POST = 'app.bsky.feed.post';
    const RECORD_TYPE_BSKY_FEED_POST = 'app.bsky.feed.post';

    /**
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function createTextPost(string $bodyText, ?array $languages, ?Carbon $createdAt, ?string $recordKey, array $facets = [], array $embeds = [], array $tags = []): ResponseInterface
    {
        $languages = $languages ?? ['en-GB'];
        $createdAt ??= Carbon::now();
        $args = [
            'collection' => self::COLLECTION_BSKY_FEED_POST,
            'repo' => $this->getAccountDid(),
            'record' => [
                'text' => $bodyText,
                'langs' => $languages,
                'createdAt' => $createdAt->format(DateTimeInterface::ATOM),
                '$type' => self::RECORD_TYPE_BSKY_FEED_POST,
            ],
        ];

        if (isset($recordKey)) {
            $args['rkey'] = $recordKey;
        }
        return $this->sendRequest(method: 'POST',
            lexicon: self::CREATE_RECORD,
            body: $args,
        );
    }

    public abstract function createWebsiteEmbedPost(): ResponseInterface;
    public abstract function deleteRecord(): ResponseInterface;
    public abstract function getRecord(): ResponseInterface;
    public abstract function listRecords(): ResponseInterface;
    public abstract function uploadBlob(): ResponseInterface;

}