<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Arr\Arr;
use Intervention\Image\ImageManagerStatic as Image;
use Slim\Http\Environment;
use Slim\Http\Uri;

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
     * @param string $path The path of directory to list.
     * @param string $mode The mode, collection or single
     *
     * @return array A list of file(s) metadata.
     */
    public function fetch(string $path, string $mode = 'collection') : array
    {
        if ($mode == 'collection') {
            $result = $this->fetchCollection($path);
        } elseif ($mode == 'single') {
            $result = $this->fetchSingle($path);
        }

        return $result;
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

            $result['path']  = $path;
            $result['full_path']   = str_replace("/.meta", "", $this->flextype['media_folders_meta']->getDirMetaLocation($path));
            $result['url'] = 'project/uploads/' . $path;

            if ($this->flextype['registry']->has('flextype.settings.url') && $this->flextype['registry']->get('flextype.settings.url') != '') {
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
            if ($folder['type'] == 'dir') {

                $result[$folder['dirname']]['full_path']   = str_replace("/.meta", "", $this->flextype['media_folders_meta']->getDirMetaLocation($folder['dirname']));
                $result[$folder['dirname']]['url'] = 'project/uploads/' . $folder['dirname'];

                if ($this->flextype['registry']->has('flextype.settings.url') && $this->flextype['registry']->get('flextype.settings.url') != '') {
                    $full_url = $this->flextype['registry']->get('flextype.settings.url');
                } else {
                    $full_url = Uri::createFromEnvironment(new Environment($_SERVER))->getBaseUrl();
                }

                $result[$folder['dirname']]['full_url'] = $full_url . '/project/uploads/' . $folder['dirname'];
            }
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
        if (!Filesystem::has($this->getDirLocation($id)) && !Filesystem::has($this->flextype['media_folders_meta']->getDirMetaLocation($id))) {
            if (Filesystem::createDir($this->getDirLocation($id)) && Filesystem::createDir($this->flextype['media_folders_meta']->getDirMetaLocation($id))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
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
        if (!Filesystem::has($this->getDirLocation($new_id)) && !Filesystem::has($this->flextype['media_folders_meta']->getDirMetaLocation($new_id))) {
            if (rename($this->getDirLocation($id), $this->getDirLocation($new_id)) && rename($this->flextype['media_folders_meta']->getDirMetaLocation($id), $this->flextype['media_folders_meta']->getDirMetaLocation($new_id))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
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
    public function delete(string $id)
    {
        Filesystem::deleteDir($this->getDirLocation($id));
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
