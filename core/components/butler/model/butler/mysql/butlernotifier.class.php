<?php
/**
 * @package butler
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/butlernotifier.class.php');
class ButlerNotifier_mysql extends ButlerNotifier {}
?>