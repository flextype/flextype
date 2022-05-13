<?php

declare(strict_types=1);

test('encode', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get();
    $this->assertEquals($string,
                        serializers()->frontmatter()
                            ->encode(['title' => 'Foo',
                                      'content' => 'Content is here.']));
                                
});

test('decode', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Content is here.'],
                        serializers()->frontmatter()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get()));

    $this->assertEquals(['title' => 'Frontmatter YAML',
                        'content' => 'Content is here.'],
                        serializers()->frontmatter()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter-yaml.md')->get()));

    $this->assertEquals(['title' => 'Frontmatter JSON',
                        'content' => 'Content is here.'],
                        serializers()->frontmatter()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter-json.md')->get()));

    $this->assertEquals(['title' => 'Frontmatter JSON5',
                        'content' => 'Content is here.'],
                        serializers()->frontmatter()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter-json5.md')->get()));

    $this->assertEquals(['title' => 'Frontmatter NEON',
                        'content' => 'Content is here.'],
                        serializers()->frontmatter()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter-neon.md')->get()));
});

test('get cache ID', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get();;
    $cache_id = serializers()->frontmatter()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
