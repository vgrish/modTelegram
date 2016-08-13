<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modChatInitializeProcessor extends modTelegramResponseProcessor
{

    function process()
    {
        $data = array();

        /** @var modTelegramUser $user */
        /** @var modTelegramChat $chat */
        if (
            $manager = $this->modtelegram->getAvailableManagerByUid(session_id())
            AND
            $user = $this->modx->getObject($this->classUser, array(
                'id'   => session_id(),
                'user' => $this->modx->user->isAuthenticated($this->modx->context->key) ? $this->modx->user->id : 0,
            ))
            AND
            $chat = $this->modx->getObject($this->classChat, array(
                'uid' => session_id(),
                'mid' => $manager
            ))
        ) {

            $user->setIp();

            if (!$chat->isActive()) {
                $message = $this->modtelegram->lexicon('chatin_manager_info_success_' . $this->action,
                    array('uid' => session_id()));
                $chat->sendMessage($message);
            }

            if ($chat->isNew()) {
                $message = 'hello!';

                $this->modtelegram->writeManagerMessage(array(
                    'uid'     => session_id(),
                    'mid'     => $manager,
                    'message' => $message
                ));
            }

            return $this->success('', $data);
        }

        $data['error'][] = $this->modtelegram->lexicon('chatin_user_info_failure_' . $this->action);

        return $this->failure('', $data);
    }

}

return 'modChatInitializeProcessor';