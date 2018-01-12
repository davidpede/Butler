<?php
/**
 * @package butler
 */
$xpdo_meta_map['ButlerLog']= array (
  'package' => 'butler',
  'version' => '1.1',
  'table' => 'butler_log',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'task_id' => NULL,
    'run_id' => NULL,
    'source' => 'TASK',
    'type' => 'INFO',
    'msg' => NULL,
    'stamp' => NULL,
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
    'run_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
      'index' => 'index',
    ),
    'source' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'default' => 'TASK',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'default' => 'INFO',
    ),
    'msg' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'stamp' => 
    array (
      'dbtype' => 'char',
      'precision' => '19',
      'phptype' => 'string',
      'null' => true,
      'index' => 'index',
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
    'stamp' => 
    array (
      'alias' => 'stamp',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'stamp' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'run_id' => 
    array (
      'alias' => 'run_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'run_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
