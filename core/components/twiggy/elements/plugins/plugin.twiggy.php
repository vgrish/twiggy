<?php

/** @var array $scriptProperties */
/** @var twiggy $twiggy */
$corePath = $modx->getOption('twiggy_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/twiggy/');
$twiggy = $modx->getService('Twiggy', 'Twiggy', $corePath . 'model/twiggy/', array('core_path' => $corePath));

$className = 'twiggy' . $modx->event->name;
$modx->loadClass('twiggyPlugin', $corePath . 'model/systems/', true, true);
$modx->loadClass($className, $corePath . 'model/systems/', true, true);
if (class_exists($className)) {
    /** @var $twiggy $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}
return;
