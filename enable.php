<?php
// Included?
if (!defined('IN_FROG')) { exit(); }

AutoLoader::addFolder(dirname(__FILE__) . '/models');

$table_name = TABLE_PREFIX.PagePartForm::TABLE_NAME;

// Connection
$pdo = Record::getConnection();
$driver = strtolower($pdo->getAttribute(Record::ATTR_DRIVER_NAME));

if ($driver == 'mysql') {
  // Create table
  $pdo->exec("CREATE TABLE $table_name (
    id          int(11) unsigned NOT NULL AUTO_INCREMENT,
    name        varchar(255) NOT NULL,
    definition  TEXT,
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
}
else if ($driver == 'sqlite') {
  // Create table
  $pdo->exec("CREATE TABLE $table_name (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        VARCHAR(255) NOT NULL,
    definition  TEXT
  )");
}
?>