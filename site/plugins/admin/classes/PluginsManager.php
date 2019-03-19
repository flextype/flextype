<?php

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Http\Http;
use Flextype\Component\Event\Event;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Token\Token;

use Slim\Http\Request;
use Slim\Http\Response;


$app->get('/admin/plugins', function (Request $request, Response $response, array $args) {
    return $this->view->render($response,
                               'plugins/admin/views/templates/extends/plugins/index.html', [
        'registry' => $this->get('registry')->dump(),
        'plugins_list' => $this->get('registry')->get('plugins')
    ]);
})->setName('plugins');

$app->post('/admin/plugins/change_status', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();

    $plugin_settings = YamlParser::decode(Filesystem::read(PATH['plugins'] . '/' . $data['plugin'] . '/' . 'settings.yaml'));
    Arr::set($plugin_settings, 'enabled', ($data['status'] == 'true' ? true : false));
    Filesystem::write(PATH['plugins'] . '/' . $data['plugin'] . '/' . 'settings.yaml', YamlParser::encode($plugin_settings));
    $this->get('cache')->clear();

})->setName('plugins-change-status');
