<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package butler
 * @subpackage build
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('butler.core_path', null, $modx->getOption('core_path') . 'components/butler/') . 'model/';
            $modx->addPackage('butler', $modelPath, 'modx_');

            $manager = $modx->getManager();

            $manager->createObjectContainer('ButlerTasks');
            $manager->createObjectContainer('ButlerAlerts');
            $manager->createObjectContainer('ButlerBaseline');
            $manager->createObjectContainer('ButlerRunlog');
            $manager->createObjectContainer('ButlerLog');
            $manager->createObjectContainer('ButlerScanlog');

            break;
    }
}

return true;