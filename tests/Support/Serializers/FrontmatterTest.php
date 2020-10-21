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
