<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Form\Form;
use Flextype\Component\Html\Html;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ServerRequestInterface as Request;
use function count;
use function date;
use function Flextype\Component\I18n\__;
use function str_replace;
use function strlen;
use function strpos;
use function strtotime;
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
     * Form controls sizes
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
        '12' => 'col-12',
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
     * @param array   $fieldset Fieldset
     * @param array   $values   Fieldset values
     * @param Request $request  PSR7 request
     *
     * @return string Returns form based on fieldsets
     *
     * @access public
     */
    public function render(array $fieldset, array $values = [], Request $request) : string
    {
        $form  = Form::open(null, ['id' => 'form']);
        $form .= $this->_csrfHiddenField();
        $form .= $this->_actionHiddenField();

        // Go through all sections
        if (count($fieldset['sections']) > 0) {
            $form .= '<ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">';

            // Go through all sections and create nav items
            foreach ($fieldset['sections'] as $key => $section) {
                $form .= '<li class="nav-item">
                            <a class="nav-link ' . ($key === 'main' ? 'active' : '') . '"
                               id="pills-' . $key . '-tab"
                               data-toggle="pill" href="#pills-' . $key . '"
                               role="tab"
                               aria-controls="pills-' . $key . '"
                               aria-selected="' . ($key === 'main' ? 'true' : 'false') . '">' . __($section['title']) . '</a>
                          </li>';
            }

            $form .= '</ul>';

            $form .= '<div class="tab-content" id="pills-tabContent">';

            // Go through all sections and create nav tabs
            foreach ($fieldset['sections'] as $key => $section) {
                $form .= '<div class="tab-pane fade  ' . ($key === 'main' ? 'show active' : '') . '" id="pills-' . $key . '" role="tabpanel" aria-labelledby="pills-' . $key . '-tab">';
                $form .= '<div class="row">';

                foreach ($section['fields'] as $element => $properties) {
                    // Set empty form field element
                    $form_field = '';

                    // Create attributes
                    $properties['attributes'] = Arr::keyExists($properties, 'attributes') ? $properties['attributes'] : [];

                    // Create attribute class
                    $properties['attributes']['class'] = Arr::keyExists($properties, 'attributes.class') ? $properties['attributes']['class'] : '';

                    // Set element name
                    $field_name = $this->getElementName($element);

                    // Set element id
                    $field_id = $this->getElementID($element);

                    // Set element default value
                    $field_value = Arr::keyExists($values, $element) ? Arr::get($values, $element) : (Arr::keyExists($properties, 'value') ? $properties['value'] : '');

                    // Field types
                    switch ($properties['type']) {
                        // Simple text-input, for multi-line fields.
                        case 'textarea':
                            $form_field = $this->textareaField($field_id, $field_name, $field_value, $properties);
                            break;
                        // The hidden field is like the text field, except it's hidden from the content editor.
                        case 'hidden':
                            $form_field = $this->hiddenField($field_id, $field_name, $field_value, $properties);
                            break;
                        // A WYSIWYG HTML field.
                        case 'html':
                            $form_field = $this->htmlField($field_id, $field_name, $field_value, $properties);
                            break;
                        // Selectbox field
                        case 'select':
                            $form_field = $this->selectField($field_id, $field_name, $properties['options'], $field_value, $properties);
                            break;
                        // Template select field for selecting entry template
                        case 'template_select':
                            $form_field = $this->templateSelectField($field_id, $field_name, $field_value, $properties);
                            break;
                        // Visibility select field for selecting entry visibility state
                        case 'visibility_select':
                            $form_field = $this->visibilitySelectField($field_id, $field_name, ['draft' => __('admin_entries_draft'), 'visible' => __('admin_entries_visible'), 'hidden' => __('admin_entries_hidden')], (! empty($field_value) ? $field_value : 'visible'), $properties);
                            break;
                        case 'heading':
                            $form_field = $this->headingField($field_id, $properties);
                            break;
                        case 'routable_select':
                            $form_field = $this->routableSelectField($field_id, $field_name, [true => __('admin_yes'), false => __('admin_no')], (is_string($field_value) ? true : ($field_value ? true : false)), $properties);
                            break;
                        case 'tags':
                            $form_field = $this->tagsField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'datetimepicker':
                            $form_field = $this->dateField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'media_select':
                            $form_field = $this->mediaSelectField($field_id, $field_name, $this->flextype->EntriesController->getMediaList($request->getQueryParams()['id'], false), $field_value, $properties);
                            break;
                        // Simple text-input, for single-line fields.
                        default:
                            $form_field = $this->textField($field_id, $field_name, $field_value, $properties);
                            break;
                    }

                    $form .= $form_field;
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
     * Get element name
     *
     * @param string $element Element
     *
     * @return string Returns form element name
     *
     * @access protected
     */
    protected function getElementName(string $element) : string
    {
        $pos = strpos($element, '.');

        if ($pos === false) {
            $field_name = $element;
        } else {
            $field_name = str_replace('.', '][', "$element") . ']';
        }

        $pos = strpos($field_name, ']');

        if ($pos !== false) {
            $field_name = substr_replace($field_name, '', $pos, strlen(']'));
        }

        return $field_name;
    }

    /**
     * Get element id
     *
     * @param string $element Element
     *
     * @return string Returns form element id
     *
     * @access protected
     */
    protected function getElementID(string $element) : string
    {
        $pos = strpos($element, '.');

        if ($pos === false) {
            $field_name = $element;
        } else {
            $field_name = str_replace('.', '_', "$element");
        }

        return $field_name;
    }

    /**
     * Media select field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param array  $options    Field options
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function mediaSelectField(string $field_id, string $field_name, array $options, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::select($field_name, $options, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Template select field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function templateSelectField(string $field_id, string $field_name, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $_templates_list = $this->flextype['themes']->getTemplates($this->flextype['registry']->get('settings.theme'));

        if (count($_templates_list) > 0) {
            foreach ($_templates_list as $template) {
                if ($template['type'] !== 'file' || $template['extension'] !== 'html') {
                    continue;
                }

                $options[$template['basename']] = $template['basename'];
            }
        }

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::select($field_name, $options, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Routable select field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param array  $options    Field options
     * @param mixed  $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function routableSelectField(string $field_id, string $field_name, array $options, $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';


        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::select($field_name, $options, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Select field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param array  $options    Field options
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function selectField(string $field_id, string $field_name, array $options,  $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::select($field_name, $options, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Heading field
     *
     * @param string $field_id   Field ID
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function headingField(string $field_id, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $h     = isset($properties['h'])     ? $properties['h'] : 3;

        $properties['attributes']['id'] = $field_id;

        $field   = '<div class="form-group ' . $size . '">';
        $field  .= Html::heading(__($title), $h, $properties['attributes']);
        $field  .= '</div>';

        return $field;
    }

    /**
     * Html field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function htmlField(string $field_id, string $field_name, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id']     = $field_id;
        $properties['attributes']['class'] .= ' js-html-editor';
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::textarea($field_name, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Hidden field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function hiddenField(string $field_id, string $field_name, string $value, array $properties) : string
    {
        $properties['attributes']['id'] = $field_id;

        return Form::hidden($field_name, $value, $properties['attributes']);
    }

    /**
     * Textarea field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function textareaField(string $field_id, string $field_name, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::textarea($field_name, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Visibility field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function visibilitySelectField(string $field_id, string $field_name, array $options, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::select($field_name, $options, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Text field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function textField(string $field_id, string $field_name, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= Form::input($field_name, $value, $properties['attributes']);
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Tags field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function tagsField(string $field_id, string $field_name, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $properties['attributes']['id'] = $field_id;
        $properties['attributes']['class'] .= ' ' . $this->field_class;

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= '<input type="text" value="' . $value . '" name="' . $field_name . '" class="' . $properties['attributes']['class'] . '" data-role="tagsinput" />';
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';

        return $field;
    }

    /**
     * Date field
     *
     * @param string $field_id   Field ID
     * @param string $field_name Field name
     * @param string $value      Field value
     * @param array  $properties Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function dateField(string $field_id, string $field_name, string $value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $field  = '<div class="form-group ' . $size . '">';
        $field .= ($title ? Form::label($field_id, __($title)) : '');
        $field .= '<div class="input-group date" id="datetimepicker" data-target-input="nearest">';
        $field .= '<input name="' . $field_name . '" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker" value="' . date($this->flextype->registry->get('settings.date_format'), strtotime($value)) . '" />
                   <div class="input-group-append" data-target="#datetimepicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                   </div>';
        $field .= ($help ? '<small class="form-text text-muted">' . __($help) . '</small>' : '');
        $field .= '</div>';
        $field .= '</div>';

        return $field;
    }

    /**
     * _csrfHiddenField
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function _csrfHiddenField() : string
    {
        $field  = '<input type="hidden" name="' . $this->flextype['csrf']->getTokenNameKey() . '" value="' . $this->flextype['csrf']->getTokenName() . '">';
        $field .= '<input type="hidden" name="' . $this->flextype['csrf']->getTokenValueKey() . '" value="' . $this->flextype['csrf']->getTokenValue() . '">';

        return $field;
    }

    /**
     * _actionHiddenField
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function _actionHiddenField() : string
    {
        return Form::hidden('action', 'save-form');
    }
}
