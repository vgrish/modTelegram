<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modChatSendMessageProcessor extends modTelegramResponseProcessor
{
    function process()
    {
        $data = array();
        $message = $this->getProperty('message', '');

        /** @var modTelegramUser $user */
        /** @var modTelegramChat $chat */
        if (
            $user = $this->modx->getObject($this->classUser, array(
                'id' => session_id(),
            ))
            AND
            $chat = $this->modx->getObject($this->classChat, array(
                'uid'    => session_id(),
                'active' => true,
            ))
            AND
            !empty($message)
        ) {

            $chat->sendMessage($message);
            $this->modtelegram->writeUserMessage(array(
                'uid'     => $chat->getUser(),
                'mid'     => $chat->getManager(),
                'message' => $message
            ));

            return $this->success('', $data);
        }

        $data['error'][] = $this->modtelegram->lexicon('chatin_user_info_failure_' . $this->action);

        return $this->failure('', $data);
    }

}

return 'modChatSendMessageProcessor';