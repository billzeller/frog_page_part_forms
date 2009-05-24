<ul id="<?php echo $css_id_prefix; ?>structure">
<?php 
$has_body = false;
foreach ($structure as $element) : ?>
  <?php
    $class = "";
    $type = "Page part";
    switch($element[PagePartFormsController::PROPERTY_TYPE]) {
      case PagePartFormsController::TYPE_PAGE_PART:
        $class = $css_class_prefix.'type-page-part';
        $type = "Page part";
        break;
      case PagePartFormsController::TYPE_TEXT:
        $class = $css_class_prefix.'type-text';
        $type = "Text";
        break;
      case PagePartFormsController::TYPE_SELECT:
        $class = $css_class_prefix.'type-select';
        $type = "Select";
        break; 
      case PagePartFormsController::TYPE_DATE:
        $class = $css_class_prefix.'type-date';
        $type = "Date";
        break; 
    }
    
    if ($element[PagePartFormsController::PROPERTY_NAME] == 'body') {
      $has_body = true;
    }
  ?>
  <li class="<?php echo $class; ?>"><?php echo $element[PagePartFormsController::PROPERTY_TITLE]; ?> (<?php echo __('Type: '.$type); ?><?php echo isset($element[PagePartFormsController::PROPERTY_LIMIT]) ? ", ".__('Limit').": ".$element[PagePartFormsController::PROPERTY_LIMIT] : ""; ?>)
    <?php
      if ($element[PagePartFormsController::PROPERTY_TYPE] == PagePartFormsController::TYPE_SELECT) {
        echo '<ul class="'.$css_class_prefix.'type-select-items">'.PHP_EOL;
        foreach ($element[PagePartFormsController::PROPERTY_VALUES] as $value) {
          echo '<li>'.$value.'</li>'.PHP_EOL;
        }
        echo '</ul>'.PHP_EOL;
      }
    ?>
  </li>
<?php endforeach; ?>
</li>
<?php
if (!$has_body) {
 echo '<div id="'.$css_id_prefix.'nomain-note">'.__("Your page part definition does not contain an entry with the name 'body'.").'</div>';
}
?>