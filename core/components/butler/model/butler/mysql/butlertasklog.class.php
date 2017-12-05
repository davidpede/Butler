<?php
/**
 * @package butler
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/butlertasklog.class.php');
class ButlerTasklog_mysql extends ButlerTasklog {}
?>