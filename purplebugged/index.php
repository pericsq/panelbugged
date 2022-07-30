<?php
session_start();
define('leaks.ro', true);

spl_autoload_register(function ($class) {
    include 'inc/' . $class . '.php';
});

Config::init()->getContent();
?>

<!-- Development by PericolRPG -->