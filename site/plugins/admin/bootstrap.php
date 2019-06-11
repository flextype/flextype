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

use Flextype\Component\Arr\Arr;
use Flextype\Component\I18n\I18n;
use function Flextype\Component\I18n\__;

// Get URI
$uri = explode('/', \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getPath());

// Set base admin route
$admin_route = 'admin';

// Ensure vendor libraries exist
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register The Auto Loader
$loader = require_once $autoload;

// Include routes
include_once 'routes/web.php';

// Set Default Admin locale
I18n::$locale = $flextype->registry->get('settings.locale');

// Add Admin Navigation
$flextype->registry->set('admin_navigation.content.entries', ['title' => '<i class="far fa-newspaper"></i>' . __('admin_entries'), 'link' => $flextype->router->pathFor('admin.entries.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.fieldsets', ['title' => '<i class="fas fa-list"></i>' . __('admin_fieldsets'), 'link' => $flextype->router->pathFor('admin.fieldsets.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.templates', ['title' => '<i class="fas fa-layer-group"></i>' . __('admin_templates'), 'link' => $flextype->router->pathFor('admin.templates.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.snippets', ['title' => '<i class="far fa-file-code"></i>' . __('admin_snippets'), 'link' => $flextype->router->pathFor('admin.snippets.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.plugins', ['title' => '<i class="fas fa-plug"></i>' . __('admin_plugins'), 'link' => $flextype->router->pathFor('admin.plugins.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.settings.settings', ['title' => '<i class="fas fa-cog"></i>' . __('admin_settings'), 'link' => $flextype->router->pathFor('admin.settings.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.settings.infomation', ['title' => '<i class="fas fa-info"></i>' . __('admin_information'), 'link' => $flextype->router->pathFor('admin.information.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.help.docs', ['title' => '<i class="far fa-question-circle"></i>' . __('admin_documentation'), 'link' => 'http://flextype.org/en/documentation', 'attributes' => ['class' => 'nav-link', 'target' => '_blank']]);

// Add Global Vars Admin Twig Extension
$flextype->view->addExtension(new GlobalVarsAdminTwigExtension($flextype));

$flextype['DashboardController'] = function ($container) {
    return new DashboardController($container);
};

$flextype['SettingsController'] = function ($container) {
    return new SettingsController($container);
};

$flextype['InformationController'] = function ($container) {
    return new InformationController($container);
};

$flextype['PluginsController'] = function ($container) {
    return new PluginsController($container);
};

$flextype['EntriesController'] = function ($container) {
    return new EntriesController($container);
};

$flextype['FieldsetsController'] = function ($container) {
    return new FieldsetsController($container);
};

$flextype['SnippetsController'] = function ($container) {
    return new SnippetsController($container);
};

$flextype['TemplatesController'] = function ($container) {
    return new TemplatesController($container);
};

$flextype['UsersController'] = function ($container) {
    return new UsersController($container);
};
