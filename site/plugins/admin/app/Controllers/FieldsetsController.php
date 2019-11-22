<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Respect\Validation\Validator as v;
use function date;
use function Flextype\Component\I18n\__;

/**
 * @property View $view
 * @property Fieldsets $fieldsets
 * @property Router $router
 * @property Slugify $slugify
 * @property Flash $flash
 */
class FieldsetsController extends Controller
{
    public function index($request, $response)
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/fieldsets/index.html',
            [
                'menu_item' => 'fieldsets',
                'fieldsets_list' => $this->fieldsets->fetchAll(),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'fieldsets_add' => [
                        'link' => $this->router->pathFor('admin.fieldsets.add'),
                        'title' => __('admin_create_new_fieldset'),
                        'attributes' => ['class' => 'float-right btn'],
                    ],
                ],
            ]
        );
    }

    public function add($request, $response)
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/fieldsets/add.html',
            [
                'menu_item' => 'fieldsets',
                'fieldsets_list' => $this->fieldsets->fetchAll(),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'fieldsets_add' => [
                        'link' => $this->router->pathFor('admin.fieldsets.add'),
                        'title' => __('admin_create_new_fieldset'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    public function addProcess($request, $response)
    {
        $data = $request->getParsedBody();

        Arr::delete($data, 'csrf_name');
        Arr::delete($data, 'csrf_value');

        $id   = $this->slugify->slugify($data['id']);
        $data = ['title' => $data['title'],
                 'default_field' => 'title',
                 'icon' => $data['icon'],
                 'hide' => (bool) $data['hide'],
                 'sections' => [
                   'main' => [
                       'title' => 'admin_main',
                       'fields' => [
                           'title' => [
                               'title' => 'admin_title',
                               'type' => 'text',
                               'size' => '12'
                           ],
                       ],
                   ],
                ]];

        if ($this->fieldsets->create($id, $data)) {
            $this->flash->addMessage('success', __('admin_message_fieldset_created'));
        } else {
            $this->flash->addMessage('error', __('admin_message_fieldset_was_not_created'));
        }

        return $response->withRedirect($this->router->pathFor('admin.fieldsets.index'));
    }

    public function edit($request, $response)
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/fieldsets/edit.html',
            [
                'menu_item' => 'fieldsets',
                'id' => $request->getQueryParams()['id'],
                'data' => $this->parser->encode($this->fieldsets->fetch($request->getQueryParams()['id']), 'yaml'),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'fieldsets_editor' => [
                        'link' => $this->router->pathFor('admin.fieldsets.edit') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_editor'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'save_entry' => [
                        'link' => 'javascript:;',
                        'title' => __('admin_save'),
                        'attributes' => ['class' => 'js-save-form-submit float-right btn'],
                    ],
                ],
            ]
        );
    }

    public function editProcess($request, $response)
    {
        $id   = $request->getParsedBody()['id'];
        $data = $request->getParsedBody()['data'];

        if ($this->fieldsets->update($request->getParsedBody()['id'], $this->parser->decode($data, 'yaml'))) {
            $this->flash->addMessage('success', __('admin_message_fieldset_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_fieldset_was_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.fieldsets.edit') . '?id=' . $id);
    }

    public function rename($request, $response)
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/fieldsets/rename.html',
            [
                'menu_item' => 'fieldsets',
                'id' => $request->getQueryParams()['id'],
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'fieldsets_rename' => [
                        'link' => $this->router->pathFor('admin.fieldsets.rename') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_rename'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    public function renameProcess($request, $response)
    {
        if ($this->fieldsets->rename($request->getParsedBody()['fieldset-id-current'], $request->getParsedBody()['id'])) {
            $this->flash->addMessage('success', __('admin_message_fieldset_renamed'));
        } else {
            $this->flash->addMessage('error', __('admin_message_fieldset_was_not_renamed'));
        }

        return $response->withRedirect($this->router->pathFor('admin.fieldsets.index'));
    }

    public function deleteProcess($request, $response)
    {
        if ($this->fieldsets->delete($request->getParsedBody()['fieldset-id'])) {
            $this->flash->addMessage('success', __('admin_message_fieldset_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_fieldset_was_not_deleted'));
        }

        return $response->withRedirect($this->router->pathFor('admin.fieldsets.index'));
    }

    public function duplicateProcess($request, $response)
    {
        if ($this->fieldsets->copy($request->getParsedBody()['fieldset-id'], $request->getParsedBody()['fieldset-id'] . '-duplicate-' . date('Ymd_His'))) {
            $this->flash->addMessage('success', __('admin_message_fieldset_duplicated'));
        } else {
            $this->flash->addMessage('error', __('admin_message_fieldset_was_not_duplicated'));
        }

        return $response->withRedirect($this->router->pathFor('admin.fieldsets.index'));
    }
}
