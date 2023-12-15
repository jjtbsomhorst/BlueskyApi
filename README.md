# BlueskyApi

Modified version of the simple class for making requests to the Bluesky API/AT protocol.  Not affiliated with Bluesky.

This version saves the refresh token received from the API to a session variable so that it can the session can be refreshed via `com.atproto.server.refreshSession` rather than always creating a new session. This will help to avoid hitting rate limits on the `com.atproto.server.createSession` endpoint depending on how you are using the library.

## Usage

```php
use cjrasmussen\BlueskyApi\BlueskyApi;

$bluesky = new BlueskyApi($handle, $app_password);

// SEND A MESSAGE
$args = [
	'collection' => 'app.bsky.feed.post',
	'repo' => $bluesky->getAccountDid(),
	'record' => [
		'text' => 'Testing #TestingInProduction',
		'langs' => ['en'],
		'createdAt' => date('c'),
		'$type' => 'app.bsky.feed.post',
	],
];
$data = $bluesky->request('POST', 'com.atproto.repo.createRecord', $args);

// SEND A MESSAGE WITH AN IMAGE, ASSUMING $file IS A PNG
$body = file_get_contents($file);
$response = $bluesky->request('POST', 'com.atproto.repo.uploadBlob', [], $body, 'image/png');
$image = $response->blob;

$args = [
	'collection' => 'app.bsky.feed.post',
	'repo' => $bluesky->getAccountDid(),
	'record' => [
		'text' => 'Testing with an image #TestingInProduction',
		'langs' => ['en'],
		'createdAt' => date('c'),
		'$type' => 'app.bsky.feed.post',
		'embed' => [
			'$type' => 'app.bsky.embed.images',
			'images' => [
				[
					'alt' => 'A test image',
					'image' => $image,
				],
			],
		],
	],
];
$response = $bluesky->request('POST', 'com.atproto.repo.createRecord', $args);
```

## Installation

Copy and include the file(s) any way you want.

## Further Reference

See the original repo [here](https://github.com/cjrasmussen/BlueskyApi).

## License

BlueskyApi is [MIT](http://opensource.org/licenses/MIT) licensed.
