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
        //'value' => '{base_path}twiggy/core/components/twiggy/elements/extensions/',

        'value' => '{core_path}components/twiggy/elements/extensions/',
        'area'  => 'twiggy_main',
    ),
    'path_templates'  => array(
        'xtype' => 'textarea',
        'value' => '{core_path}components/twiggy/elements/templates/',
        'area'  => 'twiggy_main',
    ),

    'extensions' => array(
        'xtype' => 'textarea',
        'value' => 'Tools,Cache,Pcre', // DebugBar
        'area'  => 'twiggy_main',
    ),

    'path_loaders' => array(
        'xtype' => 'textarea',
        'value' => '{core_path}components/twiggy/elements/loaders/',
        'area'  => 'twiggy_main',
    ),
    'loaders'      => array(
        'xtype' => 'textfield',
        'value' => 'Template,Chunk',
        'area'  => 'twiggy_main',
    ),


    'sandbox'            => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'twiggy_main',
    ),
    'sandbox_tags'       => array(
        'xtype' => 'textarea',
        'value' => 'if',
        'area'  => 'twiggy_sandbox',
    ),
    'sandbox_filters'    => array(
        'xtype' => 'textarea',
        'value' => 'upper',
        'area'  => 'twiggy_sandbox',
    ),
    'sandbox_methods'    => array(
        'xtype' => 'textarea',
        'value' => '{"Article":{"0":"getTitle","1":"getBody"}}',
        'area'  => 'twiggy_sandbox',
    ),
    'sandbox_properties' => array(
        'xtype' => 'textarea',
        'value' => '{"Article":{"0":"title","1":"body"}}',
        'area'  => 'twiggy_sandbox',
    ),
    'sandbox_functions'  => array(
        'xtype' => 'textarea',
        'value' => 'range',
        'area'  => 'twiggy_sandbox',
    ),


    'debug'      => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'twiggy_twig',
    ),
    'autoescape' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area'  => 'twiggy_twig',
    ),
    'cache'      => array(
        'xtype' => 'textfield',
        'value' => '{core_path}cache/default/twiggy/cache/',
        'area'  => 'twiggy_twig',
    ),

    /*'template'        => array(
        'xtype' => 'textfield',
        'value' => '{core_path}components/twiggy/elements/templates/',
        'area'  => 'twiggy_twig',
    ),*/


    /*	'assets_path'     => array(
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
        ),*/

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
