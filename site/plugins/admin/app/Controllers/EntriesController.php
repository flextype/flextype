<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
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
 */
class EntriesController extends Controller
{
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
                            'entries_list' => $this->entries->fetchAll($this->getEntryID($query), ['order_by' => ['field' => 'date', 'direction' => 'desc']]),
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
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'json') {
                    $fieldset_content = JsonParser::decode(Filesystem::read($fieldset['path']));
                    if (isset($fieldset_content['sections']) && isset($fieldset_content['sections']['main']) && isset($fieldset_content['sections']['main']['fields'])) {
                        $fieldsets[$fieldset['basename']] = $fieldset_content['title'];
                    }
                }
            }
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/add.html',
            [
                            'entries_list' => $this->entries->fetchAll($this->getEntryID($query), 'date', 'DESC'),
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

            if ($this->fieldsets->has($data['fieldset'])) {

                // Get fieldset
                $fieldset = $this->fieldsets->fetch($data['fieldset']);

                // We need to check if template for current fieldset is exists
                // if template is not exist then default template will be used!
                $template_path = PATH['site'] . '/templates/' . $data['fieldset'] . '.html';
                $template = (Filesystem::has($template_path)) ? $data['fieldset'] : 'default';

                // Init entry data
                $data_from_post = [];
                $_data_from_post = [];
                $data_result = [];

                // Define data values based on POST data
                $data_from_post['title']     = $data['title'];
                $data_from_post['template']  = $template;
                $data_from_post['fieldset']  = $data['fieldset'];
                $data_from_post['date']      = date($this->registry->get('settings.date_format'), time());

                // Predefine data values based on selected fieldset
                foreach ($fieldset['sections'] as $key => $section) {
                    foreach ($section['fields'] as $element => $property) {

                        // Get values from $data_from_post
                        if (isset($data_from_post[$element])) {
                            $value = $data_from_post[$element];

                        // Get values from fieldsets predefined field values
                        } elseif (isset($property['value'])) {
                            $value = $property['value'];

                        // or set empty value
                        } else {
                            $value = '';
                        }

                        $_data_from_post[$element] = $value;

                    }
                }

                // Merge data
                if(count($_data_from_post) > 0) {
                    $data_result = array_replace_recursive($_data_from_post, $data_from_post);
                } else {
                    $data_result = $data_from_post;
                }

                if ($this->entries->create($id, $data_result)) {
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
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'json') {
                    $fieldset_content = JsonParser::decode(Filesystem::read($fieldset['path']));
                    if (isset($fieldset_content['sections']) && isset($fieldset_content['sections']['main']) && isset($fieldset_content['sections']['main']['fields'])) {
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
        Arr::delete($_data, 'csrf_name');
        Arr::delete($_data, 'csrf_value');
        Arr::delete($_data, 'save_entry');
        Arr::delete($_data, 'id');

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

        $entry_name = $this->getEntryID($query);
        $entry = $this->entries->fetch($this->getEntryID($query));

        // Set Entries ID in parts
        if (isset($query['id'])) {
            $parts = explode("/", $query['id']);
        } else {
            $parts = [0 => ''];
        }

        $entries_list = [];
        $_entries_list = $this->entries->fetchAll('', 'slug');
        $entries_list['/'] = '/';
        foreach ($_entries_list as $_entry) {
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
                            'entry_path_current' => $entry_name,
                            'entries_list' => $entries_list,
                            'name_current' => Arr::last(explode("/", $entry_name)),
                            'entry_parent' => implode('/', array_slice(explode("/", $entry_name), 0, -1)),
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
        $data = $request->getParsedBody();

        if (!$this->entries->has($data['parent_entry'] . '/' . $data['name_current'])) {
            if ($this->entries->rename(
                $data['entry_path_current'],
                $data['parent_entry'] . '/' . $this->slugify->slugify($data['name_current'])
            )) {
                $this->flash->addMessage('success', __('admin_message_entry_moved'));
            } else {
                $this->flash->addMessage('error', __('admin_message_entry_was_not_moved'));
            }

            return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . $data['parent_entry']);
        }
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
            $this->flash->addMessage('success', __('admin_message_entry_renamed'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_created'));
        }

        return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . $data['parent_entry']);
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

        $this->entries->copy($id, $id . '-duplicate-' . date("Ymd_His"), true);

        $this->flash->addMessage('success', __('admin_message_entry_duplicated'));

        return $response->withRedirect($this->router->pathFor('admin.entries.index') . '?id=' . implode('/', array_slice(explode("/", $id), 0, -1)));
    }

    /**
     * Fetch Fieldset form
     *
     * @access public
     * @param array  $fieldset Fieldset
     * @param string $values   Fieldset values
     * @return string Returns form based on fieldsets
     */
    public function fetchForm(array $fieldset, array $values = [], Request $request) : string
    {
        // CSRF token name and value
        $csrfNameKey = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName = $this->csrf->getTokenName();
        $csrfValue = $this->csrf->getTokenValue();

        $form = '';
        $form .= Form::open(null, ['id' => 'form']);
        $form .= '<input type="hidden" name="' . $this->csrf->getTokenNameKey() . '" value="' . $this->csrf->getTokenName() . '">' .
        $form .= '<input type="hidden" name="' . $this->csrf->getTokenValueKey() . '" value="' . $this->csrf->getTokenValue() . '">';
        $form .= Form::hidden('action', 'save-form');
        if (count($fieldset['sections']) > 0) {
            $form .= '<ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">';
            foreach ($fieldset['sections'] as $key => $section) {
                $form .= '<li class="nav-item">
                            <a class="nav-link '.(($key == 'main') ? 'active' : '') . '" id="pills-' . $key . '-tab" data-toggle="pill" href="#pills-' . $key . '" role="tab" aria-controls="pills-' . $key . '" aria-selected="true">' . $section['title'] . '</a>
                          </li>';
            }
            $form .= '</ul>';
            $form .= '<div class="tab-content" id="pills-tabContent">';
            foreach ($fieldset['sections'] as $key => $section) {
                $form .= '<div class="tab-pane fade show ' . (($key == 'main') ? 'active' : '') . '" id="pills-' . $key . '" role="tabpanel" aria-labelledby="pills-' . $key . '-tab">';
                $form .= '<div class="row">';
                foreach ($section['fields'] as $element => $property) {
                    // Create attributes
                    $property['attributes'] = Arr::keyExists($property, 'attributes') ? $property['attributes'] : [];
                    // Create attribute class
                    $property['attributes']['class'] = Arr::keyExists($property, 'attributes.class') ? 'form-control ' . $property['attributes']['class'] : 'form-control';
                    // Create attribute size
                    $property['size'] = Arr::keyExists($property, 'size') ? $property['size'] : 'col-12';
                    // Create attribute value
                    $property['value'] = Arr::keyExists($property, 'value') ? $property['value'] : '';
                    $pos = strpos($element, '.');
                    if ($pos === false) {
                        $form_element_name = $element;
                    } else {
                        $form_element_name = str_replace(".", "][", "$element") . ']';
                    }
                    $pos = strpos($form_element_name, ']');
                    if ($pos !== false) {
                        $form_element_name = substr_replace($form_element_name, '', $pos, strlen(']'));
                    }
                    // Form value
                    $form_value = Arr::keyExists($values, $element) ? Arr::get($values, $element) : $property['value'];
                    // Form label
                    $form_label = Form::label($element, __($property['title']));
                    // Form elements
                    switch ($property['type']) {
                        // Simple text-input, for multi-line fields.
                        case 'textarea':
                            $form_element = Form::textarea($element, $form_value, $property['attributes']);
                        break;
                        // The hidden field is like the text field, except it's hidden from the content editor.
                        case 'hidden':
                            $form_element = Form::hidden($element, $form_value);
                        break;
                        // A WYSIWYG HTML field.
                        case 'editor':
                            $property['attributes']['class'] .= ' js-html-editor';
                            $form_element = Form::textarea($element, $form_value, $property['attributes']);
                        break;
                        // Selectbox field
                        case 'select':
                            $form_element = Form::select($form_element_name, $property['options'], $form_value, $property['attributes']);
                        break;
                        // Template select field for selecting entry template
                        case 'template_select':
                            $templates_list = [];

                            $_templates_list = $this->themes->getTemplates($this->registry->get('settings.theme'));

                            if (count($_templates_list) > 0) {
                                foreach ($_templates_list as $template) {
                                    if ($template['type'] == 'file' && $template['extension'] == 'html') {
                                        $templates_list[$template['basename']] = $template['basename'];
                                    }
                                }
                            }

                            $form_element = Form::select($form_element_name, $templates_list, $form_value, $property['attributes']);
                        break;
                        // Visibility select field for selecting entry visibility state
                        case 'visibility_select':
                            $form_element = Form::select($form_element_name, ['draft' => __('admin_entries_draft'), 'visible' => __('admin_entries_visible'), 'hidden' => __('admin_entries_hidden')], (!empty($form_value) ? $form_value : 'visible'), $property['attributes']);
                        break;
                        // Media select field
                        case 'media_select':
                            $form_element = Form::select($form_element_name, $this->getMediaList($request->getQueryParams()['id'], false), $form_value, $property['attributes']);
                        break;
                        // Simple text-input, for single-line fields.
                        default:
                            $form_element = Form::input($form_element_name, $form_value, $property['attributes']);
                        break;
                    }
                    // Render form elments with labels
                    if ($property['type'] == 'hidden') {
                        $form .= $form_element;
                    } else {
                        $form .= '<div class="form-group ' . $property['size'] . '">';
                        $form .= $form_label . $form_element;
                        $form .= '</div>';
                    }
                }
                $form .= '</div>';
                $form .= '</div>';
            }
            $form .= '</div>';
        }
        $form .= Form::close();
        return $form;
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

        // Fieldsets for current entry template
        $fieldsets_path = PATH['site'] . '/fieldsets/' . (isset($entry['fieldset']) ? $entry['fieldset'] : 'default') . '.json';
        $fieldsets = JsonParser::decode(Filesystem::read($fieldsets_path));
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
                        'data' => JsonParser::encode($entry),
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
                                            'attributes' => ['class' => 'js-save-editor-form-submit float-right btn']
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
                        ],
                        'buttons' => [
                            'save_entry' => [
                                            'link'       => 'javascript:;',
                                            'title'      => __('admin_save'),
                                            'attributes' => ['class' => 'js-save-editor-form-submit float-right btn']
                                        ],
                        ]
                ]
            );
        } else {
            return $this->view->render(
                $response,
                'plugins/admin/views/templates/content/entries/edit.html',
                [
                        'parts' => $parts,
                        'i' => count($parts),
                        'last' => Arr::last($parts),
                        'form' => $this->fetchForm($fieldsets, $entry, $request),
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
                                            'attributes' => ['class' => 'js-save-editor-form-submit float-right btn']
                                        ],
                        ]
                ]
            );
        }
    }

    public function getMediaList(string $entry, bool $path = false) : array
    {
        $base_url = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->getBaseUrl();
        $files = [];
        foreach (array_diff(scandir(PATH['entries'] . '/' . $entry), ['..', '.']) as $file) {
            if (strpos($this->registry->get('settings.entries.media.accept_file_types'), $file_ext = substr(strrchr($file, '.'), 1)) !== false) {
                if (strpos($file, strtolower($file_ext), 1)) {
                    if ($path) {
                        $files[$base_url . '/' . $entry . '/' . $file] = $base_url . '/' . $entry . '/' . $file;
                    } else {
                        $files[$file] = $file;
                    }
                }
            }
        }
        return $files;
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

            if (v::json()->validate($data['data'])) {

                // Update entry
                if (Filesystem::write(PATH['entries'] . '/' . $id . '/entry.json', $data['data'])) {
                    $this->flash->addMessage('success', __('admin_message_entry_changes_saved'));
                } else {
                    $this->flash->addMessage('error', __('admin_message_entry_changes_not_saved'));
                }
            } else {
                $this->flash->addMessage('error', __('admin_message_json_invalid'));
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

            // Fetch entry
            $entry = $this->entries->fetch($id);
            Arr::delete($entry, 'slug');

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
}
