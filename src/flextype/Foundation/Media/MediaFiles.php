<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Arr\Arr;
use Intervention\Image\ImageManagerStatic as Image;
use Slim\Http\Environment;
use Slim\Http\Uri;

class MediaFiles
{
    /**
     * Flextype Dependency Container
     *
     * @access private
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
     * Upload media file
     *
     * @param array  $file   Raw file data (multipart/form-data).
     * @param string $folder The folder you're targetting.
     *
     * @access public
     */
    public function upload(array $file, string $folder)
    {
        $upload_folder = PATH['project'] . '/uploads/' . $folder . '/';
        $upload_metadata_folder = PATH['project'] . '/uploads/.meta/' . $folder . '/';

        if (! Filesystem::has($upload_folder)) {
            Filesystem::createDir($upload_folder);
        }

        if (! Filesystem::has($upload_metadata_folder)) {
            Filesystem::createDir($upload_metadata_folder);
        }

        $accept_file_types = $this->flextype['registry']->get('flextype.settings.media.accept_file_types');
        $max_file_size = $this->flextype['registry']->get('flextype.settings.media.max_file_size');
        $safe_names = $this->flextype['registry']->get('flextype.settings.media.safe_names');
        $max_image_width = $this->flextype['registry']->get('flextype.settings.media.max_image_width');
        $max_image_height = $this->flextype['registry']->get('flextype.settings.media.max_image_height');

        $exact = false;
        $chmod = 0644;
        $filename = null;
        $exif_data = [];

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
                if (strpos($accept_file_types, strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))) !== false) {
                    //
                    // Validation rule to test if an uploaded file is allowed by file size.
                    //
                    if (($file['error'] != UPLOAD_ERR_INI_SIZE)
                                  and ($file['error'] == UPLOAD_ERR_OK)
                                  and ($file['size'] <= $max_file_size)) {
                        //
                        // Validation rule to test if an upload is an image and, optionally, is the correct size.
                        //
                        if (in_array(mime_content_type($file['tmp_name']), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                            function validateImage($file, $max_image_width, $max_image_height, $exact)
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
                                if (!$max_image_width) {
                                    // No limit, use the image width
                                    $max_image_width = $width;
                                }
                                if (!$max_image_height) {
                                    // No limit, use the image height
                                    $max_image_height = $height;
                                }
                                if ($exact) {
                                    // Check if dimensions match exactly
                                    return ($width === $max_image_width and $height === $max_image_height);
                                } else {
                                    // Check if size is within maximum dimensions
                                    return ($width <= $max_image_width and $height <= $max_image_height);
                                }
                                return false;
                            }
                            if (validateImage($file, $max_image_width, $max_image_height, $exact) === false) {
                                return false;
                            }
                        }
                        if (!isset($file['tmp_name']) or !is_uploaded_file($file['tmp_name'])) {
                            // Ignore corrupted uploads
                            return false;
                        }
                        if ($filename === null) {
                            // Use the default filename
                            $filename = $file['name'];
                        }
                        if ($safe_names === true) {
                            // Remove spaces from the filename
                            $filename = $this->flextype['slugify']->slugify(pathinfo($filename)['filename']) . '.' . pathinfo($filename)['extension'];
                        }
                        if (!is_dir($upload_folder) or !is_writable(realpath($upload_folder))) {
                            throw new \RuntimeException("Directory {$upload_folder} must be writable");
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
                                if ($this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_width') > 0 && $this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_height') > 0) {
                                    $img->resize($this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_width'), $this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_height'), function($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif ($this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_width') > 0) {
                                    $img->resize($this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_width'), null, function($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif ($this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_height') > 0) {
                                    $img->resize(null, $this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_height'), function($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                }

                                // finally we save the image as a new file
                                $img->save($filename, $this->flextype['registry']->get('plugins.admin.settings.entries.media.upload_images_quality'));

                                // destroy
                                $img->destroy();

                                $headers = exif_read_data($filename);

                                $exif_data = [];

                                foreach ($headers['COMPUTED'] as $header => $value) {
                                    $exif_data[$header] = $value;
                                }
                            }

                            $metadata = ['title' => substr(basename($filename), 0, strrpos(basename($filename), '.')),
                                         'description' => '',
                                         'type' => mime_content_type($filename),
                                         'filesize' => Filesystem::getSize($filename),
                                         'uploaded_on' => time(),
                                         'exif' => $exif_data];

                            Filesystem::write($upload_metadata_folder . basename($filename) . '.yaml',
                                              $this->flextype['serializer']->encode($metadata, 'yaml'));


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
    public function fetchsingle(string $path) : array
    {
        $result = [];

        if (Filesystem::has($this->flextype['media_files_meta']->getFileMetaLocation($path))) {
            $result = $this->flextype['serializer']->decode(Filesystem::read($this->flextype['media_files_meta']->getFileMetaLocation($path)), 'yaml');

            $result['filename']  = pathinfo(str_replace("/.meta", "", $this->flextype['media_files_meta']->getFileMetaLocation($path)))['filename'];
            $result['basename']  = explode(".", basename($this->flextype['media_files_meta']->getFileMetaLocation($path)))[0];
            $result['extension'] = ltrim(strstr($path, '.'), '.');
            $result['dirname']   = pathinfo(str_replace("/.meta", "", $this->flextype['media_files_meta']->getFileMetaLocation($path)))['dirname'];

            $result['url'] = 'project/uploads/' . $path;

            if ($this->flextype['registry']->has('flextype.settings.url') && $this->flextype['registry']->get('flextype.settings.url') != '') {
                $full_url = $this->flextype['registry']->get('flextype.settings.url');
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

        foreach (Filesystem::listContents($this->flextype['media_folders_meta']->getDirMetaLocation($path)) as $file) {
            $result[$file['basename']] = $this->flextype['serializer']->decode(Filesystem::read($file['path']), 'yaml');

            $result[$file['basename']]['filename']  = pathinfo(str_replace("/.meta", "", $this->flextype['media_files_meta']->getFileMetaLocation($file['basename'])))['filename'];
            $result[$file['basename']]['basename']  = explode(".", basename($this->flextype['media_files_meta']->getFileMetaLocation($file['basename'])))[0];
            $result[$file['basename']]['extension'] = ltrim(strstr($file['basename'], '.'), '.');
            $result[$file['basename']]['dirname']   = pathinfo(str_replace("/.meta", "", $file['path']))['dirname'];

            $result[$file['basename']]['url'] = 'project/uploads/' . $path . '/' . $file['basename'];

            if ($this->flextype['registry']->has('flextype.settings.url') && $this->flextype['registry']->get('flextype.settings.url') != '') {
                $full_url = $this->flextype['registry']->get('flextype.settings.url');
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
        if (!Filesystem::has($this->getFileLocation($new_id)) && !Filesystem::has($this->flextype['media_files_meta']->getFileMetaLocation($new_id))) {
            if (rename($this->getFileLocation($id), $this->getFileLocation($new_id)) && rename($this->flextype['media_files_meta']->getFileMetaLocation($id), $this->flextype['media_files_meta']->getFileMetaLocation($new_id))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
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
    public function delete(string $id)
    {
        Filesystem::delete($this->getFileLocation($id));
        Filesystem::delete($this->flextype['media_files_meta']->getFileMetaLocation($id));
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
