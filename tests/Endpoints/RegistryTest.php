<?php

declare(strict_types=1);

uses()->group('endpoints');

beforeEach(function() {
    $this->client = new GuzzleHttp\Client();
});

test('test GET /api/registry', function () {
    $response = $this->client->request('GET', 'https://test.flextype.org/api/registry', [
        'query' => [
            'id' => 'flextype.manifest.version',
            'token' => 'e15dbc9336c924e31a5b3b4e66f96351'
        ]
    ]);

    $this->assertEquals('flextype.manifest.version', json_decode((string) $response->getBody(), true)['data']['key']);
    $this->assertTrue(version_compare(json_decode((string) $response->getBody(), true)['data']['value'], '0.0.0', '>=' ) >= 0);
});
