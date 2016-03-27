<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'parser_class'))) {
            $tmp = $modx->newObject('modSystemSetting');
        }
        $tmp->fromArray(array(
            'namespace' => 'pdotools',
            'area'      => 'pdotools_main',
            'xtype'     => 'textfield',
            'value'     => 'twiggy.twiggyparser',
            'key'       => 'parser_class',
        ), '', true, true);
        $tmp->save();

        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'parser_class_path'))) {
            $tmp = $modx->newObject('modSystemSetting');
        }
        $tmp->fromArray(array(
            'namespace' => 'pdotools',
            'area'      => 'pdotools_main',
            'xtype'     => 'textfield',
            'value'     => '{core_path}components/twiggy/model/',
            'key'       => 'parser_class_path',
        ), '', true, true);
        $tmp->save();

        /* pdoTools */
        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'pdoFetch.class'))) {
            $tmp = $modx->newObject('modSystemSetting');
        }
        $tmp->fromArray(array(
            'namespace' => 'pdotools',
            'area'      => 'pdotools_main',
            'xtype'     => 'textfield',
            'value'     => 'twiggypdotools.twiggypdofetch',
            'key'       => 'pdoFetch.class',
        ), '', true, true);
        $tmp->save();

        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'pdoTools.class'))) {
            $tmp = $modx->newObject('modSystemSetting');
        }
        $tmp->fromArray(array(
            'namespace' => 'pdotools',
            'area'      => 'pdotools_main',
            'xtype'     => 'textfield',
            'value'     => 'twiggypdotools.twiggypdotools',
            'key'       => 'pdoTools.class',
        ), '', true, true);
        $tmp->save();

        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'pdotools_class_path'))) {
            $tmp = $modx->newObject('modSystemSetting');
        }
        $tmp->fromArray(array(
            'namespace' => 'pdotools',
            'area'      => 'pdotools_main',
            'xtype'     => 'textfield',
            'value'     => '{core_path}components/twiggy/model/',
            'key'       => 'pdotools_class_path',
        ), '', true, true);
        $tmp->save();

        if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'pdotools_fenom_default'))) {
            $tmp = $modx->newObject('modSystemSetting');
        }
        $tmp->fromArray(array(
            'namespace' => 'pdotools',
            'area'      => 'pdotools_main',
            'xtype'     => 'combo-boolean',
            'value'     => false,
            'key'       => 'pdotools_fenom_default',
        ), '', true, true);
        $tmp->save();
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        /* pdoTools */
        foreach (array('pdoFetch.class', 'pdoTools.class', 'pdotools_class_path') as $k) {
            if ($tmp = $modx->getObject('modSystemSetting', array('key' => $k))) {
                $tmp->remove();
            }
        }

        if ($tmp = $modx->getObject('modSystemSetting', array('key' => 'pdotools_fenom_default'))) {
            $tmp->set('value', true);
            $tmp->save();
        }

        if ($modx->getCount('modSystemSetting', array('key' => 'pdotools_fenom_default'))) {

            if ($tmp = $modx->getObject('modSystemSetting', array('key' => 'parser_class'))) {
                $tmp->fromArray(array(
                    'namespace' => 'pdotools',
                    'area'      => 'pdotools_main',
                    'xtype'     => 'textfield',
                    'value'     => 'pdoParser',
                    'key'       => 'parser_class',
                ), '', true, true);
                $tmp->save();
            }

            if ($tmp = $modx->getObject('modSystemSetting', array('key' => 'parser_class_path'))) {
                $tmp->fromArray(array(
                    'namespace' => 'pdotools',
                    'area'      => 'pdotools_main',
                    'xtype'     => 'textfield',
                    'value'     => '{core_path}components/pdotools/model/pdotools/',
                    'key'       => 'parser_class_path',
                ), '', true, true);
                $tmp->save();
            }
        }

        break;
}

return true;