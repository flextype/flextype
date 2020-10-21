<?php

declare(strict_types=1);

test('test encode() method', function () {
    $this->assertEquals("---\ntitle: Foo\n---\nBar",
                        flextype('frontmatter')
                            ->encode(['title' => 'Foo',
                                      'content' => 'Bar']));
});
