<?php

/** @var array $scriptProperties */

$fqn = $modx->getOption('twiggy_class', null, 'twiggy.twiggy', true);
$corePath = $modx->getOption('twiggy_class_path', null, MODX_CORE_PATH . 'components/twiggy/', true);
/** @var Twiggy $twiggy */
$twiggy = $modx->getService($fqn, '', $corePath . 'model', array('core_path' => $corePath));
if (!$twiggy) {
    return;
}

$className = 'twiggy' . $modx->event->name;
$modx->loadClass('twiggyPlugin', $corePath . 'model/systems/', true, true);
$modx->loadClass($className, $corePath . 'model/systems/', true, true);
if (class_exists($className)) {
    /** @var $twiggy $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}
return;