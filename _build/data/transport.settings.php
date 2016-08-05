<?php

$settings = array();

$tmp = array(

    'apiUrl'          => array(
        'value' => 'https://api.telegram.org/',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_main',
    ),
    'apiKey'          => array(
        'value' => '255957967:AAH9CsN4eWErXcaTnfryO7DURYwMkyoGwQo',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_main',
    ),
    'managerPassword' => array(
        'value' => '12345',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_main',
    ),

    //временные
    'assets_path'     => array(
        'value' => '{base_path}modtelegram/assets/components/modtelegram/',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_temp',
    ),
    'assets_url'      => array(
        'value' => '/modtelegram/assets/components/modtelegram/',
        'xtype' => 'textfield',
        'area'  => 'modtelegram_temp',
    ),
    'core_path'       => array(
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
