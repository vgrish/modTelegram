<?php

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

$stream = file_get_contents('php://input');
$stream = json_decode($stream, true);

if (!isset($stream['message']) OR !isset($stream['message']['text'])) {
    @session_write_close();
    die('Access denied');
} else {
    $options = strtolower(trim($stream['message']['text']));
}

define('MODX_API_MODE', true);
define('MODX_ACTION_MODE', true);

$productionIndex = dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$developmentIndex = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
if (file_exists($productionIndex)) {
    /** @noinspection PhpIncludeInspection */
    require_once $productionIndex;
} else {
    /** @noinspection PhpIncludeInspection */
    require_once $developmentIndex;
}
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$corePath = $modx->getOption('modtelegram_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtelegram/');
$modtelegram = $modx->getService('modtelegram', 'modtelegram', $corePath . 'model/modtelegram/',
    array('core_path' => $corePath));

if ($modx->error->hasError() OR !($modtelegram instanceof modtelegram)) {
    @session_write_close();
    die('Error');
}

if (strpos($options, '/') !== 0) {
    $options = array($options);
    $action = 'reply';
}
else {
    $options = ltrim($options, '/');
    $options = $modtelegram->explodeAndClean($options, '_');
    $action = array_shift($options);
}

$webHookAction = $modtelegram->explodeAndClean($modtelegram->getOption('web_hook_action', null));
if (!in_array($action, $webHookAction)) {
    @session_write_close();
    die('Error');
}

$stream['action'] = $action;
$stream['options'] = $options;

$modx->log(1, print_r($action, 1));
$modx->log(1, print_r($stream, 1));

$modtelegram->initialize($ctx);
$modtelegram->config['processorsPath'] = $modtelegram->config['processorsPath'] . 'web/hook/';
if (!$response = $modtelegram->runProcessor($action, $stream)) {
    $response = $modx->toJSON(array(
        'success' => false,
        'code'    => 401,
    ));
}
@session_write_close();
echo $response;