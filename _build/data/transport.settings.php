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
        'value' => 'action,login,logout,chatin,chatout,reply,history,status',
        'xtype' => 'textarea',
        'area'  => 'modtelegram_main',
    ),

    //временные
    'assets_path'      => array(
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
    ),


    /*
	'some_setting' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'modtelegram_main',
	),
	*/
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
