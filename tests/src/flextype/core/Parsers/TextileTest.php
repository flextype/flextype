<?php

declare(strict_types=1);

test('get textile instance', function () {
    $this->assertInstanceOf(Flextype\Parsers\Textile::class, parsers()->textile()->getInstance());
});

test('parse', function () {
    $this->assertEquals('<p><b>Bold</b></p>', trim(parsers()->textile()->parse('**Bold**')));
});

test('get cache ID', function () {
    $this->assertNotEquals(parsers()->textile()->getCacheID('**Bold**'),
                           parsers()->textile()->getCacheID('**Bold Text**'));
});
