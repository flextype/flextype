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

use Flextype\Component\Arr\Arr;

class Events
{

    /**
     * Events
     *
     * @var array
     * @access protected
     */
    protected static $events = [];

    /**
     * Protected constructor since this is a static class.
     *
     * @access protected
     */
    protected function __construct()
    {
        // Nothing here
    }

    /**
     *  Hooks a function on to a specific event.
     *
     * @access public
     * @param string  $event_name     Event name
     * @param mixed   $added_function Added function
     * @param integer $priority       Priority. Default is 10
     * @param array   $args           Arguments
     * @return void
     */
    public static function addListener(string $event_name, $added_function, int $priority = 10, array $args = null) : void
    {
        // Hooks a function on to a specific event.
        static::$events[] = array(
                        'event_name'  => $event_name,
                        'function'    => $added_function,
                        'priority'    => $priority,
                        'args'        => $args
        );
    }

    /**
     * Run functions hooked on a specific event.
     *
     * @access public
     * @param string  $event_name  Event name
     * @param array   $args        Arguments
     * @param boolean $return      Return data or not. Default is false
     * @return mixed
     */
    public static function dispatch(string $event_name, array $args = [], bool $return = false)
    {
        // Redefine arguments
        $event_name  =  $event_name;
        $return      =  $return;

        // Run event
        if (count(static::$events) > 0) {

            // Sort actions by priority
            $events = Arr::subvalSort(static::$events, 'priority');

            // Loop through $events array
            foreach ($events as $action) {

                // Execute specific action
                if ($action['event_name'] == $event_name) {
                    // isset arguments ?
                    if (isset($args)) {
                        // Return or Render specific action results ?
                        if ($return) {
                            return call_user_func_array($action['function'], $args);
                        } else {
                            call_user_func_array($action['function'], $args);
                        }
                    } else {
                        if ($return) {
                            return call_user_func_array($action['function'], $action['args']);
                        } else {
                            call_user_func_array($action['function'], $action['args']);
                        }
                    }
                }
            }
        }
    }
}
