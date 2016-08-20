<?php

$properties = array();

$tmp = array(
    'actions'     => array(
        'type'  => 'textfield',
        'value' => 'initialize,getmessage,sendmessage,attachfile',
    ),
    'helper'      => array(
        'type'  => 'textarea',
        'value' => '{"type":"popup","wrapper":""}',
    ),
    'frontendCss' => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]css/web/default.css',
    ),
    'frontendJs'  => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]js/web/default.js',
    ),
    'frontendLexicon'  => array(
        'type'  => 'textfield',
        'value' => 'modtelegram:default',
    ),
    'actionUrl'   => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]action.php',
    ),
);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        array(
            'name'    => $k,
            'desc'    => PKG_NAME_LOWER . '_prop_' . $k,
            'lexicon' => PKG_NAME_LOWER . ':properties',
        ), $v
    );
}

return $properties;