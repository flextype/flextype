<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained Flextype Community.
 *
 * @license https://github.com/flextype/flextype/blob/master/LICENSE.txt (MIT License)
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use RuntimeException;
use const EXTR_PREFIX_ALL;
use function count;
use function extract;
use function ob_get_clean;
use function ob_start;
use function rename;

class Snippets
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Flextype Application
     */
    private $app;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype, $app)
    {
        $this->flextype = $flextype;
        $this->app      = $app;
    }

    /**
     * Exec snippet
     *
     * @param  string $id Snippet id
     *
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     *
     * @access public
     */
    public function exec(string $id)
    {
        return $this->_exec_snippet(['id' => $id]);
    }

    /**
     * Fetch snippet
     *
     * @param string $id Snippet id
     *
     * @return string|false The snippet contents or false on failure.
     *
     * @access public
     */
    public function fetch(string $id)
    {
        $snippet_file = $this->_file_location($id);

        if (Filesystem::has($snippet_file)) {
            if ($snippet_body = Filesystem::read($snippet_file)) {
                return $snippet_body;
            }

            return false;
        }

        return false;
    }

    /**
     * Fetch Snippets
     *
     * @return array
     *
     * @access public
     */
    public function fetchAll() : array
    {
        $snippets = [];

        // Get snippets files
        $_snippets = Filesystem::listContents($this->_dir_location());

        // If there is any snippets file then go...
        if (count($_snippets) > 0) {
            foreach ($_snippets as $snippet) {
                if ($snippet['type'] !== 'file' || $snippet['extension'] !== 'php') {
                    continue;
                }

                $snippets[$snippet['basename']] = $snippet['basename'];
            }
        }

        // return snippets
        return $snippets;
    }

    /**
     * Rename snippet
     *
     * @param string $id     Snippet id
     * @param string $new_id New snippet id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $id, string $new_id) : bool
    {
        return rename($this->_file_location($id), $this->_file_location($new_id));
    }

    /**
     * Update Snippet
     *
     * @param string $id   Snippet id
     * @param string $data Data
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $id, string $data) : bool
    {
        $snippet_file = $this->_file_location($id);

        if (Filesystem::has($snippet_file)) {
            return Filesystem::write($snippet_file, $data);
        }

        return false;
    }

    /**
     * Create snippet
     *
     * @param string $id   Snippet id
     * @param string $data Data
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id, string $data = '') : bool
    {
        $snippet_file = $this->_file_location($id);

        // Check if new entry file exists
        if (! Filesystem::has($snippet_file)) {
            return Filesystem::write($snippet_file, $data);
        }

        return false;
    }

    /**
     * Delete snippet.
     *
     * @param string $id Snippet id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id) : bool
    {
        return Filesystem::delete($this->_file_location($id));
    }

    /**
     * Copy snippet
     *
     * @param string $id     Snippet id
     * @param string $new_id New snippet id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $new_id) : bool
    {
        return Filesystem::copy($this->_file_location($id), $this->_file_location($new_id), false);
    }

    /**
     * Check whether snippet exists.
     *
     * @param  string $id Snippet id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $id) : bool
    {
        return Filesystem::has($this->_file_location($id));
    }

    /**
     * Helper private method _exec_snippet
     *
     * @param  array $vars Vars
     *
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     *
     * @access private
     */
    private function _exec_snippet(array $vars)
    {
        // Extracts vars and set prefix bind_ for all of them
        extract($vars, EXTR_PREFIX_ALL, 'bind');

        // Get snippet id
        $snippet_id = (string) $bind_id ?? '';

        // Define snippet file path
        $snippet_file = $this->_file_location($snippet_id);

        // Process snippet
        if (Filesystem::has($snippet_file)) {
            // Turn on output buffering
            ob_start();

            // Re-init $flextype for snippets
            $flextype = $this->flextype;

            // Re-init $app for snippets
            $app = $this->app;

            // Include snippet file
            include $snippet_file;

            // Output...
            return ob_get_clean();
        }

        throw new RuntimeException("Snippet {$snippet_id} does not exist.");
    }

    /**
     * Helper method _file_location
     *
     * @param string $id Snippet id
     *
     * @access private
     */
    private function _file_location(string $id) : string
    {
        return PATH['snippets'] . '/' . $id . '.php';
    }

    /**
     * Helper method _dir_location
     *
     * @access private
     */
    private function _dir_location() : string
    {
        return PATH['snippets'] . '/';
    }
}
