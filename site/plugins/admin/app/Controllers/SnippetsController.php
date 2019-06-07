<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @property View $view
 * @property Router $router
 * @property Snippets $snippets
 */
class SnippetsController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
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
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
            'buttons' => [
                            'snippets_create' => [
                                'link' => $this->router->pathFor('admin.snippets.add'),
                                'title' => __('admin_create_new_snippet'),
                                'attributes' => ['class' => 'float-right btn']
                            ],
                        ]
        ]
        );
    }

    /**
     * Add snippet
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
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
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ]
        ]
        );
    }

    /**
     * Add snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        if ($this->snippets->create($request->getParsedBody()['id'], "")) {
            $this->flash->addMessage('success', __('admin_message_snippet_created'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippet_was_not_created'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.snippets.index'));
    }

    /**
     * Edit snippet
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function edit(Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/snippets/edit.html',
            [
            'menu_item' => 'snippets',
            'id' => $request->getQueryParams()['id'],
            'data' => $this->snippets->fetch($request->getQueryParams()['id']),
            'links' => [
                            'snippets' => [
                                'link' => $this->router->pathFor('admin.snippets.index'),
                                'title' => __('admin_snippets'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
            'buttons' => [
                            'save_snippet' => [
                                    'link'       => 'javascript:;',
                                    'title'      => __('admin_save'),
                                    'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                ]
            ]
        ]
        );
    }

    /**
     * Edit snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function editProcess(Request $request, Response $response) : Response
    {
        if ($this->snippets->update($request->getParsedBody()['id'], $request->getParsedBody()['data'])) {
            $this->flash->addMessage('success', __('admin_message_snippets_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippets_was_not_saved'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.snippets.index'));
    }

    /**
     * Rename snippet
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
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
                            'templates' => [
                                'link' => $this->router->pathFor('admin.snippets.index'),
                                'title' => __('admin_templates'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ]
        ]
        );
    }

    /**
     * Rename snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function renameProcess(Request $request, Response $response) : Response
    {
        if ($this->snippets->rename(
            $request->getParsedBody()['id_current'],
            $request->getParsedBody()['id']
        )
        ) {
            $this->flash->addMessage('success', __('admin_message_snippets_renamed'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippets_was_not_renamed'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.snippets.index'));
    }

    /**
     * Delete snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        if ($this->snippets->delete($request->getParsedBody()['snippet-id'])) {
            $this->flash->addMessage('success', __('admin_message_snippets_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippets_was_not_deleted'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.snippets.index'));
    }

    /**
     * Duplicate snippet process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function duplicateProcess(Request $request, Response $response) : Response
    {
        if ($this->snippets->copy($request->getParsedBody()['snippet-id'], $request->getParsedBody()['snippet-id'] . '-duplicate-' . date("Ymd_His"))) {
            $this->flash->addMessage('success', __('admin_message_snippets_duplicated'));
        } else {
            $this->flash->addMessage('error', __('admin_message_snippets_was_not_duplicated'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.snippets.index'));
    }
}
