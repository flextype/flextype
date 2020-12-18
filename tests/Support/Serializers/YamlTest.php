<?php

declare(strict_types=1);

test('test encode() method', function () {
    $this->assertEquals("title: Foo\ncontent: Bar\n",
                        flextype('serializers')->yaml()
                            ->encode(['title' => 'Foo',
                                      'content' => 'Bar']));
});

test('test decode() method', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Bar'],
                        flextype('serializers')->yaml()
                            ->decode("title: Foo\ncontent: Bar"));
});

test('test getCacheID() method', function () {
    $string = "title:Foo\ncontent:Bar";
    $cache_id = flextype('serializers')->yaml()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
