<?php
# Used only for running the app with internal PHP webserver
# php -S localhost:8080 route.php

if (file_exists(__DIR__ . "/" . $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    chdir(dirname(__FILE__));
    include_once "index.php";
}