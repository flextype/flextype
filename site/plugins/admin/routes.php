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
$app->get('/admin/entries/edit', 'EntriesController:index')->setName('admin.entries.edit');
$app->get('/admin/entries/add', 'EntriesController:add')->setName('admin.entries.add');
$app->get('/admin/entries/move', 'EntriesController:index')->setName('admin.entries.move');
$app->get('/admin/entries/rename', 'EntriesController:index')->setName('admin.entries.rename');
$app->get('/admin/entries/type', 'EntriesController:index')->setName('admin.entries.type');
$app->get('/admin/entries/duplicate', 'EntriesController:index')->setName('admin.entries.duplicate');
