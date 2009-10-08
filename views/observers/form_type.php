<div class="page-metadata-row">
  <label for="<?php echo $css_id_prefix; ?>selection"><?php echo __('Page part form'); ?></label>
  <div class="page-metadata-column">
    <input type="hidden" name="page_metadata[<?php echo $plugin_id; ?>][name]" value="<?php echo $plugin_id; ?>" />
    <input type="hidden" name="page_metadata[<?php echo $plugin_id; ?>][visible]" value="0" />
    <select id="<?php echo $css_id_prefix; ?>selection" name="page_metadata[<?php echo $plugin_id; ?>][value]" class="page-metadata-value">
      <option value="">&#8212; <?php echo __('none'); ?> &#8212;</option>    
    <?php foreach($page_part_forms as $form) {
      echo '<option value="'.$form->id.'"'.($form->id == $selected ? ' selected="selected"' : '').'>'.$form->name.'</option>';
    } ?>
    </select>
  </div>
</div>

<div class="page-metadata-row">
  <label for="<?php echo $css_id_prefix; ?>selection"><?php echo __('Page part form (for children)'); ?></label>
  <div class="page-metadata-column">
    <input type="hidden" name="page_metadata[<?php echo $plugin_id; ?>_children][name]" value="<?php echo $plugin_id; ?>_children" />
    <input type="hidden" name="page_metadata[<?php echo $plugin_id; ?>_children][visible]" value="0" />
    <select id="<?php echo $css_id_prefix; ?>selection" name="page_metadata[<?php echo $plugin_id; ?>_children][value]" class="page-metadata-value">
      <option value="">&#8212; <?php echo __('none'); ?> &#8212;</option>
    <?php foreach($page_part_forms as $form) {
      echo '<option value="'.$form->id.'"'.($form->id == $children_selected ? ' selected="selected"' : '').'>'.$form->name.'</option>';
    } ?>
    </select>
  </div>
</div>

