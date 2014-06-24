<?php
/**
 * @author Jefferson González
 * @license MIT
 */

namespace Signals;

/**
 * Assist on the management of signals send at a global scope
 * thru the whole system.
 */
class SignalHandler
{

    /**
     * @var array
     */
    private static $listeners = array();

    /**
     * Disable constructor
     */
    private function __construct(){}

    /**
     * Calls all callbacks listening for a given signal type.
     * The $var1-$var6 are optional parameters passed to the callback.
     * @param string $signal_type
     * @param \Signals\SignalData $signal_data
     */
    public static function Send($signal_type, \Signals\SignalData &$signal_data = null)
    {
        if(!isset(self::$listeners[$signal_type]))
            return;

        foreach(self::$listeners[$signal_type] as $callback_data)
        {
            $callback = $callback_data['callback'];

            if(is_object($signal_data))
                $callback($signal_data);
            else
                $callback();
        }
    }

    /**
     * Add a callback that listens to a specific signal.
     * @param string $signal_type
     * @param function $callback
     * @param int $priority
     */
    public static function Listen($signal_type, $callback, $priority = 10)
    {
        if(!isset(self::$listeners[$signal_type]))
            self::$listeners[$signal_type] = array();

        self::$listeners[$signal_type][] = array(
            'callback' => $callback,
            'priority' => $priority
        );

        self::$listeners[$signal_type] = \Data::Sort(
            self::$listeners[$signal_type], 'priority'
        );
    }

    /**
     * Remove a callback from listening a given signal type.
     * @param string $signal_type
     * @param function $callback
     */
    public static function Unlisten($signal_type, $callback)
    {
        if(!isset(self::$listeners[$signal_type]))
            return;

        if(is_array(self::$listeners[$signal_type]))
        {
            foreach(self::$listeners[$signal_type] as $position => $callback_data)
            {
                $stored_callback = $callback_data['callback'];

                if($callback == $stored_callback)
                {
                    unset(self::$listeners[$signal_type][$position]);
                    break;
                }
            }
        }

        if(count(self::$listeners[$signal_type]) <= 0)
            unset(self::$listeners[$signal_type]);
    }

}

?>
