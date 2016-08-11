<?php

class modTelegramMessage extends xPDOObject
{

    public function save($cacheFlag = null)
    {
        if ($this->isNew()) {
            $this->set('timestamp', time());
        }

        return parent::save($cacheFlag);
    }
}