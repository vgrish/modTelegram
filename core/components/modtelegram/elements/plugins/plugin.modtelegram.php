<?php

switch ($modx->event->name) {

    case 'OnMODXInit':
        $modx->loadClass('modUser');

        $modx->map['modUser']['composites']['TelegramManager'] = array(
            'class'       => 'modTelegramManager',
            'local'       => 'id',
            'foreign'     => 'user',
            'cardinality' => 'one',
            'owner'       => 'local',
        );

        $modx->map['modUser']['composites']['TelegramUser'] = array(
            'class'       => 'modTelegramUser',
            'local'       => 'id',
            'foreign'     => 'user',
            'cardinality' => 'one',
            'owner'       => 'local',
        );
        break;
}
