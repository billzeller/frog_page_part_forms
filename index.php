<?php
require_once('PagePartFormsController.php');
PagePartFormsController::Init();

function page_part_forms_name_to_id($name) {
  // XXX: There may be more chars to replace
  return PagePartFormsController::CSS_ID_PREFIX."Page-Part-".strtr($name, " _", "--");
}

function page_part_forms_dump_hash_delegate($key, $value) {
 return '"'.$key.'": "'.$value.'"';
}
?>