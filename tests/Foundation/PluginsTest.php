<?php

declare(strict_types=1);

use Flextype\Foundation\Flextype;
use Atomastic\Strings\Strings;

beforeEach(function() {
    // Create sandbox plugin
    @filesystem()->directory(PATH['project'])->create();
    @filesystem()->directory(PATH['project'] . '/plugins')->create();
    filesystem()->directory(PATH['project'] . '/plugins/sandbox')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/plugins/sandbox/lang/')->create(0755, true);
    filesystem()->file(PATH['project'] . '/plugins/sandbox/lang/en_US.yaml')->put('sandbox_title: Sandbox');
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

test('test getLocales() method', function () {
    $this->assertTrue(is_array(flextype('plugins')->getLocales()));
    $this->assertTrue(isset(flextype('plugins')->getLocales()['en_US']));
});

test('test getPluginsDictionary() method', function () {
    $this->assertTrue(is_array(flextype('plugins')->getPluginsDictionary(flextype('plugins')->getPLuginsList(), 'en_US')));
    $this->assertTrue(isset(flextype('plugins')->getPluginsDictionary(flextype('plugins')->getPLuginsList(), 'en_US')['en_US']['sandbox_title']));
});

test('test getPluginsCacheID() method', function () {
    $md5 = flextype('plugins')->getPluginsCacheID(flextype('plugins')->getPLuginsList());
    $this->assertTrue(strlen($md5) == 32 && ctype_xdigit($md5));
});
