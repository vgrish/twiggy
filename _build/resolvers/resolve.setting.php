<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        /** @var modContextSetting $tmp */
        if (!$tmp = $modx->getObject('modContextSetting', array('context_key' => 'web', 'key' => 'parser_class'))) {
            $tmp = $modx->newObject('modContextSetting');
        }
        $tmp->fromArray(array(
            'context_key' => 'web',
            'key'         => 'parser_class',
            'xtype'       => 'textfield',
            'namespace'   => 'twiggy',
            'area'        => 'twiggy_main',
            'value'       => 'twiggyParser',
        ), '', true, true);
        $tmp->save();
        /** @var modContextSetting $tmp */
        if (!$tmp = $modx->getObject('modContextSetting',
            array('context_key' => 'web', 'key' => 'parser_class_path'))
        ) {
            $tmp = $modx->newObject('modContextSetting');
        }
        $tmp->fromArray(array(
            'context_key' => 'web',
            'key'         => 'parser_class_path',
            'xtype'       => 'textfield',
            'namespace'   => 'twiggy',
            'area'        => 'twiggy_main',
            'value'       => '{core_path}components/twiggy/model/twiggy/',
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
            'value'     => 'twiggy.twiggypdofetch',
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
            'value'     => 'twiggy.twiggypdotools',
            'key'       => 'pdoTools.class',
        ), '', true, true);
        $tmp->save();

        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $modx->removeCollection('modSystemSetting', array('area' => 'twiggy_main'));
        $modx->removeCollection('modContextSetting', array('area' => 'twiggy_main'));

        /* pdoTools */
        if ($tmp = $modx->getObject('modSystemSetting', array('key' => 'pdoFetch.class'))) {
            $tmp->set('value', 'pdotools.pdofetch');
            $tmp->save();
        }
        if ($tmp = $modx->getObject('modSystemSetting', array('key' => 'pdoTools.class'))) {
            $tmp->set('value', 'pdotools.pdotools');
            $tmp->save();
        }

        break;
}

return true;