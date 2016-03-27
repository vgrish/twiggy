<?php

$fqn = $modx->getOption('twiggy_class', null, 'twiggy.twiggy', true);
$path = $modx->getOption('twiggy_class_path', null, MODX_CORE_PATH . 'components/twiggy/model/', true);
/** @var Twiggy $twiggy */
if (!$twiggy = $modx->getService(
    $fqn,
    '',
    $path,
    $this->config
)) {
    return 'Could not load Twiggy class!';
}

/** @var array $scriptProperties */
$tpl = $modx->getOption('tpl', $scriptProperties, '', true);
$fastMode = (boolean)$modx->getOption('fastMode', $scriptProperties, false, true);

$output = $twiggy->process($tpl, array(), $fastMode);

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
