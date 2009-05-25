<?php
 // Javascript is used to remove the existing page_parts interface provided by the frog core.
 $replace_with_id = $css_id_prefix . "Form";
?>
<script type="text/javascript">
//<![CDATA[
var multiple_content = new Array();

(function($) {
  // Document load
  $(function() {
    // Replace the existing page_part editors with the page_part_forms editors.
    $('#tab-control').replaceWith(
      $('#<?php echo $replace_with_id; ?>').remove() // jQuery copies the nodes, so delete the origin
    );
    
    // Add a handler to the form submission to prepare the page_parts (e.g. multiple values and limit check)
    $form = $('form:first'); // XXX: this is fragile!
    
    $form.submit(function() {
      submit_form = true;
      
      // Walk through all definitions
      $.each(multiple_content, function(index, item) {
        // Collect the selected values from checkboxes or lists that have multiple contents
        values = new Array();
        
        $('input[name="<?php echo $plugin_id; ?>_values_for_' + item["name"] + '"]:checked', $form).each(function() {
          values.push( $(this).val() );
        });

        $('select[name="<?php echo $plugin_id; ?>_values_for_' + item["name"] + '"] option:selected', $form).each(function() {
          values.push( $(this).val() );
        });

        // Check if the limit is reached
        if (values.length <= item["limit"]) {
          // Encode the values as JSON string and set it as content
          $('textarea[name="' + item["content"] + '"]', $form).val( $.json.encode(values) );
        }
        else {
          // I18n in javascript by replacing placeholders
          alert(
            '<?php echo __('Too many options are selected for "##TITLE##". Currently there are "##SELECTED##" selected, but only "##LIMIT##" are allowed.'); ?>'
              .replace("##TITLE##", item["title"])
              .replace("##SELECTED##", values.length)
              .replace("##LIMIT##", item["limit"])
          );
          
          // Do not submit the form
          submit_form = false;
          // This stops only the current loop, not the submission of the form
          return false;
        }
      });

      return submit_form;
    });
  });
})(jQuery);
//]]>
</script>

<div id="<?php echo $replace_with_id; ?>">
<?php
// Number all page parts
$index = 0;
// This page parts have multiple values/options
$multiple_options_elements = array();

// Process all structural elements from page_part_forms defnition and create the page_part_form.
foreach ($structure as $name => $element) {
  // Data from this page part
  $structure_name_css_id = page_part_forms_name_to_id($name);
  $page_part_id          = array_key_exists($name, $page_parts) ? $page_parts[$name]->id : null;
  $content               = array_key_exists($name, $page_parts) ? $page_parts[$name]->content : '';
  // Common fields from the structure
  $limit                 = array_key_exists(PagePartFormsController::PROPERTY_LIMIT, $element) ? $element[PagePartFormsController::PROPERTY_LIMIT] : 1;
  $title                 = $element[PagePartFormsController::PROPERTY_TITLE];
  // Name of the field
  $field_name_prefix     = 'part['.$index.']';
  $field_name_content    = $field_name_prefix.'[content]';
  
?>
  <div class="<?php echo $css_class_prefix; ?>row">
    <input id="part_<?php echo $index; ?>_name" name="<?php echo $field_name_prefix; ?>[name]" type="hidden" value="<?php echo $name; ?>" />
<?php if ($page_part_id): ?>
    <input id="part_<?php echo $index; ?>_id" name="<?php echo $field_name_prefix; ?>[id]" type="hidden" value="<?php echo $page_part_id; ?>" />
<?php endif; ?>
    <label class="<?php echo $css_class_prefix;?>label" for="<?php echo $structure_name_css_id; ?>"><?php echo $title; ?></label>

    <div class="<?php echo $css_class_prefix; ?>input">
<?php
switch($element[PagePartFormsController::PROPERTY_TYPE]) {
  case PagePartFormsController::TYPE_PAGE_PART:
    $filter_id = array_key_exists($name, $page_parts) ? $page_parts[$name]->filter_id : null;

    echo '<div class="'.($css_class_prefix.'part-page-part').'">'.PHP_EOL;
    
    // Take care of page filters for page parts
    echo '<p>';
    echo '<label for="part_'.$index.'_filter_id">'.__('Filter').'</label>'.PHP_EOL;
    echo '<select id="part_'.$index.'_filter_id" name="'.$field_name_prefix.'[filter_id]" onchange="setTextAreaToolbar(\''.$structure_name_css_id.'\', this[this.selectedIndex].value)">
           <option value=""'.(empty($filter_id) ? ' selected="selected"' : '').'>&#8212; '.__('none').' &#8212;</option>';
    foreach (Filter::findAll() as $filter) {
      echo '<option value="'.$filter.'"'. ($filter_id == $filter ? ' selected="selected"' : '') .'>';
      echo Inflector::humanize($filter);
      echo '</option>';
    }
    echo '</select></p>'.PHP_EOL;

    // Show the page part
    echo '<textarea class="textarea" id="'.$structure_name_css_id.'" name="'.$field_name_content.'" style="width: 100%" rows="20" cols="40"';
    echo ' onkeydown="return allowTab(event, this);" onkeyup="return allowTab(event,this);" onkeypress="return allowTab(event,this);">';
    echo htmlentities($content, ENT_COMPAT, 'UTF-8');
    echo '</textarea>';

    // Apply the filter
    echo '<script type="text/javascript">';
    echo 'setTextAreaToolbar(\''.$structure_name_css_id.'\', \''.$filter_id.'\');';
    echo '</script>'.PHP_EOL;

    echo '</div>'.PHP_EOL;
  break;
  case PagePartFormsController::TYPE_TEXT:
    echo '<input type="text" id="'.$structure_name_css_id.'" class="'.$css_class_prefix.'part-text" name="'.$field_name_content.'" value="'.$content.'" maxlength="'.$limit.'" />';
    break;
  case PagePartFormsController::TYPE_SELECT:
    echo '<div class="'.$css_class_prefix.'select">'.PHP_EOL;
  
    // Get type based on limit and number of choices
    $expand      = false;
    $select_many = false;
        
    switch(PagePartFormsController::Get_select_type($element)) {
      case PagePartFormsController::SELECT_TYPE_LIST_MORE:
        // Same as list one but with multiple="multiple"
        $expand      = true;
        $select_many = true;
      case PagePartFormsController::SELECT_TYPE_LIST_ONE:
        // Same as dropdown but with size
        $expand      = true;
      case PagePartFormsController::SELECT_TYPE_DROPDOWN:
        $values = $structure[$name][PagePartFormsController::PROPERTY_VALUES];
        $multiple_values = PagePartFormsController::Get_multiple_values($content);

        // NOTE: Frog can only handle simple strings as values instead of arrays. Use javascript to create a string out of the selected items.
        if ($select_many) {
          echo '<textarea style="display: none;" name="'.$field_name_content.'" rows="1" cols="1">'.$content.'</textarea>';
        }

        // Select widget
        echo '<select id="'.$structure_name_css_id.'"';
  
        // Show all values at once
        if ($expand) {
          echo ' size="'.count($values).'"';
        }
      
        // Select multiple
        if ($select_many) {
          echo ' name="'.$plugin_id.'_values_for_'.$name.'"';
          echo ' multiple="multiple"';

          // Add node information for multiple options
          array_push($multiple_options_elements, array(
            'title'    => $title,
            'content'  => $field_name_content,
            'name'     => $name,
            'limit'    => $limit,
          ));
        }
        else {
          echo ' name="'.$field_name_content.'"';
        }
            
        echo '>';
  
        // Add the values
        foreach ($values as $value) {
          echo '<option value="'.$value.'"'.(array_key_exists($value, $multiple_values) ? 'selected="selected"' : '').'>'.$value.'</option>';
        }
  
        echo '</select>';
        break;
      case PagePartFormsController::SELECT_TYPE_RADIO:
        foreach ($structure[$name][PagePartFormsController::PROPERTY_VALUES] as $value) {
          echo '<span class="'.$css_class_prefix.'part-radio">';
          // NOTE: if changed back from many to one for this page_part, no value will be selected.
          echo '<input type="radio" '.($content == $value ? 'checked="checked"' : '').' name="'.$field_name_content.'" value="'.$value.'" />'.$value;
          echo '</span>'.PHP_EOL;
        }
        break;
      case PagePartFormsController::SELECT_TYPE_CHECKBOX:
        echo '<textarea style="display: none;" name="'.$field_name_content.'" rows="1" cols="1">'.$content.'</textarea>';
          
        $multiple_values = PagePartFormsController::Get_multiple_values($content);
          
        foreach ($structure[$name][PagePartFormsController::PROPERTY_VALUES] as $value) {
          echo '<span class="'.$css_class_prefix.'part-checkbox">';
          // NOTE: Frog can only handle simple strings as values instead of arrays. Use javascript to create a string out of the selected items
          echo '<input type="checkbox" '.(array_key_exists($value, $multiple_values) ? 'checked="checked"' : '').' name="'.$plugin_id.'_values_for_'.$name.'" value="'.$value.'" />'.$value;
          echo '</span>'.PHP_EOL;
        }
            
        // Add node information for multiple options
        array_push($multiple_options_elements, array(
          'title'   => $title,
          'content' => $field_name_content,
          'name'    => $name,
          'limit'   => $limit,
        ));
        break;
    }
        
    echo '</div>';
    break; 
  case PagePartFormsController::TYPE_DATE:
    echo '<span class="'.$css_class_prefix.'part-date">';
    echo '<input type="text" name="'.$field_name_content.'" size="10" maxlength="10" value="'.$content.'" />';
    echo '<img onclick="displayDatePicker(\''.$field_name_content.'\');" src="images/icon_cal.gif" alt="'.__('Show Calendar').'" />';
    echo '</span>'.PHP_EOL;
    break; 
}
?>
    </div> <!-- INPUT -->
  </div> <!-- ROW -->
<? $index++; } ?>
</div>

<script type="text/javascript">
<?php
foreach ($multiple_options_elements as $e) {
 echo 'multiple_content.push( {';
 echo implode(", ", array_map("page_part_forms_dump_hash_delegate", array_keys($e), array_values($e)));
 echo '});'.PHP_EOL;
}
?>
</script>