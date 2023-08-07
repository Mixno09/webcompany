<?php

const CONFIG_DIR = __DIR__ . '/../config';
const PUBLIC_PATH = '/upload/image_user/';
define("IMAGE_DOCUMENT_ROOT", "{$_SERVER['DOCUMENT_ROOT']}/upload/image_user/");

require_once __DIR__ . '/function.php';

spl_autoload_register(function ($className) {
    if (strpos($className, 'App\\') === 0) {
        $className = mb_substr($className, 4);
        require_once __DIR__ . '/' . $className . '.php';
    }
});

