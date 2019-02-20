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
     * Get snippet
     *
     * Snippets::fetch('snippet-name');
     *
     * @access public
     * @param  string  $snippet_name  Snippet name
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     */
    public static function fetch(string $snippet)
    {
        $vars = [];

        $vars['fetch'] = $snippet;

        return Snippets::_fetch_snippet($vars);
    }

    /**
     * Rename snippet.
     *
     * @access public
     * @param string $snippet     Snippet
     * @param string $new_snippet New snippet
     * @return bool True on success, false on failure.
     */
    public static function rename(string $snippet, string $new_snippet) : bool
    {
        return rename(Snippets::_file_location($snippet), Snippets::_file_location($new_snippet));
    }

    /**
     * Update Snippet
     *
     * @access public
     * @param string $snippet Snippet
     * @param string $data    Data
     * @return bool True on success, false on failure.
     */
    public static function update(string $snippet, string $data) : bool
    {
        $snippet_file = Snippets::_file_location($snippet);

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
    public static function create(string $snippet, string $data = '') : bool
    {
        $snippet_file = Snippets::_file_location($snippet);

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
    public static function delete(string $snippet) : bool
    {
        return Filesystem::delete(Snippets::_file_location($snippet));
    }

    /**
     * Copy snippet
     *
     * @access public
     * @param string $snippet      Snippet
     * @param string $new_snippet  New snippet
     * @return bool True on success, false on failure.
     */
    public static function copy(string $snippet, string $new_snippet) : bool
    {
        return Filesystem::copy(Snippets::_file_location($snippet), Snippets::_file_location($new_snippet), false);
    }

    /**
     * Check whether snippet exists.
     *
     * @access public
     * @param string $snippet Snippet
     * @return bool True on success, false on failure.
     */
    public static function has(string $snippet) : bool
    {
        return Filesystem::has(Snippets::_file_location($snippet));
    }

    /**
     * Helper private method _fetch_snippet
     *
     * @access private
     * @param  array  $vars Vars
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     */
    private static function _fetch_snippet(array $vars) {

        // Extracst attributes
        extract($vars);

        // Get snippet name
        $name = (isset($fetch)) ? (string) $fetch : '';

        // Define snippet path
        $snippet_file = Snippets::_file_location($name);

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
    private static function _file_location($name)
    {
        return PATH['snippets'] . '/' . $name . '.php';
    }
}
