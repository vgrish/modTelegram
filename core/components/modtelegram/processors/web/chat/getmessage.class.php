<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modChatInitializeProcessor extends modTelegramResponseProcessor
{
    function process()
    {
        //set_time_limit(0);

        $data = array();

        $this->modx->log(1, print_r($this->getProperties(), 1));

        /** @var modTelegramUser $manager */
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
        ) {

            $q = $this->modx->newQuery($this->classMessage);
            $q->where(array(
                'uid'         => $chat->getUser(),
                'mid'         => $chat->getManager(),
                'timestamp:>' => $this->getProperty('timestamp', 0)
            ));

            $q->select($this->modx->getSelectColumns($this->classMessage, $this->classMessage));
            $q->sortby("{$this->classMessage}.timestamp", "ASC");
            $q->limit($this->getProperty('limit', 10));

            if ($q->prepare() AND $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data['messages'][] = $this->modtelegram->processChatMessage($row);
                }
            }

            $data['user'] = $this->modtelegram->getUserData($chat->getUser());
            $data['manager'] = $this->modtelegram->getManagerData($chat->getManager());

        }

        $this->modx->log(1, print_r($data, 1));

        return $this->sendRequest($data);
    }
}

return 'modChatInitializeProcessor';