<?php

define('BASE_PATH', realpath(__DIR__ . '/../'));
require BASE_PATH . '/vendor/autoload.php';

set_exception_handler(function () {
    throw new Exception();
});