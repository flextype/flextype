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
use Throwable;

use function emitter;
use function filesystem;
use function getimagesize;
use function image;
use function is_array;
use function is_string;
use function media;
use function registry;
use function strings;

class Media extends Entries
{
    use Macroable;

    /**
     * Constructor.
     *
     * @param array $options Media options.
     *
     * @access public
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        filesystem()
            ->directory(PATH['project'] . registry()->get('flextype.settings.media.upload.directory'))
            ->ensureExists(0755, true);
    }

    /**
     * Create media entry.
     *
     * @param string $id   Unique identifier of the media entry.
     * @param array  $data Data to create for the media entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id, array $data = []): bool
    {
        $data['resource'] = $data['resource'] ?? '';

        if (is_array($data['resource'])) {
            media()->upload($data['resource'], $id);
            unset($data['resource']);
        } elseif (! strings($data['resource'])->isUrl()) {
            unset($data['resource']);
        }
        
        return parent::create($id, $data);
    }

    /**
     * Move media entry.
     *
     * @param string $id    Unique identifier of the media entry.
     * @param string $newID New Unique identifier of the media entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function move(string $id, string $newID): bool
    {
        $mediaResourceDirectoryCurrentLocation = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $id;
        $mediaResourceDirectoryNewLocation = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $newID;
       
        if (filesystem()->directory($mediaResourceDirectoryCurrentLocation)->exists()) {
            filesystem()->directory($mediaResourceDirectoryCurrentLocation)->move($mediaResourceDirectoryNewLocation);
        } 

        return parent::move($id, $newID);
    }

    /**
     * Copy media entry.
     *
     * @param string $id    Unique identifier of the media entry.
     * @param string $newID New Unique identifier of the media entry.
     *
     * @return bool|null True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $newID): bool
    {
        $mediaResourceDirectoryCurrentLocation = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $id;
        $mediaResourceDirectoryNewLocation = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $newID;
       
        if (filesystem()->directory($mediaResourceDirectoryCurrentLocation)->exists()) {
            filesystem()->directory($mediaResourceDirectoryCurrentLocation)->copy($mediaResourceDirectoryNewLocation);
        } 

        return parent::copy($id, $newID);
    }

    /**
     * Delete media entry.
     *
     * @param string $id Unique identifier of the media entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id): bool
    {
        $mediaResourceDirectoryCurrentLocation = PATH['project'] . registry()->get('flextype.settings.media.upload.directory') . '/' . $id;
        
        if (filesystem()->directory($mediaResourceDirectoryCurrentLocation)->exists()) {
            filesystem()->directory($mediaResourceDirectoryCurrentLocation)->delete();
        } 

        return parent::delete($id);
    }

    /**
     * Upload media file.
     *
     * @param array  $file   Raw file data (multipart/form-data).
     * @param string $folder The folder you're targetting.
     *
     * @access public
     */
    public function upload(array $file, string $folder)
    {
        $settings = registry()->get('flextype.settings.media.upload');

        $uploadFolder = strings(PATH['project']  . '/' . registry()->get('flextype.settings.media.upload.directory') . '/' . $folder . '/')->reduceSlashes()->toString();

        filesystem()->directory($uploadFolder)->ensureExists(0755, true);

        $uploadHandler = new UploadHandler($uploadFolder);
        $uploadHandler->setOverwrite($settings['overwrite']);
        $uploadHandler->setAutoconfirm($settings['autoconfirm']);
        $uploadHandler->setPrefix($settings['prefix']);

        // Set up the validation rules
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

            $mediaFile = $uploadFolder . '/media.' . filesystem()->file($result->name)->extension();

            filesystem()->file($uploadFolder . '/' . $result->name)->move($mediaFile);

            if (getimagesize($mediaFile)) {
                image($mediaFile, $settings['process']['image']);
            }
        } catch (Throwable $e) {
            $result->clear();

            throw $e;
        }

        return $result;
    }
}
