<?php

declare(strict_types=1);

/**
 * @link http://digital.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\I18n\I18n;
use function Flextype\Component\I18n\__;

// Set Default Admin locale
I18n::$locale = $flextype->registry->get('settings.locale');

// Add Admin Navigation
$flextype->registry->set('admin_navigation.content.entries', ['title' => __('admin_entries'), 'icon' => 'fas fa-database', 'link' => $flextype->router->pathFor('admin.entries.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.fieldsets', ['title' => __('admin_fieldsets'),'icon' => 'far fa-list-alt', 'link' => $flextype->router->pathFor('admin.fieldsets.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.themes', ['title' => __('admin_themes'),'icon' => 'fas fa-palette', 'link' => $flextype->router->pathFor('admin.themes.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.snippets', ['title' => __('admin_snippets'),'icon' => 'far fa-file-code', 'link' => $flextype->router->pathFor('admin.snippets.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.plugins', ['title' => __('admin_plugins'),'icon' => 'fas fa-plug', 'link' => $flextype->router->pathFor('admin.plugins.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.settings.tools', ['title' => __('admin_tools'),'icon' => 'fas fa-toolbox', 'link' => $flextype->router->pathFor('admin.tools.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.settings.settings', ['title' => __('admin_settings'),'icon' => 'fas fa-cog', 'link' => $flextype->router->pathFor('admin.settings.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.settings.api', ['title' => __('admin_api'),'icon' => 'fas fa-network-wired', 'link' => $flextype->router->pathFor('admin.api.index'), 'attributes' => ['class' => 'nav-link']]);

// Add Global Vars Admin Twig Extension
$flextype->view->addExtension(new GlobalVarsAdminTwigExtension($flextype));

// Add Icon Admin Twig Extension
$flextype->view->addExtension(new IconAdminTwigExtension($flextype));


$flextype['DashboardController'] = static function ($container) {
    return new DashboardController($container);
};

$flextype['SettingsController'] = static function ($container) {
    return new SettingsController($container);
};

$flextype['InformationController'] = static function ($container) {
    return new InformationController($container);
};

$flextype['PluginsController'] = static function ($container) {
    return new PluginsController($container);
};

$flextype['EntriesController'] = static function ($container) {
    return new EntriesController($container);
};

$flextype['FieldsetsController'] = static function ($container) {
    return new FieldsetsController($container);
};

$flextype['SnippetsController'] = static function ($container) {
    return new SnippetsController($container);
};

$flextype['ThemesController'] = static function ($container) {
    return new ThemesController($container);
};

$flextype['TemplatesController'] = static function ($container) {
    return new TemplatesController($container);
};

$flextype['UsersController'] = static function ($container) {
    return new UsersController($container);
};

$flextype['ToolsController'] = static function ($container) {
    return new ToolsController($container);
};

$flextype['ApiController'] = static function ($container) {
    return new ApiController($container);
};
