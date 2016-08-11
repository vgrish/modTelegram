<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookReplayProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        @list($message) = $this->getProperty('options', array());

        /** @var modTelegramChat $chat */
        if ($chat = $this->modx->getObject($this->classChat, array(
            'mid'    => $this->getProperty('from'),
            'active' => true,
        ))
        ) {
            $this->modtelegram->writeManagerMessage(array(
                'uid'     => $chat->getUser(),
                'mid'     => $chat->getManager(),
                'message' => $message
            ));

            return $this->success('', $message);
        }

        return $this->failure('', $message);
    }

}

return 'modHookReplayProcessor';