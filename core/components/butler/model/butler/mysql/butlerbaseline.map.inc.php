<?php
/**
 * @package butler
 */
$xpdo_meta_map['ButlerBaseline']= array (
  'package' => 'butler',
  'version' => '1.1',
  'table' => 'butler_baseline',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'file_name' => NULL,
    'file_path' => NULL,
    'file_hash' => NULL,
    'file_last_mod' => NULL,
    'task_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'file_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'file_path' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'index' => 'index',
    ),
    'file_hash' => 
    array (
      'dbtype' => 'char',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
    ),
    'file_last_mod' => 
    array (
      'dbtype' => 'char',
      'precision' => '19',
      'phptype' => 'string',
      'null' => true,
      'index' => 'index',
    ),
    'task_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
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
    'file_path' => 
    array (
      'alias' => 'file_path',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'file_path' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'file_last_mod' => 
    array (
      'alias' => 'file_last_mod',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'file_last_mod' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
