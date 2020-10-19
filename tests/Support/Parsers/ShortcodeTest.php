<?php

declare(strict_types=1);

use Thunder\Shortcode\ShortcodeFacade;

test('test getInstance() method', function () {
    $this->assertInstanceOf(ShortcodeFacade::class, flextype('shortcode')->getInstance());
});
