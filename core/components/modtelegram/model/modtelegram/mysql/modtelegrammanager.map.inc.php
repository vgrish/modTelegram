<?php
$xpdo_meta_map['modTelegramManager']= array (
  'package' => 'modtelegram',
  'version' => '1.1',
  'table' => 'modtelegram_managers',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'telegram_id' => '',
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'telegram_id' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'telegram_id' => 
    array (
      'alias' => 'telegram_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'telegram_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Chats' => 
    array (
      'class' => 'modTelegramChat',
      'local' => 'id',
      'foreign' => 'manager_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
