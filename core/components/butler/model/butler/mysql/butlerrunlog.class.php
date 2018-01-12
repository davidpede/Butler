<?php
/**
 * @package butler
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/butlerrunlog.class.php');
class ButlerRunlog_mysql extends ButlerRunlog {}
?>