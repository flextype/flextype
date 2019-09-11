<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function date;
use function Flextype\Component\I18n\__;

/**
 * @property View $view
 * @property Router $router
 * @property Cache $cache
 * @property Themes $themes
 * @property Slugify $slugify
 * @property Flash $flash
 */
class TemplatesController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        // Get theme from request query params
        $theme = $request->getQueryParams()['theme'];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/themes/templates/index.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'templates_list' => $this->themes->getTemplates($theme),
                'partials_list' => $this->themes->getPartials($theme),
                'links' =>  [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.themes.index'),
                        'title' => __('admin_themes'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'templates' => [
                        'link' => $this->router->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('admin_templates'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'templates_create' => [
                        'link' => $this->router->pathFor('admin.templates.add') . '?theme=' . $theme,
                        'title' => __('admin_create_new_template'),
                        'attributes' => ['class' => 'float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Add template
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function add(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        // Get theme from request query params
        $theme = $request->getQueryParams()['theme'];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/themes/templates/add.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'links' =>  [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.themes.index'),
                        'title' => __('admin_themes'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'templates' => [
                        'link' => $this->router->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('admin_templates'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'templates_add' => [
                        'link' => $this->router->pathFor('admin.templates.add') . '?theme=' . $theme,
                        'title' => __('admin_create_new_template'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Add template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        $type  = $request->getParsedBody()['type'];
        $theme = $request->getParsedBody()['theme'];

        $id = $this->slugify->slugify($request->getParsedBody()['id']) . '.html';

        $file = PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $id;

        if (! Filesystem::has($file)) {
            if (Filesystem::write(
                $file,
                ''
            )) {
                $this->flash->addMessage('success', __('admin_message_' . $type . '_created'));
            } else {
                $this->flash->addMessage('error', __('admin_message_' . $type . '_was_not_created'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $type . '_was_not_created'));
        }

        return $response->withRedirect($this->router->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    /**
     * Edit template
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function edit(Request $request, Response $response) : Response
    {
        // Get type and theme from request query params
        $type  = $request->getQueryParams()['type'];
        $theme = $request->getQueryParams()['theme'];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/themes/templates/edit.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'id' => $request->getQueryParams()['id'],
                'data' => Filesystem::read(PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $request->getQueryParams()['id'] . '.html'),
                'type' => ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template'),
                'links' => [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.themes.index'),
                        'title' => __('admin_themes'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'templates' => [
                        'link' => $this->router->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('admin_templates'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'templates_editor' => [
                        'link' => $this->router->pathFor('admin.templates.edit') . '?id=' . $request->getQueryParams()['id'] . '&type=' . ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template') . '&theme=' . $theme,
                        'title' => __('admin_editor'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'save_template' => [
                        'link'       => 'javascript:;',
                        'title'      => __('admin_save'),
                        'attributes' => ['class' => 'js-save-form-submit float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Edit template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function editProcess(Request $request, Response $response) : Response
    {
        // Get theme and type and id from request query params
        $theme = $request->getParsedBody()['theme'];
        $id    = $request->getParsedBody()['id'];
        $type  = $request->getParsedBody()['type'];

        if (Filesystem::write(PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html', $request->getParsedBody()['data'])) {
            $this->flash->addMessage('success', __('admin_message_' . $type . '_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $type . '_was_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.templates.edit') . '?id=' . $id . '&type=' . $type . '&theme=' . $theme);
    }

    /**
     * Rename template
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function rename(Request $request, Response $response) : Response
    {
        // Get theme from request query params
        $theme = $request->getQueryParams()['theme'];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/themes/templates/rename.html',
            [
                'menu_item' => 'themes',
                'theme' => $theme,
                'types' => ['partial' => __('admin_partial'), 'template' => __('admin_template')],
                'id_current' => $request->getQueryParams()['id'],
                'type_current' => ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template'),
                'links' => [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.themes.index'),
                        'title' => __('admin_themes'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'templates' => [
                        'link' => $this->router->pathFor('admin.templates.index') . '?theme=' . $theme,
                        'title' => __('admin_templates'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'templates_rename' => [
                        'link' => $this->router->pathFor('admin.templates.rename') . '?id=' . $request->getQueryParams()['id'] . '&type=' . ($request->getQueryParams()['type'] && $request->getQueryParams()['type'] === 'partial' ? 'partial' : 'template') . '&theme=' . $theme,
                        'title' => __('admin_rename'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Rename template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function renameProcess(Request $request, Response $response) : Response
    {
        // Get theme and type from request query params
        $theme = $request->getParsedBody()['theme'];
        $type  = $request->getParsedBody()['type_current'];

        if (! Filesystem::has(PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html')) {
            if (Filesystem::rename(
                PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()['id_current'] . '.html',
                PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html'
            )
            ) {
                $this->flash->addMessage('success', __('admin_message_' . $type . '_renamed'));
            } else {
                $this->flash->addMessage('error', __('admin_message_' . $type . '_was_not_renamed'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $type . '_was_not_renamed'));
        }

        return $response->withRedirect($this->router->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    /**
     * Delete template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        // Get theme and type from request query params
        $theme = $request->getParsedBody()['theme'];
        $type  = $request->getParsedBody()['type'];

        $file_path = PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()[$type . '-id'] . '.html';

        if (Filesystem::delete($file_path)) {
            $this->flash->addMessage('success', __('admin_message_' . $type . '_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $type . '_was_not_deleted'));
        }

        return $response->withRedirect($this->router->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    /**
     * Duplicate template process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function duplicateProcess(Request $request, Response $response) : Response
    {
        // Get theme and type from request query params
        $theme = $request->getParsedBody()['theme'];
        $type  = $request->getParsedBody()['type'];

        $file_path     = PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()[$type . '-id'] . '.html';
        $file_path_new = PATH['themes'] . '/' . $theme . '/' . $this->_type_location($type) . $request->getParsedBody()[$type . '-id'] . '-duplicate-' . date('Ymd_His') . '.html';

        if (Filesystem::copy($file_path, $file_path_new)) {
            $this->flash->addMessage('success', __('admin_message_' . $type . '_duplicated'));
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $type . '_was_not_duplicated'));
        }

        return $response->withRedirect($this->router->pathFor('admin.templates.index') . '?theme=' . $theme);
    }

    private function _type_location($type)
    {
        if ($type === 'partial') {
            $_type = '/templates/partials/';
        } else {
            $_type = '/templates/';
        }

        return $_type;
    }
}
