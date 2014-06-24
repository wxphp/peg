<?php
/**
 * @author Jefferson González
 * @license MIT
 * @link http://github.com/wxphp/peg Source code.
 */

namespace Peg\CommandLine;

/**
 * Functions to throw error messages.
 */
class Error
{

    /**
     * Displays a message and exits the application with error status code.
     * @param string $message The message to display before exiting the application.
     */
    public static function Show($message)
    {
        print t("Error:") . " " . $message . "\n";
        exit(1);
    }

}

?>
