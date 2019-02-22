<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Notification\Notification;
use Flextype\Component\Form\Form;
use Flextype\Component\Arr\Arr;
use function Flextype\Component\I18n\__;

class FieldsetsManager
{
    public static function getFieldsetsManager()
    {
        Registry::set('sidebar_menu_item', 'fieldsets');

        switch (Http::getUriSegment(2)) {
            case 'add':
                FieldsetsManager::addFieldsets();
            break;
            case 'delete':
                FieldsetsManager::deleteFieldsets();
            break;
            case 'rename':
                FieldsetsManager::renameFieldsets();
            break;
            case 'duplicate':
                FieldsetsManager::duplicateFieldsets();
            break;
            case 'edit':
                FieldsetsManager::editFieldsets();
            break;
            default:
                FieldsetsManager::listFieldsets();
            break;
        }
    }

    /**
     * Fetch Fieldset form
     *
     * @access public
     * @param array  $fieldset Fieldset
     * @param string $values   Fieldset values
     * @return string Returns form based on fieldsets
     */
    public static function fetchForm(array $fieldset, array $values = []) : string
    {
        $form = '';

        $form .= Form::open(null, ['id' => 'form']);
        $form .= Form::hidden('token', Token::generate());
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
                            $property['attributes']['class'] .= ' js-html-editor';
                            $form_element = Form::textarea($element, $form_value, $property['attributes']);
                        break;

                        // Selectbox field
                        case 'select':
                            $form_element = Form::select($form_element_name, $property['options'], $form_value, $property['attributes']);
                        break;

                        // Template select field for selecting entry template
                        case 'template_select':
                            $form_element = Form::select($form_element_name, Themes::getTemplates(), $form_value, $property['attributes']);
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

    protected static function addFieldsets()
    {
        $create_fieldset = Http::post('create_fieldset');

        if (isset($create_fieldset)) {
            if (Token::check((Http::post('token')))) {

                $file = PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Text::safeString(Http::post('name'), '-', true) . '.yaml';

                if (!Filesystem::has($file)) {
                    // Create a fieldset!
                    if (Filesystem::write(
                            $file,
                            YamlParser::encode(['title' => Http::post('title')])
                    )) {
                        Notification::set('success', __('admin_message_fieldset_created'));
                        Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
                    }
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/fieldsets/add')
            ->display();
    }

    protected static function renameFieldsets()
    {
        $rename_fieldset = Http::post('rename_fieldset');

        if (isset($rename_fieldset)) {
            if (Token::check((Http::post('token')))) {
                if (!Filesystem::has(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml')) {
                    if (rename(
                        PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name_current') . '.yaml',
                        PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml')
                    ) {
                        Notification::set('success', __('admin_message_fieldset_renamed'));
                        Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
                    }
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/fieldsets/rename')
            ->assign('name_current', Http::get('fieldset'))
            ->display();
    }

    protected static function duplicateFieldsets()
    {
        if (Http::get('fieldset') != '') {
            if (Token::check((Http::get('token')))) {
                Filesystem::copy(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml',
                                    PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '-duplicate-' . date("Ymd_His") . '.yaml');
                Notification::set('success', __('admin_message_fieldset_duplicated'));
                Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    protected static function deleteFieldsets()
    {
        if (Http::get('fieldset') != '') {
            if (Token::check((Http::get('token')))) {
                Filesystem::delete(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml');
                Notification::set('success', __('admin_message_fieldset_deleted'));
                Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    protected static function editFieldsets()
    {
        $action = Http::post('action');

        if (isset($action) && $action == 'save-form') {
            if (Token::check((Http::post('token')))) {

                // Save a fieldset!
                if (Filesystem::write(
                        PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml',
                        Http::post('fieldset')
                )) {
                    Notification::set('success', __('admin_message_fieldset_saved'));
                    Http::redirect(Http::getBaseUrl() . '/admin/fieldsets/edit?fieldset=' . Http::post('name'));
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/fieldsets/edit')
            ->assign('fieldset', Filesystem::read(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml'))
            ->display();
    }

    protected static function listFieldsets()
    {
        Themes::view('admin/views/templates/extends/fieldsets/list')
            ->assign('fieldsets_list', Fieldsets::fetchList())
            ->display();
    }
}
