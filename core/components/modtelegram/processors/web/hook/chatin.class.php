<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookChatInProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        @list($uid) = $this->getProperty('options', array());

        /** @var modTelegramChat $chat */
        if ($chat = $this->modx->getObject($this->classChat, array(
            'uid' => $uid,
            'mid' => $this->getProperty('from'),
        ))
        ) {
            if ($chat->setActive(true)) {
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

return 'modHookChatInProcessor';