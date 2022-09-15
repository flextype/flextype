<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype;

use Sirius\Upload\Handler as UploadHandler;
use Sirius\Upload\Result\File as UploadResultFile;
use Sirius\Upload\Result\ResultInterface;
use Throwable;

use function function_exists;
use function getimagesize;
use function Glowy\Filesystem\filesystem;
use function Glowy\Strings\strings;

if (! function_exists('upload')) {
    /**
     * Upload file.
     *
     * @param array  $file   Raw file data (multipart/form-data).
     * @param string $folder The folder you're targetting.
     *
     * @return \Sirius\Upload\Result\ResultInterface|array Result file or arrays with messages.
     */
    function upload(array $file, string $folder): \Sirius\Upload\Result\ResultInterface|array
    {
        $settings = registry()->get('flextype.settings.upload');

        $uploadFolder = strings(FLEXTYPE_PATH_PROJECT . '/' . $settings['directory'] . '/' . $folder . '/')->reduceSlashes()->toString();

        filesystem()->directory($uploadFolder)->ensureExists(0755, true);

        $uploadHandler = new UploadHandler($uploadFolder);
        $uploadHandler->setOverwrite($settings['overwrite']);
        $uploadHandler->setAutoconfirm($settings['autoconfirm']);
        $uploadHandler->setPrefix($settings['prefix']);

        // Set the validation rules
        $uploadHandler->addRule('extension', ['allowed' => $settings['validation']['allowed_file_extensions']], 'Should be a valid image');
        $uploadHandler->addRule('size', ['max' => $settings['validation']['max_file_size']], 'Should have less than {max}');
        $uploadHandler->addRule('imagewidth', 'min=' . $settings['validation']['image']['width']['min'] . '&max=' . $settings['validation']['image']['width']['max']);
        $uploadHandler->addRule('imageheight', 'min=' . $settings['validation']['image']['height']['min'] . '&max=' . $settings['validation']['image']['width']['max']);

        if (isset($settings['validation']['image']['ratio'])) {
            $uploadHandler->addRule('imageratio', 'ratio=' . $settings['validation']['image']['ratio']['size'] . '&error_margin=' . $settings['validation']['image']['ratio']['error_margin']);
        }

        $result = $uploadHandler->process($_FILES['file']);

        if (! $result->isValid()) {
            return $result->getMessages();
        }

        try {
            $result->confirm();

            // If upload file is image, do image file processing
            if (property_exists($result, 'name')) {
                $mediaFile = $uploadFolder . '/' . $result->name;

                if (getimagesize($mediaFile)) {
                    imageProcessFile($mediaFile, $settings['process']['image']);
                }
            }
        } catch (Throwable $e) {
            $result->clear();

            throw $e;
        }

        return $result;
    }
}
