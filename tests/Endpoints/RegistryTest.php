<?php

declare(strict_types=1);

uses()->group('endpoints');

beforeEach(function() {
    $this->client = new GuzzleHttp\Client();
});

test('test /api/registry', function () {
    $response = $this->client->request('GET', 'https://test.flextype.org/api/registry', [
        'id' => 'flextype.manifest.version',
        'token' => 'e15dbc9336c924e31a5b3b4e66f96351'
    ]);

    $this->assertEquals('0.9.12', json_decode($response->getBody())['data']['key']);
});
