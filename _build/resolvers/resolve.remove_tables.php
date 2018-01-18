<?php
if ($object->xpdo) {
  switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_UNINSTALL:
      /** @var modX $modx */
      $modx =& $object->xpdo;

      $modelPath = $modx->getOption('butler.core_path',null,$modx->getOption('core_path').'components/butler/').'model/';
      $modx->addPackage('butler',$modelPath);

      $manager = $modx->getManager();

      $manager->removeObjectContainer('ButlerTasks');
      $manager->removeObjectContainer('ButlerAlerts');
      $manager->removeObjectContainer('ButlerBaseline');
      $manager->removeObjectContainer('ButlerRunlog');
      $manager->removeObjectContainer('ButlerLog');
      $manager->removeObjectContainer('ButlerScanlog');
      break;
  }
}
return true;