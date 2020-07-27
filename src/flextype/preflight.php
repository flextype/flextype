<?php


use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Session\Session;
use Flextype\App\Foundation\Cache\Cache;
use Flextype\App\Foundation\Entries;
use Flextype\App\Foundation\Plugins;
use Flextype\App\Foundation\Cors;
use Flextype\App\Foundation\Config;
use Flextype\App\Support\Parsers\Markdown;
use Flextype\App\Support\Parsers\Shortcode;
use Flextype\App\Support\Serializers\Yaml;
use Flextype\App\Support\Serializers\Json;
use Flextype\App\Support\Serializers\Frontmatter;
use RuntimeException;
use Slim\App;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;
use function array_replace_recursive;
use function date_default_timezone_set;
use function error_reporting;
use function file_exists;
use function function_exists;
use function mb_internal_encoding;
use function mb_language;
use function mb_regex_encoding;
use function trim;

$flextype_manifest_file_path         = ROOT_DIR . '/src/flextype/flextype.yaml';
$default_flextype_settings_file_path = ROOT_DIR . '/src/flextype/settings.yaml';
$custom_flextype_settings_file_path  = PATH['project'] . '/config/flextype/settings.yaml';
$preflight_flextype_path             = PATH['cache'] . '/preflight/flextype/';

! Filesystem::has($preflight_flextype_path) and Filesystem::createDir($preflight_flextype_path);

$f1 = file_exists($flextype_manifest_file_path) ? filemtime($flextype_manifest_file_path) : '';
$f2 = file_exists($default_flextype_settings_file_path) ? filemtime($default_flextype_settings_file_path) : '';
$f3 = file_exists($custom_flextype_settings_file_path) ? filemtime($custom_flextype_settings_file_path) : '';

// Create Unique Cache ID
$cache_id = md5($flextype_manifest_file_path . $default_flextype_settings_file_path . $custom_flextype_settings_file_path . $f1 . $f2 . $f3);

if (Filesystem::has($preflight_flextype_path . '/' . $cache_id . '.php')) {
    $flextype_data = require $preflight_flextype_path . '/' . $cache_id . '.php';
} else {

    // Drop the flextype preflight dir and create new one.
    Filesystem::deleteDir($preflight_flextype_path) and Filesystem::createDir($preflight_flextype_path);

    // Set settings if Flextype Default settings config files exist
    if (! Filesystem::has($default_flextype_settings_file_path)) {
        throw new RuntimeException('Flextype Default settings config file does not exist.');
    }

    if (($default_flextype_settings_content = Filesystem::read($default_flextype_settings_file_path)) === false) {
        throw new RuntimeException('Load file: ' . $default_flextype_settings_file_path . ' - failed!');
    } else {
        if (trim($default_flextype_settings_content) === '') {
            $default_flextype_settings['settings'] = [];
        } else {
            $default_flextype_settings['settings'] = SymfonyYaml::parse($default_flextype_settings_content);
        }
    }

    // Create flextype custom settings file
    ! Filesystem::has($custom_flextype_settings_file_path) and Filesystem::write($custom_flextype_settings_file_path, $default_flextype_settings_content);

    if (($custom_flextype_settings_content = Filesystem::read($custom_flextype_settings_file_path)) === false) {
        throw new RuntimeException('Load file: ' . $custom_flextype_settings_file_path . ' - failed!');
    } else {
        if (trim($custom_flextype_settings_content) === '') {
            $custom_flextype_settings['settings'] = [];
        } else {
            $custom_flextype_settings['settings'] = SymfonyYaml::parse($custom_flextype_settings_content);
        }
    }

    if (($flextype_manifest_content = Filesystem::read($flextype_manifest_file_path)) === false) {
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

    Filesystem::write($preflight_flextype_path . $cache_id . '.php',  sprintf('<?php return %s;', var_export($flextype_data, true)));
}

// Store flextype merged data in the flextype registry.
$registry->set('flextype', $flextype_data);
