<?php

declare(strict_types=1);

test('test encode() method', function () {
    $this->assertEquals("---\ntitle: Foo\n---\nBar",
                        flextype('frontmatter')
                            ->encode(['title' => 'Foo',
                                      'content' => 'Bar']));
});

test('test decode() method', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Bar'],
                        flextype('frontmatter')
                            ->decode("---\ntitle: Foo\n---\nBar"));
});

test('test getCacheID() method', function () {
    $string = "---\ntitle: Foo\n---\nBar";
    $cache_id = flextype('frontmatter')
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
