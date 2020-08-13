<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation\Media;

class MediaFoldersMeta
{
    /**
     * Application
     */
    protected $app;

    /**
     * Dependency Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($app)
    {
        $this->app       = $app;
        $this->container = $app->getContainer();
    }

    /**
     * Get files directory meta location
     *
     * @param string $id Unique identifier of the folder.
     *
     * @return string entry directory location
     *
     * @access public
     */
    public function getDirMetaLocation(string $id) : string
    {
        return PATH['project'] . '/uploads/.meta/' . $id;
    }
}
