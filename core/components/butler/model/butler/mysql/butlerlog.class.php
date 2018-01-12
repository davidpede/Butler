<?php
/**
 * @package butler
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/butlerlog.class.php');
class ButlerLog_mysql extends ButlerLog {}
?>