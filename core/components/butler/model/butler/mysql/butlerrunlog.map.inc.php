<?php
/**
 * @package butler
 */
$xpdo_meta_map['ButlerRunlog']= array (
  'package' => 'butler',
  'version' => '1.1',
  'table' => 'butler_runlog',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'task_id' => NULL,
    'task_type' => NULL,
    'task_name' => NULL,
    'task_status' => NULL,
    'notify_flag' => 0,
    'notifier_status' => NULL,
    'start' => NULL,
    'finish' => NULL,
    'duration' => NULL,
  ),
  'fieldMeta' => 
  array (
    'task_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
      'index' => 'index',
    ),
    'task_type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '12',
      'phptype' => 'string',
      'null' => true,
    ),
    'task_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'task_status' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
    ),
    'notify_flag' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'notifier_status' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
    ),
    'start' => 
    array (
      'dbtype' => 'char',
      'precision' => '19',
      'phptype' => 'string',
      'null' => true,
    ),
    'finish' => 
    array (
      'dbtype' => 'char',
      'precision' => '19',
      'phptype' => 'string',
      'null' => true,
    ),
    'duration' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '12',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'task_id' => 
    array (
      'alias' => 'task_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'task_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
