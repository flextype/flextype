<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Event\Event;

class Snippets
{
    /**
     * Get snippet
     *
     * Snippets::get('snippet-name');
     *
     * @access public
     * @param  string  $snippet_name  Snippet name
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     */
    public static function get(string $snippet_name)
    {
        $vars['get'] = $snippet_name;

        return Snippets::_snippet($vars);
    }

    /**
     * _snippet
     *
     * Snippets::get('snippet-name');
     * Snippets::get('snippetname', ['message' => 'Hello World']);
     *
     * @access private
     * @param  array  $vars Vars
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     */
    private static function _snippet(array $vars) {

        // Extracst attributes
        extract($vars);

        // Get snippet name
        $name = (isset($get)) ? (string) $get : '';

        // Define snippet path
        $snippet_path = PATH['snippets'] . '/' . $name . '.php';

        // Process snippet
        if (Filesystem::has($snippet_path)) {

            // Turn on output buffering
            ob_start();

            // Include view file
            include $snippet_path;

            // Output...
            return ob_get_clean();
        } else {
            throw new \RuntimeException("Snippet {$name} does not exist.");
        }
    }
}
