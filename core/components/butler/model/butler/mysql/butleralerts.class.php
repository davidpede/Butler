<?php
/**
 * @package butler
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/butleralerts.class.php');
class ButlerAlerts_mysql extends ButlerAlerts {}
?>