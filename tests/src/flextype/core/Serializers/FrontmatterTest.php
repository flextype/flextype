<?php

declare(strict_types=1);

test('test encode() method', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get();
    $this->assertEquals($string,
                        serializers()->frontmatter()
                            ->encode(['title' => 'Foo',
                                      'content' => 'Content is here.']));
});

test('test decode() method', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Content is here.'],
                        serializers()->frontmatter()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get()));
});

test('test getCacheID() method', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get();;
    $cache_id = serializers()->frontmatter()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
