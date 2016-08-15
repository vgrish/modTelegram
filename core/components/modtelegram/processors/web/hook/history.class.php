<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookHistoryProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        @list($uid) = $this->getProperty('options', array());

        $q = $this->modx->newQuery($this->classChat);
        $q->where(array(
            'mid' => $this->getProperty('from'),
        ));
        if (!empty($uid)) {
            $q->andCondition(array(
                'uid' => $uid,
            ));
        }
        else {
            $q->andCondition(array(
                'active' => true,
            ));
        }

        /** @var modTelegramChat $chat */
        if ($chat = $this->modx->getObject($this->classChat, $q)) {
            $q = $this->modx->newQuery($this->classMessage);
            $q->where(array(
                'uid'  => $chat->getUser(),
                'mid'  => $chat->getManager(),
                'type' => 'text'
            ));
            $q->select($this->modx->getSelectColumns($this->classMessage, $this->classMessage));
            $q->sortby("{$this->classMessage}.timestamp", "ASC");
            $q->limit($this->getProperty('limit', 0));

            $message = array();
            $message[] = $this->modtelegram->lexicon('hook_info_success_' . $this->action);

            if ($q->prepare() AND $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $row = $this->modtelegram->processTelegramMessage($row);
                    $message[] = $this->modtelegram->lexicon('history', $row);
                }
            }

            for ($i = 0; $i <= 1000; $i = $i + 10) {
                $this->sendMessage(array_slice($message, $i, $i));
                usleep(250000);
            }

            return $this->success('', $message);
        }

        $message = $this->modtelegram->lexicon('hook_info_failure_' . $this->action);
        $this->sendMessage($message);

        return $this->failure('', $message);
    }

}

return 'modHookHistoryProcessor';
