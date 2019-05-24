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
$app->post('/admin/plugins/change-status', 'PluginsController:changeStatus')->setName('admin.plugins.change-status');

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
$app->get('/admin/entries/duplicate', 'EntriesController:duplicateProcess')->setName('admin.entries.duplicateProcess');
$app->get('/admin/entries/delete', 'EntriesController:deleteProcess')->setName('admin.entries.deleteProcess');

// FieldsetsController
$app->get('/admin/fieldsets', 'FieldsetsController:index')->setName('admin.fieldsets.index');
$app->get('/admin/fieldsets/add', 'FieldsetsController:add')->setName('admin.fieldsets.add');
$app->post('/admin/fieldsets/add', 'FieldsetsController:addProcess')->setName('admin.fieldsets.addProcess');
$app->get('/admin/fieldsets/edit', 'FieldsetsController:edit')->setName('admin.fieldsets.edit');
$app->post('/admin/fieldsets/edit', 'FieldsetsController:editProcess')->setName('admin.fieldsets.addProcess');
$app->get('/admin/fieldsets/rename', 'FieldsetsController:rename')->setName('admin.fieldsets.rename');
$app->post('/admin/fieldsets/rename', 'FieldsetsController:renameProcess')->setName('admin.fieldsets.renameProcess');
$app->get('/admin/fieldsets/duplicate', 'FieldsetsController:duplicateProcess')->setName('admin.fieldsets.duplicateProcess');
$app->get('/admin/fieldsets/delete', 'FieldsetsController:deleteProcess')->setName('admin.fieldsets.deleteProcess');

// TemplatesController
$app->get('/admin/templates', 'TemplatesController:index')->setName('admin.templates.index');
$app->get('/admin/templates/add', 'TemplatesController:add')->setName('admin.templates.add');
$app->post('/admin/templates/add', 'TemplatesController:addProcess')->setName('admin.templates.addProcess');
$app->get('/admin/templates/edit', 'TemplatesController:edit')->setName('admin.templates.edit');
$app->post('/admin/templates/edit', 'TemplatesController:editProcess')->setName('admin.templates.addProcess');
$app->get('/admin/templates/rename', 'TemplatesController:rename')->setName('admin.templates.rename');
$app->post('/admin/templates/rename', 'TemplatesController:renameProcess')->setName('admin.templates.renameProcess');
$app->get('/admin/templates/duplicate', 'TemplatesController:duplicateProcess')->setName('admin.templates.duplicateProcess');
$app->get('/admin/templates/delete', 'TemplatesController:deleteProcess')->setName('admin.templates.deleteProcess');
