<?php

namespace Flextype;

/**
 *
 * Flextype Admin Plugin
 *
 * @author Romanenko Sergey / Awilum <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flextype\Component\Registry\Registry;
use Flextype\Component\I18n\I18n;
use Flextype\Component\Arr\Arr;

$uri = explode('/', \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getPath());

if (isset($uri) && isset($uri[0]) && $uri[0] == 'admin') {

    // Ensure vendor libraries exist
    !is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

    // Register The Auto Loader
    $loader = require_once $autoload;

    include_once 'routes.php';

    // Set Default Admin locale
    I18n::$locale = $flextype->registry->get('settings.locale');

    $flextype['SettingsController'] = function($container) {
        return new SettingsController($container);
    };

    $flextype['InformationController'] = function($container) {
        return new InformationController($container);
    };

    $flextype['PluginsController'] = function($container) {
        return new PluginsController($container);
    };

    $flextype['EntriesController'] = function($container) {
        return new EntriesController($container);
    };

    $flextype['FieldsetsController'] = function($container) {
        return new FieldsetsController($container);
    };

    $flextype['SnippetsController'] = function($container) {
        return new SnippetsController($container);
    };
}
