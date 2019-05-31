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
use Flextype\Component\Arr\Arr;
use function Flextype\Component\I18n\__;

$uri = explode('/', \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getPath());

if (isset($uri) && isset($uri[0]) && $uri[0] == 'admin') {

    // Ensure vendor libraries exist
    !is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

    // Register The Auto Loader
    $loader = require_once $autoload;

    include_once 'routes.php';

    // Set Default Admin locale
    I18n::$locale = $flextype->registry->get('settings.locale');

    $flextype->registry->set('admin_navigation.content.entries', ['title' => '<i class="far fa-newspaper"></i>' . __('admin_entries'), 'link' => $flextype->router->urlFor('admin.entries.index'), 'attributes' => ['class' => 'nav-link']]);
    $flextype->registry->set('admin_navigation.extends.fieldsets', ['title' => '<i class="fas fa-list"></i>' . __('admin_fieldsets'), 'link' => $flextype->router->urlFor('admin.fieldsets.index'), 'attributes' => ['class' => 'nav-link']]);
    $flextype->registry->set('admin_navigation.extends.templates', ['title' => '<i class="fas fa-layer-group"></i>' . __('admin_templates'), 'link' => $flextype->router->urlFor('admin.templates.index'), 'attributes' => ['class' => 'nav-link']]);
    $flextype->registry->set('admin_navigation.extends.snippets', ['title' => '<i class="far fa-file-code"></i>' . __('admin_snippets'), 'link' => $flextype->router->urlFor('admin.snippets.index'), 'attributes' => ['class' => 'nav-link']]);
    $flextype->registry->set('admin_navigation.extends.plugins', ['title' => '<i class="fas fa-plug"></i>' . __('admin_plugins'), 'link' => $flextype->router->urlFor('admin.plugins.index'), 'attributes' => ['class' => 'nav-link']]);
    $flextype->registry->set('admin_navigation.settings.settings', ['title' => '<i class="fas fa-cog"></i>' . __('admin_settings'), 'link' => $flextype->router->urlFor('admin.settings.index'), 'attributes' => ['class' => 'nav-link']]);
    $flextype->registry->set('admin_navigation.settings.infomation', ['title' => '<i class="fas fa-info"></i>' . __('admin_information'), 'link' => $flextype->router->urlFor('admin.information.index'), 'attributes' => ['class' => 'nav-link']]);

    $flextype['SettingsController'] = function($container) {
        return new SettingsController($container);
    };

    $flextype['InformationController'] = function($container) {
        return new InformationController($container);
    };

    $flextype['PluginsController'] = function($container) {
        return new PluginsController($container);
    };

    $flextype['EntriesController'] = function($container) {
        return new EntriesController($container);
    };

    $flextype['FieldsetsController'] = function($container) {
        return new FieldsetsController($container);
    };

    $flextype['SnippetsController'] = function($container) {
        return new SnippetsController($container);
    };

    $flextype['TemplatesController'] = function($container) {
        return new TemplatesController($container);
    };

    $flextype['UsersController'] = function($container) {
        return new UsersController($container);
    };
}
