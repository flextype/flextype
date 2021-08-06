<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Media;

use Atomastic\Macroable\Macroable;
use Flextype\Entries;
use Sirius\Upload\Handler as UploadHandler;
use Intervention\Image\ImageManagerStatic as Image;

class Media extends Entries
{
    use Macroable;

    public function __construct(array $options = []) {
        parent::__construct($options);

        emitter()->addListener('onMediaCreate', static function (): void {
            if (media()->registry()->has('create.data.file')) {
                $file = media()->registry()->get('create.data.file');
                if (is_array($file)) {
                    $id = media()->registry()->get('create.id');
                    $url = registry()->get('flextype.settings.url');
                    $media = media()->upload($file, $id);
                    if (is_string($media)) {
                        $fileField = $media;
                    } else {
                        $fileField = strings($url . '/project' . registry()->get('flextype.settings.media.uploads.directory') . '/' . $id . '/media.' . filesystem()->file($media->name)->extension())->reduceSlashes()->toString();
                    }
                    media()->registry()->set('create.data.file', $fileField);
                } else {
                    media()->registry()->set('create.data.file', $file);
                }
            }
        });
    
        emitter()->addListener('onMediaDelete', static function (): void {
            $currentPath = PATH['project'] . registry()->get('flextype.settings.media.uploads.directory') . media()->registry()->get('delete.id');
            filesystem()->directory($currentPath)->delete();
        });

        emitter()->addListener('onMediaCopy', static function (): void {
            $currentPath = PATH['project'] . registry()->get('flextype.settings.media.uploads.directory') . media()->registry()->get('copy.id');
            $newPath     = PATH['project'] . registry()->get('flextype.settings.media.uploads.directory') . media()->registry()->get('copy.newID');
            filesystem()->directory($currentPath)->copy($newPath);
        });

        emitter()->addListener('onMediaMove', static function (): void {
            $currentPath = PATH['project'] . registry()->get('flextype.settings.media.uploads.directory') . media()->registry()->get('move.id');
            $newPath     = PATH['project'] . registry()->get('flextype.settings.media.uploads.directory') . media()->registry()->get('move.newID');
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
        $settings = registry()->get('flextype.settings.media.upload');

        $uploadFolder = PATH['project'] . '/uploads/media/' . $folder . '/';

        filesystem()->directory($uploadFolder)->ensureExists(0755, true);

        $uploadHandler = new UploadHandler($uploadFolder);
        $uploadHandler->setOverwrite($settings['overwrite']);
        $uploadHandler->setAutoconfirm($settings['autoconfirm']);
        $uploadHandler->setPrefix($settings['prefix']);

        // Set up the validation rules
        $uploadHandler->addRule('extension', ['allowed' => $settings['validation']['allowed_file_extensions']], 'Should be a valid image');
        $uploadHandler->addRule('size', ['max' => $settings['validation']['max_file_size']], 'Should have less than {max}');
        $uploadHandler->addRule('imagewidth', 'min='.$settings['validation']['image']['width']['min'].'&max='.$settings['validation']['image']['width']['max']);
        $uploadHandler->addRule('imageheight', 'min='.$settings['validation']['image']['height']['min'].'&max='.$settings['validation']['image']['width']['max']);
       
        if (isset($settings['validation']['image']['ratio'])) {
            $uploadHandler->addRule('imageratio', 'ratio='.$settings['validation']['image']['ratio']['size'].'&error_margin='.$settings['validation']['image']['ratio']['error_margin']);
        }

        $result = $uploadHandler->process($_FILES['file']);

        if ($result->isValid()) {
            try {
                $result->confirm();
                
                $mediaFile = $uploadFolder . '/media.' . filesystem()->file($result->name)->extension();
                
                filesystem()->file($uploadFolder . '/' . $result->name)->move($mediaFile);

                if (getimagesize($mediaFile)) {
                    $this->processImage($mediaFile, $settings);
                }
            } catch (\Exception $e) {
                $result->clear();
                throw $e;
            }
        } else {
            return $result->getMessages();
        }

        return $result;
    }

    /**
     * Process image
     *
     * @param string  $mediaFile Media file.
     * @param array   $settings  Media file settings.
     *
     * @access private
     */
    private function processImage(string $mediaFile, array $settings): void
    {
        $image = Image::make($mediaFile);

        if (isset($settings['process']['image']['blur'])) {
            $image->blur($settings['process']['image']['blur']);
        }

        if (isset($settings['process']['image']['brightness'])) {
            $image->brightness($settings['process']['image']['brightness']);
        }

        if (isset($settings['process']['image']['colorize']) && 
            isset($settings['process']['image']['colorize']['red']) &&
            isset($settings['process']['image']['colorize']['green']) &&
            isset($settings['process']['image']['colorize']['blue'])) {
            $image->colorize($settings['process']['image']['colorize']['red'],
                             $settings['image']['process']['colorize']['green'], 
                             $settings['image']['process']['colorize']['blue']);
        }

        if (isset($settings['process']['image']['contrast'])) {
            $image->contrast($settings['process']['image']['contrast']);
        }

        if (isset($settings['process']['image']['flip'])) {
            $image->flip($settings['process']['image']['flip']);
        }

        if (isset($settings['process']['image']['gamma'])) {
            $image->gamma($settings['process']['image']['gamma']);
        }

        if (isset($settings['process']['image']['rotate'])) {
            $image->rotate($settings['process']['image']['rotate']);
        }

        if (isset($settings['process']['image']['pixelate'])) {
            $image->pixelate($settings['process']['image']['pixelate']);
        }

        if (isset($settings['process']['image']['heighten'])) {
            $image->heighten($settings['process']['image']['heighten']['height'],
                            function ($constraint) use ($settings) {
                                if (isset($settings['process']['image']['heighten']['constraint']) &&
                                    is_array($settings['process']['image']['heighten']['constraint'])) {
                                        foreach ($settings['process']['image']['heighten']['constraint'] as $method) {
                                            if (in_array($method, ['upsize'])) {
                                                $constraint->{$method}();
                                            }
                                        }
                                }
                            });
        }

        if (isset($settings['process']['image']['widen'])) {
            $image->heighten($settings['process']['image']['widen']['width'], 
                            function ($constraint) use ($settings) {
                                if (isset($settings['process']['image']['widen']['constraint']) &&
                                    is_array($settings['process']['image']['widen']['constraint'])) {
                                        foreach ($settings['process']['image']['widen']['constraint'] as $method) {
                                            if (in_array($method, ['upsize'])) {
                                                $constraint->{$method}();
                                            }
                                        }
                                }
                            });
        }

        if (isset($settings['process']['image']['fit']) &&
            isset($settings['process']['image']['fit']['width'])) {
            $image->fit($settings['process']['image']['fit']['width'], 
                        $settings['process']['image']['fit']['height'] ?? null,
                        function ($constraint) use ($settings) {
                            if (isset($settings['process']['image']['fit']['constraint']) &&
                                is_array($settings['process']['image']['fit']['constraint'])) {
                                    foreach ($settings['process']['image']['fit']['constraint'] as $method) {
                                        if (in_array($method, ['upsize'])) {
                                            $constraint->{$method}();
                                        }
                                    }
                            }
                        },
                        $settings['process']['image']['fit']['position'] ?? 'center');
        }

        if (isset($settings['process']['image']['crop']) &&
            isset($settings['process']['image']['crop']['width']) &&
            isset($settings['process']['image']['crop']['height'])) {
            $image->crop($settings['process']['image']['crop']['width'], 
                             $settings['process']['image']['crop']['height'],
                             $settings['process']['image']['crop']['x'] ?? null,
                             $settings['process']['image']['crop']['y'] ?? null);
        }

        
        if (isset($settings['process']['image']['invert']) &&
            $settings['process']['image']['invert'] == true) {
            $image->invert();
        }

        if (isset($settings['process']['image']['sharpen'])) {
            $image->sharpen($settings['process']['image']['sharpen']);
        }

        if (isset($settings['process']['image']['resize'])) {
            $image->resize($settings['process']['image']['resize']['width'] ?? null,
                            $settings['process']['image']['resize']['height'] ?? null,
                            function ($constraint) use ($settings) {
                                if (isset($settings['process']['image']['resize']['constraint']) &&
                                    is_array($settings['process']['image']['resize']['constraint'])) {
                                        foreach ($settings['process']['image']['resize']['constraint'] as $method) {
                                            if (in_array($method, ['aspectRatio', 'upsize'])) {
                                                $constraint->{$method}();
                                            }
                                        }
                                }
                            });
        }

        $image->save($mediaFile, $settings['process']['image']['quality'] ?? 70);
        $image->destroy();
    }
}