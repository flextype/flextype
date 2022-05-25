<?php

declare(strict_types=1);

beforeEach(function (): void {
    $this->tempDir = __DIR__ . '/tmp-foo';
    @mkdir($this->tempDir);
});

afterEach(function (): void {
    $filesystem = filesystem();
    $filesystem->directory($this->tempDir)->delete();
    unset($this->tempDir);
});

test('[filesystem] shortcode', function () {
    $filesystem = filesystem();
    $filesystem->file($this->tempDir . '/foo.txt')->put('Foo');
    $this->assertEquals("Foo", parsers()->shortcodes()->parse("(filesystem get:'". $this->tempDir . "/foo.txt')"));
});
