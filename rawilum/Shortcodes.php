<?php namespace Rawilum;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Shortcodes
{

    /**
     * Shortcode tags array
     *
     * @var shortcode_tags
     */
    protected static $shortcode_tags = [];

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        // Nothing here
    }

    /**
     * Add new shortcode
     *
     * @param string $shortcode         Shortcode tag to be searched in content.
     * @param string $callback_function The callback function to replace the shortcode with.
     */
    public static function add(string $shortcode, $callback_function)
    {
        // Add new shortcode
        if (is_callable($callback_function)) {
            static::$shortcode_tags[$shortcode] = $callback_function;
        }
    }

    /**
     * Remove a specific registered shortcode.
     *
     * @param string $shortcode Shortcode tag.
     */
    public static function delete(string $shortcode)
    {
        // Delete shortcode
        if (static::exists($shortcode)) {
            unset(static::$shortcode_tags[$shortcode]);
        }
    }

    /**
     * Remove all registered shortcodes.
     *
     *  <code>
     *      Shortcode::clear();
     *  </code>
     *
     */
    public static function clear()
    {
        static::$shortcode_tags = array();
    }

    /**
     * Check if a shortcode has been registered.
     *
     * @param string $shortcode Shortcode tag.
     */
    public static function exists(string $shortcode)
    {
        // Check shortcode
        return array_key_exists($shortcode, static::$shortcode_tags);
    }

    /**
     * Parse a string, and replace any registered shortcodes within it with the result of the mapped callback.
     *
     * @param  string $content Content
     * @return string
     */
    public static function parse(string $content)
    {
        if (! static::$shortcode_tags) {
            return $content;
        }

        $shortcodes = implode('|', array_map('preg_quote', array_keys(static::$shortcode_tags)));
        $pattern    = "/(.?)\{([$shortcodes]+)(.*?)(\/)?\}(?(4)|(?:(.+?)\{\/\s*\\2\s*\}))?(.?)/s";

        return preg_replace_callback($pattern, 'static::_handle', $content);
    }

    /**
     * _handle()
     */
    protected static function _handle($matches)
    {
        $prefix    = $matches[1];
        $suffix    = $matches[6];
        $shortcode = $matches[2];

        // Allow for escaping shortcodes by enclosing them in {{shortcode}}
        if ($prefix == '{' && $suffix == '}') {
            return substr($matches[0], 1, -1);
        }

        $attributes = array(); // Parse attributes into into this array.

        if (preg_match_all('/(\w+) *= *(?:([\'"])(.*?)\\2|([^ "\'>]+))/', $matches[3], $match, PREG_SET_ORDER)) {
            foreach ($match as $attribute) {
                if (! empty($attribute[4])) {
                    $attributes[strtolower($attribute[1])] = $attribute[4];
                } elseif (! empty($attribute[3])) {
                    $attributes[strtolower($attribute[1])] = $attribute[3];
                }
            }
        }

        // Check if this shortcode realy exists then call user function else return empty string
        return (isset(static::$shortcode_tags[$shortcode])) ? $prefix . call_user_func(static::$shortcode_tags[$shortcode], $attributes, $matches[5], $shortcode) . $suffix : '';
    }
}
