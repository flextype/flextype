<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Date\Date;
use Flextype\Component\Form\Form;
use Flextype\Component\Arr\Arr;
use Flextype\Component\Text\Text;
use Flextype\Component\Registry\Registry;
use function Flextype\Component\I18n\__;
use Respect\Validation\Validator as v;
use Intervention\Image\ImageManagerStatic as Image;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


/**
 * @property View $view
 * @property Router $router
 * @property Registry $registry
 * @property Entries $entries
 * @property Fieldsets $fieldsets
 * @property Flash $flash
 * @property Csrf $csrf
 * @property Themes $themes
 * @property Slugify $slugify
 * @property Forms $forms
 */
class EntriesController extends Controller
{

    /**
     * Get Entry ID
     *
     * @param array Query
     */
    protected function getEntryID($query)
    {
        if (isset($query['id'])) {
            $_id = $query['id'];
        } else {
            $_id = '';
        }

        return $_id;
    }

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function index(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/index.html',
            [
                            'entries_list' => $this->entries->fetch($this->getEntryID($query), ['order_by' => ['field' => 'published_at', 'direction' => 'desc']]),
                            'id_current' => $this->getEntryID($query),
                            'menu_item' => 'entries',
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => Arr::last($parts),
                            'links' => [
                                        'entries' => [
                                                'link' => $this->router->pathFor('admin.entries.index'),
                                                'title' => __('admin_entries'),
                                                'attributes' => ['class' => 'navbar-item active']
                                            ]
                                        ],
                            'buttons'  => [
                                        'create' => [
                                                'link'       => $this->router->pathFor('admin.entries.add') . '?id=' . $this->getEntryID($query),
                                                'title'      => __('admin_create_new_entry'),
                                                'attributes' => ['class' => 'float-right btn']
                                            ]
                                        ]
                            ]
        );
    }

    /**
     * Create new entry page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function add(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        // Init Fieldsets
        $fieldsets = [];

        // Get fieldsets files
        $fieldsets_list = Filesystem::listContents(PATH['site'] . '/fieldsets/');

        // If there is any fieldset file then go...
        if (count($fieldsets_list) > 0) {
            foreach ($fieldsets_list as $fieldset) {
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'yaml') {
                    $fieldset_content = $this->parser->decode(Filesystem::read($fieldset['path']), 'yaml');
                    if (isset($fieldset_content['sections']) &&
                        isset($fieldset_content['sections']['main']) &&
                        isset($fieldset_content['sections']['main']['fields']) &&
                        isset($fieldset_content['sections']['main']['fields']['title'])) {
                        if (isset($fieldset_content['hide']) && $fieldset_content['hide'] == true) {
                            continue;
                        }
                        $fieldsets[$fieldset['basename']] = $fieldset_content['title'];
                    }
                }
            }
        }
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/add.html',
            [
                            'entries_list' => $this->entries->fetch($this->getEntryID($query), ['order_by' => ['field' => 'title', 'direction' => 'asc']]),
                            'menu_item' => 'entries',
                            'fieldsets' => $fieldsets,
                            'current_id' => $this->getEntryID($query),
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => Arr::last($parts),
                            'links' => [
                                        'entries' => [
                                            'link' => $this->router->pathFor('admin.entries.index'),
                                            'title' => __('admin_entries'),
                                            'attributes' => ['class' => 'navbar-item']
                                        ],
                                        'entries_add' => [
                                            'link' => $this->router->pathFor('admin.entries.add') . '?id=' . $this->getEntryID($query),
                                            'title' => __('admin_create_new_entry'),
                                            'attributes' => ['class' => 'navbar-item active']
                                            ]
                                        ]
                        ]
        );
    }

    /**
     * Create new entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        // Get data from POST
        $data = $request->getParsedBody();

        // Set parent Entry ID
        if ($data['current_id']) {
            $parent_entry_id = $data['current_id'];
        } else {
            $parent_entry_id = '';
        }

        // Set new Entry ID
        $id = $parent_entry_id . '/' . $this->slugify->slugify($data['id']);

        // Check if entry exists then try to create
        if (!$this->entries->has($id)) {

            // Check if we have fieldset for this entry
            if ($this->fieldsets->has($data['fieldset'])) {

                // Get fieldset
                $fieldset = $this->fieldsets->fetch($data['fieldset']);

                // We need to check if template for current fieldset is exists
                // if template is not exist then `default` template will be used!
                $template_path = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/templates/' . $data['fieldset'] . '.html';
                $template = (Filesystem::has($template_path)) ? $data['fieldset'] : 'default';

                // Init entry data
                $data_from_post          = [];
                $data_from_post_override = [];
                $data_result             = [];

                // Define data values based on POST data
                $data_from_post['title']    = $data['title'];
                $data_from_post['template'] = $template;
                $data_from_post['fieldset'] = $data['fieldset'];

                // Predefine data values based on fieldset default values
                foreach ($fieldset['sections'] as $section_name => $section_body) {
                    foreach ($section_body['fields'] as $field => $properties) {

                        // Ingnore fields where property: heading
                        if ($properties['type'] == 'heading') {
                            continue;
                        }

                        // Get values from $data_from_post
                        if (isset($data_from_post[$field])) {
                            $value = $data_from_post[$field];

                        // Get values from fieldsets predefined field values
                        } elseif (isset($properties['value'])) {
                            $value = $properties['value'];

                        // or set empty value
                        } else {
                            $value = '';
                        }

                        $data_from_post_override[$field] = $value;

                    }
                }

                // Merge data
                if (count($data_from_post_override) > 0) {
                    $data_result = array_replace_recursive($data_from_post_override, $data_from_post);
                } else {
                    $data_result = $data_from_post;
                }

                if ($this->entries->create($id, $data_result)) {
                    $this->clearEntryCounter($parent_entry_id);
                    $this->flash->addMessage('success', __('admin_message_entry_created'));
                } else {
                    $this->flash->addMessage('error', __('admin_message_entry_was_not_created'));
                }

            } else {
                $this->flash->addMessage('error', __('admin_message_fieldset_not_found'));
            }

            return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . $parent_entry_id);
        }
    }

    /**
     * Change entry type
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function type(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        $entry = $this->entries->fetch($this->getEntryID($query));

        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::listContents(PATH['site'] . '/fieldsets/');

        // If there is any template file then go...
        if (count($_fieldsets) > 0) {
            foreach ($_fieldsets as $fieldset) {
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'yaml') {
                    $fieldset_content = $this->parser->decode(Filesystem::read($fieldset['path']), 'yaml');
                    if (isset($fieldset_content['sections']) &&
                        isset($fieldset_content['sections']['main']) &&
                        isset($fieldset_content['sections']['main']['fields']) &&
                        isset($fieldset_content['sections']['main']['fields']['title'])) {
                        if (isset($fieldset_content['hide']) && $fieldset_content['hide'] == true) {
                            continue;
                        }
                        $fieldsets[$fieldset['basename']] = $fieldset_content['title'];
                    }
                }
            }
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/type.html',
            [
                            'fieldset' => $entry['fieldset'],
                            'fieldsets' => $fieldsets,
                            'id' => $this->getEntryID($query),
                            'menu_item' => 'entries',
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => Arr::last($parts),
                            'links' => [
                                'entries' => [
                                    'link' => $this->router->pathFor('admin.entries.index'),
                                    'title' => __('admin_entries'),
                                    'attributes' => ['class' => 'navbar-item']
                                ],
                                'entries_type' => [
                                    'link' => $this->router->pathFor('admin.entries.type') . '?id=' . $this->getEntryID($query),
                                    'title' => __('admin_type'),
                                    'attributes' => ['class' => 'navbar-item active']
                                    ]
                                ]
                        ]
        );
    }

    /**
     * Change entry type - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function typeProcess(Request $request, Response $response) : Response
    {
        $_data = $request->getParsedBody();

        $id = $_data['id'];

        $entry = $this->entries->fetch($id);

        Arr::delete($entry, 'slug');
        Arr::delete($entry, 'modified_at');
        Arr::delete($_data, 'csrf_name');
        Arr::delete($_data, 'csrf_value');
        Arr::delete($_data, 'save_entry');
        Arr::delete($_data, 'id');

        $_data['published_by'] = Session::get('uuid');

        $data = array_merge($entry, $_data);

        if ($this->entries->update(
            $id,
            $data
        )) {
            $this->flash->addMessage('success', __('admin_message_entry_changes_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $id), 0, -1)));
    }

    /**
     * Move entry
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function move(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Get Entry from Query Params
        $entry_id = $this->getEntryID($query);

        // Get current Entry ID
        $entry_id_current = Arr::last(explode("/", $entry_id));

        // Fetch entry
        $entry = $this->entries->fetch($this->getEntryID($query));

        // Set Entries IDs in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        // Get entries list
        $entries_list['/'] = '/';
        foreach ($this->entries->fetch('', ['order_by' => ['field' => ['slug']], 'recursive' => true]) as $_entry) {
            if ($_entry['slug'] != '') {
                $entries_list[$_entry['slug']] = $_entry['slug'];
            } else {
                $entries_list[$this->registry->get('settings.entries.main')] = $this->registry->get('settings.entries.main');
            }
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/move.html',
            [
                            'menu_item' => 'entries',
                            'entries_list' => $entries_list,
                            'entry_id_current' => $entry_id_current,
                            'entry_id_path_current' => $entry_id,
                            'entry_id_path_parent' => implode('/', array_slice(explode("/", $entry_id), 0, -1)),
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => Arr::last($parts),
                            'links' => [
                                'entries' => [
                                    'link' => $this->router->pathFor('admin.entries.index'),
                                    'title' => __('admin_entries'),
                                    'attributes' => ['class' => 'navbar-item']
                                ],
                                'entries_move' => [
                                    'link' => $this->router->pathFor('admin.entries.move'),
                                    'title' => __('admin_move'),
                                    'attributes' => ['class' => 'navbar-item active']
                                    ]
                                ]
                        ]
        );
    }

    /**
     * Move entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function moveProcess(Request $request, Response $response)
    {
        // Get data from POST
        $data = $request->getParsedBody();

        if (!$this->entries->has($data['parent_entry'] . '/' . $data['entry_id_current'])) {
            if ($this->entries->rename(
                $data['entry_id_path_current'],
                $data['parent_entry'] . '/' . $this->slugify->slugify($data['entry_id_current'])
            )) {
                $this->clearEntryCounter($data['parent_entry']);
                $this->flash->addMessage('success', __('admin_message_entry_moved'));
            } else {
                $this->flash->addMessage('error', __('admin_message_entry_was_not_moved'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . (($data['parent_entry'] == '/') ? '' : $data['parent_entry']));
    }

    /**
     * Rename entry
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function rename(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/rename.html',
            [
                            'name_current' => Arr::last(explode("/", $this->getEntryID($query))),
                            'entry_path_current' => $this->getEntryID($query),
                            'entry_parent' => implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                            'menu_item' => 'entries',
                            'parts' => $parts,
                            'i' => count($parts),
                            'last' => Arr::last($parts),
                            'links' => [
                                'entries' => [
                                    'link' => $this->router->pathFor('admin.entries.index'),
                                    'title' => __('admin_entries'),
                                    'attributes' => ['class' => 'navbar-item']
                                ],
                                'entries_type' => [
                                    'link' => $this->router->pathFor('admin.entries.rename') . '?id=' . $this->getEntryID($query),
                                    'title' => __('admin_rename'),
                                    'attributes' => ['class' => 'navbar-item active']
                                    ]
                                ]
                        ]
        );
    }

    /**
     * Rename entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function renameProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        if ($this->entries->rename(
            $data['entry_path_current'],
            $data['entry_parent'] . '/' . $this->slugify->slugify($data['name'])
        )) {
            $this->clearEntryCounter($data['entry_path_current']);
            $this->flash->addMessage('success', __('admin_message_entry_renamed'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_created'));
        }

        return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . $data['entry_parent']);
    }

    /**
     * Delete entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id = $data['id'];
        $id_current = $data['id-current'];

        if ($this->entries->delete($id)) {

            $this->clearEntryCounter($id_current);

            $this->flash->addMessage('success', __('admin_message_entry_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_deleted'));
        }

        return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . $id_current);
    }

    /**
     * Duplicate entry - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function duplicateProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id = $data['id'];
        $parent_id = implode('/', array_slice(explode("/", $id), 0, -1));

        $this->entries->copy($id, $id . '-duplicate-' . date("Ymd_His"), true);

        $this->clearEntryCounter($parent_id);

        $this->flash->addMessage('success', __('admin_message_entry_duplicated'));

        return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . $parent_id);
    }

    /**
     * Edit entry
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function edit(Request $request, Response $response) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        // Get Entry type
        $type = $request->getQueryParams()['type'];

        // Get Entry
        $entry = $this->entries->fetch($this->getEntryID($query));
        Arr::delete($entry, 'slug');
        Arr::delete($entry, 'modified_at');

        // Fieldsets for current entry template
        $fieldsets_path = PATH['site'] . '/fieldsets/' . (isset($entry['fieldset']) ? $entry['fieldset'] : 'default') . '.yaml';
        $fieldsets = $this->parser->decode(Filesystem::read($fieldsets_path), 'yaml');
        is_null($fieldsets) and $fieldsets = [];

        if ($type == 'source') {
            return $this->view->render(
                $response,
                'plugins/admin/views/templates/content/entries/source.html',
                [
                        'parts' => $parts,
                        'i' => count($parts),
                        'last' => Arr::last($parts),
                        'id' => $this->getEntryID($query),
                        'data' => $this->parser->encode($entry, 'frontmatter'),
                        'type' => $type,
                        'menu_item' => 'entries',
                        'links' => [
                            'entries' => [
                                'link' => $this->router->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                                'title' => __('admin_entries'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                            'edit_entry' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query). '&type=editor',
                                'title' => __('admin_editor'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                            'edit_entry_media' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=media',
                                'title' => __('admin_media'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                            'edit_entry_source' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=source',
                                'title' => __('admin_source'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                        ],
                        'buttons' => [
                            'save_entry' => [
                                            'link'       => 'javascript:;',
                                            'title'      => __('admin_save'),
                                            'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                        ],
                        ]
                ]
            );
        } elseif ($type == 'media') {
            return $this->view->render(
                $response,
                'plugins/admin/views/templates/content/entries/media.html',
                [
                        'parts' => $parts,
                        'i' => count($parts),
                        'last' => Arr::last($parts),
                        'id' => $this->getEntryID($query),
                        'files' => $this->getMediaList($this->getEntryID($query), true),
                        'menu_item' => 'entries',
                        'links' => [
                            'entries' => [
                                'link' => $this->router->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                                'title' => __('admin_entries'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                            'edit_entry' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=editor',
                                'title' => __('admin_editor'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                            'edit_entry_media' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=media',
                                'title' => __('admin_media'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                            'edit_entry_source' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=source',
                                'title' => __('admin_source'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                        ]
                ]
            );
        } else {

            // Merge current entry fieldset with global fildset
            if (isset($entry['entry_fieldset'])) {
                $form = $this->forms->render(array_replace_recursive($fieldsets, $entry['entry_fieldset']), $entry, $request);
            } else {
                $form = $this->forms->render($fieldsets, $entry, $request);
            }

            return $this->view->render(
                $response,
                'plugins/admin/views/templates/content/entries/edit.html',
                [
                        'parts' => $parts,
                        'i' => count($parts),
                        'last' => Arr::last($parts),
                        'form' => $form,
                        'menu_item' => 'entries',
                        'links' => [
                            'entries' => [
                                'link' => $this->router->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $this->getEntryID($query)), 0, -1)),
                                'title' => __('admin_entries'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                            'edit_entry' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=editor',
                                'title' => __('admin_editor'),
                                'attributes' => ['class' => 'navbar-item active']
                            ],
                            'edit_entry_media' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=media',
                                'title' => __('admin_media'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                            'edit_entry_source' => [
                                'link' => $this->router->pathFor('admin.entries.edit') . '?id=' . $this->getEntryID($query) . '&type=source',
                                'title' => __('admin_source'),
                                'attributes' => ['class' => 'navbar-item']
                            ],
                        ],
                        'buttons' => [
                            'save_entry' => [
                                            'link'       => 'javascript:;',
                                            'title'      => __('admin_save'),
                                            'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                        ],
                        ]
                ]
            );
        }
    }

    /**
     * Edit entry process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function editProcess(Request $request, Response $response) : Response
    {
        $query = $request->getQueryParams();

        // Get Entry ID and TYPE from GET param
        $id = $query['id'];
        $type = $query['type'];

        if ($type == 'source') {

            // Data from POST
            $data = $request->getParsedBody();

            $entry = $this->parser->decode($data['data'], 'frontmatter');

            $entry['published_by'] = Session::get('uuid');

            // Update entry
            if (Filesystem::write(PATH['entries'] . '/' . $id . '/entry.md', $this->parser->encode($entry, 'frontmatter'))) {
                $this->flash->addMessage('success', __('admin_message_entry_changes_saved'));
            } else {
                $this->flash->addMessage('error', __('admin_message_entry_changes_not_saved'));
            }
        } else {
            // Result data to save
            $result_data = [];

            // Data from POST
            $data = $request->getParsedBody();

            // Delete system fields
            Arr::delete($data, 'slug');
            Arr::delete($data, 'csrf_value');
            Arr::delete($data, 'csrf_name');
            Arr::delete($data, 'action');

            $data['published_by'] = Session::get('uuid');
            $data['routable'] = isset($data['routable']) ? (bool) $data['routable'] : false;

            // Fetch entry
            $entry = $this->entries->fetch($id);
            Arr::delete($entry, 'slug');
            Arr::delete($entry, 'modified_at');

            // Merge entry data with $to_save_data
            $result_data = array_merge($entry, $data);

            // Update entry
            if ($this->entries->update($id, $result_data)) {
                $this->flash->addMessage('success', __('admin_message_entry_changes_saved'));
            } else {
                $this->flash->addMessage('error', __('admin_message_entry_changes_not_saved'));
            }
        }

        return $response->withRedirect($this->router->pathFor('admin.entries.edit') . '?id=' . $id . '&type=' . $type);
    }

    /**
     * Delete media file - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function deleteMediaFileProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $entry_id = $data['entry-id'];
        $media_id = $data['media-id'];

        $files_directory = PATH['entries'] . '/' . $entry_id . '/' . $media_id;

        Filesystem::delete($files_directory);

        $this->flash->addMessage('success', __('admin_message_entry_file_deleted'));

        return $response->withRedirect($this->router->pathFor('admin.entries.edit') . '?id=' . $entry_id . '&type=media');
    }

    /**
     * Upload media file - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function uploadMediaFileProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id = $data['entry-id'];

        $files_directory = PATH['entries'] . '/' . $id . '/';

        $file = $this->_uploadFile($_FILES['file'], $files_directory, $this->registry->get('settings.entries.media.accept_file_types'), 27000000);

        if ($file !== false) {
            if (in_array(pathinfo($file)['extension'], ['jpg', 'jpeg', 'png', 'gif'])) {
                // open an image file
                $img = Image::make($file);
                // now you are able to resize the instance
                if ($this->registry->get('settings.entries.media.upload_images_width') > 0 && $this->registry->get('settings.entries.media.upload_images_height') > 0) {
                    $img->resize($this->registry->get('settings.entries.media.upload_images_width'), $this->registry->get('settings.entries.media.upload_images_height'), function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } elseif ($this->registry->get('settings.entries.media.upload_images_width') > 0) {
                    $img->resize($this->registry->get('settings.entries.media.upload_images_width'), null, function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } elseif ($this->registry->get('settings.entries.media.upload_images_height') > 0) {
                    $img->resize(null, $this->registry->get('settings.entries.media.upload_images_height'), function($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                // finally we save the image as a new file
                $img->save($file, $this->registry->get('settings.entries.media.upload_images_quality'));

                // destroy
                $img->destroy();
            }

            $this->flash->addMessage('success', __('admin_message_entry_file_uploaded'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_file_not_uploaded'));
        }

        return $response->withRedirect($this->router->pathFor('admin.entries.edit') . '?id=' . $id . '&type=media');
    }

    /**
     * Upload files on the Server with several type of Validations!
     *
     * _uploadFile($_FILES['file'], $files_directory);
     *
     * @param   array   $file             Uploaded file data
     * @param   string  $upload_directory Upload directory
     * @param   string  $allowed          Allowed file extensions
     * @param   int     $max_size         Max file size in bytes
     * @param   string  $filename         New filename
     * @param   bool    $remove_spaces    Remove spaces from the filename
     * @param   int     $max_width        Maximum width of image
     * @param   int     $max_height       Maximum height of image
     * @param   bool    $exact            Match width and height exactly?
     * @param   int     $chmod            Chmod mask
     * @return  string  on success, full path to new file
     * @return  false   on failure
     */
    public function _uploadFile(
        array $file,
        string $upload_directory,
        string $allowed = 'jpeg, png, gif, jpg',
        int $max_size = 3000000,
        string $filename = null,
        bool $remove_spaces = true,
        int $max_width = null,
        int $max_height = null,
        bool $exact = false,
        int $chmod = 0644
    ) {
        //
        // Tests if a successful upload has been made.
        //
        if (isset($file['error'])
            and isset($file['tmp_name'])
            and $file['error'] === UPLOAD_ERR_OK
            and is_uploaded_file($file['tmp_name'])) {
            //
            // Tests if upload data is valid, even if no file was uploaded.
            //
            if (isset($file['error'])
                    and isset($file['name'])
                    and isset($file['type'])
                    and isset($file['tmp_name'])
                    and isset($file['size'])) {
                //
                // Test if an uploaded file is an allowed file type, by extension.
                //
                if (strpos($allowed, strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))) !== false) {
                    //
                    // Validation rule to test if an uploaded file is allowed by file size.
                    //
                    if (($file['error'] != UPLOAD_ERR_INI_SIZE)
                                  and ($file['error'] == UPLOAD_ERR_OK)
                                  and ($file['size'] <= $max_size)) {
                        //
                        // Validation rule to test if an upload is an image and, optionally, is the correct size.
                        //
                        if (in_array(mime_content_type($file['tmp_name']), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                            function validateImage($file, $max_width, $max_height, $exact)
                            {
                                try {
                                    // Get the width and height from the uploaded image
                                    list($width, $height) = getimagesize($file['tmp_name']);
                                } catch (ErrorException $e) {
                                    // Ignore read errors
                                }
                                if (empty($width) or empty($height)) {
                                    // Cannot get image size, cannot validate
                                    return false;
                                }
                                if (!$max_width) {
                                    // No limit, use the image width
                                    $max_width = $width;
                                }
                                if (!$max_height) {
                                    // No limit, use the image height
                                    $max_height = $height;
                                }
                                if ($exact) {
                                    // Check if dimensions match exactly
                                    return ($width === $max_width and $height === $max_height);
                                } else {
                                    // Check if size is within maximum dimensions
                                    return ($width <= $max_width and $height <= $max_height);
                                }
                                return false;
                            }
                            if (validateImage($file, $max_width, $max_height, $exact) === false) {
                                return false;
                            }
                        }
                        if (!isset($file['tmp_name']) or !is_uploaded_file($file['tmp_name'])) {
                            // Ignore corrupted uploads
                            return false;
                        }
                        if ($filename === null) {
                            // Use the default filename
                            $filename = $file['name'];
                        }
                        if ($remove_spaces === true) {
                            // Remove spaces from the filename
                            $filename = $this->slugify->slugify(pathinfo($filename)['filename']) . '.' . pathinfo($filename)['extension'];
                        }
                        if (!is_dir($upload_directory) or !is_writable(realpath($upload_directory))) {
                            throw new \RuntimeException("Directory {$upload_directory} must be writable");
                        }
                        // Make the filename into a complete path
                        $filename = realpath($upload_directory) . DIRECTORY_SEPARATOR . $filename;
                        if (move_uploaded_file($file['tmp_name'], $filename)) {
                            // Set permissions on filename
                            chmod($filename, $chmod);
                            // Return new file path
                            return $filename;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get media list
     *
     * @param string $id Entry ID
     * @param bool   $path if true returns with url paths
     *
     * @return array
     */
    public function getMediaList(string $id, bool $path = false) : array
    {
        $base_url = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getBaseUrl();
        $files = [];
        foreach (array_diff(scandir(PATH['entries'] . '/' . $id), ['..', '.']) as $file) {
            if (strpos($this->registry->get('settings.entries.media.accept_file_types'), $file_ext = substr(strrchr($file, '.'), 1)) !== false) {
                if (strpos($file, strtolower($file_ext), 1)) {
                    if ($file !== 'entry.md') {
                        if ($path) {
                            $files[$base_url . '/' . $id . '/' . $file] = $base_url . '/' . $id . '/' . $file;
                        } else {
                            $files[$file] = $file;
                        }
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Clear entry counter
     *
     * @param string $id Entry ID
     */
    public function clearEntryCounter($id) : void
    {
        if ($this->cache->contains($id . '_counter')) {
            $this->cache->delete($id . '_counter');
        }
    }
}
