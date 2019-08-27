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
     *
     * @var
     * @access private
     */
    private $flextype;

    /**
     * Sizes
     *
     * @var array
     * @access private
     */
    private $sizes = [
        '1/12' => 'col-1',
        '2/12' => 'col-2',
        '3/12' => 'col-3',
        '4/12' => 'col-4',
        '5/12' => 'col-5',
        '6/12' => 'col-6',
        '7/12' => 'col-7',
        '8/12' => 'col-8',
        '9/12' => 'col-9',
        '10/12' => 'col-19',
        '12/12' => 'col-11',
        '12' => 'col-12'
    ];

    /**
     * Field class
     *
     * @var string
     * @access private
     */
    private $field_class = 'form-control';

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
     * Render form
     *
     * @param array  $fieldset Fieldset
     * @param array  $values   Fieldset values
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return string Returns form based on fieldsets
     *
     * @access public
     */
    public function render(array $fieldset, array $values = [], Request $request, Response $response) : string
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
                    $property['attributes']['class'] = Arr::keyExists($property, 'attributes.class') ? $this->field_class . ' ' . $property['attributes']['class'] : $this->field_class;

                    // Create attribute size
                    $property['size'] = Arr::keyExists($property, 'size') ? $this->sizes[$property['size']] : $this->sizes['12'];

                    // Create attribute value
                    $property['value'] = Arr::keyExists($property, 'value') ? $property['value'] : '';

                    // Create attribute value
                    $property['label'] = Arr::keyExists($property, 'label') ? $property['label'] : true;

                    $pos = strpos($element, '.');

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

                    // Form elements
                    switch ($property['type']) {
                        // Simple text-input, for multi-line fields.
                        case 'textarea':
                            $form_element = $this->textareaField($element, $form_value, $property);
                            break;
                        // The hidden field is like the text field, except it's hidden from the content editor.
                        case 'hidden':
                            $form_element = $this->hiddenField($element, $form_value, $property);
                            break;
                        // A WYSIWYG HTML field.
                        case 'html':
                            $form_element = $this->htmlField($element, $form_value, $property);
                            break;
                        // Selectbox field
                        case 'select':
                            $form_element = $this->selectField($form_element_name, $property['options'], $form_value, $property);
                            break;
                        // Template select field for selecting entry template
                        case 'template_select':
                            $form_element = $this->templateSelectField($form_element_name, [], $form_value, $property);
                            break;
                        // Visibility select field for selecting entry visibility state
                        case 'visibility_select':
                            $form_element = $this->visibilitySelectField($form_element_name, ['draft' => __('admin_entries_draft'), 'visible' => __('admin_entries_visible'), 'hidden' => __('admin_entries_hidden')], (! empty($form_value) ? $form_value : 'visible'), $property);
                            break;
                        // Media select field
                        case 'media_select':
                            //$form_element = $this->mediaSelectField($form_element_name, $this->getMediaList($request->getQueryParams()['id'], false), $form_value, $property['attributes']);
                            break;
                        // Simple text-input, for single-line fields.
                        default:
                            $form_element = $this->textField($form_element_name, $form_value, $property);
                            break;
                    }

                    if ($property['label'] == true) {
                        $form_label = Form::label($element, __($property['title']));
                    } else {
                        $form_label = '';
                    }

                    $form .= '<div class="form-group ' . $property['size'] . '">';
                    $form .= $form_label . $form_element;
                    $form .= '</div>';
                }
                $form .= '</div>';
                $form .= '</div>';
            }
            $form .= '</div>';
        }

        $form .= Form::close();

        return $form;
    }

    protected function templateSelectField($name, $options, $value, $property)
    {
        $form_element = '';

        if ($this->flextype['registry']->has('settings.theme')) {
            $_templates_list = $this->flextype['themes']->getTemplates($this->flextype['registry']->get('settings.theme'));

            $options = [];

            if (count($_templates_list) > 0) {
                foreach ($_templates_list as $template) {
                    if ($template['type'] !== 'file' || $template['extension'] !== 'html') {
                        continue;
                    }

                    $options[$template['basename']] = $template['basename'];
                }
            }

            $form_element = Form::select($name, $options, $value, $property['attributes']);
        }

        return $form_element;
    }

    protected function selectField($name, $options, $value, $property)
    {
        return Form::select($name, $options, $value, $property['attributes']);
    }

    protected function htmlField($name, $value, $property)
    {
        $property['attributes']['class'] .= ' js-html-editor';

        return Form::textarea($name, $value, $property['attributes']);
    }

    protected function hiddenField($name, $value, $property)
    {
        return Form::hidden($name, $value, $property['attributes']);
    }

    protected function textareaField($name, $value, $property)
    {
        return Form::textarea($name, $value, $property['attributes']);
    }

    protected function visibilitySelectField($name, $options, $value, $property)
    {
        return Form::select($name, $options, $value, $property['attributes']);
    }

    protected function mediaSelectField($name, $options, $value, $property)
    {
        return Form::select($name, $options, $value, $property['attributes']);
    }

    protected function textField($name, $value, $property)
    {
        return Form::input($name, $value, $property['attributes']);
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
