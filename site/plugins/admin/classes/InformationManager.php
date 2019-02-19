<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;

class InformationManager
{
    public static function getInformationManager()
    {
        Registry::set('sidebar_menu_item', 'infomation');

        Themes::view('admin/views/templates/system/information/list')->display();
    }

    /**
     * Tests whether a file is writable for anyone.
     *
     * @param  string  $file File to check
     * @return bool
     */
    public static function isFileWritable(string $file) : bool
    {
        // Is file exists ?
        if (! file_exists($file)) {
            throw new RuntimeException(vsprintf("%s(): The file '{$file}' doesn't exist", array(__METHOD__)));
        }

        // Gets file permissions
        $perms = fileperms($file);

        // Is writable ?
        if (is_writable($file) || ($perms & 0x0080) || ($perms & 0x0010) || ($perms & 0x0002)) {
            return true;
        }
    }
}
