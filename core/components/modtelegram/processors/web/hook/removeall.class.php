<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookRemoveAllProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        @list($password) = $this->getProperty('options', array());

        if ($password == $this->modtelegram->getOption('action_password', null)) {

            foreach (array(
                         $this->modtelegram->classUser,
                         $this->modtelegram->classManager,
                         $this->modtelegram->classChat,
                         $this->modtelegram->classMessage
                     ) as $class) {
                $this->clearTable($class);
            }

            $message = $this->modtelegram->lexicon('hook_info_success_' . $this->action);
            $this->sendMessage($message);

            return $this->success('', $message);

        }

        $message = $this->modtelegram->lexicon('hook_info_failure_' . $this->action);
        $this->sendMessage($message);

        return $this->failure('', $message);
    }

    protected function clearTable($class = '')
    {
        if (!empty($class)) {
            $this->modx->query("DELETE FROM {$this->modx->getTableName($class)}");
            $this->modx->query("ALTER TABLE {$this->modx->getTableName($class)} AUTO_INCREMENT=1");
        }
    }

}

return 'modHookRemoveAllProcessor';