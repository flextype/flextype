<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Form\Form;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function count;
use function Flextype\Component\I18n\__;
use function str_replace;
use function strlen;
use function strpos;
use function substr_replace;

class Forms
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Fetch Fieldset form
     *
     * @param array  $fieldset Fieldset
     * @param string $values   Fieldset values
     *
     * @return string Returns form based on fieldsets
     *
     * @access public
     */
    public function fetch(array $fieldset, array $values = [], Request $request, Response $response) : string
    {
        $form  = Form::open(null, ['id' => 'form']);
        $form .= $this->_csrfHiddenField();
        $form .= $this->_actionHiddenField();

        if (count($fieldset['sections']) > 0) {

            $form .= '<ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">';

            foreach ($fieldset['sections'] as $key => $section) {
                $form .= '<li class="nav-item">
                            <a class="nav-link ' . ($key === 'main' ? 'active' : '') . '" id="pills-' . $key . '-tab" data-toggle="pill" href="#pills-' . $key . '" role="tab" aria-controls="pills-' . $key . '" aria-selected="true">' . $section['title'] . '</a>
                          </li>';
            }

            $form .= '</ul>';

            $form .= '<div class="tab-content" id="pills-tabContent">';


            foreach ($fieldset['sections'] as $key => $section) {

                $form .= '<div class="tab-pane fade show ' . ($key === 'main' ? 'active' : '') . '" id="pills-' . $key . '" role="tabpanel" aria-labelledby="pills-' . $key . '-tab">';
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

                    $pos               = strpos($element, '.');

                    if ($pos === false) {
                        $form_element_name = $element;
                    } else {
                        $form_element_name = str_replace('.', '][', "$element") . ']';
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
                            $form_element                     = Form::textarea($element, $form_value, $property['attributes']);
                            break;
                        // Selectbox field
                        case 'select':
                            $form_element = Form::select($form_element_name, $property['options'], $form_value, $property['attributes']);
                            break;
                        // Template select field for selecting entry template
                        case 'template_select':
                            if ($this->flextype['registry']->has('settings.theme')) {
                                $_templates_list = $this->flextype['themes']->getTemplates($this->flextype['registry']->get('settings.theme'));

                                $templates_list = [];

                                if (count($_templates_list) > 0) {
                                    foreach ($_templates_list as $template) {
                                        if ($template['type'] !== 'file' || $template['extension'] !== 'html') {
                                            continue;
                                        }

                                        $templates_list[$template['basename']] = $template['basename'];
                                    }
                                }

                                $form_element = Form::select($form_element_name, $templates_list, $form_value, $property['attributes']);
                            }
                            break;
                        // Visibility select field for selecting entry visibility state
                        case 'visibility_select':
                            $form_element = Form::select($form_element_name, ['draft' => __('admin_entries_draft'), 'visible' => __('admin_entries_visible'), 'hidden' => __('admin_entries_hidden')], (! empty($form_value) ? $form_value : 'visible'), $property['attributes']);
                            break;
                        // Media select field
                        case 'media_select':
                            $form_element = $this->mediaSelectField($form_element_name, $this->getMediaList($request->getQueryParams()['id'], false), $form_value, $property['attributes']);
                            break;
                        // Simple text-input, for single-line fields.
                        default:
                            $form_element = $this->textField($form_element_name, $form_value, $property['attributes']);
                            break;
                    }
                    // Render form elments with labels
                    if ($property['type'] === 'hidden') {
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

    protected function mediaSelectField($name, $options, $value, $attributes)
    {
        return Form::select($name, $options, $value, $attributes);
    }

    protected function textField($name, $value, $attributes)
    {
        return Form::input($name, $value, $attributes);
    }

    protected function _csrfHiddenField()
    {
        $field  = '<input type="hidden" name="' . $this->flextype['csrf']->getTokenNameKey() . '" value="' . $this->flextype['csrf']->getTokenName() . '">';
        $field .= '<input type="hidden" name="' . $this->flextype['csrf']->getTokenValueKey() . '" value="' . $this->flextype['csrf']->getTokenValue() . '">';

        return $field;
    }

    protected function _actionHiddenField()
    {
        return Form::hidden('action', 'save-form');
    }
}
