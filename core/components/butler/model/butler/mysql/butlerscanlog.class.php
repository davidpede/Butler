<?php
/**
 * @package butler
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/butlerscanlog.class.php');
class ButlerScanlog_mysql extends ButlerScanlog {}
?>