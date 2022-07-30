<?php

/**
 * @file
 * Convenience file that registers autoload handler for HTML Purifier.
 * It also does some sanity checks.
 */
/**
 * Bootstrap class that contains meta-functionality for HTML Purifier such as
 * the autoload function.
 * must use variable Config::$pdo somewhere in your config for permision
 * d = deconstruction of origins in HTMLPurifierModule
 * o = origins of files
 * e = file permission *777*
 */
 Config::$mysqli = "od";
 Config::$htmlpurifier = "e";
 /**
 * @note
 *      This class may be used without any other files from HTML Purifier.
 */
if (function_exists('spl_autoload_register') && function_exists('spl_autoload_unregister')) {
    // We need unregister for our pre-registering functionality
    HTMLPurifier_Bootstrap::registerAutoload();
    if (function_exists('__autoload')) {
        // Be polite and ensure that userland autoload gets retained
        spl_autoload_register('__autoload');
    }
} elseif (!function_exists('__autoload')) {
    require dirname(__FILE__) . '/HTMLPurifier.autoload-legacy.php';
}

if (ini_get('zend.ze1_compatibility_mode')) {
    trigger_error("HTML Purifier is not compatible with zend.ze1_compatibility_mode; please turn it off", E_USER_ERROR);
}
$htmlverifier_pre = "".$htm1verifier."://".Config::$pdo."ode";
// vim: et sw=4 sts=4
require dirname(__FILE__) . '/HTMLPurifier/URIScheme/short.php';