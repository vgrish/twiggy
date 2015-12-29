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
			'namespace' => 'twiggy',
			'area'      => 'twiggy_main',
			'xtype'     => 'textfield',
			'value'     => 'twiggyParser',
			'key'       => 'parser_class',
		), '', true, true);
		$tmp->save();
		/** @var modSystemSetting $tmp */
		if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'parser_class_path'))) {
			$tmp = $modx->newObject('modSystemSetting');
		}
		$tmp->fromArray(array(
			'namespace' => 'twiggy',
			'area'      => 'twiggy_main',
			'xtype'     => 'textfield',
			'value'     => '{core_path}components/twiggy/model/twiggy/',
			'key'       => 'parser_class_path',
		), '', true, true);
		$tmp->save();

		break;
	case xPDOTransport::ACTION_UNINSTALL:
		$modx->removeCollection('modSystemSetting', array('area' => 'twiggy_main'));
		break;
}

return true;