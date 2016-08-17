<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookChatOutProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        /** @var modTelegramChat $chat */
        if ($chat = $this->modx->getObject($this->classChat, array(
            'mid'    => $this->getProperty('from'),
            'active' => true
        ))
        ) {
            if ($chat->remove()) {
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