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

class Media
{
    /**
     * Constructor.
     *
     * @param array $options Media options.
     *
     * @access public
     */
    public function __construct(array $options = [])
    {
        filesystem()
            ->directory(PATH['project'] . registry()->get('flextype.settings.upload.directory') . '/media')
            ->ensureExists(0755, true);
            
        container()->get('emitter')->addListener('onEntriesCreate', static function (): void {
            entries()->registy()->get('collections');
        });
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
        
            $media = upload($data['resource'], $id);
        
            if ($media->name) {

                //$mediaFile = $uploadFolder . '/media.' . filesystem()->file($media->name)->extension();
                //filesystem()->file($uploadFolder . '/' . $media->name)->move($mediaFile);

                
                $data['resource'] = strings($id . '/media.' . filesystem()->file($media->name)->extension())->reduceSlashes()->toString();
            } else {
                $data['resource'] = '';
            }
        }
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
    }
}
