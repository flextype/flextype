<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

$flextypeManifestFilePath        = ROOT_DIR . '/src/flextype/flextype.yaml';
$defaultFlextypeSettingsFilePath = ROOT_DIR . '/src/flextype/settings.yaml';
$customFlextypeSettingsFilePath  = PATH['project'] . '/config/flextype/settings.yaml';
$preflightFlextypePath           = PATH['tmp'] . '/preflight/flextype/';
$customFlextypeSettingsPath   = PATH['project'] . '/config/flextype/';

! filesystem()->directory($preflightFlextypePath)->exists() and filesystem()->directory($preflightFlextypePath)->create(0755, true);
! filesystem()->directory($customFlextypeSettingsPath)->exists() and filesystem()->directory($customFlextypeSettingsPath)->create(0755, true);

$f1 = file_exists($flextypeManifestFilePath) ? filemtime($flextypeManifestFilePath) : '';
$f2 = file_exists($defaultFlextypeSettingsFilePath) ? filemtime($defaultFlextypeSettingsFilePath) : '';
$f3 = file_exists($customFlextypeSettingsFilePath) ? filemtime($customFlextypeSettingsFilePath) : '';

// Create Unique Cache ID
$cacheID = md5($flextypeManifestFilePath . $defaultFlextypeSettingsFilePath . $customFlextypeSettingsFilePath . $f1 . $f2 . $f3);

if (filesystem()->file($preflightFlextypePath . '/' . $cacheID . '.txt')->exists()) {
    $flextypeData = unserialize(filesystem()->file($preflightFlextypePath . '/' . $cacheID . '.txt')->get());
} else {
    // Set settings if Flextype Default settings config files exist
    if (! filesystem()->file($defaultFlextypeSettingsFilePath)->exists()) {
        throw new RuntimeException('Flextype Default settings config file does not exist.');
    }

    if (($defaultFlextypeSettingsContent = filesystem()->file($defaultFlextypeSettingsFilePath)->get()) === false) {
        throw new RuntimeException('Load file: ' . $defaultFlextypeSettingsFilePath . ' - failed!');
    } else {
        if (trim($defaultFlextypeSettingsContent) === '') {
            $defaultFlextypeSettings['settings'] = [];
        } else {
            $defaultFlextypeSettings['settings'] = SymfonyYaml::parse($defaultFlextypeSettingsContent);
        }
    }

    // Create flextype custom settings file
    ! filesystem()->file($customFlextypeSettingsFilePath)->exists() and filesystem()->file($customFlextypeSettingsFilePath)->put($defaultFlextypeSettingsContent);

    if (($customFlextypeSettingsContent = filesystem()->file($customFlextypeSettingsFilePath)->get()) === false) {
        throw new RuntimeException('Load file: ' . $customFlextypeSettingsFilePath . ' - failed!');
    } else {
        if (trim($customFlextypeSettingsContent) === '') {
            $customFlextypeSettings['settings'] = [];
        } else {
            $customFlextypeSettings['settings'] = SymfonyYaml::parse($customFlextypeSettingsContent);
        }
    }

    if (($flextypeManifestContent = filesystem()->file($flextypeManifestFilePath)->get()) === false) {
        throw new RuntimeException('Load file: ' . $flextypeManifestFilePath . ' - failed!');
    } else {
        if (trim($flextypeManifestContent) === '') {
            $flextypeManifest['manifest'] = [];
        } else {
            $flextypeManifest['manifest'] = SymfonyYaml::parse($flextypeManifestContent);
        }
    }

    // Merge flextype default settings with custom project settings.
    $flextypeData = array_replace_recursive($defaultFlextypeSettings, $customFlextypeSettings, $flextypeManifest);

    filesystem()->file($preflightFlextypePath . $cacheID . '.txt')->put(serialize($flextypeData));
}

// Store flextype merged data in the flextype registry.
$registry->set('flextype', $flextypeData);
