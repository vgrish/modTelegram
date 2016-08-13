<?php

class modTelegramManager extends xPDOObject
{

    /** @var modtelegram $modtelegram */
    public $modtelegram;

    /**
     * modTelegramManager constructor.
     *
     * @param xPDO $xpdo
     */
    public function __construct(xPDO & $xpdo)
    {
        parent::__construct($xpdo);
        $this->modtelegram = $this->xpdo->getService('modtelegram');
    }


    /**
     * @param xPDO   $xpdo
     * @param string $className
     * @param mixed  $criteria
     * @param bool   $cacheFlag
     *
     * @return modTelegramManager
     */
    public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag = true)
    {
        /** @var $instance modTelegramManager */
        $instance = parent::load($xpdo, 'modTelegramManager', $criteria, $cacheFlag);
        if (!is_object($instance) OR !($instance instanceof $className)) {
            if (is_scalar($criteria) OR (is_array($criteria) AND !empty($criteria['id']))) {
                $id = is_scalar($criteria) ? $criteria : $criteria['id'];
                $instance = $xpdo->newObject('modTelegramManager');
                $instance->set('id', $id);
                if (!empty($criteria['user']) AND $xpdo->getCount('modUser', array('id' => $criteria['user']))) {
                    $instance->set('user', $criteria['user']);
                }
                $instance->save();
            }
        }

        return $instance;
    }

    /**
     * @param int $active
     *
     * @return bool
     */
    public function setActive($active = 1)
    {
        $this->setDirty();
        $this->set('active', $active);
        $set = $this->save();

        return $set;
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    public function setIp($ip = '')
    {
        $this->setDirty();
        if (empty($ip)) {
            $ip = $this->modtelegram->getUserIp();
        }
        $properties = array_merge((array)$this->get('properties'), array(
            'ip' => $ip
        ));
        $this->set('properties', $properties);
        $set = $this->save();

        return $set;
    }

    /**
     * @param string $message
     *
     * @return array|bool
     */
    public function sendMessage($message = '')
    {
        return $this->modtelegram->sendMessage($message, $this->get('id'));
    }
}