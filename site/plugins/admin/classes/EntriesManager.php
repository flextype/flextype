<?php

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Number\Number;
use Flextype\Component\I18n\I18n;
use Flextype\Component\Http\Http;
use Flextype\Component\Event\Event;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Form\Form;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;
use Gajus\Dindent\Indenter;
use Intervention\Image\ImageManagerStatic as Image;

class EntriesManager
{

    public static function getEntriesManager()
    {
        Registry::set('sidebar_menu_item', 'entries');

        if (Http::get('entry') && Http::get('entry') != '') {
            $query = Http::get('entry');
        } else {
            $query = '';
        }

        switch (Http::getUriSegment(2)) {
            case 'add':
                $create_entry = Http::post('create_entry');

                if (isset($create_entry)) {
                    if (Token::check((Http::post('token')))) {
                        $file = PATH['entries'] . '/' . Http::post('parent_entry') . '/' . Text::safeString(Http::post('slug'), '-', true) . '/entry.html';

                        if (!Filesystem::fileExists($file)) {

                            // Get fieldset
                            $fieldset = YamlParser::decode(Filesystem::getFileContent(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('fieldset') . '.yaml'));

                            // We need to check if template for current fieldset is exists
                            // if template is not exist then default template will be used!
                            $template_path = PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/templates/' . Http::post('fieldset') . '.php';
                            if (Filesystem::fileExists($template_path)) {
                                $template = Http::post('fieldset');
                            } else {
                                $template = 'default';
                            }

                            // Init frontmatter
                            $frontmatter = [];
                            $_frontmatter = [];

                            // Define frontmatter values based on POST data
                            $_frontmatter['title']     = Http::post('title');
                            $_frontmatter['template']  = $template;
                            $_frontmatter['fieldset']  = Http::post('fieldset');
                            $_frontmatter['date']      = date(Registry::get('settings.date_format'), time());

                            // Define frontmatter values based on fieldset
                            foreach ($fieldset['fields'] as $key => $field) {

                                if (isset($_frontmatter[$key])) {
                                    $_value = $_frontmatter[$key];
                                } elseif(isset($field['value'])) {
                                    $_value = $field['value'];
                                } else {
                                    $_value = '';
                                }

                                $frontmatter[$key] = $_value;
                            }

                            // Delete content field from frontmatter
                            Arr::delete($frontmatter, 'content');

                            // Create a entry!
                            if (Filesystem::setFileContent(
                                  $file,
                                  '---'."\n".
                                  YamlParser::encode(array_replace_recursive($frontmatter, $_frontmatter)).
                                  '---'."\n"
                            )) {
                                Notification::set('success', __('admin_message_entry_created'));
                                Http::redirect(Http::getBaseUrl().'/admin/entries/?entry='.Http::post('parent_entry'));
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                Themes::view('admin/views/templates/content/entries/add')
                    ->assign('fieldsets', Themes::getFieldsets())
                    ->assign('entries_list', Entries::getEntries('', 'slug'))
                    ->display();
            break;
            case 'delete':
                if (Http::get('entry') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::deleteDir(PATH['entries'] . '/' . Http::get('entry'));
                        Notification::set('success', __('admin_message_entry_deleted'));
                        Http::redirect(Http::getBaseUrl() . '/admin/entries/?entry=' . Http::get('entry_current'));
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }
            break;
            case 'duplicate':
                if (Http::get('entry') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::recursiveCopy(PATH['entries'] . '/' . Http::get('entry'),
                                                  PATH['entries'] . '/' . Http::get('entry') . '-duplicate-' . date("Ymd_His"));
                        Notification::set('success', __('admin_message_entry_duplicated'));
                        Http::redirect(Http::getBaseUrl().'/admin/entries/?entry='.implode('/', array_slice(explode("/", Http::get('entry')), 0, -1)));
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }
            break;
            case 'rename':
                $entry = Entries::processEntry(PATH['entries'] . '/' . Http::get('entry') . '/entry.html', false, true);

                $rename_entry = Http::post('rename_entry');

                if (isset($rename_entry)) {
                    if (Token::check((Http::post('token')))) {
                        if (!Filesystem::dirExists(PATH['entries'] . '/' . Http::post('name'))) {
                            if (rename(
                                PATH['entries'] . '/' . Http::post('entry_path_current'),
                                PATH['entries'] . '/' . Http::post('entry_parent') . '/' . Text::safeString(Http::post('name'), '-', true)
                            )) {
                                Notification::set('success', __('admin_message_entry_renamed'));
                                Http::redirect(Http::getBaseUrl().'/admin/entries/?entry='.Http::post('entry_parent'));
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                Themes::view('admin/views/templates/content/entries/rename')
                    ->assign('name_current', Arr::last(explode("/", Http::get('entry'))))
                    ->assign('entry_path_current', Http::get('entry'))
                    ->assign('entry_parent', implode('/', array_slice(explode("/", Http::get('entry')), 0, -1)))
                    ->assign('entry', $entry)
                    ->display();
            break;
            case 'type':

                $type_entry = Http::post('type_entry');

                if (isset($type_entry)) {
                    if (Token::check((Http::post('token')))) {

                        $entry = Entries::processEntry(PATH['entries'] . '/' . Http::get('entry') . '/entry.html', false, true);

                        $content = $entry['content'];
                        Arr::delete($entry, 'content');
                        Arr::delete($entry, 'url');
                        Arr::delete($entry, 'slug');
                        Arr::delete($entry, 'base_url');

                        $frontmatter = $_POST;
                        Arr::delete($frontmatter, 'token');
                        Arr::delete($frontmatter, 'type_entry');
                        Arr::delete($frontmatter, 'entry');
                        $frontmatter = YamlParser::encode(array_merge($entry, $frontmatter));

                        if (Filesystem::setFileContent(
                            PATH['entries'] . '/' . Http::post('entry') . '/entry.html',
                                                  '---'."\n".
                                                  $frontmatter."\n".
                                                  '---'."\n".
                                                  $content
                        )) {
                              Notification::set('success', __('admin_message_entry_changes_saved'));
                              Http::redirect(Http::getBaseUrl() . '/admin/entries?entry='.implode('/', array_slice(explode("/", Http::get('entry')), 0, -1)));
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                $entry = Entries::processEntry(PATH['entries'] . '/' . Http::get('entry') . '/entry.html', false, true);

                Themes::view('admin/views/templates/content/entries/type')
                    ->assign('fieldset', $entry['fieldset'])
                    ->assign('fieldsets', Themes::getFieldsets())
                    ->display();
            break;
            case 'move':
                $entry = Entries::processEntry(PATH['entries'] . '/' . Http::get('entry') . '/entry.html', false, true);

                $move_entry = Http::post('move_entry');

                if (isset($move_entry)) {
                    if (Token::check((Http::post('token')))) {
                        if (!Filesystem::dirExists(realpath(PATH['entries'] . '/' . Http::post('parent_entry') . '/' . Http::post('name_current')))) {
                            if (rename(
                                PATH['entries'] . '/' . Http::post('entry_path_current'),
                                PATH['entries'] . '/' . Http::post('parent_entry') . '/' . Text::safeString(Http::post('name_current'), '-', true)
                            )) {
                                Notification::set('success', __('admin_message_entry_moved'));
                                Http::redirect(Http::getBaseUrl().'/admin/entries/?entry='.Http::post('parent_entry'));
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                $_entries_list = Entries::getEntries('', 'slug');
                $entries_list['/'] = '/';
                foreach ($_entries_list as $_entry) {
                    if ($_entry['slug'] != '') {
                        $entries_list[$_entry['slug']] = $_entry['slug'];
                    } else {
                        $entries_list[Registry::get('settings.entries.main')] = Registry::get('settings.entries.main');
                    }
                }

                Themes::view('admin/views/templates/content/entries/move')
                    ->assign('entry_path_current', Http::get('entry'))
                    ->assign('entries_list', $entries_list)
                    ->assign('name_current', Arr::last(explode("/", Http::get('entry'))))
                    ->assign('entry_parent', implode('/', array_slice(explode("/", Http::get('entry')), 0, -1)))
                    ->assign('entry', $entry)
                    ->display();
            break;
            case 'edit':
                $entry = Entries::processEntry(PATH['entries'] . '/' . Http::get('entry') . '/entry.html', false, true);

                if (Http::get('media') && Http::get('media') == 'true') {
                    EntriesManager::processFilesManager();

                    Themes::view('admin/views/templates/content/entries/media')
                        ->assign('entry_name', Http::get('entry'))
                        ->assign('files', EntriesManager::getMediaList(Http::get('entry')), true)
                        ->assign('entry', $entry)
                        ->display();
                } else {
                    if (Http::get('source') && Http::get('source') == 'true') {
                        $action = Http::post('action');

                        if (isset($action) && $action == 'save-form') {
                            if (Token::check((Http::post('token')))) {
                                Filesystem::setFileContent(
                                    PATH['entries'] . '/' . Http::post('entry_name') . '/entry.html',
                                                          Http::post('entry_content')
                                );
                                Notification::set('success', __('admin_message_entry_changes_saved'));
                                Http::redirect(Http::getBaseUrl().'/admin/entries/edit?entry='.Http::post('entry_name').'&source=true');
                            } else {
                                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                            }
                        }

                        $entry_content = Filesystem::getFileContent(PATH['entries'] . '/' . Http::get('entry') . '/entry.html');

                        Themes::view('admin/views/templates/content/entries/source')
                            ->assign('entry_name', Http::get('entry'))
                            ->assign('entry_content', $entry_content)
                            ->assign('entry', $entry)
                            ->assign('files', EntriesManager::getMediaList(Http::get('entry')), true)
                            ->display();
                    } else {
                        $action = Http::post('action');
                        $indenter = new Indenter();

                        if (isset($action) && $action == 'save-form') {
                            if (Token::check((Http::post('token')))) {
                                $entry = Entries::processEntry(PATH['entries'] . '/' . Http::get('entry') . '/entry.html', false, true);
                                Arr::delete($entry, 'content');
                                Arr::delete($entry, 'url');
                                Arr::delete($entry, 'slug');
                                Arr::delete($entry, 'base_url');

                                $frontmatter = $_POST;
                                Arr::delete($frontmatter, 'token');
                                Arr::delete($frontmatter, 'action');
                                Arr::delete($frontmatter, 'content');
                                $frontmatter = YamlParser::encode(array_merge($entry, $frontmatter));

                                $content = Http::post('content');
                                $content = (isset($content)) ? $indenter->indent($content) : '';

                                Filesystem::setFileContent(
                                    PATH['entries'] . '/' . Http::get('entry') . '/entry.html',
                                                          '---'."\n".
                                                          $frontmatter."\n".
                                                          '---'."\n".
                                                          $content
                                );
                                Notification::set('success', __('admin_message_entry_changes_saved'));
                                Http::redirect(Http::getBaseUrl().'/admin/entries/edit?entry='.Http::get('entry'));
                            }
                        }

                        // Fieldset for current entry template
                        $fieldset_path = PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . (isset($entry['fieldset']) ? $entry['fieldset'] : 'default') . '.yaml';
                        $fieldset = YamlParser::decode(Filesystem::getFileContent($fieldset_path));
                        is_null($fieldset) and $fieldset = [];

                        Themes::view('admin/views/templates/content/entries/content')
                            ->assign('entry_name', Http::get('entry'))
                            ->assign('entry', $entry)
                            ->assign('fieldset', $fieldset)
                            ->assign('templates', Themes::getTemplates())
                            ->assign('files', EntriesManager::getMediaList(Http::get('entry')), true)
                            ->display();
                    }
                }
            break;
            default:
                if (!Http::get('add')) {
                    Themes::view('admin/views/templates/content/entries/list')
                        ->assign('entries_list', Entries::getEntries($query, 'date', 'DESC'))
                        ->display();
                }
            break;
        }
    }

    public static function getMediaList($entry, $path = false)
    {
        $files = [];
        foreach (array_diff(scandir(PATH['entries'] . '/' . $entry), ['..', '.']) as $file) {
            if (strpos(Registry::get('settings.entries.media.accept_file_types'), $file_ext = substr(strrchr($file, '.'), 1)) !== false) {
                if (strpos($file, strtolower($file_ext), 1)) {
                    if ($path) {
                        $files[Http::getBaseUrl().'/'.$entry.'/'.$file] = Http::getBaseUrl().'/'.$entry.'/'.$file;
                    } else {
                        $files[$file] = $file;
                    }
                }
            }
        }
        return $files;
    }

    public static function displayEntryForm(array $form, array $values = [], string $content)
    {
        echo Form::open(null, ['id' => 'form', 'class' => 'row']);
        echo Form::hidden('token', Token::generate());
        echo Form::hidden('action', 'save-form');

        if (isset($form) > 0) {
            foreach ($form as $element => $property) {

                // Create attributes
                $property['attributes'] = Arr::keyExists($property, 'attributes') ? $property['attributes'] : [] ;

                // Create attribute class
                $property['attributes']['class'] = Arr::keyExists($property, 'attributes.class') ? 'form-control ' . $property['attributes']['class'] : 'form-control' ;

                // Create attribute size
                $property['size'] = Arr::keyExists($property, 'size') ? $property['size'] : 'col-12' ;

                // Create attribute value
                $property['value'] = Arr::keyExists($property, 'value') ? $property['value'] : '' ;

                $pos = strpos($element, '.');

                if ($pos === false) {
                    $form_element_name = $element;
                } else {
                    $form_element_name = str_replace(".", "][", "$element").']';
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

                    // A specific WYSIWYG HTML field for entry content editing
                    case 'content':
                        $form_element = Form::textarea($element, $content, $property['attributes']);
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
                    echo $form_element;
                } else {
                    echo '<div class="form-group '.$property['size'].'">';
                    echo $form_label . $form_element;
                    echo '</div>';
                }
            }
        }

        echo Form::close();
    }

    protected static function processFilesManager()
    {
        $files_directory = PATH['entries'] . '/' . Http::get('entry') . '/';

        if (Http::get('delete_file') != '') {
            if (Token::check((Http::get('token')))) {
                Filesystem::deleteFile($files_directory . Http::get('delete_file'));
                Notification::set('success', __('admin_message_entry_file_deleted'));
                Http::redirect(Http::getBaseUrl().'/admin/entries/edit?entry='.Http::get('entry').'&media=true');
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }

        if (Http::post('upload_file')) {
            if (Token::check(Http::post('token'))) {
                //echo Registry::get('settings.entries.media.accept_file_types');

                $file = EntriesManager::uploadFile($_FILES['file'], $files_directory, Registry::get('settings.entries.media.accept_file_types'), 27000000);

                if($file !== false) {

                    if (in_array(pathinfo($file)['extension'], ['jpg', 'jpeg', 'png', 'gif'])) {

                        // open an image file
                        $img = Image::make($file);

                        // now you are able to resize the instance
                        if (Registry::get('settings.entries.media.upload_images_width') > 0 && Registry::get('settings.entries.media.upload_images_height') > 0) {
                            $img->resize(Registry::get('settings.entries.media.upload_images_width'), Registry::get('settings.entries.media.upload_images_height'), function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        } elseif (Registry::get('settings.entries.media.upload_images_width') > 0) {
                            $img->resize(Registry::get('settings.entries.media.upload_images_width'), null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        } elseif (Registry::get('settings.entries.media.upload_images_height') > 0) {
                            $img->resize(null, Registry::get('settings.entries.media.upload_images_height'), function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }

                        // finally we save the image as a new file
                        $img->save($file, Registry::get('settings.entries.media.upload_images_quality'));

                        // destroy
                        $img->destroy();
                    }

                    Notification::set('success', __('admin_message_entry_file_uploaded'));
                    Http::redirect(Http::getBaseUrl().'/admin/entries/edit?entry='.Http::get('entry').'&media=true');
                } else {
                    Notification::set('error', __('admin_message_entry_file_not_uploaded'));
                    Http::redirect(Http::getBaseUrl().'/admin/entries/edit?entry='.Http::get('entry').'&media=true');
                }

            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
            }
        }
    }

    /**
     * Upload files on the Server with several type of Validations!
     *
     * Entries::uploadFile($_FILES['file'], $files_directory);
     *
     * @param   array   $file             Uploaded file data
     * @param   string  $upload_directory Upload directory
     * @param   string  $allowed          Allowed file extensions
     * @param   int     $max_size         Max file size in bytes
     * @param   string  $filename         New filename
     * @param   bool    $remove_spaces    Remove spaces from the filename
     * @param   int     $max_width        Maximum width of image
     * @param   int     $max_height       Maximum height of image
     * @param   bool    $exact            Match width and height exactly?
     * @param   int     $chmod            Chmod mask
     * @return  string  on success, full path to new file
     * @return  false   on failure
     */
    public static function uploadFile(
        array $file,
                                      string $upload_directory,
                                      string $allowed = 'jpeg, png, gif, jpg',
                                      int $max_size = 3000000,
                                      string $filename = null,
                                      bool $remove_spaces = true,
                                      int $max_width = null,
                                      int $max_height = null,
                                      bool $exact = false,
                                      int $chmod = 0644
    ) {
        //
        // Tests if a successful upload has been made.
        //
        if (isset($file['error'])
            and isset($file['tmp_name'])
            and $file['error'] === UPLOAD_ERR_OK
            and is_uploaded_file($file['tmp_name'])) {

            //
            // Tests if upload data is valid, even if no file was uploaded.
            //
            if (isset($file['error'])
                    and isset($file['name'])
                    and isset($file['type'])
                    and isset($file['tmp_name'])
                    and isset($file['size'])) {

                //
                // Test if an uploaded file is an allowed file type, by extension.
                //
                if (strpos($allowed, strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))) !== false) {

                    //
                    // Validation rule to test if an uploaded file is allowed by file size.
                    //
                    if (($file['error'] != UPLOAD_ERR_INI_SIZE)
                                  and ($file['error'] == UPLOAD_ERR_OK)
                                  and ($file['size'] <= $max_size)) {

                        //
                        // Validation rule to test if an upload is an image and, optionally, is the correct size.
                        //
                        if (in_array(mime_content_type($file['tmp_name']), ['image/jpeg', 'image/jpg', 'image/png','image/gif'])) {
                            function validateImage($file, $max_width, $max_height, $exact)
                            {
                                try {
                                    // Get the width and height from the uploaded image
                                    list($width, $height) = getimagesize($file['tmp_name']);
                                } catch (ErrorException $e) {
                                    // Ignore read errors
                                }

                                if (empty($width) or empty($height)) {
                                    // Cannot get image size, cannot validate
                                    return false;
                                }

                                if (! $max_width) {
                                    // No limit, use the image width
                                    $max_width = $width;
                                }

                                if (! $max_height) {
                                    // No limit, use the image height
                                    $max_height = $height;
                                }

                                if ($exact) {
                                    // Check if dimensions match exactly
                                    return ($width === $max_width and $height === $max_height);
                                } else {
                                    // Check if size is within maximum dimensions
                                    return ($width <= $max_width and $height <= $max_height);
                                }

                                return false;
                            }

                            if (validateImage($file, $max_width, $max_height, $exact) === false) {
                                return false;
                            }
                        }

                        if (! isset($file['tmp_name']) or ! is_uploaded_file($file['tmp_name'])) {

                            // Ignore corrupted uploads
                            return false;
                        }

                        if ($filename === null) {

                            // Use the default filename
                            $filename = $file['name'];
                        }

                        if ($remove_spaces === true) {

                            // Remove spaces from the filename
                            $filename = Text::safeString(pathinfo($filename)['filename'], '-', true) . '.' . pathinfo($filename)['extension'];
                        }

                        if (! is_dir($upload_directory) or ! is_writable(realpath($upload_directory))) {
                            throw new \RuntimeException("Directory {$upload_directory} must be writable");
                        }

                        // Make the filename into a complete path
                        $filename = realpath($upload_directory).DIRECTORY_SEPARATOR.$filename;

                        if (move_uploaded_file($file['tmp_name'], $filename)) {

                            if ($chmod !== false) {
                                // Set permissions on filename
                                chmod($filename, $chmod);
                            }

                            // Return new file path
                            return $filename;
                        }
                    }
                }
            }
        }

        return false;
    }

}
