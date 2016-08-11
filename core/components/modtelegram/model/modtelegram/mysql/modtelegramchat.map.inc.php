<?php
$xpdo_meta_map['modTelegramChat']= array (
  'package' => 'modtelegram',
  'version' => '1.1',
  'table' => 'modtelegram_chats',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'uid' => NULL,
    'mid' => NULL,
    'active' => 0,
  ),
  'fieldMeta' => 
  array (
    'uid' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'mid' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'chat' => 
    array (
      'alias' => 'chat',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'uid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'mid' => 
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
    'User' => 
    array (
      'class' => 'modTelegramUser',
      'local' => 'uid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Manager' => 
    array (
      'class' => 'modTelegramManager',
      'local' => 'mid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
