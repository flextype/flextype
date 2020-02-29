<?php

declare(strict_types=1);

/**
 * @link http://digital.flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
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

class FormController extends Controller
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
        '1/12' => 'col w-1/12',
        '2/12' => 'col w-2/12',
        '3/12' => 'col w-3/12',
        '4/12' => 'col w-4/12',
        '5/12' => 'col w-5/12',
        '6/12' => 'col w-6/12',
        '7/12' => 'col w-7/12',
        '8/12' => 'col w-8/12',
        '9/12' => 'col w-9/12',
        '10/12' => 'col w-10/12',
        '12/12' => 'col w-full',
        '12' => 'col w-full',
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
        $form  = '<form method="post" id="form">';
        $form .= $this->_csrfHiddenField();
        $form .= $this->_actionHiddenField();

        // Go through all sections
        if (count($fieldset['sections']) > 0) {

            $form .= '<nav class="tabs__nav w-full"><div class="flex bg-dark text-white">';

            // Go through all sections and create nav items
            foreach ($fieldset['sections'] as $key => $section) {
                $form .= '<a href="javascript:;" class="tabs__nav__link ' . ($key === 'main' ? 'tabs__nav__link--active' : '') . '">' . __($section['title']) . '</a>';
            }

            $form .= '</div></nav>';

            $form .= '<div class="tabs flex">';

            // Go through all sections and create nav tabs
            foreach ($fieldset['sections'] as $key => $section) {
                $form .= '<div class="tabs__content w-full ' . ($key === 'main' ? 'tabs__content--active' : '') . '">';
                $form .= '<div class="row">';

                foreach ($section['fields'] as $element => $properties) {
                    // Set empty form field element
                    $form_field = '';

                    // Set element name
                    $field_name = $this->getElementName($element);

                    // Set element id
                    $field_id = $this->getElementID($element);

                    // Set element default value
                    $field_value = $this->getElementValue($element, $values, $properties);

                    // Seletct field type
                    switch ($properties['type']) {
                        case 'textarea':
                            $form_field = $this->textareaField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'hidden':
                            $form_field = $this->hiddenField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'html':
                            $form_field = $this->htmlField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'select':
                            $form_field = $this->selectField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'template_select':
                            $form_field = $this->templateSelectField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'visibility_select':
                            $form_field = $this->visibilitySelectField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'heading':
                            $form_field = $this->headingField($field_id, $properties);
                            break;
                        case 'routable_select':
                            $form_field = $this->routableSelectField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'tags':
                            $form_field = $this->tagsField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'datetimepicker':
                            $form_field = $this->dateField($field_id, $field_name, $field_value, $properties);
                            break;
                        case 'media_select':
                            $form_field = $this->mediaSelectField($field_id, $field_name, $field_value, $properties, $request);
                            break;
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

        $form .= '</form>';

        return $form;
    }

    /**
     * Get element value
     *
     * @param string $element    Form Element
     * @param array  $values     Form Values
     * @param array  $properties Field properties
     *
     * @return mixed Returns form element value
     *
     * @access protected
     */
    protected function getElementValue(string $element, array $values, array $properties)
    {
        if (Arr::keyExists($values, $element)) {
            $field_value = Arr::get($values, $element);
        } elseif(Arr::keyExists($properties, 'default')) {
            $field_value = $properties['default'];
        } else {
            $field_value = '';
        }

        return $field_value;
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
     * @param string  $field_id    Field ID
     * @param string  $field_name  Field name
     * @param mixed   $field_value Field value
     * @param array   $properties  Field properties
     * @param Request $request     PSR7 request
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function mediaSelectField(string $field_id, string $field_name, $field_value, array $properties, Request $request) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $id      = isset($properties['id'])    ? $properties['id'] : $field_id;
        $class   = isset($properties['class']) ? $properties['class'] . $this->field_class : $this->field_class;
        $name    = isset($properties['name'])  ? $properties['name'] : $field_name;
        $current_value   = isset($properties['value']) ? $properties['value'] : $field_value;

        $options = $this->flextype->EntriesController->getMediaList($request->getQueryParams()['id'], false);

        return $this->flextype['view']->fetch('plugins/form/templates/fields/select-template/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'options' => $options, 'current_value' => $current_value]);
    }

    /**
     * Template select field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function templateSelectField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';

        $id      = isset($properties['id'])    ? $properties['id'] : $field_id;
        $class   = isset($properties['class']) ? $properties['class'] . $this->field_class : $this->field_class;
        $name    = isset($properties['name'])  ? $properties['name'] : $field_name;
        $current_value   = isset($properties['value']) ? $properties['value'] : $field_value;

        $_templates_list = $this->flextype['themes']->getTemplates($this->flextype['registry']->get('flextype.theme'));

        $options = [];

        if (count($_templates_list) > 0) {
            foreach ($_templates_list as $template) {
                if ($template['type'] !== 'file' || $template['extension'] !== 'html') {
                    continue;
                }

                $options[$template['basename']] = $template['basename'];
            }
        }

        return $this->flextype['view']->fetch('plugins/form/templates/fields/select-template/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'options' => $options, 'current_value' => $current_value]);
    }

    /**
     * Routable select field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function routableSelectField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';
        $options = [true => __('admin_yes'), false => __('admin_no')];
        $id      = isset($properties['id'])    ? $properties['id'] : $field_id;
        $class   = isset($properties['class']) ? $properties['class'] . $this->field_class : $this->field_class;
        $name    = isset($properties['name'])  ? $properties['name'] : $field_name;
        $current_value   = isset($properties['value']) ? $properties['value'] : $field_value;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/select-routable/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'options' => $options, 'current_value' => $current_value]);
    }

    /**
     * Select field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function selectField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title   = isset($properties['title']) ? $properties['title'] : '';
        $size    = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help    = isset($properties['help'])  ? $properties['help'] : '';
        $options = isset($properties['options']) ? $properties['options'] : [];
        $id      = isset($properties['id'])    ? $properties['id'] : $field_id;
        $class   = isset($properties['class']) ? $properties['class'] . $this->field_class : $this->field_class;
        $name    = isset($properties['name'])  ? $properties['name'] : $field_name;
        $current_value   = isset($properties['value']) ? $properties['value'] : $field_value;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/select/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'options' => $options, 'current_value' => $current_value]);
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
        $id    = isset($properties['id'])    ? $properties['id'] : $field_id;
        $class = isset($properties['class']) ? $properties['class'] : '';

        return $this->flextype['view']->fetch('plugins/form/templates/fields/heading/field.html', ['title' => $title, 'size' => $size, 'h' => $h, 'id' => $id, 'class' => $class]);
    }

    /**
     * Html field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function htmlField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $help  = isset($properties['help'])  ? $properties['help'] : '';
        $class = isset($properties['class']) ? $properties['class'] : $this->field_class;
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $id    = isset($properties['id'])    ? $properties['id'] : $field_id;
        $name  = isset($properties['name'])  ? $properties['name'] : $field_name;
        $value  = isset($properties['value'])  ? $properties['value'] : $field_value;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/html/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'value' => $value]);
    }

    /**
     * Hidden field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function hiddenField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $id    = isset($properties['field_name'])  ? $properties['field_name'] : $field_id;
        $name  = isset($properties['field_name'])  ? $properties['field_name'] : $field_name;
        $value = isset($properties['field_value']) ? $properties['field_value'] : $field_value;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/hidden/field.html', ['id' => $id, 'name' => $name, 'value' => $value]);
    }

    /**
     * Textarea field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param string $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function textareaField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';
        $value = isset($properties['value']) ? $properties['value'] : $field_value;
        $id    = isset($properties['id'])    ? $properties['id'] : $field_id;
        $name  = isset($properties['name'])  ? $properties['name'] : $field_name;
        $class = isset($properties['class']) ? $properties['class'] : $this->field_class;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/textarea/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'value' => $value]);
    }

    /**
     * Visibility field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function visibilitySelectField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title = isset($properties['title']) ? $properties['title'] : '';
        $size  = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help  = isset($properties['help'])  ? $properties['help'] : '';
        $options = ['draft' => __('admin_entries_draft'), 'visible' => __('admin_entries_visible'), 'hidden' => __('admin_entries_hidden')];
        $id      = isset($properties['id'])    ? $properties['id'] : $field_id;
        $class   = isset($properties['class']) ? $properties['class'] . $this->field_class : $this->field_class;
        $name    = isset($properties['name'])  ? $properties['name'] : $field_name;
        $current_value   = isset($properties['value']) ? $properties['value'] : $field_value;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/select-visibility/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'options' => $options, 'current_value' => $current_value]);
    }

    /**
     * Text field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function textField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title  = isset($properties['title']) ? $properties['title'] : '';
        $size   = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help   = isset($properties['help'])  ? $properties['help'] : '';
        $id     = isset($properties['id'])    ? $properties['id'] : $field_id;
        $name   = isset($properties['name'])  ? $properties['name'] : $field_name;
        $class  = isset($properties['class']) ? $properties['class'] : $this->field_class;
        $value  = isset($properties['value'])  ? $properties['value'] : $field_value;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/text/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help, 'value' => $value]);
    }

    /**
     * Tags field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function tagsField(string $field_id, string $field_name, $field_value, array $properties) : string
    {
        $title   = isset($properties['title']) ? $properties['title'] : '';
        $size    = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help    = isset($properties['help'])  ? $properties['help'] : '';
        $options = isset($properties['options']) ? $properties['options'] : [];
        $id      = isset($properties['id'])    ? $properties['id'] : $field_id;
        $class   = isset($properties['class']) ? $properties['class'] . $this->field_class : $this->field_class;
        $name    = isset($properties['name'])  ? $properties['name'] : $field_name;
        $current_value   = isset($properties['value']) ? $properties['value'] : $field_value;

        if (! empty($current_value)) {
            $current_value = array_map('trim', explode(',', $current_value));
        }
        
        return $this->flextype['view']->fetch('plugins/form/templates/fields/tags/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help , 'options' => $options, 'current_value' => $current_value]);
    }

    /**
     * Date field
     *
     * @param string $field_id    Field ID
     * @param string $field_name  Field name
     * @param mixed  $field_value Field value
     * @param array  $properties  Field properties
     *
     * @return string Returns field
     *
     * @access protected
     */
    protected function dateField(string $field_id, string $field_name, $field_value, array $properties) : string
    {

        $title  = isset($properties['title']) ? $properties['title'] : '';
        $size   = isset($properties['size'])  ? $this->sizes[$properties['size']] : $this->sizes['12'];
        $help   = isset($properties['help'])  ? $properties['help'] : '';
        $id     = isset($properties['id'])    ? $properties['id'] : $field_id;
        $name   = isset($properties['name'])  ? $properties['name'] : $field_name;
        $class  = isset($properties['class']) ? $properties['class'] : $this->field_class;
        $value  = isset($properties['value'])  ? $properties['value'] : $field_value;

        return $this->flextype['view']->fetch('plugins/form/templates/fields/datetimepicker/field.html', ['title' => $title, 'size' => $size, 'name' => $name, 'id' => $id, 'class' => $class, 'help' => $help, 'value' => $value]);
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
        return $this->flextype['view']->fetch('plugins/form/templates/fields/hidden-csrf/field.html',
                           ['getTokenNameKey' => $this->flextype['csrf']->getTokenNameKey(),
                            'getTokenName' => $this->flextype['csrf']->getTokenName(),
                            'getTokenValueKey' => $this->flextype['csrf']->getTokenValueKey(),
                            'getTokenValue' => $this->flextype['csrf']->getTokenValue()]);
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
        return $this->flextype['view']->fetch('plugins/form/templates/fields/hidden-action/field.html');
    }
}
