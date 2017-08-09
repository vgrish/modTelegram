<?php

abstract class modTelegramResponseProcessor extends modProcessor
{
    /** @var  modtelegram $modtelegram */
    public $modtelegram;
    public $action = 'default';

    public $classUser = 'modTelegramUser';
    public $classManager = 'modTelegramManager';
    public $classChat = 'modTelegramChat';
    public $classMessage = 'modTelegramMessage';

    function __construct(modX &$modx, array $properties = array())
    {
        parent::__construct($modx, $properties);

        if (!$namespace = $modx->getObject('modNamespace', 'modtelegram')) {
            $error = "[modtelegram] Not found modNamespace: modtelegram ";
            $this->modx->log(modX::LOG_LEVEL_ERROR, $error);

            return $this->failure($error);
        }
    }

    public function initialize()
    {
        /** @var modtelegram $modtelegram */
        $corePath = $this->modx->getOption('modtelegram_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtelegram/');
        /** @var modtelegram $modtelegram */
        $this->modtelegram = $this->modx->getService(
            'modtelegram',
            'modtelegram',
            $corePath . 'model/modtelegram/',
            array_merge($this->properties, array('core_path' => $corePath))
        );

        $propKey = $this->getProperty('propkey');
        if (empty($propKey)) {
            return $this->modtelegram->lexicon('err_propkey_ns');
        }

        $properties = $this->modtelegram->getProperties($propKey);
        if (empty($properties)) {
            return $this->modtelegram->lexicon('err_properties_ns');
        }

        $this->setProperties(array_merge($properties, $this->getProperties()));
        $this->modtelegram->initialize($this->getProperty('ctx', $this->modx->context->key), $this->getProperties());

        if (!$this->checkAction()) {
            return $this->modtelegram->lexicon('err_lock');
        }

        return true;
    }

    protected function checkAction()
    {
        $this->action = $this->modtelegram->explodeAndClean($this->getProperty('action'), '/');
        $this->action = end($this->action);

        $actions = $this->getProperty('actions');
        $actions = $this->modtelegram->explodeAndClean($actions);

        return in_array($this->action, $actions);
    }

    public function sendMessage($message = '', $uid = '')
    {
        if (empty($uid)) {
            $uid = $this->getProperty('from');
        }

        return $this->modtelegram->sendMessage($message, $uid);
    }

    public function sendHeader()
    {
        header('Content-Type: text/event-stream; charset=utf-8');
        header('Transfer-Encoding: identity');
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
    }

    public function sendRequest(array $data = array())
    {
        echo "data: " . json_encode($data, true);
        if (isset($data['timestamp'])) {
            echo "\n";
            echo "id: " . $data['timestamp'];
        }

        echo "\n";
        echo "retry: 1000";

        echo "\n\n";

        @ob_flush();
        flush();
        @session_write_close();
        //sleep(1);
    }

    public function sendExit()
    {
        echo "\n";
        echo "retry: 3000";
        echo "\n\n";
        @ob_flush();
        @session_write_close();
        exit();
    }

    public function failure($message = '', $data = array())
    {
        return $this->modtelegram->failure($message, $data);
    }

    public function success($message = '', $data = array())
    {
        return $this->modtelegram->success($message, $data);
    }


}

return 'modtelegramResponseProcessor';