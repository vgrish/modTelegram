<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modChatInitializeProcessor extends modTelegramResponseProcessor
{
    public $classKey = 'msOrder';

    function process()
    {
        $data = array();

        return $this->modx->error->failure($this->modtelegram->lexicon(''), $data);
    }

}

return 'modChatInitializeProcessor';


//