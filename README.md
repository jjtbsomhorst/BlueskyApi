# BlueskyApi

Modified version of the simple class for making requests to the Bluesky API/AT protocol.  Not affiliated with Bluesky.
This version uses Guzzle to do the requests. It will throw exceptions when the request returned an error instead of just
returning the body itself. 

Also, it features some basic helper methods to do requests so you don't have to invent the wheel yourself. 

## Usage

```php
use cjrasmussen\BlueskyApi\Traits\BlueskyApi;

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
