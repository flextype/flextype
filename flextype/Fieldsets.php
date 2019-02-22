<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Form\Form;
use Flextype\Component\Token\Token;
use Flextype\Component\Arr\Arr;
use function Flextype\Component\I18n\__;

class Fieldsets
{

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

                $form .= '<div class="tab-pane fade show '.(($key == 'main') ? 'active' : '').'" id="pills-'.$key.'" role="tabpanel" aria-labelledby="pills-'.$key.'-tab">';
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

    /**
     * Fetch Fieldsets for current theme
     *
     * @access public
     * @return array
     */
    public static function fetchList() : array
    {
        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::listContents(Fieldsets::_dir_location());

        // If there is any template file then go...
        if (count($_fieldsets) > 0) {
            foreach ($_fieldsets as $fieldset) {
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'yaml') {
                    $fieldset_content = YamlParser::decode(Filesystem::read($fieldset['path']));
                    $fieldsets[$fieldset['basename']] = $fieldset_content['title'];
                }
            }
        }

        // return fieldsets
        return $fieldsets;
    }

    /**
     * Rename fieldset
     *
     * @access public
     * @param string $fieldset     Fieldset
     * @param string $new_fieldset New fieldset
     * @return bool True on success, false on failure.
     */
    public static function rename(string $fieldset, string $new_fieldset) : bool
    {
        return rename(Fieldsets::_file_location($fieldset), Fieldsets::_file_location($new_fieldset));
    }

    /**
     * Update fieldset
     *
     * @access public
     * @param string $fieldset Fieldset
     * @param string $data     Data
     * @return bool True on success, false on failure.
     */
    public static function update(string $fieldset, string $data) : bool
    {
        $fieldset_file = Fieldsets::_file_location($fieldset);

        if (Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, $data);
        } else {
            return false;
        }
    }

    /**
     * Create fieldset
     *
     * @access public
     * @param string $fieldset Fieldset
     * @param string $data     Data
     * @return bool True on success, false on failure.
     */
    public static function create(string $fieldset, string $data = '') : bool
    {
        $fieldset_file = Fieldsets::_file_location($fieldset);

        // Check if new entry file exists
        if (!Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, $data);
        } else {
            return false;
        }
    }

    /**
     * Delete fieldset.
     *
     * @access public
     * @param string $fieldset Fieldset
     * @return bool True on success, false on failure.
     */
    public static function delete(string $fieldset) : bool
    {
        return Filesystem::delete(Fieldsets::_file_location($fieldset));
    }

    /**
     * Copy fieldset
     *
     * @access public
     * @param string $fieldset      Fieldset
     * @param string $new_fieldset  New fieldset
     * @return bool True on success, false on failure.
     */
    public static function copy(string $fieldset, string $new_fieldset) : bool
    {
        return Filesystem::copy(Fieldsets::_file_location($fieldset), Fieldsets::_file_location($new_fieldset), false);
    }

    /**
     * Check whether fieldset exists.
     *
     * @access public
     * @param string $fieldset Fieldset
     * @return bool True on success, false on failure.
     */
    public static function has(string $fieldset) : bool
    {
        return Filesystem::has(Fieldsets::_file_location($fieldset));
    }

    /**
     * Helper method _dir_location
     *
     * @access private
     * @return string
     */
    private static function _dir_location() : string
    {
        return PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/';
    }

    /**
     * Helper method _file_location
     *
     * @access private
     * @param string $name Name
     * @return string
     */
    private static function _file_location(string $name) : string
    {
        return PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . $name . '.yaml';
    }
}
