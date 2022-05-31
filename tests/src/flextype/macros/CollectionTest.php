<?php

declare(strict_types=1);

test('onlyFromCollection macros', function () {
    expect(collection(['blog' => ['post-1' => 'Post 1', 'post-2' => 'Post 2']])->onlyFromCollection(['post-2'])->toArray())->toBe(['blog' => ['post-2' => 'Post 2']]);
});

test('exceptFromCollection macros', function () {
    expect(collection(['blog' => ['post-1' => 'Post 1', 'post-2' => 'Post 2']])->exceptFromCollection(['post-1'])->toArray())->toBe(['blog' => ['post-2' => 'Post 2']]);
});