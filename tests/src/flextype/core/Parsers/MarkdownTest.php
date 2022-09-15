<?php

declare(strict_types=1);

use function Flextype\parsers;

test('get markdown instance', function () {
    $this->assertInstanceOf(Flextype\Parsers\Markdown::class, parsers()->markdown()->getInstance());
});

test('parse', function () {
    $this->assertEquals('<p><strong>Bold</strong></p>', trim(parsers()->markdown()->parse('**Bold**')));
});

test('get cache ID', function () {
    $this->assertNotEquals(parsers()->markdown()->getCacheID('**Bold**'),
                           parsers()->markdown()->getCacheID('**Bold Text**'));
});
