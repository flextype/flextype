<?php

declare(strict_types=1);

namespace Flextype;

// UsersController
$app->group('/' . $admin_route, function () use ($app) : void {
    $app->get('/installation', 'UsersController:installation')->setName('admin.users.installation');
    $app->post('/installation', 'UsersController:installationProcess')->setName('admin.users.installationProcess');
    $app->get('/login', 'UsersController:login')->setName('admin.users.login');
    $app->post('/login', 'UsersController:loginProcess')->setName('admin.users.loginProcess');
});

$app->group('/' . $admin_route, function () use ($app) : void {
    // Dashboard
    $app->get('', 'DashboardController:index')->setName('admin.dashboard.index');

    // UsersController
    $app->get('/profile', 'UsersController:profile')->setName('admin.users.profile');
    $app->post('/logout', 'UsersController:logoutProcess')->setName('admin.users.logoutProcess');

    // EntriesController
    $app->get('/entries', 'EntriesController:index')->setName('admin.entries.index');
    $app->get('/entries/edit', 'EntriesController:edit')->setName('admin.entries.edit');
    $app->post('/entries/edit', 'EntriesController:editProcess')->setName('admin.entries.editProcess');
    $app->get('/entries/add', 'EntriesController:add')->setName('admin.entries.add');
    $app->post('/entries/add', 'EntriesController:addProcess')->setName('admin.entries.addProcess');
    $app->get('/entries/move', 'EntriesController:move')->setName('admin.entries.move');
    $app->post('/entries/move', 'EntriesController:moveProcess')->setName('admin.entries.moveProcess');
    $app->get('/entries/rename', 'EntriesController:rename')->setName('admin.entries.rename');
    $app->post('/entries/rename', 'EntriesController:renameProcess')->setName('admin.entries.renameProcess');
    $app->get('/entries/type', 'EntriesController:type')->setName('admin.entries.type');
    $app->post('/entries/type', 'EntriesController:typeProcess')->setName('admin.entries.typeProcess');
    $app->post('/entries/duplicate', 'EntriesController:duplicateProcess')->setName('admin.entries.duplicateProcess');
    $app->post('/entries/delete', 'EntriesController:deleteProcess')->setName('admin.entries.deleteProcess');
    $app->post('/entries/delete-media-file', 'EntriesController:deleteMediaFileProcess')->setName('admin.entries.deleteMediaFileProcess');
    $app->post('/entries/upload-media-file', 'EntriesController:uploadMediaFileProcess')->setName('admin.entries.uploadMediaFileProcess');

    // Settings Controller
    $app->get('/settings', 'SettingsController:index')->setName('admin.settings.index');
    $app->post('/settings', 'SettingsController:updateSettingsProcess')->setName('admin.settings.update');

    // Plugins Controller
    $app->get('/plugins', 'PluginsController:index')->setName('admin.plugins.index');
    $app->get('/plugins/information', 'PluginsController:information')->setName('admin.plugins.information');
    $app->get('/plugins/settings', 'PluginsController:settings')->setName('admin.plugins.settings');
    $app->post('/plugins/settings', 'PluginsController:settingsProcess')->setName('admin.plugins.settingsProcess');
    $app->post('/plugins/update-status', 'PluginsController:pluginStatusProcess')->setName('admin.plugins.update-status');

    // FieldsetsController
    $app->get('/fieldsets', 'FieldsetsController:index')->setName('admin.fieldsets.index');
    $app->get('/fieldsets/add', 'FieldsetsController:add')->setName('admin.fieldsets.add');
    $app->post('/fieldsets/add', 'FieldsetsController:addProcess')->setName('admin.fieldsets.addProcess');
    $app->get('/fieldsets/edit', 'FieldsetsController:edit')->setName('admin.fieldsets.edit');
    $app->post('/fieldsets/edit', 'FieldsetsController:editProcess')->setName('admin.fieldsets.editProcess');
    $app->get('/fieldsets/rename', 'FieldsetsController:rename')->setName('admin.fieldsets.rename');
    $app->post('/fieldsets/rename', 'FieldsetsController:renameProcess')->setName('admin.fieldsets.renameProcess');
    $app->post('/fieldsets/duplicate', 'FieldsetsController:duplicateProcess')->setName('admin.fieldsets.duplicateProcess');
    $app->post('/fieldsets/delete', 'FieldsetsController:deleteProcess')->setName('admin.fieldsets.deleteProcess');

    // ThemesController
    $app->get('/themes', 'ThemesController:index')->setName('admin.themes.index');
    $app->get('/themes/information', 'ThemesController:information')->setName('admin.themes.information');
    $app->get('/themes/settings', 'ThemesController:settings')->setName('admin.themes.settings');
    $app->post('/themes/settings', 'ThemesController:settingsProcess')->setName('admin.themes.settingsProcess');
    $app->post('/themes/activateProcess', 'ThemesController:activateProcess')->setName('admin.themes.activateProcess');

    // TemplatesController
    $app->get('/templates', 'TemplatesController:index')->setName('admin.templates.index');
    $app->get('/templates/add', 'TemplatesController:add')->setName('admin.templates.add');
    $app->post('/templates/add', 'TemplatesController:addProcess')->setName('admin.templates.addProcess');
    $app->get('/templates/edit', 'TemplatesController:edit')->setName('admin.templates.edit');
    $app->post('/templates/edit', 'TemplatesController:editProcess')->setName('admin.templates.addProcess');
    $app->get('/templates/rename', 'TemplatesController:rename')->setName('admin.templates.rename');
    $app->post('/templates/rename', 'TemplatesController:renameProcess')->setName('admin.templates.renameProcess');
    $app->post('/templates/duplicate', 'TemplatesController:duplicateProcess')->setName('admin.templates.duplicateProcess');
    $app->post('/templates/delete', 'TemplatesController:deleteProcess')->setName('admin.templates.deleteProcess');

    // SnippetsController
    $app->get('/snippets', 'SnippetsController:index')->setName('admin.snippets.index');
    $app->get('/snippets/add', 'SnippetsController:add')->setName('admin.snippets.add');
    $app->post('/snippets/add', 'SnippetsController:addProcess')->setName('admin.snippets.addProcess');
    $app->get('/snippets/edit', 'SnippetsController:edit')->setName('admin.snippets.edit');
    $app->post('/snippets/edit', 'SnippetsController:editProcess')->setName('admin.snippets.addProcess');
    $app->get('/snippets/rename', 'SnippetsController:rename')->setName('admin.snippets.rename');
    $app->post('/snippets/rename', 'SnippetsController:renameProcess')->setName('admin.snippets.renameProcess');
    $app->post('/snippets/duplicate', 'SnippetsController:duplicateProcess')->setName('admin.snippets.duplicateProcess');
    $app->post('/snippets/delete', 'SnippetsController:deleteProcess')->setName('admin.snippets.deleteProcess');

    // ToolsController
    $app->get('/tools', 'ToolsController:index')->setName('admin.tools.index');
    $app->get('/tools/information', 'ToolsController:information')->setName('admin.tools.information');
    $app->get('/tools/registry', 'ToolsController:registry')->setName('admin.tools.registry');
    $app->get('/tools/cache', 'ToolsController:cache')->setName('admin.tools.cache');
    $app->post('/tools/cache', 'ToolsController:clearCacheProcess')->setName('admin.tools.clearCacheProcess');
    $app->post('/tools/cache-all', 'ToolsController:clearCacheAllProcess')->setName('admin.tools.clearCacheAllProcess');
})->add(new AuthMiddleware($flextype));
