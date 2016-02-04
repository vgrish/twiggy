<?php

/** @var array $scriptProperties */
$tpl = $modx->getOption('tpl', $scriptProperties, '', true);
$fastMode = (boolean)$modx->getOption('fastMode', $scriptProperties, false, true);

/** @var Twiggy $Twiggy */
$corePath = $modx->getOption('twiggy_core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/twiggy/');
if (!$Twiggy = $modx->getService('Twiggy', 'Twiggy', $corePath . 'model/twiggy/', $scriptProperties)) {
	return 'Could not load Twiggy class!';
}

$output = $Twiggy->process($tpl, array(), $fastMode);

if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
} else {
	return $output;
}