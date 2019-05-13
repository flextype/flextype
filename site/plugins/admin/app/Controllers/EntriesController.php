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

    }

    public function move($request, $response, $args)
    {

    }

    public function rename($request, $response, $args)
    {

    }
}
