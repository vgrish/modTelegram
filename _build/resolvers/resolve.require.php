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
if (!is_file($composer) OR !is_readable($composer)) {
    return true;
}

$path = MODX_CORE_PATH . 'components/modtelegram/';
$params = "--working-dir {$path} --no-progress 2>&1";
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $message = shell_exec("php {$composer} require {$params}");
        break;
    case xPDOTransport::ACTION_UPGRADE:
        $message = shell_exec("php {$composer} update {$params}");
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $message = shell_exec("php {$composer} remove {$params}");
        break;
    default:
        $message = '';
        break;
}
$modx->log(modX::LOG_LEVEL_INFO, $message);

return true;