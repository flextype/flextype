<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH_PROJECT . '/entries')->delete();
});

test('markdown shortcode', function () {
    $this->assertEquals("<p><strong>Foo</strong></p>\n", parsers()->shortcodes()->parse('(markdown)**Foo**(/markdown)'));

    expect(entries()->create('md', ['test' => '(markdown) **Foo**']))->toBeTrue();
    expect(entries()->fetch('md')['test'])->toBe("<p> <strong>Foo</strong></p>");
});

test('markdown shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.markdown.enabled', false);
    expect(parsers()->shortcodes()->parse("(markdown)**Foo**(/markdown)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.markdown.enabled', true);
});