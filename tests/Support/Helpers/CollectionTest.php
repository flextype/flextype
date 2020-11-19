<?php

declare(strict_types=1);

use Atomastic\Arrays\Arrays;

test('test collect() method', function () {
    $this->assertInstanceOf(Arrays::class, collect());
});

test('test collect_filter() method', function () {
    $this->assertEquals([], collect_filter());
    $this->assertEquals([], collect_filter([]));
    $this->assertEquals([], collect_filter([], []));
    $this->assertEquals(['foo', 'bar'], collect_filter(['foo', 'bar'], []));

    $data = ['home'  => ['title' => 'Home'],
             'about' => ['title' => 'About'],
             'blog'  => ['title' => 'Blog']];

    // return: first
    $this->assertEquals(['title' => 'Home'], collect_filter($data, ['return' => 'first']));

    // return: last
    $this->assertEquals(['title' => 'Blog'], collect_filter($data, ['return' => 'last']));

    // return: next
    $this->assertEquals(['title' => 'About'], collect_filter($data, ['return' => 'next']));

    // return: random
    $random = collect_filter($data, ['return' => 'random']);
    $this->assertContains($random, $data);

    $random = collect_filter($data, ['return' => 'random', 'random' => 0]);
    $this->assertIsArray($random);
    $this->assertCount(0, $random);

    $random = collect_filter($data, ['return' => 'random', 'random' => 1]);
    $this->assertIsArray($random);
    $this->assertCount(1, $random);
    $this->assertContains(collect_filter($data, ['return' => 'first']), $data);

    $random = collect_filter($data, ['return' => 'random', 'random' => 2]);
    $this->assertIsArray($random);
    $this->assertCount(2, $random);
    $this->assertContains(collect_filter($random, ['return' => 'first']), $data);
    $this->assertContains(collect_filter($random, ['return' => 'last']), $data);

    // return: count
    $this->assertEquals(3, collect_filter($data, ['return' => 'count']));

    // param: limit and return: all
    $this->assertEquals(['home'  => ['title' => 'Home']], collect_filter($data, ['return' => 'all', 'limit' => 1]));

    // param: offset and return: all
    $this->assertEquals(['about' => ['title' => 'About'],
                         'blog'  => ['title' => 'Blog']], collect_filter($data, ['return' => 'all', 'offset' => 1]));

    // param: slice_offset slice_limit and return: all
    $this->assertEquals(['about' => ['title' => 'About']], collect_filter($data, ['return' => 'all', 'slice_offset' => 1, 'slice_limit' => 1]));

    // param: sort_by and return: all
    $this->assertEquals(['about' => ['title' => 'About'],
                         'blog'  => ['title' => 'Blog'],
                         'home'  => ['title' => 'Home']],
                            collect_filter($data, ['return' => 'all',
                                                   'sort_by' => ['key' => 'title',
                                                                 'direction' => 'ASC']]));

    $this->assertEquals(['home'  => ['title' => 'Home'],
                         'blog'  => ['title' => 'Blog'],
                         'about' => ['title' => 'About']],
                            collect_filter($data, ['return' => 'all',
                                                   'sort_by' => ['key' => 'title',
                                                                 'direction' => 'DESC']]));

     $this->assertEquals(['Home' => [0 => ['title' => 'Home']],
                          'About' => [0 => ['title' => 'About']],
                          'Blog' => [0 => ['title' => 'Blog']]],
                             collect_filter($data, ['return' => 'all',
                                                    'group_by' => 'title']));
    // param: where and return: all
    $this->assertEquals(['about' => ['title' => 'About']],
                        collect_filter($data, ['return' => 'all',
                                               'where' => [['key' => 'title', 'operator' => '=', 'value' => 'About']]]));
});
