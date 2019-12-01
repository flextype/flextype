<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Date\Date;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_merge;
use function Flextype\Component\I18n\__;

/**
 * @property View $view
 * @property Router $router
 * @property Cache $cache
 * @property Entries $entries
 * @property Plugins $plugins
 * @property Registry $registry
 * @property Flash $flash
 */
class SettingsController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        $entries = [];
        foreach ($this->entries->fetch('', ['order_by' => ['field' => 'published_at', 'direction' => 'desc']]) as $entry) {
            $entries[$entry['slug']] = $entry['title'];
        }

        $themes = [];
        foreach ($this->registry->get('themes') as $key => $theme) {
            $themes[$key] = $theme['name'];
        }

        $available_locales = Filesystem::listContents(PATH['plugins'] . '/admin/lang/');
        $system_locales    = $this->plugins->getLocales();
        $locales           = [];
        foreach ($available_locales as $locale) {
            if ($locale['type'] !== 'file' || $locale['extension'] !== 'yaml') {
                continue;
            }

            $locales[$locale['basename']] = $system_locales[$locale['basename']]['nativeName'];
        }

        $cache_driver = [
            'auto' => 'Auto Detect',
            'file' => 'File',
            'apcu' => 'APCu',
            'wincache' => 'WinCache',
            'memcached' => 'Memcached',
            'redis' => 'Redis',
            'sqlite3' => 'SQLite3',
            'zend' => 'Zend',
            'array' => 'Array',
        ];

        $image_driver = [
            'gd' => 'gd',
            'imagick' => 'imagick',
        ];

        $whoops_editor = [
            'emacs' => 'Emacs',
            'idea' => 'IDEA',
            'macvim' => 'MacVim',
            'phpstorm' => 'PhpStorm (macOS only)',
            'sublime' => 'Sublime Text',
            'textmate' => 'Textmate',
            'xdebug' => 'xDebug',
            'vscode' => 'VSCode',
            'atom' => 'Atom',
            'espresso' => 'Espresso',
        ];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/settings/index.html',
            [
                'timezones' => Date::timezones(),
                'date_formats' => $this->dateFormats(),
                'date_display_format' => $this->displayDateFormats(),
                'cache_driver' => $cache_driver,
                'locales' => $locales,
                'entries' => $entries,
                'themes' => $themes,
                'image_driver' => $image_driver,
                'whoops_editor' => $whoops_editor,
                'menu_item' => 'settings',
                'links' => [
                    'settings' => [
                        'link' => $this->router->pathFor('admin.settings.index'),
                        'title' => __('admin_settings'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons'  => [
                    'save' => [
                        'link'       => 'javascript:;',
                        'title'      => __('admin_save'),
                        'attributes' => ['class' => 'js-save-form-submit float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Update settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function updateSettingsProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        Arr::delete($data, 'csrf_name');
        Arr::delete($data, 'csrf_value');
        Arr::delete($data, 'action');

        Arr::set($data, 'errors.display', ($data['errors']['display'] === '1'));
        Arr::set($data, 'cache.enabled', ($data['cache']['enabled'] === '1'));
        Arr::set($data, 'slugify.lowercase_after_regexp', ($data['slugify']['lowercase_after_regexp'] === '1'));
        Arr::set($data, 'slugify.strip_tags', ($data['slugify']['strip_tags'] === '1'));
        Arr::set($data, 'slugify.trim', ($data['slugify']['trim'] === '1'));
        Arr::set($data, 'slugify.lowercase', ($data['slugify']['lowercase'] === '1'));
        Arr::set($data, 'cache.lifetime', (int) $data['cache']['lifetime']);
        Arr::set($data, 'entries.media.upload_images_quality', (int) $data['entries']['media']['upload_images_quality']);
        Arr::set($data, 'entries.media.upload_images_width', (int) $data['entries']['media']['upload_images_width']);
        Arr::set($data, 'entries.media.upload_images_height', (int) $data['entries']['media']['upload_images_height']);

        if (Filesystem::write(PATH['config']['site'] . '/settings.yaml', $this->parser->encode(array_merge($this->registry->get('settings'), $data), 'yaml'))) {
            $this->flash->addMessage('success', __('admin_message_settings_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_settings_was_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.settings.index'));
    }

    /**
     * Return date formats allowed
     *
     * @return array
     */
    public function dateFormats()
    {
        $now = new \DateTime();

        $date_formats = [
            'd-m-Y H:i' => $now->format('d-m-Y H:i'),
            'Y-m-d H:i' => $now->format('Y-m-d H:i'),
            'm/d/Y h:i a' => $now->format('m/d/Y h:i a'),
            'H:i d-m-Y' => $now->format('H:i d-m-Y'),
            'h:i a m/d/Y' => $now->format('h:i a m/d/Y'),
        ];

        return $date_formats;
    }

    /**
     * Return display date formats allowed
     *
     * @return array
     */
    public function displayDateFormats() : array
    {
        $now = new \DateTime();

        $date_formats = [
            'F jS \\a\\t g:ia' => $now->format('F jS \\a\\t g:ia'),
            'l jS \\of F g:i A' => $now->format('l jS \\of F g:i A'),
            'D, d M Y G:i:s' => $now->format('m/d/Y h:i a'),
            'd-m-y G:i' => $now->format('d-m-y G:i'),
            'jS M Y' => $now->format('jS M Y'),
        ];

        return $date_formats;
    }
}
