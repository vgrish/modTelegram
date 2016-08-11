<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookActionProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        $actions = $this->modtelegram->getOption('web_hook_action', null);
        $actions = $this->modtelegram->explodeAndClean($actions);

        $message = array();
        foreach ($actions as $action) {
            $description = $this->modtelegram->lexicon('description_action_' . $action, array('action' => $action));
            if (!empty($description)) {
                $message[] = $description;
            }
        }

        $this->sendMessage($message);

        return $this->success('', $message);
    }

}

return 'modHookActionProcessor';