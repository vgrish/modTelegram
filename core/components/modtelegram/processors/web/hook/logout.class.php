<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookLogOutProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        /** @var modTelegramManager $manager */
        if ($manager = $this->modx->getObject($this->classManager, array(
            'id' => $this->getProperty('from'),
        ))
        ) {
            if ($manager->remove()) {
                $message = $this->modtelegram->lexicon('hook_info_success_' . $this->action);
                $this->sendMessage($message);

                return $this->success('', $message);
            }
        }

        $message = $this->modtelegram->lexicon('hook_info_failure_' . $this->action);
        $this->sendMessage($message);

        return $this->failure('', $message);
    }

}

return 'modHookLogOutProcessor';