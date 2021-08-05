<?php

declare(strict_types=1);

test('test getInstance() method', function () {
    $this->assertInstanceOf(Flextype\Parsers\Markdown::class, parsers()->markdown()->getInstance());
});

test('test parse() method', function () {
    $this->assertEquals('<p><strong>Bold</strong></p>', trim(parsers()->markdown()->parse('**Bold**')));
});

test('test getCacheID() method', function () {
    $this->assertNotEquals(parsers()->markdown()->getCacheID('**Bold**'),
                           parsers()->markdown()->getCacheID('**Bold Text**'));
});
