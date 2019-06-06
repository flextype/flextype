<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;

class TemplatesController extends Controller
{
    public function index($request, $response)
    {
        return $this->view->render(
           $response,
           'plugins/admin/views/templates/extends/templates/index.html',
           [
           'menu_item' => 'templates',
           'templates_list' => $this->themes->getTemplates(),
           'partials_list' => $this->themes->getPartials(),
           'links' =>  [
                            'templates' => [
                                'link' => $this->router->pathFor('admin.templates.index'),
                                'title' => __('admin_templates'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
            'buttons' => [
                            'templates_create' => [
                                'link' => $this->router->pathFor('admin.templates.add'),
                                'title' => __('admin_create_new_template'),
                                'attributes' => ['class' => 'float-right btn']
                            ],
                        ]
       ]
       );
    }

    public function add($request, $response)
    {
        return $this->view->render(
           $response,
           'plugins/admin/views/templates/extends/templates/add.html',
           [
           'menu_item' => 'templates',
           'links' =>  [
                            'templates' => [
                                'link' => $this->router->pathFor('admin.templates.index'),
                                'title' => __('admin_templates'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ]
       ]
       );
    }

    public function addProcess($request, $response)
    {
        $type = $request->getParsedBody()['type'];

        $id = Text::safeString($request->getParsedBody()['id'], '-', true) . '.html';

        $file = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $id;

        if (!Filesystem::has($file)) {
            if (Filesystem::write(
               $file,
               ""
           )) {
                $this->flash->addMessage('success', __('admin_message_'.$type.'_created'));
            } else {
                $this->flash->addMessage('error', __('admin_message_'.$type.'_was_not_created'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_'.$type.'_was_not_created'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.templates.index'));
    }

    public function edit($request, $response)
    {
        $type = $request->getQueryParams()['type'];

        return $this->view->render(
           $response,
           'plugins/admin/views/templates/extends/templates/edit.html',
           [
           'menu_item' => 'templates',
           'id' => $request->getQueryParams()['id'],
           'data' => Filesystem::read(PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getQueryParams()['id'] . '.html'),
           'type' => (($request->getQueryParams()['type'] && $request->getQueryParams()['type'] == 'partial') ? 'partial' : 'template'),
           'links' => [
                            'templates' => [
                                'link' => $this->router->pathFor('admin.templates.index'),
                                'title' => __('admin_templates'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                       ],
            'buttons' => [
                            'save_template' => [
                                    'link'       => 'javascript:;',
                                    'title'      => __('admin_save'),
                                    'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                ]
            ]
       ]
       );
    }

    public function editProcess($request, $response)
    {
        $type = $request->getParsedBody()['type_current'];

        if (Filesystem::write(PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html', $request->getParsedBody()['data'])) {
            $this->flash->addMessage('success', __('admin_message_'.$type.'_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_'.$type.'_was_not_saved'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.fieldsets.index'));
    }

    public function rename($request, $response)
    {
        return $this->view->render(
           $response,
           'plugins/admin/views/templates/extends/templates/rename.html',
           [
           'menu_item' => 'templates',
           'types' => ['partial' => __('admin_partial'), 'template' => __('admin_template')],
           'id_current' => $request->getQueryParams()['id'],
           'type_current' => (($request->getQueryParams()['type'] && $request->getQueryParams()['type'] == 'partial') ? 'partial' : 'template'),
           'links' => [
                            'templates' => [
                                'link' => $this->router->pathFor('admin.templates.index'),
                                'title' => __('admin_templates'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                       ]
       ]
       );
    }

    public function renameProcess($request, $response)
    {
        $type = $request->getParsedBody()['type_current'];

        if (!Filesystem::has(PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) .  $request->getParsedBody()['id'] . '.html')) {
            if (Filesystem::rename(
              PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()['id_current'] . '.html',
              PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()['id'] . '.html'
           )
           ) {
                $this->flash->addMessage('success', __('admin_message_'.$type.'_renamed'));
            } else {
                $this->flash->addMessage('error', __('admin_message_'.$type.'_was_not_renamed'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_'.$type.'_was_not_renamed'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.templates.index'));
    }

    public function deleteProcess($request, $response)
    {
        $type = $request->getParsedBody()['type'];

        $file_path = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()[$type.'-id'] . '.html';

        if (Filesystem::delete($file_path)) {
            $this->flash->addMessage('success', __('admin_message_'.$type.'_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_'.$type.'_was_not_deleted'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.templates.index'));
    }

    public function duplicateProcess($request, $response)
    {
        $type = $request->getParsedBody()['type'];

        $file_path = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()[$type.'-id'] . '.html';
        $file_path_new = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/' . $this->_type_location($type) . $request->getParsedBody()[$type.'-id'] . '-duplicate-' . date("Ymd_His") . '.html';

        if (Filesystem::copy($file_path, $file_path_new)) {
            $this->flash->addMessage('success', __('admin_message_'.$type.'_duplicated'));
        } else {
            $this->flash->addMessage('error', __('admin_message_'.$type.'_was_not_duplicated'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.templates.index'));
    }

    private function _type_location($type)
    {
        if ($type == 'partial') {
            $_type = '/templates/partials/';
        } else {
            $_type = '/templates/';
        }

        return $_type;
    }
}
