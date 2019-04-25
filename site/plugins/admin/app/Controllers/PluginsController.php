<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Date\Date;
use Flextype\Component\Arr\Arr;
use Flextype\Component\Registry\Registry;
use function Flextype\Component\I18n\__;

class PluginsController extends Controller
{
    public function index($request, $response, $args)
    {
        return $this->view->render($response,
                           'plugins/admin/views/templates/extends/plugins/index.html', [
                           'plugins_list' => $this->registry->get('plugins'),
                           'menu_item' => 'plugins'
                        ]);
    }

    public function changeStatus($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $plugin_settings = JsonParser::decode(Filesystem::read(PATH['plugins'] . '/' . $data['plugin'] . '/' . 'settings.json'));
        Arr::set($plugin_settings, 'enabled', ($data['status'] == 'true' ? true : false));
        Filesystem::write(PATH['plugins'] . '/' . $data['plugin'] . '/' . 'settings.json', JsonParser::encode($plugin_settings));
        $this->cache->clear();
    }

}
