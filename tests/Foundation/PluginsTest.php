<?php

declare(strict_types=1);

use Flextype\Foundation\Flextype;
use Atomastic\Strings\Strings;

beforeEach(function() {
    //filesystem()->directory(PATH['project'] . '/entries')->create();

    // Create sandbox plugin
    filesystem()->directory(PATH['project'] . '/plugins/sandbox')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/plugins/sandbox/lang/')->create(0755, true);
    filesystem()->file(PATH['project'] . '/plugins/sandbox/lang/en_US.yaml')->put('title: Sandbox');
    filesystem()->file(PATH['project'] . '/plugins/sandbox/settings.yaml')->put('enabled: true');
    filesystem()->file(PATH['project'] . '/plugins/sandbox/plugin.yaml')->put('name: Sandbox');
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/plugins/sandbox')->delete();
});

test('test getPluginsList() method', function () {
    $this->assertTrue(is_array(flextype('plugins')->getPLuginsList()));
    $this->assertTrue(isset(flextype('plugins')->getPLuginsList()['sandbox']));
});
