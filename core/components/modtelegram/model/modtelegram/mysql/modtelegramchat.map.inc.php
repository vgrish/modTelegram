<?php
$xpdo_meta_map['modTelegramChat']= array (
  'package' => 'modtelegram',
  'version' => '1.1',
  'table' => 'modtelegram_chats',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'chat_id' => '',
    'manager_id' => 0,
  ),
  'fieldMeta' => 
  array (
    'chat_id' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'manager_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'chat_id' => 
    array (
      'alias' => 'chat_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'chat_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'manager_id' => 
    array (
      'alias' => 'manager_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'manager_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Manager' => 
    array (
      'class' => 'modTelegramManager',
      'local' => 'manager_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
