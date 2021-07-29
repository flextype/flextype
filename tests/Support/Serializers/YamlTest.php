<?php

declare(strict_types=1);

test('test encode() method', function () {
    $this->assertEquals("title: Foo\nentries: Bar\n",
                        serializers()->yaml()
                            ->encode(['title' => 'Foo',
                                      'entries' => 'Bar']));
});

test('test decode() method', function () {
    $this->assertEquals(['title' => 'Foo',
                         'entries' => 'Bar'],
                        serializers()->yaml()
                            ->decode("title: Foo\nentries: Bar"));
});

test('test getCacheID() method', function () {
    $string = "title:Foo\nentries:Bar";
    $cache_id = serializers()->yaml()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
