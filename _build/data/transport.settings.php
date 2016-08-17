<?php

$settings = array();

$tmp = array(

    'api_url'         => array(
        'value' => 'https://api.telegram.org/',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_main',
    ),
    'api_key'         => array(
        'value' => '269086151:AAHqare4N9AWiMvQONeB8d7KpQIxxhCh19U',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_main',
    ),
    'web_hook_url'    => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_main',
    ),
    'web_hook_action' => array(
        'value' => 'action,login,logout,chatin,chatout,reply,history,status,location,removeall',
        'xtype' => 'textarea',
        'area'  => 'modtelegram_main',
    ),
    'action_password' => array(
        'value' => '000000',
        'xtype' => 'textarea',
        'area'  => 'modtelegram_main',
    ),

    'pusher_active'  => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modtelegram_pusher',
    ),
    'pusher_id'     => array(
        'value' => '236793',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_pusher',
    ),
    'pusher_key'     => array(
        'value' => '847894834d2742855180',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_pusher',
    ),
    'pusher_secret'     => array(
        'value' => 'f14dd376a3558f19c6b5',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_pusher',
    ),
    'pusher_encrypted'  => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area'  => 'modtelegram_pusher',
    ),

    //временные
    /* 'assets_path'      => array(
         'value' => '{base_path}modtelegram/assets/components/modtelegram/',
         'xtype' => 'textfield',
         'area'  => 'modtelegram_temp',
     ),
     'assets_url'       => array(
         'value' => '/modtelegram/assets/components/modtelegram/',
         'xtype' => 'textfield',
         'area'  => 'modtelegram_temp',
     ),
     'core_path'        => array(
         'value' => '{base_path}modtelegram/core/components/modtelegram/',
         'xtype' => 'textfield',
         'area'  => 'modtelegram_temp',
     ),*/

);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'modtelegram_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
