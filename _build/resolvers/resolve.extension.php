<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
    return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modx->addExtensionPackage('twiggy', '[[++core_path]]components/twiggy/model/');
        $modx->removeExtensionPackage('twiggypdotools');
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $modx->removeExtensionPackage('twiggy');
        $modx->removeExtensionPackage('twiggypdotools');
        break;
}

return true;