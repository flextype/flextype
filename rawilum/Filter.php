<?php
namespace Rawilum;

/**
 * This file is part of the Rawilum.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Filter
{

    /**
     * @var Rawilum
     */
    protected $rawilum;

    /**
     * Filters
     *
     * @var array
     * @access protected
     */
    protected static $filters = [];

    /**
     * Construct
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;
    }

    /**
     * Apply filters
     *
     *  <code>
     *      Filter::apply('content', $content);
     *  </code>
     *
     * @access  public
     * @param  string $filter_name The name of the filter hook.
     * @param  mixed  $value       The value on which the filters hooked.
     * @return mixed
     */
    public function dispatch($filter_name, $value)
    {
        // Redefine arguments
        $filter_name = (string) $filter_name;

        $args = array_slice(func_get_args(), 2);

        if (! isset(static::$filters[$filter_name])) {
            return $value;
        }

        foreach (static::$filters[$filter_name] as $priority => $functions) {
            if (! is_null($functions)) {
                foreach ($functions as $function) {
                    $all_args = array_merge(array($value), $args);
                    $function_name = $function['function'];
                    $accepted_args = $function['accepted_args'];
                    if ($accepted_args == 1) {
                        $the_args = array($value);
                    } elseif ($accepted_args > 1) {
                        $the_args = array_slice($all_args, 0, $accepted_args);
                    } elseif ($accepted_args == 0) {
                        $the_args = null;
                    } else {
                        $the_args = $all_args;
                    }
                    $value = call_user_func_array($function_name, $the_args);
                }
            }
        }

        return $value;
    }

    /**
     * Add filter
     *
     *  <code>
     *      Filter::add('content', 'replacer');
     *
     *      function replacer($content) {
     *          return preg_replace(array('/\[b\](.*?)\[\/b\]/ms'), array('<strong>\1</strong>'), $content);
     *      }
     *  </code>
     *
     * @access  public
     * @param  string  $filter_name     The name of the filter to hook the $function_to_add to.
     * @param  string  $function_to_add The name of the function to be called when the filter is applied.
     * @param  integer $priority        Function to add priority - default is 10.
     * @param  integer $accepted_args   The number of arguments the function accept default is 1.
     * @return boolean
     */
    public function addListener($filter_name, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        // Redefine arguments
        $filter_name     = (string) $filter_name;
        $function_to_add = $function_to_add;
        $priority        = (int) $priority;
        $accepted_args   = (int) $accepted_args;

        // Check that we don't already have the same filter at the same priority. Thanks to WP :)
        if (isset(static::$filters[$filter_name]["$priority"])) {
            foreach (static::$filters[$filter_name]["$priority"] as $filter) {
                if ($filter['function'] == $function_to_add) {
                    return true;
                }
            }
        }

        static::$filters[$filter_name]["$priority"][] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);

        // Sort
        ksort(static::$filters[$filter_name]["$priority"]);

        return true;
    }
}
