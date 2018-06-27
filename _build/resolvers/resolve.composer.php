<?php

/** @var $modx modX */
/** @var $options */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

if (!function_enabled('shell_exec')) {
    return true;
}

$composer = MODX_BASE_PATH . 'composer.phar';
if (!file_exists($composer) AND !download('https://getcomposer.org/composer.phar', $composer)) {
    $modx->log(modX::LOG_LEVEL_ERROR, "Could not download Composer into {$composer}. Please do it manually.");
}

return true;