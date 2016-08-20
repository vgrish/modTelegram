<?php

require_once dirname(dirname(__FILE__)) . '/actions.class.php';

class modPusherEventProcessor extends modTelegramActionsProcessor
{
    public function initialize()
    {
        /** @var modtelegram $modtelegram */
        $corePath = $this->modx->getOption('modtelegram_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/modtelegram/');
        /** @var modtelegram $modtelegram */
        $this->modtelegram = $this->modx->getService(
            'modtelegram',
            'modtelegram',
            $corePath . 'model/modtelegram/',
            array_merge($this->properties, array('core_path' => $corePath))
        );

        $this->modtelegram->initialize($this->getProperty('ctx', $this->modx->context->key), $this->getProperties());

        return true;
    }

    function process()
    {
        if ($events = (array)$this->getProperty('events')) {
            foreach ($events as $event) {
                $uid = $this->modx->getOption('channel', $event);
                $mode = $this->modx->getOption('name', $event);

                switch (true) {
                    case $uid AND $mode == 'channel_vacated':
                        $this->userChatOut($uid);
                        break;
                    default:
                        break;
                }

            }
        }

        $message = array();

        return $this->success('', $message);
    }

    public function userChatOut($uid = '')
    {
        /** @var modTelegramChat $chat */
        if ($chat = $this->modx->getObject($this->classChat, array(
                'uid'    => $uid,
                'active' => true,
            ))
            AND
            $chat->setActive(false)
        ) {

            $message = $this->modtelegram->lexicon('event_user_chatout_success', array('uid' => $uid));
            $chat->sendMessage($message);
        }

        $message = $this->modtelegram->lexicon('event_user_chatout_failure', array('uid' => $uid));
        $chat->sendMessage($message);
    }

}

return 'modPusherEventProcessor';