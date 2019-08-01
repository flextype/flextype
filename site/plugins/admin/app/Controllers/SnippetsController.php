<?php

declare(strict_types=1);

namespace Flextype;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function date;
use function Flextype\Component\I18n\__;

/**
 * @property View $view
 * @property Router $router
 * @property Snippets $snippets
 * @property Slugify $slugify
 * @property Flash $flash
 */
class SnippetsController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/snippets/index.html',
            [
                'menu_item' => 'snippets',
                'snippets_list' => $this->snippets->fetchAll(),
                'links' =>  [
                    'snippets' => [
                        'link' => $this->router->pathFor('admin.snippets.index'),
                        'title' => __('admin_snippets'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'snippets_create' => [
                        'link' => $this->router->pathFor('admin.snippets.add'),
                        'title' => __('admin_create_new_snippet'),
                        'attributes' => ['class' => 'float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Add snippet
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function add(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/snippets/add.html',
            [
                'menu_item' => 'snippets',
                'links' =>  [
                    'snippets' => [
                        'link' => $this->router->pathFor('admin.snippets.index'),
                        'title' => __('admin_snippets'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'snippets_rename' => [
                        'link' => $this->router->pathFor('admin.snippets.add'),
                        'title' => __('admin_create_new_snippet'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Add snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        $id = $this->slugify->slugify($request->getParsedBody()['id']);

        if ($this->snippets->create($id, '')) {
            $this->flash->addMessage('success', __('admin_message_snippet_created'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippet_was_not_created'));
        }

        return $response->withRedirect($this->router->pathFor('admin.snippets.index'));
    }

    /**
     * Edit snippet
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function edit(Request $request, Response $response) : Response
    {
        $id = $request->getQueryParams()['id'];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/snippets/edit.html',
            [
                'menu_item' => 'snippets',
                'id' => $id,
                'data' => $this->snippets->fetch($id),
                'links' => [
                    'snippets' => [
                        'link' => $this->router->pathFor('admin.snippets.index'),
                        'title' => __('admin_snippets'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'snippets_editor' => [
                        'link' => $this->router->pathFor('admin.snippets.edit') . '?id=' . $id,
                        'title' => __('admin_editor'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'save_snippet' => [
                        'link'       => 'javascript:;',
                        'title'      => __('admin_save'),
                        'attributes' => ['class' => 'js-save-form-submit float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Edit snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function editProcess(Request $request, Response $response) : Response
    {
        $id   = $request->getParsedBody()['id'];
        $data = $request->getParsedBody()['data'];

        if ($this->snippets->update($id, $data)) {
            $this->flash->addMessage('success', __('admin_message_snippet_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippet_was_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.snippets.edit') . '?id=' . $id);
    }

    /**
     * Rename snippet
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function rename(Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/snippets/rename.html',
            [
                'menu_item' => 'snippets',
                'id_current' => $request->getQueryParams()['id'],
                'links' => [
                    'snippets' => [
                        'link' => $this->router->pathFor('admin.snippets.index'),
                        'title' => __('admin_snippets'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'snippets_rename' => [
                        'link' => $this->router->pathFor('admin.snippets.rename') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_rename'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Rename snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function renameProcess(Request $request, Response $response) : Response
    {
        $id         = $this->slugify->slugify($request->getParsedBody()['id']);
        $id_current = $request->getParsedBody()['id_current'];

        if ($this->snippets->rename(
            $id_current,
            $id
        )
        ) {
            $this->flash->addMessage('success', __('admin_message_snippet_renamed'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippet_was_not_renamed'));
        }

        return $response->withRedirect($this->router->pathFor('admin.snippets.index'));
    }

    /**
     * Delete snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        $id = $request->getParsedBody()['snippet-id'];

        if ($this->snippets->delete($id)) {
            $this->flash->addMessage('success', __('admin_message_snippet_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippet_was_not_deleted'));
        }

        return $response->withRedirect($this->router->pathFor('admin.snippets.index'));
    }

    /**
     * Duplicate snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function duplicateProcess(Request $request, Response $response) : Response
    {
        $id = $request->getParsedBody()['snippet-id'];

        if ($this->snippets->copy($id, $id . '-duplicate-' . date('Ymd_His'))) {
            $this->flash->addMessage('success', __('admin_message_snippet_duplicated'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippet_was_not_duplicated'));
        }

        return $response->withRedirect($this->router->pathFor('admin.snippets.index'));
    }
}
