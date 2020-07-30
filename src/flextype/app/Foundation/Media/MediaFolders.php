<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation\Media;

use Flextype\Component\Filesystem\Filesystem;
use Slim\Http\Environment;
use Slim\Http\Uri;
use function rename;
use function str_replace;

class MediaFolders
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
     * Fetch folders(s)
     *
     * @param string $path       The path of directory to list.
     * @param bool   $collection Set `true` if collection of folders need to be fetched.
     *
     * @return array A list of file(s) metadata.
     */
    public function fetch(string $path, bool $collection = false) : array
    {
        if ($collection) {
            return $this->fetchCollection($path);
        }

        return $this->fetchSingle($path);
    }

    /**
     * Fetch single folder
     *
     * @param string $path The path to file.
     *
     * @return array A file metadata.
     */
    public function fetchsingle(string $path) : array
    {
        $result = [];

        if (Filesystem::has($this->flextype['media_folders_meta']->getDirMetaLocation($path))) {
            $result['path']      = $path;
            $result['full_path'] = str_replace('/.meta', '', $this->flextype['media_folders_meta']->getDirMetaLocation($path));
            $result['url']       = 'project/uploads/' . $path;

            if ($this->flextype['registry']->has('flextype.settings.url') && $this->flextype['registry']->get('flextype.settings.url') !== '') {
                $full_url = $this->flextype['registry']->get('flextype.settings.url');
            } else {
                $full_url = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
            }

            $result['full_url'] = $full_url . '/project/uploads/' . $path;
        }

        return $result;
    }

    /**
     * Fetch folder collection
     *
     * @param string $path The path to files collection.
     *
     * @return array A list of files metadata.
     */
    public function fetchCollection(string $path) : array
    {
        $result = [];

        foreach (Filesystem::listContents($this->flextype['media_folders_meta']->getDirMetaLocation($path)) as $folder) {
            if ($folder['type'] !== 'dir') {
                continue;
            }

            $result[$folder['dirname']] = $this->fetchSingle($path . '/' . $folder['dirname']);
        }

        return $result;
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
    public function create(string $id) : bool
    {
        if (! Filesystem::has($this->getDirLocation($id)) && ! Filesystem::has($this->flextype['media_folders_meta']->getDirMetaLocation($id))) {
            return Filesystem::createDir($this->getDirLocation($id)) && Filesystem::createDir($this->flextype['media_folders_meta']->getDirMetaLocation($id));
        }

        return false;
    }

    /**
     * Rename folder
     *
     * @param string $id     Unique identifier of the folder.
     * @param string $new_id New Unique identifier of the folder.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $id, string $new_id) : bool
    {
        if (! Filesystem::has($this->getDirLocation($new_id)) && ! Filesystem::has($this->flextype['media_folders_meta']->getDirMetaLocation($new_id))) {
            return rename($this->getDirLocation($id), $this->getDirLocation($new_id)) && rename($this->flextype['media_folders_meta']->getDirMetaLocation($id), $this->flextype['media_folders_meta']->getDirMetaLocation($new_id));
        }

        return false;
    }

    /**
     * Copy folder
     *
     * @param string $id     Unique identifier of the folder.
     * @param string $new_id New Unique identifier of the folder.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $new_id) : bool
    {
        if (! Filesystem::has($this->getDirLocation($new_id)) && ! Filesystem::has($this->flextype['media_folders_meta']->getDirMetaLocation($new_id))) {
            Filesystem::copy($this->getDirLocation($id),
                                      $this->getDirLocation($new_id), true);
            Filesystem::copy($this->flextype['media_folders_meta']->getDirMetaLocation($id),
                                      $this->flextype['media_folders_meta']->getDirMetaLocation($new_id), true);

            return (Filesystem::has($this->getDirLocation($new_id)) && Filesystem::has($this->flextype['media_folders_meta']->getDirMetaLocation($new_id)) === true) ? true : false;
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
    public function delete(string $id) : bool
    {
        return Filesystem::deleteDir($this->getDirLocation($id)) &&
            Filesystem::deleteDir($this->flextype['media_folders_meta']->getDirMetaLocation($id));
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
    public function getDirLocation(string $id) : string
    {
        return PATH['project'] . '/uploads/' . $id;
    }
}
