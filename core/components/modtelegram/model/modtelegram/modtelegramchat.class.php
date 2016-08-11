<?php

class modTelegramChat extends xPDOObject
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
        $instance = parent::load($xpdo, 'modTelegramChat', $criteria, $cacheFlag);
        if (!is_object($instance) OR !($instance instanceof $className)) {
            if ((is_array($criteria) AND isset($criteria['uid'], $criteria['mid']))) {
                if (
                    $xpdo->getCount('modTelegramUser', array('id' => $criteria['uid']))
                    AND
                    $xpdo->getCount('modTelegramManager', array('id' => $criteria['mid']))
                ) {
                    $instance = $xpdo->newObject('modTelegramChat');
                    $instance->set('uid', $criteria['uid']);
                    $instance->set('mid', $criteria['mid']);
                    $instance->save();
                }
            }
        }

        return $instance;
    }

    public function setActive($active = 1, $mid = true, $save = true)
    {
        $q = $this->xpdo->newQuery('modTelegramChat');
        $q->command('UPDATE');
        $q->set(array(
            'active' => 0,
        ));

        if ($mid) {
            $q->where(array(
                'mid' => $this->get('mid'),
            ));
        }

        $q->prepare();
        $q->stmt->execute();

        $this->setDirty();
        $this->set('active', $active);
        $set = $save ? $this->save() : true;

        return $set;
    }

    public function getActive()
    {
        $q = $this->xpdo->newQuery('modTelegramChat');
        $q->where(array(
            'mid'    => $this->get('mid'),
            'active' => true,
        ));

        return $this->xpdo->getValue($q->prepare());
    }

    public function getManager()
    {
        return $this->get('mid');
    }

    public function getUser()
    {
        return $this->get('uid');
    }
    
    public function isActive()
    {
        return $this->get('uid') == $this->getActive();
    }

    public function sendMessage($message = '')
    {
        return $this->modtelegram->sendMessage($message, $this->get('mid'));
    }

    public function isNew()
    {
        $new = parent::isNew();
        if (!$new) {
            $q = $this->xpdo->newQuery('modTelegramMessage');
            $q->where(array(
                'uid' => $this->get('uid'),
                'mid' => $this->get('mid'),
            ));
            $new = !$this->xpdo->getCount('modTelegramMessage', $q);
        }

        return $new;
    }

}