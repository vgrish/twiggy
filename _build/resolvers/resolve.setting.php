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
		if (!$tmp = $modx->getObject('modContextSetting', array('context_key' => 'web', 'key' => 'parser_class_path'))) {
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
		break;
	case xPDOTransport::ACTION_UNINSTALL:
		$modx->removeCollection('modSystemSetting', array('area' => 'twiggy_main'));
		$modx->removeCollection('modContextSetting', array('area' => 'twiggy_main'));
		break;
}

return true;