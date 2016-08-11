<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookChatOutProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        /** @var modTelegramChat $chat */
        if ($chat = $this->modx->newObject($this->classChat)) {
            $chat->set('mid', $this->getProperty('from'));
            if ($chat->setActive(false, false, false)) {
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

return 'modHookChatOutProcessor';