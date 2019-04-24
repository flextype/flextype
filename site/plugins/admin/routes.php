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
