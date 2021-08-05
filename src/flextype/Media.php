<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Media;

use Atomastic\Macroable\Macroable;
use Flextype\Entries;

class Media extends Entries
{
    use Macroable;

    public function __construct(array $options = []) {
        parent::__construct($options);

        emitter()->addListener('onMediaCreate', static function (): void {
            if (media()->registry()->has('create.data.file')) {
                $file = media()->registry()->get('create.data.file');
                if (is_array($file)) {
                    media()->registry()->set('create.data.file', $this->upload($file));
                } else {
                    media()->registry()->set('create.data.file', $file);
                }
            }
        });
    
        emitter()->addListener('onMediaDelete', static function (): void {
            $currentPath = PATH['project'] . '/uploads/media/' . media()->registry()->get('delete.id');
            filesystem()->directory($currentPath)->delete();
        });

        emitter()->addListener('onMediaCopy', static function (): void {
            $currentPath = PATH['project'] . '/uploads/media/' . media()->registry()->get('copy.id');
            $newPath     = PATH['project'] . '/uploads/media/' . media()->registry()->get('copy.newID');
            filesystem()->directory($currentPath)->copy($newPath);
        });

        emitter()->addListener('onMediaMove', static function (): void {
            $currentPath = PATH['project'] . '/uploads/media/' . media()->registry()->get('move.id');
            $newPath     = PATH['project'] . '/uploads/media/' . media()->registry()->get('move.newID');
            filesystem()->directory($currentPath)->move($newPath);
        });
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
        $uploadFolder         = PATH['project'] . '/uploads/' . $folder . '/';
        //$uploadMetadataFolder = PATH['project'] . '/media/.meta/' . $folder . '/';

        /*
        if (! filesystem()->directory($uploadFolder)->exists()) {
            filesystem()->directory($uploadFolder)->create(0755, true);
        }
        */
        /*if (! filesystem()->directory($uploadMetadataFolder)->exists()) {
            filesystem()->directory($uploadMetadataFolder)->create(0755, true);
        }*/

        $acceptFileTypes = registry()->get('flextype.settings.media.accept_file_types');
        $maxFileSize     = registry()->get('flextype.settings.media.max_file_size');
        $safeNames       = registry()->get('flextype.settings.media.safe_names');
        $maxImageWidth   = registry()->get('flextype.settings.media.images.max_image_width');
        $maxImageHeight  = registry()->get('flextype.settings.media.images.max_image_height');

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
                                if (registry()->get('flextype.settings.media.images.image_width') > 0 && registry()->get('flextype.settings.media.images.image_height') > 0) {
                                    $img->resize(registry()->get('flextype.settings.media.images.image_width'), registry()->get('flextype.settings.media.images.image_height'), static function ($constraint): void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif (registry()->get('flextype.settings.media.images.image_width') > 0) {
                                    $img->resize(registry()->get('flextype.settings.media.images.image_width'), null, static function ($constraint): void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } elseif (registry()->get('flextype.settings.media.images.image_height') > 0) {
                                    $img->resize(null, registry()->get('flextype.settings.media.images.image_height'), static function ($constraint): void {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                }

                                // finally we save the image as a new file
                                $img->save($filename, registry()->get('flextype.settings.media.images.image_quality'));

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

                            /*
                            filesystem()
                                ->file($uploadMetadataFolder . basename($filename) . '.yaml')
                                ->put(serializers()->yaml()->encode($metadata));
                                */

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