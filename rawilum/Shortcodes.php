<?php
namespace Rawilum;

/**
 * This file is part of the Rawilum.
 *
 * (c) Romanenko Sergey / Awilum <awilum@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Shortcodes
{
    /**
     * @var Rawilum
     */
    protected $rawilum;

    /**
     * Shortcode tags array
     *
     * @var shortcode_tags
     */
    protected $shortcode_tags = array();

    /**
     * Construct
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;
    }

    /**
     * Add new shortcode
     *
     * @param string $shortcode         Shortcode tag to be searched in content.
     * @param string $callback_function The callback function to replace the shortcode with.
     */
    public function add(string $shortcode, $callback_function)
    {
        // Add new shortcode
        if (is_callable($callback_function)) {
            $this->shortcode_tags[$shortcode] = $callback_function;
        }
    }

    /**
     * Remove a specific registered shortcode.
     *
     * @param string $shortcode Shortcode tag.
     */
    public function delete(string $shortcode)
    {
        // Delete shortcode
        if ($this->exists($shortcode)) {
            unset($this->shortcode_tags[$shortcode]);
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
    public function clear()
    {
        $this->shortcode_tags = array();
    }

    /**
     * Check if a shortcode has been registered.
     *
     * @param string $shortcode Shortcode tag.
     */
    public function exists(string $shortcode)
    {
        // Check shortcode
        return array_key_exists($shortcode, $this->shortcode_tags);
    }

    /**
     * Parse a string, and replace any registered shortcodes within it with the result of the mapped callback.
     *
     * @param  string $content Content
     * @return string
     */
    public function parse(string $content)
    {
        if (! $this->shortcode_tags) {
            return $content;
        }

        $shortcodes = implode('|', array_map('preg_quote', array_keys($this->shortcode_tags)));
        $pattern    = "/(.?)\{([$shortcodes]+)(.*?)(\/)?\}(?(4)|(?:(.+?)\{\/\s*\\2\s*\}))?(.?)/s";

        return preg_replace_callback($pattern, array($this, '_handle'), $content);
    }

    /**
     * _handle()
     */
    protected function _handle($matches)
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
        return (isset($this->shortcode_tags[$shortcode])) ? $prefix . call_user_func($this->shortcode_tags[$shortcode], $attributes, $matches[5], $shortcode) . $suffix : '';
    }
}
