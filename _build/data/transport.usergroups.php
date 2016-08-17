<?php

$groups = array();

$tmp = array(
    'all' => array(
        'name'   => 'Telegram Manager',
        'parent' => 0,
    )
);

foreach ($tmp as $k => $v) {
    /* @avr modUserGroup $group */
    $group = $modx->newObject('modUserGroup');
    $group->fromArray($v, '', true, true);

    $groups[] = $group;
}

unset($tmp, $properties);
return $groups;