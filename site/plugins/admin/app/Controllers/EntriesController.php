<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Date\Date;
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
        return $this->view->render($response,
                           'plugins/admin/views/templates/content/entries/index.html', [
                           'entries_list' => $this->entries->fetchAll($this->getEntriesQuery($request->getQueryParams()['entry']), 'date', 'DESC'),
                           'entry_current' => $this->getEntriesQuery($request->getQueryParams()['entry']),
                           'menu_item' => 'entries',
                           'links' => [
                                        'entries' => [
                                               'link' => $this->router->urlFor('admin.entries.index'),
                                               'title' => __('admin_entries'),
                                               'attributes' => ['class' => 'navbar-item active']
                                           ]
                                       ],
                           'buttons'  => [
                                       'create' => [
                                               'link'       => $this->router->urlFor('admin.entries.add') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                               'title'      => __('admin_create_new_entry'),
                                               'attributes' => ['class' => 'float-right btn']
                                            ]
                                       ]
                           ]);
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

        return $this->view->render($response,
                           'plugins/admin/views/templates/content/entries/add.html', [
                           'entries_list' => $this->entries->fetchAll($this->getEntriesQuery($request->getQueryParams()['entry']), 'date', 'DESC'),
                           'menu_item' => 'entries',
                           'fieldsets' => $fieldsets,
                           'links' => [
                                       'entries' => [
                                           'link' => $this->router->urlFor('admin.entries.index'),
                                           'title' => __('admin_entries'),
                                           'attributes' => ['class' => 'navbar-item']
                                       ],
                                       'entries_add' => [
                                           'link' => $this->router->urlFor('admin.entries.add') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                           'title' => __('admin_create_new_entry'),
                                           'attributes' => ['class' => 'navbar-item active']
                                           ]
                                       ]
                        ]);
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
                $this->flash->addMessage('success', __('admin_message_entry_was_not_created'));
            }

            return $response->withRedirect($this->container->get('router')->urlFor('admin.entries.index') . '?entry=' . $data['parent_entry']);
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

        return $this->view->render($response,
                           'plugins/admin/views/templates/content/entries/type.html', [
                           'fieldset' => $entry['fieldset'],
                           'entry' => $this->getEntriesQuery($request->getQueryParams()['entry']),
                           'fieldsets' => $fieldsets,
                           'menu_item' => 'entries',
                           'links' => [
                               'entries' => [
                                   'link' => $this->router->urlFor('admin.entries.index'),
                                   'title' => __('admin_entries'),
                                   'attributes' => ['class' => 'navbar-item']
                               ],
                               'entries_type' => [
                                   'link' => $this->router->urlFor('admin.entries.type') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                   'title' => __('admin_type'),
                                   'attributes' => ['class' => 'navbar-item active']
                                   ]
                               ]
                        ]);
    }

    public function typeProcess($request, $response, $args)
    {

        $data  = [];

        $_data = $request->getParsedBody();
        $entry_name = $_data['entry'];
        $entry = $this->entries->fetch($_data['entry']);

        Arr::delete($entry, 'slug');
        Arr::delete($_data, 'csrf_name');
        Arr::delete($_data, 'csrf_value');
        Arr::delete($_data, 'type_entry');
        Arr::delete($_data, 'entry');

        $data = array_merge($entry, $_data);

        if ($this->entries->update(
            $entry_name,
            $data
        )) {
            $this->flash->addMessage('success', __('admin_message_entry_changes_saved'));
        } else {
            $this->flash->addMessage('success', __('admin_message_entry_was_not_moved'));
        }

        return $response->withRedirect($this->container->get('router')->urlFor('admin.entries.index') . '?entry=' . implode('/', array_slice(explode("/", $entry_name), 0, -1)));
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

        return $this->view->render($response,
                           'plugins/admin/views/templates/content/entries/move.html', [
                           'entry_path_current' => $entry_name,
                           'entries_list' => $entries_list,
                           'name_current' => Arr::last(explode("/", $entry_name)),
                           'entry_parent' => implode('/', array_slice(explode("/", $entry_name), 0, -1)),
                           'menu_item' => 'entries',
                           'links' => [
                               'entries' => [
                                   'link' => $this->router->urlFor('admin.entries.index'),
                                   'title' => __('admin_entries'),
                                   'attributes' => ['class' => 'navbar-item']
                               ],
                               'entries_move' => [
                                   'link' => $this->router->urlFor('admin.entries.move'),
                                   'title' => __('admin_move'),
                                   'attributes' => ['class' => 'navbar-item active']
                                   ]
                               ]
                        ]);
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
                $this->flash->addMessage('success', __('admin_message_entry_was_not_moved'));
            }

            return $response->withRedirect($this->container->get('router')->urlFor('admin.entries.index') . '?entry=' . $data['parent_entry']);
        }
    }

    public function rename($request, $response, $args)
    {
        return $this->view->render($response,
                           'plugins/admin/views/templates/content/entries/rename.html', [
                           'name_current' => Arr::last(explode("/", $this->getEntriesQuery($request->getQueryParams()['entry']))),
                           'entry_path_current' => $this->getEntriesQuery($request->getQueryParams()['entry']),
                           'entry_parent' => implode('/', array_slice(explode("/", $this->getEntriesQuery($request->getQueryParams()['entry'])), 0, -1)),
                           'menu_item' => 'entries',
                           'links' => [
                               'entries' => [
                                   'link' => $this->router->urlFor('admin.entries.index'),
                                   'title' => __('admin_entries'),
                                   'attributes' => ['class' => 'navbar-item']
                               ],
                               'entries_type' => [
                                   'link' => $this->router->urlFor('admin.entries.rename') . '?entry=' . $this->getEntriesQuery($request->getQueryParams()['entry']),
                                   'title' => __('admin_rename'),
                                   'attributes' => ['class' => 'navbar-item active']
                                   ]
                               ]
                        ]);
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
            $this->flash->addMessage('success', __('admin_message_entry_was_not_created'));
        }

        return $response->withRedirect($this->container->get('router')->urlFor('admin.entries.index') . '?entry=' . $data['parent_entry']);
    }

    public function deleteProcess($request, $response, $args)
    {
        $entry_name = $this->getEntriesQuery($request->getQueryParams()['entry']);
        $entry_name_current = $this->getEntriesQuery($request->getQueryParams()['entry_current']);

        if ($this->entries->delete($entry_name)) {
            $this->flash->addMessage('success', __('admin_message_entry_deleted'));
        } else {
            $this->flash->addMessage('success', __('admin_message_entry_was_not_deleted'));
        }

        return $response->withRedirect($this->container->get('router')->urlFor('admin.entries.index') . '?entry=' . $entry_name_current);
    }

    public function duplicateProcess($request, $response, $args)
    {
        $entry_name = $this->getEntriesQuery($request->getQueryParams()['entry']);

        $this->entries->copy($entry_name, $entry_name . '-duplicate-' . date("Ymd_His"), true);

        $this->flash->addMessage('success', __('admin_message_entry_duplicated'));

        return $response->withRedirect($this->container->get('router')->urlFor('admin.entries.index') . '?entry=' . implode('/', array_slice(explode("/", $entry_name), 0, -1)));
    }

    public function edit($request, $response, $args)
    {
        $entry_name = $request->getQueryParams()['entry'];

        $entry = $this->entries->fetch($entry_name);

        // Fieldset for current entry template
        $fieldset_path = PATH['themes'] . '/' . $this->registry->get('settings.theme') . '/fieldsets/' . (isset($entry['fieldset']) ? $entry['fieldset'] : 'default') . '.json';
        $fieldset = JsonParser::decode(Filesystem::read($fieldset_path));
        is_null($fieldset) and $fieldset = [];

        return $this->view->render($response,
                           'plugins/admin/views/templates/content/entries/edit.html', [
                           'entry_name' => $entry_name,
                           'entry' => $entry,
                           'fieldset' => $fieldset,
                           'templates' => $this->themes->getTemplates(),
                           'files' => $this->getMediaList($entry_name),
                           'menu_item' => 'entries',
                           'links' => [
                               'entries' => [
                                   'link' => $this->router->urlFor('admin.entries.index'),
                                   'title' => __('admin_entries'),
                                   'attributes' => ['class' => 'navbar-item']
                               ]
                            ]
                        ]);
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
