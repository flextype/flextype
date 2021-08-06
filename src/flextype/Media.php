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
        $uploadHandler->addRule('extension', ['allowed' => $settings['allowed_file_extensions']], 'Should be a valid image');
        $uploadHandler->addRule('size', ['max' => $settings['max_file_size']], 'Should have less than {max}');
        $uploadHandler->addRule('imagewidth', 'min='.$settings['image']['width']['min'].'&max='.$settings['image']['width']['max']);
        $uploadHandler->addRule('imageheight', 'min='.$settings['image']['height']['min'].'&max='.$settings['image']['width']['max']);
       
        if (isset($settings['image']['ratio'])) {
            $uploadHandler->addRule('imageratio', 'ratio='.$settings['image']['ratio']['size'].'&error_margin='.$settings['image']['ratio']['error_margin']);
        }

        $result = $uploadHandler->process($_FILES['file']);

        if ($result->isValid()) {
            try {
                $result->confirm();
                $mediaFile = $uploadFolder . '/media.' . filesystem()->file($result->name)->extension();
                filesystem()->file($uploadFolder . '/' . $result->name)->move($mediaFile);
            } catch (\Exception $e) {
                $result->clear();
                throw $e;
            }
        } else {
            dd($result->getMessages());
            return $result->getMessages();
        }

        return $result;
    }
}