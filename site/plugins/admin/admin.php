<?php

namespace Flextype;

/**
 *
 * Flextype Admin Plugin
 *
 * @author Romanenko Sergey / Awilum <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flextype\Component\Registry\Registry;
use Flextype\Component\I18n\I18n;
use function Flextype\Component\I18n\__;
use Flextype\Component\Arr\Arr;
use Slim\Http\Request;
use Slim\Http\Response;


$uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
$uri = explode('/', $uri->getPath());

if (isset($uri) && isset($uri[0]) && $uri[0] == 'admin') {

    // Ensure vendor libraries exist
    !is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

    // Register The Auto Loader
    $loader = require_once $autoload;


    // Set Default Admin locale
    I18n::$locale = $flextype->registry->get('settings.locale');

    include_once 'classes/UsersManager.php';
    include_once 'classes/PluginsManager.php';

    addItem('content', 'entries', '<i class="far fa-newspaper"></i>' . __('admin_entries'), '/admin/entries', ['class' => 'nav-link'], $flextype);
    addItem('extends', 'fieldsets', '<i class="fas fa-list"></i>' . __('admin_fieldsets'), '/admin/fieldsets', ['class' => 'nav-link'], $flextype);
    addItem('extends', 'templates', '<i class="fas fa-layer-group"></i>' . __('admin_templates'), '/admin/templates', ['class' => 'nav-link'], $flextype);
    addItem('extends', 'snippets', '<i class="far fa-file-code"></i>' . __('admin_snippets'), '/admin/snippets', ['class' => 'nav-link'], $flextype);
    addItem('extends', 'plugins', '<i class="fas fa-plug"></i>' . __('admin_plugins'), '/admin/plugins', ['class' => 'nav-link'], $flextype);
    addItem('settings', 'settings', '<i class="fas fa-cog"></i>' . __('admin_settings'), '/admin/settings', ['class' => 'nav-link'], $flextype);
    addItem('help', 'infomation', '<i class="fas fa-info"></i>' . __('admin_information'), '/admin/information', ['class' => 'nav-link'], $flextype);


    if (UsersManager::isLoggedIn()) {
        //$app->redirect('/', $app->getContainer()->get('router')->pathFor('root'));
    } else {
        if (UsersManager::isUsersExists()) {
            //header('HTTP/1.1 301 Moved Permanently');
            //header('Location: '.$app->getContainer()->get('request')->getUri());
            //$app->redirect($app->getContainer()->get('request')->getUri(), $app->getContainer()->get('router')->pathFor('login'));
        } else {
            //$app->redirect('/', $app->getContainer()->get('router')->pathFor('redirect'));
        }
    }
}

function addItem(string $area, string $item, string $title, string $link, array $attributes = [], $flextype) : void
{
    $flextype->registry->set("admin_navigation.{$area}.{$item}.area", $area);
    $flextype->registry->set("admin_navigation.{$area}.{$item}.item", $item);
    $flextype->registry->set("admin_navigation.{$area}.{$item}.title", $title);
    $flextype->registry->set("admin_navigation.{$area}.{$item}.link", $link);
    $flextype->registry->set("admin_navigation.{$area}.{$item}.attributes", $attributes);
}
