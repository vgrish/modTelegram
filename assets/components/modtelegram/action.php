<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

if (empty($_REQUEST['action'])) {
    @session_write_close();
    die('Access denied');
}
$_REQUEST['action'] = strtolower(ltrim($_REQUEST['action'], '/'));
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
$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
if ($ctx != 'web') {
    $modx->switchContext($ctx);
    $modx->user = null;
    $modx->getUser($ctx);
}

/* read options from header */
if (isset($_SERVER['HTTP_DATA'])) {
    $_REQUEST = array_merge($_REQUEST, json_decode($_SERVER['HTTP_DATA'], true));
}

$corePath = $modx->getOption('modtelegram_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtelegram/');
$modtelegram = $modx->getService('modtelegram', 'modtelegram', $corePath . 'model/modtelegram/',
    array('core_path' => $corePath));

if ($modx->error->hasError() OR !($modtelegram instanceof modtelegram)) {
    @session_write_close();
    die('Error');
}

$modtelegram->initialize($ctx);
$modtelegram->config['processorsPath'] = $modtelegram->config['processorsPath'] . 'web/';
if (!$response = $modtelegram->runProcessor($_REQUEST['action'], $_REQUEST)) {
    $response = $modx->toJSON(array(
        'success' => false,
        'code'    => 401,
    ));
}
@session_write_close();
echo $response;