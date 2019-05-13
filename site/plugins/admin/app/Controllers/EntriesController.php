<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Date\Date;
use Flextype\Component\Arr\Arr;
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

    }
}
