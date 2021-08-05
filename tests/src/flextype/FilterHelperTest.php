<?php

declare(strict_types=1);

use Atomastic\Arrays\Arrays;

test('test filter() method', function () {
    $this->assertEquals([], filter());
    $this->assertEquals([], filter([]));
    $this->assertEquals([], filter([], []));
    $this->assertEquals(['foo', 'bar'], filter(['foo', 'bar'], []));

    $data = ['home'  => ['title' => 'Home'],
             'about' => ['title' => 'About'],
             'blog'  => ['title' => 'Blog']];

    // return: first
    $this->assertEquals(['title' => 'Home'], filter($data, ['return' => 'first']));

    // return: last
    $this->assertEquals(['title' => 'Blog'], filter($data, ['return' => 'last']));

    // return: next
    $this->assertEquals(['title' => 'About'], filter($data, ['return' => 'next']));

    // return: random
    $random = filter($data, ['return' => 'random']);
    $this->assertContains($random, $data);

    $random = filter($data, ['return' => 'random', 'random' => 0]);
    $this->assertIsArray($random);
    $this->assertCount(0, $random);

    $random = filter($data, ['return' => 'random', 'random' => 1]);
    $this->assertIsArray($random);
    $this->assertCount(1, $random);
    $this->assertContains(filter($data, ['return' => 'first']), $data);

    $random = filter($data, ['return' => 'random', 'random' => 2]);
    $this->assertIsArray($random);
    $this->assertCount(2, $random);
    $this->assertContains(filter($random, ['return' => 'first']), $data);
    $this->assertContains(filter($random, ['return' => 'last']), $data);

    // return: shuffle
    $this->assertTrue(
        is_array(filter($data, ['return' => 'shuffle'])) &&
        is_array(filter($data, ['return' => 'shuffle']))
    );

    // param: offset and return: all
    $this->assertEquals(['about' => ['title' => 'About'],
                         'blog'  => ['title' => 'Blog']], filter($data, ['return' => 'all', 'offset' => 1]));

    // param: limit and return: all
    $this->assertEquals(['home'  => ['title' => 'Home']], filter($data, ['return' => 'all', 'limit' => 1]));

    // param: sort_by and return: all
    $this->assertEquals(['about' => ['title' => 'About'],
                         'blog'  => ['title' => 'Blog'],
                         'home'  => ['title' => 'Home']],
                            filter($data, ['return' => 'all',
                                                   'sort_by' => ['key' => 'title',
                                                                 'direction' => 'ASC']]));

    $this->assertEquals(['home'  => ['title' => 'Home'],
                         'blog'  => ['title' => 'Blog'],
                         'about' => ['title' => 'About']],
                            filter($data, ['return' => 'all',
                                                   'sort_by' => ['key' => 'title',
                                                                 'direction' => 'DESC']]));

     $this->assertEquals(['Home' => [0 => ['title' => 'Home']],
                          'About' => [0 => ['title' => 'About']],
                          'Blog' => [0 => ['title' => 'Blog']]],
                             filter($data, ['return' => 'all',
                                                    'group_by' => 'title']));
    // param: where and return: all
    $this->assertEquals(['about' => ['title' => 'About']],
                        filter($data, ['return' => 'all',
                                               'where' => [['key' => 'title', 'operator' => '=', 'value' => 'About']]]));
});
