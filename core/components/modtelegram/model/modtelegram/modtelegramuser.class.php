<?php

class modTelegramUser extends xPDOObject
{
    /** @var modtelegram $modtelegram */
    public $modtelegram;

    public function __construct(xPDO & $xpdo)
    {
        parent::__construct($xpdo);
        $this->modtelegram = $this->xpdo->getService('modtelegram');
    }

    public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag = true)
    {
        /** @var $instance modTelegramManager */
        $instance = parent::load($xpdo, 'modTelegramUser', $criteria, $cacheFlag);
        if (!is_object($instance) OR !($instance instanceof $className)) {
            if (is_scalar($criteria) OR (is_array($criteria) AND !empty($criteria['id']))) {
                $id = is_scalar($criteria) ? $criteria : $criteria['id'];
                $instance = $xpdo->newObject('modTelegramUser');
                $instance->set('id', $id);
                if (!empty($criteria['user']) AND $xpdo->getCount('modUser', array('id' => $criteria['user']))) {
                    $instance->set('user', $criteria['user']);
                }
                $instance->save();
            }
        }

        return $instance;
    }

    public function setActive($active = 1)
    {
        $this->setDirty();
        $this->set('active', $active);
        $set = $this->save();

        return $set;
    }

    public function sendMessage($message = '')
    {
        return $this->modtelegram->sendMessage($message, $this->get('id'));
    }

}