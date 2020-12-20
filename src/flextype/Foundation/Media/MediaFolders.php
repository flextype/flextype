<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation\Media;

use Atomastic\Arrays\Arrays;
use Atomastic\Macroable\Macroable;
use Slim\Http\Environment;
use Slim\Http\Uri;

use function arrays;
use function filesystem;
use function filter;
use function flextype;
use function str_replace;
use function strings;

class MediaFolders
{
    use Macroable;

    /**
     * Create a Media Folders Meta instance.
     */
    public function meta(): MediaFoldersMeta
    {
        return new MediaFoldersMeta();
    }

    /**
     * Fetch.
     *
     * @param string $id      The path to folder.
     * @param array  $options Options array.
     *
     * @return self Returns instance of The Arrays class.
     *
     * @access public
     */
    public function fetch(string $id, array $options = []): Arrays
    {
        // Run event: onEntriesFetch
        flextype('emitter')->emit('onMediaFoldersFetch');

        // Single fetch helper
        $single = static function ($id, $options) {
            $result = [];

            if (filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id))->exists()) {
                $result['path']      = $id;
                $result['full_path'] = str_replace('/.meta', '', flextype('media')->folders()->meta()->getDirectoryMetaLocation($id));
                $result['url']       = 'project/media/' . $id;

                if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') !== '') {
                    $fullUrl = flextype('registry')->get('flextype.settings.url');
                } else {
                    $fullUrl = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
                }

                $result['full_url'] = $fullUrl . '/project/media/' . $id;
            }

            $result = filter($result, $options);

            return arrays($result);
        };

        if (
            isset($options['collection']) &&
            strings($options['collection'])->isTrue()
        ) {
                $result = [];

            foreach (filesystem()->find()->directories()->in(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id)) as $folder) {
                $result[$folder->getFilename()] = $single($id . '/' . $folder->getFilename(), [])->toArray();
            }

                $result = filter($result, $options);

                return arrays($result);
        }

        return $single($id, $options);
    }

    /**
     * Create folder
     *
     * @param string $id Unique identifier of the folder.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id): bool
    {
        if (
            ! filesystem()->directory($this->getDirectoryLocation($id))->exists() &&
            ! filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id))->exists()
        ) {
            return filesystem()->directory($this->getDirectoryLocation($id))->create(0755, true) &&
                   filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id))->create(0755, true);
        }

        return false;
    }

    /**
     * Move folder
     *
     * @param string $id    Unique identifier of the folder.
     * @param string $newID New Unique identifier of the folder.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function move(string $id, string $newID): bool
    {
        if (
            (filesystem()->directory($this->getDirectoryLocation($newID))->exists() === false &&
             filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($newID))->exists() === false)
        ) {
            return filesystem()->directory($this->getDirectoryLocation($id))->move($this->getDirectoryLocation($newID)) &&
                                                     filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id))->move(flextype('media')->folders()->meta()->getDirectoryMetaLocation($newID));
        }

        return false;
    }

    /**
     * Copy folder
     *
     * @param string $id    Unique identifier of the folder.
     * @param string $newID New Unique identifier of the folder.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $newID): bool
    {
        if (
            (filesystem()->directory($this->getDirectoryLocation($newID))->exists() === false &&
             filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($newID))->exists() === false)
        ) {
            filesystem()
                ->directory($this->getDirectoryLocation($id))
                ->copy($this->getDirectoryLocation($newID));
            filesystem()
                ->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id))
                ->copy(flextype('media')->folders()->meta()->getDirectoryMetaLocation($newID));

            return filesystem()->directory($this->getDirectoryLocation($newID))->exists() &&
                   filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($newID))->exists();
        }

        return false;
    }

    /**
     * Delete dir
     *
     * @param string $id Unique identifier of the file.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id): bool
    {
        return filesystem()->directory($this->getDirectoryLocation($id))->delete() &&
               filesystem()->directory(flextype('media')->folders()->meta()->getDirectoryMetaLocation($id))->delete();
    }

    /**
     * Get files directory location
     *
     * @param string $id Unique identifier of the folder.
     *
     * @return string entry directory location
     *
     * @access public
     */
    public function getDirectoryLocation(string $id): string
    {
        return PATH['project'] . '/media/' . $id;
    }
}
