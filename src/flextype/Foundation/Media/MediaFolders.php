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

class MediaFolders
{
    use Macroable;

    /**
     * Fetch single folder.
     *
     * @param string $path    The path to folder.
     * @param array  $options Options array.
     *
     * @access public
     */
    public function fetchSingle(string $path, array $options = []): Arrays
    {
        $result = [];

        if (filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($path))->exists()) {
            $result['path']      = $path;
            $result['full_path'] = str_replace('/.meta', '', flextype('media_folders_meta')->getDirectoryMetaLocation($path));
            $result['url']       = 'project/uploads/' . $path;

            if (flextype('registry')->has('flextype.settings.url') && flextype('registry')->get('flextype.settings.url') !== '') {
                $fullUrl = flextype('registry')->get('flextype.settings.url');
            } else {
                $fullUrl = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
            }

            $result['full_url'] = $fullUrl . '/project/uploads/' . $path;
        }

        $result = filter($result, $options);

        return arrays($result);
    }

    /**
     * Fetch folders collection.
     *
     * @param string $path    The path to folder.
     * @param array  $options Options array.
     *
     * @access public
     */
    public function fetchCollection(string $path, array $options = []): Arrays
    {
        $result = [];

        foreach (filesystem()->find()->directories()->in(flextype('media_folders_meta')->getDirectoryMetaLocation($path)) as $folder) {
            $result[$folder->getFilename()] = $this->fetchSingle($path . '/' . $folder->getFilename())->toArray();
        }

        $result = filter($result, $options);

        return arrays($result);
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
            ! filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($id))->exists()
        ) {
            return filesystem()->directory($this->getDirectoryLocation($id))->create(0755, true) &&
                   filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($id))->create(0755, true);
        }

        return false;
    }

    /**
     * Move folder
     *
     * @param string $id     Unique identifier of the folder.
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
             filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($newID))->exists() === false)
        ) {
            return filesystem()->directory($this->getDirectoryLocation($id))->move($this->getDirectoryLocation($newID)) &&
                                                     filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($id))->move(flextype('media_folders_meta')->getDirectoryMetaLocation($newID));
        }

        return false;
    }

    /**
     * Copy folder
     *
     * @param string $id     Unique identifier of the folder.
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
             filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($newID))->exists() === false)
        ) {
            filesystem()
                ->directory($this->getDirectoryLocation($id))
                ->copy($this->getDirectoryLocation($newID));
            filesystem()
                ->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($id))
                ->copy(flextype('media_folders_meta')->getDirectoryMetaLocation($newID));

            return filesystem()->directory($this->getDirectoryLocation($newID))->exists() &&
                   filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($newID))->exists();
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
               filesystem()->directory(flextype('media_folders_meta')->getDirectoryMetaLocation($id))->delete();
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
        return PATH['project'] . '/uploads/' . $id;
    }
}
