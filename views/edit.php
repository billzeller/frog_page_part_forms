<h1><?php echo __('Edit page part form'); ?></h1>

<form action="<?php echo $plugin_url.$action.'/'.$page_part_form->id; ?>" method="post">
<div class="form-area" id="<?php echo $css_id_prefix;?>form-area">
  <div class="title">
    <label for="<?php echo $css_id_prefix; ?>title"><?php echo __('Name'); ?></label>
    <input class="textbox" id="<?php echo $css_id_prefix; ?>title" maxlength="100" name="<?php echo $plugin_id; ?>[name]" size="255" type="text" value="<?php echo $page_part_form->name; ?>" />
  </div>
  
  <div class="content">
    <div id="<?php echo $css_id_prefix; ?>columns-2">
      <div class="<?php echo $css_class_prefix; ?>column">
        <label for="<?php echo $css_id_prefix; ?>definition"><?php echo __('Page part definiton'); ?></label>
        <textarea id="<?php echo $css_id_prefix; ?>definition" name="<?php echo $plugin_id; ?>[definition]"><?php echo $page_part_form->definition; ?></textarea>
      </div>
      
      <?php if (isset($outline_structure)) : ?>
      <div class="<?php echo $css_class_prefix; ?>column">
        <label for="<?php echo $css_id_prefix; ?>structure-outline"><?php echo __('Page part definiton'); ?></label>
        <div id="<?php echo $css_id_prefix; ?>structure-outline"><?php echo $outline_structure; ?></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<p class="buttons">
  <input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save'); ?>" />
  <input class="button" name="continue" type="submit" accesskey="e" value="<?php echo __('Save and Continue Editing'); ?>" />
  <?php echo __('or'); ?> <a href="<?php echo $plugin_url; ?>"><?php echo __('Cancel'); ?></a>
</p>
</form>