<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
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
$flextype->registry->set('admin_navigation.content.entries', ['title' => '<i class="fas fa-database"></i>' . __('admin_entries'), 'link' => $flextype->router->pathFor('admin.entries.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.fieldsets', ['title' => '<i class="far fa-list-alt"></i>' . __('admin_fieldsets'), 'link' => $flextype->router->pathFor('admin.fieldsets.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.themes', ['title' => '<i class="fas fa-palette"></i>' . __('admin_themes'), 'link' => $flextype->router->pathFor('admin.themes.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.snippets', ['title' => '<i class="far fa-file-code"></i>' . __('admin_snippets'), 'link' => $flextype->router->pathFor('admin.snippets.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.extends.plugins', ['title' => '<i class="fas fa-plug"></i>' . __('admin_plugins'), 'link' => $flextype->router->pathFor('admin.plugins.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.settings.tools', ['title' => '<i class="fas fa-toolbox"></i>' . __('admin_tools'), 'link' => $flextype->router->pathFor('admin.tools.index'), 'attributes' => ['class' => 'nav-link']]);
$flextype->registry->set('admin_navigation.settings.settings', ['title' => '<i class="fas fa-cog"></i>' . __('admin_settings'), 'link' => $flextype->router->pathFor('admin.settings.index'), 'attributes' => ['class' => 'nav-link']]);

// Add Global Vars Admin Twig Extension
$flextype->view->addExtension(new GlobalVarsAdminTwigExtension($flextype));

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
