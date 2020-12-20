<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation\Media;

use Atomastic\Arrays\Arrays;
use Atomastic\Macroable\Macroable;
use ErrorException;
use Intervention\Image\ImageManagerStatic as Image;
use RuntimeException;
use Slim\Http\Environment;
use Slim\Http\Uri;

use function arrays;
use function basename;
use function chmod;
use function exif_read_data;
use function explode;
use function filesystem;
use function filter;
use function flextype;
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
use function str_replace;
use function strings;
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
    use Macroable;

    /**
     * Create a Media Files Meta instance.
     */
    public function meta(): MediaFilesMeta
    {
        return new MediaFilesMeta();
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
        $uploadFolder         = PATH['project'] . '/media/' . $folder . '/';
        $uploadMetadataFolder = PATH['project'] . '/media/.meta/' . $folder . '/';

        if (! filesystem()->directory($uploadFolder)->exists()) {
            filesystem()->directory($uploadFolder)->create(0755, true);
        }

        if (! filesystem()->directory($uploadMetadataFolder)->exists()) {
            filesystem()->directory($uploadMetadataFolder)->create(0755, true);
        }

        $acceptFileTypes = flextype('registry')->get('flextype.settings.media.accept_file_types');
        $maxFileSize     = flextype('registry')->get('flextype.settings.media.max_file_size');
        $safeNames       = flextype('registry')->get('flextype.settings.media.safe_names');
        $maxImageWidth   = flextype('registry')->get('flextype.settings.media.max_image_width');
        $maxImageHeight  = flextype('registry')->get('flextype.settings.media.max_image_height');

        $exact    = false;
        $chmod    = 0644;
        $filename = null;
        $exifData = [];

        // Tests if a successful upload has been made.
        if (
            isset($file['error'])
            and isset($file['tmp_name'])
            and $file['error'] === UPLOAD_ERR_OK
            and is_uploaded_file($file['tmp_name'])
        ) {
            // Tests if upload data is valid, even if no file was uploaded.
            if (
                isset($file['error'])
                    and isset($file['name'])
                    and isset($file['type'])
                    and isset($file['tmp_name'])
                    and isset($file['size'])
            ) {
                // Test if an uploaded file is an allowed file type, by extension.
                if (strpos($acceptFileTypes, strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))) !== false) {
                    // Validation rule to test if an uploaded file is allowed by file size.
                    if (
                        ($file['error'] !== UPLOAD_ERR_INI_SIZE)
                                  and ($file['error'] === UPLOAD_ERR_OK)
                                  and ($file['size'] <= $maxFileSize)
                    ) {
                        // Validation rule to test if an upload is an image and, optionally, is the correct size.
                        if (in_array(mime_content_type($file['tmp_name']), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                            if ($this->validateImage($file, $maxImageWidth, $maxImageHeight, $exact) === false) {
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

                        if ($safeNames === true) {
                            // Remove spaces from the filename
                            $filename = flextype('slugify')->slugify(pathinfo($filename)['filename']) . '.' . pathinfo($filename)['extension'];
                        }

                        if (! is_dir($uploadFolder) or ! is_writable(realpath($uploadFolder))) {
                            throw new RuntimeException("Directory {$uploadFolder} must be writable");
                        }

                        // Make the filename into a complete path
                        $filename = realpath($uploadFolder) . DIRECTORY_SEPARATOR . $filename;
                        if (move_uploaded_file($file['tmp_name'], $filename)) {
                            // Set permissions on filename
                            chmod($filename, $chmod);

                            if (in_array(mime_content_type($filename), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                                // open an image file
                                $img = Image::make($filename);

                                // now you are able to resize the instance
                                if (flextype('registry')->get('flextype.settings.media.image_width') > 0 && flextype('registry')->get('flextype.settings.media.image_height') > 0) {
                                    $img->resize(flextype('registry')->get('flextype.settings.media.image_width'), flextype('registry')->get('flextype.settings.media.image_height'), static function ($constraint): void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif (flextype('registry')->get('flextype.settings.media.image_width') > 0) {
                                    $img->resize(flextype('registry')->get('flextype.settings.media.image_width'), null, static function ($constraint): void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif (flextype('registry')->get('flextype.settings.media.image_height') > 0) {
                                    $img->resize(null, flextype('registry')->get('flextype.settings.media.image_height'), static function ($constraint): void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                }

                                // finally we save the image as a new file
                                $img->save($filename, flextype('registry')->get('flextype.settings.media.image_quality'));

                                // destroy
                                $img->destroy();

                                $exifData = [];

                                try {
                                    $headers = @exif_read_data($filename);
                                    if ($headers !== false) {
                                        foreach ($headers['COMPUTED'] as $header => $value) {
                                            $exifData[$header] = $value;
                                        }
                                    }
                                } catch (RuntimeException $e) {
                                    // catch... @todo
                                }
                            }

                            $metadata = [
                                'title' => substr(basename($filename), 0, strrpos(basename($filename), '.')),
                                'description' => '',
                                'type' => mime_content_type($filename),
                                'filesize' => filesystem()->file($filename)->size(),
                                'uploaded_on' => time(),
                                'exif' => $exifData,
                            ];

                            filesystem()
                                ->file($uploadMetadataFolder . basename($filename) . '.yaml')
                                ->put(flextype('serializers')->yaml()->encode($metadata));

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
     * Fetch.
     *
     * @param string $id      The path to file.
     * @param array  $options Options array.
     *
     * @return self Returns instance of The Arrays class.
     *
     * @access public
     */
    public function fetch(string $id, array $options = []): Arrays
    {
        // Run event: onEntriesFetch
        flextype('emitter')->emit('onMediaFilesFetch');

        if (
            isset($options['collection']) &&
            strings($options['collection'])->isTrue()
        ) {
                $result = [];

            foreach (filesystem()->find()->files()->in(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id)) as $file) {
                $basename = $file->getBasename('.' . $file->getExtension());

                $result[$basename]              = flextype('serializers')->yaml()->decode(filesystem()->file($file->getPathname())->get());
                $result[$basename]['filename']  = pathinfo(str_replace('/.meta', '', flextype('media')->files()->meta()->getFileMetaLocation($basename)))['filename'];
                $result[$basename]['basename']  = explode('.', basename(flextype('media')->files()->meta()->getFileMetaLocation($basename)))[0];
                $result[$basename]['extension'] = ltrim(strstr($basename, '.'), '.');
                $result[$basename]['dirname']   = pathinfo(str_replace('/.meta', '', $file->getPathname()))['dirname'];
                $result[$basename]['url']       = 'project/media/' . $id . '/' . $basename;

                if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') !== '') {
                    $fullUrl = flextype('registry')->get('flextype.settings.url');
                } else {
                    $fullUrl = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
                }

                $result[$basename]['full_url'] = $fullUrl . '/project/media/' . $id . '/' . $basename;
            }

                $result = filter($result, $options);

                return arrays($result);
        }

        $result = [];

        if (filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($id))->exists()) {
            $result = flextype('serializers')->yaml()->decode(filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($id))->get());

            $result['filename']  = pathinfo(str_replace('/.meta', '', flextype('media')->files()->meta()->getFileMetaLocation($id)))['filename'];
            $result['basename']  = explode('.', basename(flextype('media')->files()->meta()->getFileMetaLocation($id)))[0];
            $result['extension'] = ltrim(strstr($id, '.'), '.');
            $result['dirname']   = pathinfo(str_replace('/.meta', '', flextype('media')->files()->meta()->getFileMetaLocation($id)))['dirname'];

            $result['url'] = 'project/media/' . $id;

            if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') !== '') {
                $fullUrl = flextype('registry')->get('flextype.settings.url');
            } else {
                $fullUrl = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
            }

            $result['full_url'] = $fullUrl . '/project/media/' . $id;
        }

        $result = filter($result, $options);

        return arrays($result);
    }

    /**
     * Move file
     *
     * @param string $id    Unique identifier of the file.
     * @param string $newID New Unique identifier of the file.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function move(string $id, string $newID): bool
    {
        if (! filesystem()->file($this->getFileLocation($newID))->exists() && ! filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($newID))->exists()) {
            return filesystem()->file($this->getFileLocation($id))->move($this->getFileLocation($newID)) &&
                   filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($id))->move(flextype('media')->files()->meta()->getFileMetaLocation($newID));
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
    public function delete(string $id): bool
    {
        return filesystem()->file($this->getFileLocation($id))->delete() &&
               filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($id))->delete();
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
    public function has(string $id): bool
    {
        return filesystem()->file($this->getFileLocation($id))->exists() &&
               filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($id))->exists();
    }

    /**
     * Copy file
     *
     * @param string $id    Unique identifier of the file.
     * @param string $newID New Unique identifier of the file.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $newID): bool
    {
        if (! filesystem()->file($this->getFileLocation($newID))->exists() && ! filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($newID))->exists()) {
            filesystem()->file($this->getFileLocation($id))->copy($this->getFileLocation($newID));
            filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($id))->copy(flextype('media')->files()->meta()->getFileMetaLocation($newID));

            return filesystem()->file($this->getFileLocation($newID))->exists() &&
                   filesystem()->file(flextype('media')->files()->meta()->getFileMetaLocation($newID))->exists();
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
    public function getFileLocation(string $id): string
    {
        return PATH['project'] . '/media/' . $id;
    }

    /**
     * Validate Image
     */
    protected function validateImage($file, $maxImageWidth, $maxImageHeight, $exact)
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

        if (! $maxImageWidth) {
            // No limit, use the image width
            $maxImageWidth = $width;
        }

        if (! $maxImageHeight) {
            // No limit, use the image height
            $maxImageHeight = $height;
        }

        if ($exact) {
            // Check if dimensions match exactly
            return $width === $maxImageWidth and $height === $maxImageHeight;
        }

        // Check if size is within maximum dimensions
        return $width <= $maxImageWidth and $height <= $maxImageHeight;
    }
}
