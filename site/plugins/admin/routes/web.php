<?php

namespace Flextype;

// Information Controller
$app->get('/admin/information', 'InformationController:index')->setName('admin.information.index');

// Settings Controller
$app->get('/admin/settings', 'SettingsController:index')->setName('admin.settings.index');
$app->post('/admin/settings', 'SettingsController:update')->setName('admin.settings.update');
$app->post('/admin/settings/clear-cache', 'SettingsController:clearCache')->setName('admin.settings.clear-cache');

// Plugins Controller
$app->get('/admin/plugins', 'PluginsController:index')->setName('admin.plugins.index');
$app->post('/admin/plugins/update-status', 'PluginsController:pluginStatusProcess')->setName('admin.plugins.update-status');

// EntriesController
$app->get('/admin/entries', 'EntriesController:index')->setName('admin.entries.index');
$app->get('/admin/entries/edit', 'EntriesController:edit')->setName('admin.entries.edit');
$app->get('/admin/entries/add', 'EntriesController:add')->setName('admin.entries.add');
$app->post('/admin/entries/add', 'EntriesController:addProcess')->setName('admin.entries.addProcess');
$app->get('/admin/entries/move', 'EntriesController:move')->setName('admin.entries.move');
$app->post('/admin/entries/move', 'EntriesController:moveProcess')->setName('admin.entries.moveProcess');
$app->get('/admin/entries/rename', 'EntriesController:rename')->setName('admin.entries.rename');
$app->post('/admin/entries/rename', 'EntriesController:renameProcess')->setName('admin.entries.renameProcess');
$app->get('/admin/entries/type', 'EntriesController:type')->setName('admin.entries.type');
$app->post('/admin/entries/type', 'EntriesController:typeProcess')->setName('admin.entries.typeProcess');
$app->post('/admin/entries/duplicate', 'EntriesController:duplicateProcess')->setName('admin.entries.duplicateProcess');
$app->post('/admin/entries/delete', 'EntriesController:deleteProcess')->setName('admin.entries.deleteProcess');

// FieldsetsController
$app->get('/admin/fieldsets', 'FieldsetsController:index')->setName('admin.fieldsets.index');
$app->get('/admin/fieldsets/add', 'FieldsetsController:add')->setName('admin.fieldsets.add');
$app->post('/admin/fieldsets/add', 'FieldsetsController:addProcess')->setName('admin.fieldsets.addProcess');
$app->get('/admin/fieldsets/edit', 'FieldsetsController:edit')->setName('admin.fieldsets.edit');
$app->post('/admin/fieldsets/edit', 'FieldsetsController:editProcess')->setName('admin.fieldsets.editProcess');
$app->get('/admin/fieldsets/rename', 'FieldsetsController:rename')->setName('admin.fieldsets.rename');
$app->post('/admin/fieldsets/rename', 'FieldsetsController:renameProcess')->setName('admin.fieldsets.renameProcess');
$app->post('/admin/fieldsets/duplicate', 'FieldsetsController:duplicateProcess')->setName('admin.fieldsets.duplicateProcess');
$app->post('/admin/fieldsets/delete', 'FieldsetsController:deleteProcess')->setName('admin.fieldsets.deleteProcess');

// TemplatesController
$app->get('/admin/templates', 'TemplatesController:index')->setName('admin.templates.index');
$app->get('/admin/templates/add', 'TemplatesController:add')->setName('admin.templates.add');
$app->post('/admin/templates/add', 'TemplatesController:addProcess')->setName('admin.templates.addProcess');
$app->get('/admin/templates/edit', 'TemplatesController:edit')->setName('admin.templates.edit');
$app->post('/admin/templates/edit', 'TemplatesController:editProcess')->setName('admin.templates.addProcess');
$app->get('/admin/templates/rename', 'TemplatesController:rename')->setName('admin.templates.rename');
$app->post('/admin/templates/rename', 'TemplatesController:renameProcess')->setName('admin.templates.renameProcess');
$app->post('/admin/templates/duplicate', 'TemplatesController:duplicateProcess')->setName('admin.templates.duplicateProcess');
$app->post('/admin/templates/delete', 'TemplatesController:deleteProcess')->setName('admin.templates.deleteProcess');

// SnippetsController
$app->get('/admin/snippets', 'SnippetsController:index')->setName('admin.snippets.index');
$app->get('/admin/snippets/add', 'SnippetsController:add')->setName('admin.snippets.add');
$app->post('/admin/snippets/add', 'SnippetsController:addProcess')->setName('admin.snippets.addProcess');
$app->get('/admin/snippets/edit', 'SnippetsController:edit')->setName('admin.snippets.edit');
$app->post('/admin/snippets/edit', 'SnippetsController:editProcess')->setName('admin.snippets.addProcess');
$app->get('/admin/snippets/rename', 'SnippetsController:rename')->setName('admin.snippets.rename');
$app->post('/admin/snippets/rename', 'SnippetsController:renameProcess')->setName('admin.snippets.renameProcess');
$app->post('/admin/snippets/duplicate', 'SnippetsController:duplicateProcess')->setName('admin.snippets.duplicateProcess');
$app->post('/admin/snippets/delete', 'SnippetsController:deleteProcess')->setName('admin.snippets.deleteProcess');

// UsersController
$app->get('/admin/registration', 'UsersController:registration')->setName('admin.users.registration');
$app->post('/admin/registration', 'UsersController:registrationProcess')->setName('admin.users.registrationProcess');
$app->get('/admin/login', 'UsersController:login')->setName('admin.users.login');
$app->post('/admin/login', 'UsersController:loginProcess')->setName('admin.users.loginProcess');
$app->get('/admin/profile', 'UsersController:profile')->setName('admin.users.profile');
$app->post('/admin/logout', 'UsersController:logoutProcess')->setName('admin.users.logoutProcess');
