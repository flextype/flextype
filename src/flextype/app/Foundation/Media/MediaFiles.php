<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation\Media;

use Flextype\Component\Filesystem\Filesystem;
use Intervention\Image\ImageManagerStatic as Image;
use RuntimeException;
use Slim\Http\Environment;
use Slim\Http\Uri;
use function basename;
use function chmod;
use function exif_read_data;
use function explode;
use function getimagesize;
use function in_array;
use function is_dir;
use function is_uploaded_file;
use function is_writable;
use function ltrim;
use function mime_content_type;
use function move_uploaded_file;
use function pathinfo;
use function realpath;
use function rename;
use function str_replace;
use function strpos;
use function strrpos;
use function strstr;
use function strtolower;
use function substr;
use function time;
use const DIRECTORY_SEPARATOR;
use const PATHINFO_EXTENSION;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_OK;

class MediaFiles
{
    /**
     * Upload media file
     *
     * @param array  $file   Raw file data (multipart/form-data).
     * @param string $folder The folder you're targetting.
     *
     * @access public
     */
    public function upload(array $file, string $folder)
    {
        $upload_folder          = PATH['project'] . '/uploads/' . $folder . '/';
        $upload_metadata_folder = PATH['project'] . '/uploads/.meta/' . $folder . '/';

        if (! Filesystem::has($upload_folder)) {
            Filesystem::createDir($upload_folder);
        }

        if (! Filesystem::has($upload_metadata_folder)) {
            Filesystem::createDir($upload_metadata_folder);
        }

        $accept_file_types = flextype('registry')->get('flextype.settings.media.accept_file_types');
        $max_file_size     = flextype('registry')->get('flextype.settings.media.max_file_size');
        $safe_names        = flextype('registry')->get('flextype.settings.media.safe_names');
        $max_image_width   = flextype('registry')->get('flextype.settings.media.max_image_width');
        $max_image_height  = flextype('registry')->get('flextype.settings.media.max_image_height');

        $exact     = false;
        $chmod     = 0644;
        $filename  = null;
        $exif_data = [];

        // Tests if a successful upload has been made.
        if (isset($file['error'])
            and isset($file['tmp_name'])
            and $file['error'] === UPLOAD_ERR_OK
            and is_uploaded_file($file['tmp_name'])) {
            // Tests if upload data is valid, even if no file was uploaded.
            if (isset($file['error'])
                    and isset($file['name'])
                    and isset($file['type'])
                    and isset($file['tmp_name'])
                    and isset($file['size'])) {
                // Test if an uploaded file is an allowed file type, by extension.
                if (strpos($accept_file_types, strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))) !== false) {
                    // Validation rule to test if an uploaded file is allowed by file size.
                    if (($file['error'] !== UPLOAD_ERR_INI_SIZE)
                                  and ($file['error'] === UPLOAD_ERR_OK)
                                  and ($file['size'] <= $max_file_size)) {
                        // Validation rule to test if an upload is an image and, optionally, is the correct size.
                        if (in_array(mime_content_type($file['tmp_name']), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                            function validateImage($file, $max_image_width, $max_image_height, $exact)
                            {
                                try {
                                    // Get the width and height from the uploaded image
                                    [$width, $height] = getimagesize($file['tmp_name']);
                                } catch (ErrorException $e) {
                                    // Ignore read errors
                                }

                                if (empty($width) or empty($height)) {
                                    // Cannot get image size, cannot validate
                                    return false;
                                }

                                if (! $max_image_width) {
                                    // No limit, use the image width
                                    $max_image_width = $width;
                                }

                                if (! $max_image_height) {
                                    // No limit, use the image height
                                    $max_image_height = $height;
                                }

                                if ($exact) {
                                    // Check if dimensions match exactly
                                    return $width === $max_image_width and $height === $max_image_height;
                                }

                                // Check if size is within maximum dimensions
                                return $width <= $max_image_width and $height <= $max_image_height;
                            }

                            if (validateImage($file, $max_image_width, $max_image_height, $exact) === false) {
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

                        if ($safe_names === true) {
                            // Remove spaces from the filename
                            $filename = flextype('slugify')->slugify(pathinfo($filename)['filename']) . '.' . pathinfo($filename)['extension'];
                        }

                        if (! is_dir($upload_folder) or ! is_writable(realpath($upload_folder))) {
                            throw new RuntimeException("Directory {$upload_folder} must be writable");
                        }

                        // Make the filename into a complete path
                        $filename = realpath($upload_folder) . DIRECTORY_SEPARATOR . $filename;
                        if (move_uploaded_file($file['tmp_name'], $filename)) {
                            // Set permissions on filename
                            chmod($filename, $chmod);

                            if (in_array(mime_content_type($filename), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                                // open an image file
                                $img = Image::make($filename);

                                // now you are able to resize the instance
                                if (flextype('registry')->get('flextype.settings.media.image_width') > 0 && flextype('registry')->get('flextype.settings.media.image_height') > 0) {
                                    $img->resize(flextype('registry')->get('flextype.settings.media.image_width'), flextype('registry')->get('flextype.settings.media.image_height'), static function ($constraint) : void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif (flextype('registry')->get('flextype.settings.media.image_width') > 0) {
                                    $img->resize(flextype('registry')->get('flextype.settings.media.image_width'), null, static function ($constraint) : void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif (flextype('registry')->get('flextype.settings.media.image_height') > 0) {
                                    $img->resize(null, flextype('registry')->get('flextype.settings.media.image_height'), static function ($constraint) : void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                }

                                // finally we save the image as a new file
                                $img->save($filename, flextype('registry')->get('flextype.settings.media.image_quality'));

                                // destroy
                                $img->destroy();

                                $exif_data = [];

                                try {
                                    $headers = exif_read_data($filename);
                                    foreach ($headers['COMPUTED'] as $header => $value) {
                                        $exif_data[$header] = $value;
                                    }
                                } catch (\Exception $e) {

                                }
                            }

                            $metadata = [
                                'title' => substr(basename($filename), 0, strrpos(basename($filename), '.')),
                                'description' => '',
                                'type' => mime_content_type($filename),
                                'filesize' => Filesystem::getSize($filename),
                                'uploaded_on' => time(),
                                'exif' => $exif_data,
                            ];

                            Filesystem::write(
                                $upload_metadata_folder . basename($filename) . '.yaml',
                                flextype('yaml')->encode($metadata)
                            );

                            // Return new file path
                            return $filename;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Fetch file(s)
     *
     * @param string $directory The directory to list.
     *
     * @return array A list of file(s) metadata.
     */
    public function fetch(string $path) : array
    {
        // Get list if file or files for specific folder
        if (is_dir($path)) {
            $files = $this->fetchCollection($path);
        } else {
            $files = $this->fetchSingle($path);
        }

        return $files;
    }

    /**
     * Fetch single file
     *
     * @param string $path The path to file.
     *
     * @return array A file metadata.
     */
    public function fetchSingle(string $path) : array
    {
        $result = [];

        if (Filesystem::has(flextype('media_files_meta')->getFileMetaLocation($path))) {
            $result = flextype('yaml')->decode(Filesystem::read(flextype('media_files_meta')->getFileMetaLocation($path)));

            $result['filename']  = pathinfo(str_replace('/.meta', '', flextype('media_files_meta')->getFileMetaLocation($path)))['filename'];
            $result['basename']  = explode('.', basename(flextype('media_files_meta')->getFileMetaLocation($path)))[0];
            $result['extension'] = ltrim(strstr($path, '.'), '.');
            $result['dirname']   = pathinfo(str_replace('/.meta', '', flextype('media_files_meta')->getFileMetaLocation($path)))['dirname'];

            $result['url'] = 'project/uploads/' . $path;

            if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') !== '') {
                $full_url = flextype('registry')->get('flextype.settings.url');
            } else {
                $full_url = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
            }

            $result['full_url'] = $full_url . '/project/uploads/' . $path;
        }

        return $result;
    }

    /**
     * Fetch files collection
     *
     * @param string $path The path to files collection.
     *
     * @return array A list of files metadata.
     */
    public function fetchCollection(string $path) : array
    {
        $result = [];

        foreach (Filesystem::listContents(flextype('media_folders_meta')->getDirMetaLocation($path)) as $file) {
            $result[$file['basename']] = flextype('yaml')->decode(Filesystem::read($file['path']));

            $result[$file['basename']]['filename']  = pathinfo(str_replace('/.meta', '', flextype('media_files_meta')->getFileMetaLocation($file['basename'])))['filename'];
            $result[$file['basename']]['basename']  = explode('.', basename(flextype('media_files_meta')->getFileMetaLocation($file['basename'])))[0];
            $result[$file['basename']]['extension'] = ltrim(strstr($file['basename'], '.'), '.');
            $result[$file['basename']]['dirname']   = pathinfo(str_replace('/.meta', '', $file['path']))['dirname'];

            $result[$file['basename']]['url'] = 'project/uploads/' . $path . '/' . $file['basename'];

            if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') !== '') {
                $full_url = flextype('registry')->get('flextype.settings.url');
            } else {
                $full_url = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
            }

            $result[$file['basename']]['full_url'] = $full_url . '/project/uploads/' . $path . '/' . $file['basename'];
        }

        return $result;
    }

    /**
     * Rename file
     *
     * @param string $id     Unique identifier of the file.
     * @param string $new_id New Unique identifier of the file.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $id, string $new_id) : bool
    {
        if (! Filesystem::has($this->getFileLocation($new_id)) && ! Filesystem::has(flextype('media_files_meta')->getFileMetaLocation($new_id))) {
            return rename($this->getFileLocation($id), $this->getFileLocation($new_id)) && rename(flextype('media_files_meta')->getFileMetaLocation($id), flextype('media_files_meta')->getFileMetaLocation($new_id));
        }

        return false;
    }

    /**
     * Delete file
     *
     * @param string $id Unique identifier of the file.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id) : bool
    {
        return Filesystem::delete($this->getFileLocation($id)) &&
            Filesystem::delete(flextype('media_files_meta')->getFileMetaLocation($id));
    }

    /**
     * Check whether a file exists.
     *
     * @param string $id Unique identifier of the file.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $id) : bool
    {
        return Filesystem::has($this->getFileLocation($id)) &&
            Filesystem::has(flextype('media_files_meta')->getFileMetaLocation($id));
    }

    /**
     * Copy file
     *
     * @param string $id     Unique identifier of the file.
     * @param string $new_id New Unique identifier of the file.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $new_id) : bool
    {
        if (! Filesystem::has($this->getFileLocation($new_id)) && ! Filesystem::has(flextype('media_files_meta')->getFileMetaLocation($new_id))) {
            Filesystem::copy(
                $this->getFileLocation($id),
                $this->getFileLocation($new_id),
                false
            );
            Filesystem::copy(
                flextype('media_files_meta')->getFileMetaLocation($id),
                flextype('media_files_meta')->getFileMetaLocation($new_id),
                false
            );

            return Filesystem::has($this->getFileLocation($new_id)) && Filesystem::has(flextype('media_files_meta')->getFileMetaLocation($new_id)) === true;
        }

        return false;
    }

    /**
     * Get file location
     *
     * @param string $id Unique identifier of the file.
     *
     * @return string entry file location
     *
     * @access public
     */
    public function getFileLocation(string $id) : string
    {
        return PATH['project'] . '/uploads/' . $id;
    }
}
