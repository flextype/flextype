<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Arr\Arr;
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
            'plugins/admin/templates/extends/fieldsets/index.html',
            [
                'menu_item' => 'fieldsets',
                'fieldsets_list' => $this->fieldsets->fetchAll(),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'fieldsets_add' => [
                        'link' => $this->router->pathFor('admin.fieldsets.add'),
                        'title' => __('admin_create_new_fieldset')
                    ]
                ],
            ]
        );
    }

    public function add($request, $response)
    {
        return $this->view->render(
            $response,
            'plugins/admin/templates/extends/fieldsets/add.html',
            [
                'menu_item' => 'fieldsets',
                'fieldsets_list' => $this->fieldsets->fetchAll(),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                    ],
                    'fieldsets_add' => [
                        'link' => $this->router->pathFor('admin.fieldsets.add'),
                        'title' => __('admin_create_new_fieldset'),
                        'active' => true
                    ],
                ],
            ]
        );
    }

    public function addProcess($request, $response)
    {
        // Get data from POST
        $post_data = $request->getParsedBody();

        Arr::delete($post_data, 'csrf_name');
        Arr::delete($post_data, 'csrf_value');

        $id   = $this->slugify->slugify($post_data['id']);
        $data = [
            'title' => $post_data['title'],
            'default_field' => 'title',
            'icon' => $post_data['icon'],
            'hide' => (bool) $post_data['hide'],
            'sections' => [
                'main' => [
                    'title' => 'admin_main',
                    'fields' => [
                        'title' => [
                            'title' => 'admin_title',
                            'type' => 'text',
                            'size' => '12',
                        ],
                    ],
                ],
            ],
        ];

        if ($this->fieldsets->create($id, $data)) {
            $this->flash->addMessage('success', __('admin_message_fieldset_created'));
        } else {
            $this->flash->addMessage('error', __('admin_message_fieldset_was_not_created'));
        }

        if (isset($post_data['create-and-edit'])) {
            return $response->withRedirect($this->router->pathFor('admin.fieldsets.edit') . '?id=' . $id);
        }

        return $response->withRedirect($this->router->pathFor('admin.fieldsets.index'));
    }

    public function edit($request, $response)
    {
        return $this->view->render(
            $response,
            'plugins/admin/templates/extends/fieldsets/edit.html',
            [
                'menu_item' => 'fieldsets',
                'id' => $request->getQueryParams()['id'],
                'data' => $this->parser->encode($this->fieldsets->fetch($request->getQueryParams()['id']), 'yaml'),
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                    ],
                    'fieldsets_editor' => [
                        'link' => $this->router->pathFor('admin.fieldsets.edit') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_editor'),
                        'active' => true
                    ],
                ],
                'buttons' => [
                    'save_entry' => [
                        'type' => 'action',
                        'link' => 'javascript:;',
                        'title' => __('admin_save')
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
            'plugins/admin/templates/extends/fieldsets/rename.html',
            [
                'menu_item' => 'fieldsets',
                'id' => $request->getQueryParams()['id'],
                'links' =>  [
                    'fieldsets' => [
                        'link' => $this->router->pathFor('admin.fieldsets.index'),
                        'title' => __('admin_fieldsets'),
                    ],
                    'fieldsets_rename' => [
                        'link' => $this->router->pathFor('admin.fieldsets.rename') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_rename'),
                        'active' => true
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
