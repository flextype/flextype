<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Date\Date;
use Flextype\Component\Form\Form;
use Flextype\Component\Arr\Arr;
use Flextype\Component\Text\Text;
use Flextype\Component\Registry\Registry;
use function Flextype\Component\I18n\__;

class EntriesController extends Controller
{
    protected function getEntriesQuery($entry)
    {
        if ($entry && $entry != '') {
            $query = $entry;
        } else {
            $query = '';
        }

        return $query;
    }

    public function index($request, $response, $args)
    {
        $id = $request->getQueryParams()['id'];

        if ($id == null) {
            $id = [0 => ''];
        } else {
            $id = explode("/", $request->getQueryParams()['id']);
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/index.html',
            [
                           'entries_list' => $this->entries->fetchAll($this->getEntriesQuery($request->getQueryParams()['id']), 'date', 'DESC'),
                           'entry_current' => $this->getEntriesQuery($request->getQueryParams()['id']),
                           'menu_item' => 'entries',
                           'parts' => $id,
                           'i' => count($id),
                           'last' => Arr::last($id),
                           'links' => [
                                        'entries' => [
                                               'link' => $this->router->pathFor('admin.entries.index'),
                                               'title' => __('admin_entries'),
                                               'attributes' => ['class' => 'navbar-item active']
                                           ]
                                       ],
                           'buttons'  => [
                                       'create' => [
                                               'link'       => $this->router->pathFor('admin.entries.add') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                               'title'      => __('admin_create_new_entry'),
                                               'attributes' => ['class' => 'float-right btn']
                                            ]
                                       ]
                           ]
        );
    }

    public function add($request, $response, $args)
    {
        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::listContents(PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/fieldsets/');

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
            'plugins/admin/views/templates/content/entries/add.html',
            [
                           'entries_list' => $this->entries->fetchAll($this->getEntriesQuery($request->getQueryParams()['entry']), 'date', 'DESC'),
                           'menu_item' => 'entries',
                           'fieldsets' => $fieldsets,
                           'links' => [
                                       'entries' => [
                                           'link' => $this->router->pathFor('admin.entries.index'),
                                           'title' => __('admin_entries'),
                                           'attributes' => ['class' => 'navbar-item']
                                       ],
                                       'entries_add' => [
                                           'link' => $this->router->pathFor('admin.entries.add') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                           'title' => __('admin_create_new_entry'),
                                           'attributes' => ['class' => 'navbar-item active']
                                           ]
                                       ]
                        ]
        );
    }

    public function addProcess($request, $response, $args)
    {
        $data = $request->getParsedBody();

        // Set parent entry
        if ($data['parent_entry']) {
            $parent_entry = '/' . $data['parent_entry'];
        } else {
            $parent_entry = '/';
        }

        // Set new entry name
        $entry = $parent_entry . Text::safeString($data['slug'], '-', true);

        // Check if new entry exists
        if (!$this->entries->has($entry)) {

            // Get fieldset
            $fieldset = JsonParser::decode(Filesystem::read(PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/fieldsets/' . $data['fieldset'] . '.json'));

            // We need to check if template for current fieldset is exists
            // if template is not exist then default template will be used!
            $template_path = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/views/templates/' . $data['fieldset'] . '.html';
            if (Filesystem::has($template_path)) {
                $template = $data['fieldset'];
            } else {
                $template = 'default';
            }

            // Init entry data
            $data = [];
            $default_data = [];

            // Define data values based on POST data
            $default_data['title']     = $data['title'];
            $default_data['template']  = $template;
            $default_data['fieldset']  = $data['fieldset'];
            $default_data['date']      = date($this->registry->get('settings.date_format'), time());


            // Predefine data values based on selected fieldset
            foreach ($fieldset['sections'] as $section) {
                foreach ($section as $key => $field) {

                    // Get values from default data
                    if (isset($default_data[$key])) {
                        $_value = $default_data[$key];

                    // Get values from fieldsets predefined field values
                    } elseif (isset($field['value'])) {
                        $_value = $field['value'];

                    // or set empty value
                    } else {
                        $_value = '';
                    }

                    $data[$key] = $_value;
                }
            }

            // Merge data
            $data = array_replace_recursive($data, $default_data);

            if ($this->entries->create($entry, $data)) {
                $this->flash->addMessage('success', __('admin_message_entry_created'));
            } else {
                $this->flash->addMessage('error', __('admin_message_entry_was_not_created'));
            }

            return $response->withRedirect($this->container->get('router')->pathFor('admin.entries.index') . '?entry=' . $data['parent_entry']);
        }
    }

    public function type($request, $response, $args)
    {
        $entry = $this->entries->fetch($this->getEntriesQuery($request->getQueryParams()['entry']));

        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::listContents(PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/fieldsets/');

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
                           'entry_name' => $this->getEntriesQuery($request->getQueryParams()['entry']),
                           'fieldsets' => $fieldsets,
                           'menu_item' => 'entries',
                           'links' => [
                               'entries' => [
                                   'link' => $this->router->pathFor('admin.entries.index'),
                                   'title' => __('admin_entries'),
                                   'attributes' => ['class' => 'navbar-item']
                               ],
                               'entries_type' => [
                                   'link' => $this->router->pathFor('admin.entries.type') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                   'title' => __('admin_type'),
                                   'attributes' => ['class' => 'navbar-item active']
                                   ]
                               ]
                        ]
        );
    }

    public function typeProcess($request, $response, $args)
    {
        $data  = [];

        $_data = $request->getParsedBody();
        $entry_name = $_data['entry_name'];
        $entry = $this->entries->fetch($_data['entry_name']);

        Arr::delete($entry, 'slug');
        Arr::delete($_data, 'csrf_name');
        Arr::delete($_data, 'csrf_value');
        Arr::delete($_data, 'type_entry');
        Arr::delete($_data, 'entry_name');

        $data = array_merge($entry, $_data);

        if ($this->entries->update(
            $entry_name,
            $data
        )) {
            $this->flash->addMessage('success', __('admin_message_entry_changes_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.entries.index') . '?entry=' . implode('/', array_slice(explode("/", $entry_name), 0, -1)));
    }

    public function move($request, $response, $args)
    {
        $entry_name = $this->getEntriesQuery($request->getQueryParams()['entry']);
        $entry = $this->entries->fetch($this->getEntriesQuery($request->getQueryParams()['entry']));

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

    public function moveProcess($request, $response, $args)
    {
        $data = $request->getParsedBody();

        if (!$this->entries->has($data['parent_entry'] . '/' . $data['name_current'])) {
            if ($this->entries->rename(
                $data['entry_path_current'],
                $data['parent_entry'] . '/' . Text::safeString($data['name_current'], '-', true)
            )) {
                $this->flash->addMessage('success', __('admin_message_entry_moved'));
            } else {
                $this->flash->addMessage('error', __('admin_message_entry_was_not_moved'));
            }

            return $response->withRedirect($this->container->get('router')->pathFor('admin.entries.index') . '?entry=' . $data['parent_entry']);
        }
    }

    public function rename($request, $response, $args)
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/rename.html',
            [
                           'name_current' => Arr::last(explode("/", $this->getEntriesQuery($request->getQueryParams()['entry']))),
                           'entry_path_current' => $this->getEntriesQuery($request->getQueryParams()['entry']),
                           'entry_parent' => implode('/', array_slice(explode("/", $this->getEntriesQuery($request->getQueryParams()['entry'])), 0, -1)),
                           'menu_item' => 'entries',
                           'links' => [
                               'entries' => [
                                   'link' => $this->router->pathFor('admin.entries.index'),
                                   'title' => __('admin_entries'),
                                   'attributes' => ['class' => 'navbar-item']
                               ],
                               'entries_type' => [
                                   'link' => $this->router->pathFor('admin.entries.rename') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                   'title' => __('admin_rename'),
                                   'attributes' => ['class' => 'navbar-item active']
                                   ]
                               ]
                        ]
        );
    }

    public function renameProcess($request, $response, $args)
    {
        $data = $request->getParsedBody();

        if ($this->entries->rename(
            $data['entry_path_current'],
            $data['entry_parent'] . '/' . Text::safeString($data['name'], '-', true)
        )) {
            $this->flash->addMessage('success', __('admin_message_entry_renamed'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_created'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.entries.index') . '?entry=' . $data['parent_entry']);
    }

    public function deleteProcess($request, $response, $args)
    {
        $entry_name = $this->getEntriesQuery($request->getQueryParams()['entry']);
        $entry_name_current = $this->getEntriesQuery($request->getQueryParams()['entry_current']);

        if ($this->entries->delete($entry_name)) {
            $this->flash->addMessage('success', __('admin_message_entry_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_entry_was_not_deleted'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.entries.index') . '?entry=' . $entry_name_current);
    }

    public function duplicateProcess($request, $response, $args)
    {
        $entry_name = $this->getEntriesQuery($request->getQueryParams()['entry']);

        $this->entries->copy($entry_name, $entry_name . '-duplicate-' . date("Ymd_His"), true);

        $this->flash->addMessage('success', __('admin_message_entry_duplicated'));

        return $response->withRedirect($this->container->get('router')->pathFor('admin.entries.index') . '?entry=' . implode('/', array_slice(explode("/", $entry_name), 0, -1)));
    }

    /**
     * Fetch Fieldset form
     *
     * @access public
     * @param array  $fieldset Fieldset
     * @param string $values   Fieldset values
     * @return string Returns form based on fieldsets
     */
    public function fetchForm(array $fieldset, array $values = []) : string
    {
        // CSRF token name and value
        $csrfNameKey = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName = $this->csrf->getTokenName();
        $csrfValue = $this->csrf->getTokenValue();

        $form = '';
        $form .= Form::open(null, ['id' => 'form']);
        $form .= '<input type="hidden" name="'.$this->csrf->getTokenNameKey().'" value="'.$this->csrf->getTokenName().'">'.
        $form .= '<input type="hidden" name="'.$this->csrf->getTokenValueKey().'" value="'.$this->csrf->getTokenValue().'">';
        $form .= Form::hidden('action', 'save-form');
        if (count($fieldset['sections']) > 0) {
            $form .= '<ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">';
            foreach ($fieldset['sections'] as $key => $section) {
                $form .=  '<li class="nav-item">
                            <a class="nav-link '.(($key == 'main') ? 'active' : '').'" id="pills-'.$key.'-tab" data-toggle="pill" href="#pills-'.$key.'" role="tab" aria-controls="pills-'.$key.'" aria-selected="true">'.$section['title'].'</a>
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
                        case 'html':
                            //$property['attributes']['id'] .= 'codex-editor';
                            //$form_element = Form::textarea($element, $form_value, $property['attributes']);
                            //$form_element = '<div id="editorjs" class="editor"></div>';
                            $form_element = $this->view->fetch(
                                'plugins/admin/views/templates/content/entries/editor.html',
                                [
                                    'form_element' => $element,
                                    'form_value' => $form_value
                                ]
                            );
                        break;
                        // Selectbox field
                        case 'select':
                            $form_element = Form::select($form_element_name, $property['options'], $form_value, $property['attributes']);
                        break;
                        // Template select field for selecting entry template
                        case 'template_select':
                            $form_element = Form::select($form_element_name, $this->themes->getTemplates(), $form_value, $property['attributes']);
                        break;
                        // Visibility select field for selecting entry visibility state
                        case 'visibility_select':
                            $form_element = Form::select($form_element_name, ['draft' => __('admin_entries_draft'), 'visible' => __('admin_entries_visible'), 'hidden' => __('admin_entries_hidden')], (!empty($form_value) ? $form_value : 'visible'), $property['attributes']);
                        break;
                        // Media select field
                        case 'media_select':
                            $form_element = Form::select($form_element_name, EntriesManager::getMediaList(Http::get('entry'), false), $form_value, $property['attributes']);
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

    public function edit($request, $response, $args)
    {
        $entry_name = $request->getQueryParams()['id'];

        $entry = $this->entries->fetch($entry_name);

        // Fieldset for current entry template
        $fieldsets_path = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/fieldsets/' . (isset($entry['fieldset']) ? $entry['fieldset'] : 'default') . '.json';
        $fieldsets = JsonParser::decode(Filesystem::read($fieldsets_path));
        is_null($fieldsets) and $fieldsets = [];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/content/entries/edit.html',
            [
                           'entry_name' => $entry_name,
                           'entry_body' => $entry,
                           'fieldsets' => $fieldsets,
                           'form' => $this->fetchForm($fieldsets, $entry),
                           'templates' => $this->themes->getTemplates(),
                           'files' => $this->getMediaList($entry_name),
                           'menu_item' => 'entries',
                           'links' => [
                               'edit_entry' => [
                                   'link' => $this->router->pathFor('admin.entries.edit') . '?entry=' . $entry_name,
                                   'title' => __('admin_content'),
                                   'attributes' => ['class' => 'navbar-item active']
                               ],
                               'edit_entry_media' => [
                                   'link' => $this->router->pathFor('admin.entries.edit') . '?entry=' . $entry_name . '&media=true',
                                   'title' => __('admin_media'),
                                   'attributes' => ['class' => 'navbar-item']
                               ],
                               'edit_entry_source' => [
                                   'link' => $this->router->pathFor('admin.entries.edit') . '?entry=' . $entry_name . '&source=true',
                                   'title' => __('admin_source'),
                                   'attributes' => ['class' => 'navbar-item']
                               ],
                            ]
                        ]
        );
    }

    public function getMediaList(string $entry, bool $path = false) : array
    {
        $files = [];
        foreach (array_diff(scandir(PATH['entries'] . '/' . $entry), ['..', '.']) as $file) {
            if (strpos($this->registry->get('settings.entries.media.accept_file_types'), $file_ext = substr(strrchr($file, '.'), 1)) !== false) {
                if (strpos($file, strtolower($file_ext), 1)) {
                    if ($path) {
                        $files[$this->uri->getBaseUrl() . '/' . $entry . '/' . $file] = $this->uri->getBaseUrl() . '/' . $entry . '/' . $file;
                    } else {
                        $files[$file] = $file;
                    }
                }
            }
        }
        return $files;
    }

    public function editProcess()
    {
        /*
        $indenter = new Indenter();

        $entry = Entries::fetch(Http::get('entry'));
        Arr::delete($entry, 'slug');
        $data = [];
        $_data = $_POST;
        Arr::delete($_data, 'token');
        Arr::delete($_data, 'action');

        foreach ($_data as $key => $_d) {
            $data[$key] = $indenter->indent($_d);
        }

        $data = array_merge($entry, $data);

        if (Entries::update(Http::get('entry'), $data)) {
            Notification::set('success', __('admin_message_entry_changes_saved'));
        } else {
            Notification::set('error', __('admin_message_entry_changes_not_saved'));
        }
        Http::redirect(Http::getBaseUrl() . '/admin/entries/edit?entry=' . Http::get('entry'));
        */
    }
}
