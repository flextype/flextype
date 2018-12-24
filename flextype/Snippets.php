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

// Event: onShortcodesInitialized
Event::addListener('onShortcodesInitialized', function () {

    // Shortcode: [snippet name=snippet-name]
    Content::shortcode()->addHandler('snippet', function(ShortcodeInterface $s) {
        return Snippet::get($s->getParameter('name'));
    });
});

class Snippets
{
    /**
     * Get snippet
     *
     * Snippet::get('snippet-name');
     *
     * @access public
     * @param  string  $snippet_name  Snippet name
     * @return string|bool Returns the contents of the output buffer and end output buffering.
     *                     If output buffering isn't active then FALSE is returned.
     */
    public static function get(string $snippet_name)
    {
        $snippet_path = PATH['snippets'] . '/' . $snippet_name . '.php';

        if (Filesystem::fileExists($snippet_path)) {

            // Turn on output buffering
            ob_start();

            // Include view file
            include $snippet_path;

            // Output...
            return ob_get_clean();
        } else {
            throw new \RuntimeException("Snippet {$snippet_name} does not exist.");
        }
    }
}
