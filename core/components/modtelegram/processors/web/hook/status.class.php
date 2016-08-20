<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookStatusProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        @list($param) = $this->getProperty('options', array());

        /** @var modTelegramManager $manager */
        if ($manager = $this->modx->getObject($this->classManager, array(
            'id' => $this->getProperty('from'),
        ))
        ) {
            $message = array();
            $message[] = $this->modtelegram->lexicon('hook_info_success_' . $this->action);

            switch (true) {
                case $param == 'all':
                    $q = $this->modx->newQuery($this->classManager);
                    $q->where(array());
                    if ($q->prepare() AND $q->stmt->execute()) {
                        $ids = (array)$q->stmt->fetch(PDO::FETCH_COLUMN);
                    } else {
                        $ids = array();
                    }

                    foreach ($ids as $id) {
                        $row = $this->modtelegram->getManagerData($id, false);
                        $row = $this->modtelegram->flattenArray($row);
                        $message[] = $this->modtelegram->lexicon('status', $row);
                    }
                    break;
                default:
                    $row = $this->modtelegram->getManagerData($this->getProperty('from'), false);
                    $row = $this->modtelegram->flattenArray($row);
                    $message[] = $this->modtelegram->lexicon('status', $row);
                    break;
            }

            $this->sendMessage($message);

            return $this->success('', $message);
        }

        $message = $this->modtelegram->lexicon('hook_info_failure_' . $this->action);
        $this->sendMessage($message);

        return $this->failure('', $message);
    }

}

return 'modHookStatusProcessor';