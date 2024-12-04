<?php

require dirname(__DIR__) . '/vendor/autoload.php';

set_exception_handler(function () {
    throw new Exception();
});
