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
        $snippet_file = PATH['snippets'] . '/' . $snippet . '.php';
        $new_snippet_file = PATH['snippets'] . '/' . $new_snippet . '.php';

        return rename($snippet_file, $new_snippet_file);
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
        $snippet_file = PATH['snippets'] . '/' . $snippet . '.php';

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
        $snippet_file = PATH['snippets'] . '/' . $snippet . '.php';

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
        $snippet_file = PATH['snippets'] . '/' . $snippet . '.php';

        return Filesystem::delete($snippet_file);
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
        $snippet_file = PATH['snippets'] . '/' . $snippet . '.php';
        $new_snippet_file = PATH['snippets'] . '/' . $new_snippet . '.php';

        return Filesystem::copy($snippet_file, $new_snippet_file, false);
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
        $snippet_file = PATH['snippets'] . '/' . $snippet . '.php';

        return Filesystem::has($snippet_file);
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
        $snippet_file = PATH['snippets'] . '/' . $name . '.php';

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
}
