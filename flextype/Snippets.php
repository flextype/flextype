<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;

class Snippets
{
    /**
     * Flextype Dependency Container
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
     * Get snippet
     *
     * @access public
     * @param  string  $snippet_name  Snippet name
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     */
    public function fetch(string $snippet)
    {
        $vars = [];

        $vars['fetch'] = $snippet;

        return $this->_fetch_snippet($vars);
    }

    /**
     * Rename snippet.
     *
     * @access public
     * @param string $snippet     Snippet
     * @param string $new_snippet New snippet
     * @return bool True on success, false on failure.
     */
    public function rename(string $snippet, string $new_snippet) : bool
    {
        return rename($this->_file_location($snippet), $this->_file_location($new_snippet));
    }

    /**
     * Update Snippet
     *
     * @access public
     * @param string $snippet Snippet
     * @param string $data    Data
     * @return bool True on success, false on failure.
     */
    public function update(string $snippet, string $data) : bool
    {
        $snippet_file = $this->_file_location($snippet);

        if (Filesystem::has($snippet_file)) {
            return Filesystem::write($snippet_file, $data);
        } else {
            return false;
        }
    }

    /**
     * Create snippet
     *
     * @access public
     * @param string $snippet Snippet
     * @param string $data    Data
     * @return bool True on success, false on failure.
     */
    public function create(string $snippet, string $data = '') : bool
    {
        $snippet_file = $this->_file_location($snippet);

        // Check if new entry file exists
        if (!Filesystem::has($snippet_file)) {
            return Filesystem::write($snippet_file, $data);
        } else {
            return false;
        }
    }

    /**
     * Delete snippet.
     *
     * @access public
     * @param string $snippet Snippet
     * @return bool True on success, false on failure.
     */
    public function delete(string $snippet) : bool
    {
        return Filesystem::delete($this->_file_location($snippet));
    }

    /**
     * Copy snippet
     *
     * @access public
     * @param string $snippet      Snippet
     * @param string $new_snippet  New snippet
     * @return bool True on success, false on failure.
     */
    public function copy(string $snippet, string $new_snippet) : bool
    {
        return Filesystem::copy($this->_file_location($snippet), $this->_file_location($new_snippet), false);
    }

    /**
     * Check whether snippet exists.
     *
     * @access public
     * @param string $snippet Snippet
     * @return bool True on success, false on failure.
     */
    public function has(string $snippet) : bool
    {
        return Filesystem::has($this->_file_location($snippet));
    }

    /**
     * Helper private method _fetch_snippet
     *
     * @access private
     * @param  array  $vars Vars
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     */
    private function _fetch_snippet(array $vars) {

        // Extracst attributes
        extract($vars);

        // Get snippet name
        $name = (isset($fetch)) ? (string) $fetch : '';

        // Define snippet path
        $snippet_file = $this->_file_location($name);

        // Process snippet
        if (Filesystem::has($snippet_file)) {

            // Turn on output buffering
            ob_start();

            // Include view file
            include $snippet_file;

            // Output...
            return ob_get_clean();
        } else {
            throw new \RuntimeException("Snippet {$name} does not exist.");
        }
    }

    /**
     * Helper method _file_location
     *
     * @access private
     * @param string $name Name
     * @return string
     */
    private function _file_location(string $name) : string
    {
        return PATH['snippets'] . '/' . $name . '.php';
    }
}
