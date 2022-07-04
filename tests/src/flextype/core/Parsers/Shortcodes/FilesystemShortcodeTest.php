<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;

beforeEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);

    $this->tempDir = __DIR__ . '/tmp-foo';
    @mkdir($this->tempDir);
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
    $filesystem = filesystem();
    $filesystem->directory($this->tempDir)->delete();
    unset($this->tempDir);
});

test('filesystem shortcode', function () {
    $filesystem = filesystem();
    $filesystem->file($this->tempDir . '/foo.txt')->put('Foo');
    $this->assertEquals("Foo", parsers()->shortcodes()->parse("(filesystem get file:'". $this->tempDir . "/foo.txt')"));
});

test('filesystem shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.filesystem.enabled', false);
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.filesystem.get.enabled', false);
    expect(entries()->create('foo', ['test' => '(filesystem get file:1.txt)']))->toBeTrue();
    expect(entries()->fetch('foo')['test'])->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.filesystem.enabled', true);
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.filesystem.get.enabled', true);
});