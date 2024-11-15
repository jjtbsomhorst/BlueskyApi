<?php

namespace cjrasmussen\BlueskyApi\Traits;

use Carbon\Carbon;
use cjrasmussen\BlueskyApi\Exceptions\ClientException;
use cjrasmussen\BlueskyApi\Exceptions\ServerException;
use DateTimeInterface;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Requests that are done on the com.atproto.repo lexicon.
 * See com-atproto-repo-* endpoints at https://docs.bsky.app/docs/api )
 */
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
     * @throws JsonException
     */
    private function getDefaultArgs(): array
    {
        return [
            'collection' => self::COLLECTION_BSKY_FEED_POST,
            'repo' => $this->getAccountDid(),
        ];
    }

    /**
     * @param  array<string>|null  $languages
     * @param  array<object>  $facets
     * @param  array<object>  $embeds
     * @param  array<object>  $tags
     *
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws ClientException
     * @throws ServerException
     */
    public function createPost(string $bodyText, ?array $languages, ?Carbon $createdAt, ?string $recordKey, array $facets = [], array $embeds = [], array $tags = []): \stdClass
    {
        $languages = $languages ?? ['en-GB'];
        $createdAt ??= Carbon::now();
        $args = $this->getDefaultArgs();
        $args['record'] = [
            'text' => $bodyText,
            'langs' => $languages,
            'createdAt' => $createdAt->format(DateTimeInterface::ATOM),
            '$type' => self::RECORD_TYPE_BSKY_FEED_POST,
        ];

        if (isset($recordKey)) {
            $args['rkey'] = $recordKey;
        }

        return $this->sendRequest(method: 'POST',
            lexicon: self::CREATE_RECORD,
            body: $args,
        );
    }

    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws ServerException
     */
    public function getRecord(string $recordKey): \stdClass
    {
        $args = $this->getDefaultArgs();
        $args['rkey'] = $recordKey;

        return $this->sendRequest('GET', lexicon: self::GET_RECORD, body: null, query: $args);
    }

    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws ServerException
     */
    public function deleteRecord(string $recordKey): void
    {
        $args = $this->getDefaultArgs();
        $args['rkey'] = $recordKey;
        $this->sendRequest('POST', lexicon: self::DELETE_RECORD, body: $args);
    }

    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws ServerException
     */
    public function listRecords(int $limit = 50, ?string $did = null): \stdClass
    {
        $args = $this->getDefaultArgs();
        if (isset($did)) {
            $args['repo'] = $did;
        }

        $args['limit'] = $limit;

        return $this->sendRequest(method: 'GET', lexicon: self::LIST_RECORD, body: null, query: $args);
    }

    /**
     * @throws ClientException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws ServerException
     */
    public function uploadBlob(string $imageData, string $contentType): \stdClass
    {
        $this->authenticate();

        return $this->sendRequest(method: 'POST', lexicon: self::UPLOAD_BLOB, body: $imageData, content_type: $contentType);
    }
}
