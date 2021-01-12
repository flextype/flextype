<?php

declare(strict_types=1);

test('test session', function () {
    $this->assertInstanceOf(Atomastic\Session\Session::class, flextype()->container('session'));
});
