<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modHookLogInProcessor extends modTelegramActionsProcessor
{
    function process()
    {
        @list($email, $password) = $this->getProperty('options', array());

        /** @var modTelegramManager $manager */
        if (
            $user = $this->modtelegram->getUserByEmailPassword($email, $password)
            AND
            $manager = $this->modx->getObject($this->classManager, array(
                'id'   => $this->getProperty('from'),
                'user' => $user
            ))
        ) {

            if ($manager->setActive(true)) {
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

return 'modHookLogInProcessor';