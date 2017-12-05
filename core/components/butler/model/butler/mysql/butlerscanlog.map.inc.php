<?php
/**
 * @package butler
 */
$xpdo_meta_map['ButlerScanlog']= array (
  'package' => 'butler',
  'version' => '1.1',
  'table' => 'butler_scanlog',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'MyISAM',
  ),
  'fields' => 
  array (
    'status' => NULL,
    'file_name' => NULL,
    'file_path' => NULL,
    'file_hash_new' => NULL,
    'file_hash_org' => NULL,
    'file_last_mod' => NULL,
    'stamp' => NULL,
    'task_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'status' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
    ),
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
    'file_hash_new' => 
    array (
      'dbtype' => 'char',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
    ),
    'file_hash_org' => 
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
    'stamp' => 
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
