<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookStatusProcessor extends modTelegramActionsProcessor
{
    function process()
    {

        $message = array();

        /** @var modTelegramManager $manager */
        if ($manager = $this->modx->getObject($this->classManager, array(
            'id' => $this->getProperty('from'),
        ))
        ) {

            $manager = $this->modtelegram->getManagerData($this->getProperty('from'));
            $message[] = $this->modtelegram->lexicon('status', $manager);
          
            $this->sendMessage($message);

            return $this->success('', $message);
        }

        $message = $this->modtelegram->lexicon('hook_info_failure_' . $this->action);
        $this->sendMessage($message);

        return $this->failure('', $message);
    }

}

return 'modHookStatusProcessor';