<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

$flextype_manifest_file_path         = ROOT_DIR . '/src/flextype/flextype.yaml';
$default_flextype_settings_file_path = ROOT_DIR . '/src/flextype/settings.yaml';
$custom_flextype_settings_file_path  = PATH['project'] . '/config/flextype/settings.yaml';
$preflight_flextype_path             = PATH['tmp'] . '/preflight/flextype/';
$custom_flextype_settings_path       = PATH['project'] . '/config/flextype/';

! filesystem()->directory($preflight_flextype_path)->exists() and filesystem()->directory($preflight_flextype_path)->create(0755, true);
! filesystem()->directory($custom_flextype_settings_path)->exists() and filesystem()->directory($custom_flextype_settings_path)->create(0755, true);

$f1 = file_exists($flextype_manifest_file_path) ? filemtime($flextype_manifest_file_path) : '';
$f2 = file_exists($default_flextype_settings_file_path) ? filemtime($default_flextype_settings_file_path) : '';
$f3 = file_exists($custom_flextype_settings_file_path) ? filemtime($custom_flextype_settings_file_path) : '';

// Create Unique Cache ID
$cache_id = md5($flextype_manifest_file_path . $default_flextype_settings_file_path . $custom_flextype_settings_file_path . $f1 . $f2 . $f3);

if (filesystem()->file($preflight_flextype_path . '/' . $cache_id . '.txt')->exists()) {
    $flextype_data = unserialize(filesystem()->file($preflight_flextype_path . '/' . $cache_id . '.txt')->get());
} else {
    // Set settings if Flextype Default settings config files exist
    if (! filesystem()->file($default_flextype_settings_file_path)->exists()) {
        throw new RuntimeException('Flextype Default settings config file does not exist.');
    }

    if (($default_flextype_settings_content = filesystem()->file($default_flextype_settings_file_path)->get()) === false) {
        throw new RuntimeException('Load file: ' . $default_flextype_settings_file_path . ' - failed!');
    } else {
        if (trim($default_flextype_settings_content) === '') {
            $default_flextype_settings['settings'] = [];
        } else {
            $default_flextype_settings['settings'] = SymfonyYaml::parse($default_flextype_settings_content);
        }
    }

    // Create flextype custom settings file
    ! filesystem()->file($custom_flextype_settings_file_path)->exists() and filesystem()->file($custom_flextype_settings_file_path)->put($default_flextype_settings_content);

    if (($custom_flextype_settings_content = filesystem()->file($custom_flextype_settings_file_path)->get()) === false) {
        throw new RuntimeException('Load file: ' . $custom_flextype_settings_file_path . ' - failed!');
    } else {
        if (trim($custom_flextype_settings_content) === '') {
            $custom_flextype_settings['settings'] = [];
        } else {
            $custom_flextype_settings['settings'] = SymfonyYaml::parse($custom_flextype_settings_content);
        }
    }

    if (($flextype_manifest_content = filesystem()->file($flextype_manifest_file_path)->get()) === false) {
        throw new RuntimeException('Load file: ' . $flextype_manifest_file_path . ' - failed!');
    } else {
        if (trim($flextype_manifest_content) === '') {
            $flextype_manifest['manifest'] = [];
        } else {
            $flextype_manifest['manifest'] = SymfonyYaml::parse($flextype_manifest_content);
        }
    }

    // Merge flextype default settings with custom project settings.
    $flextype_data = array_replace_recursive($default_flextype_settings, $custom_flextype_settings, $flextype_manifest);

    filesystem()->file($preflight_flextype_path . $cache_id . '.txt')->put(serialize($flextype_data));
}

// Store flextype merged data in the flextype registry.
$registry->set('flextype', $flextype_data);
