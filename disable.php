<?php
$PDO = Record::getConnection();

$table = TABLE_PREFIX . "page_part_forms";
$PDO->exec("DROP TABLE $table");
?>