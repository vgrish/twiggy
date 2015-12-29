<?php

$settings = array();

$tmp = array(

	'class'           => array(
		'xtype' => 'textfield',
		'value' => 'twiggy.Twiggy',
		'area'  => 'twiggy_main',
	),
	'path_extensions' => array(
		'xtype' => 'textfield',
		'value' => '{base_path}twiggy/core/components/twiggy/elements/extensions/',

		//'value' => '{core_path}components/twiggy/elements/extensions/',
		'area'  => 'twiggy_main',
	),
	'extensions'      => array(
		'xtype' => 'textarea',
		'value' => 'UniqID,Text,Pcre,Cache',//'UniqID,Text,Pcre,Cache',
		'area'  => 'twiggy_main',
	),

	'debug'           => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area'  => 'twiggy_twig',
	),
	'autoescape'      => array(
		'xtype' => 'textfield',
		'value' => '',
		'area'  => 'twiggy_twig',
	),
	'cache'           => array(
		'xtype' => 'textfield',
		'value' => '{core_path}cache/default/twiggy/cache/',
		'area'  => 'twiggy_twig',
	),
	'template'        => array(
		'xtype' => 'textfield',
		'value' => '{core_path}components/twiggy/elements/templates/',
		'area'  => 'twiggy_twig',
	),


	'assets_path'     => array(
		'value' => '{base_path}twiggy/assets/components/twiggy/',
		'xtype' => 'textfield',
		'area'  => 'twiggy_temp',
	),
	'assets_url'      => array(
		'value' => '/twiggy/assets/components/twiggy/',
		'xtype' => 'textfield',
		'area'  => 'twiggy_temp',
	),
	'core_path'       => array(
		'value' => '{base_path}twiggy/core/components/twiggy/',
		'xtype' => 'textfield',
		'area'  => 'twiggy_temp',
	),

);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key'       => 'twiggy_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
