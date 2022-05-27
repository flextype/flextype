<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('textile shortcode', function () {
    $this->assertEquals("<p><b>Foo</b></p>", parsers()->shortcodes()->parse('(textile)**Foo**(/textile)'));

    expect(entries()->create('textile', ['test' => '(textile) **Foo**']))->toBeTrue();
    expect(entries()->fetch('textile')['test'])->toBe("<p> <b>Foo</b></p>");
});
