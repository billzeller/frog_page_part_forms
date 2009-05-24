<h1><?php echo __('Page Part Forms'); ?></h1>

<ul class="index">
<?php foreach($page_part_forms as $page_part_form) { ?>
  <li id="<?php echo $css_id_prefix . $page_part_form->id; ?>" class="page_part_form node <?php echo odd_even(); ?>">
    <img align="middle" alt="<? echo __('Page part form'); ?>" src="../../frog/plugins/<?php echo $plugin_id; ?>/images/page_part_form.png" title="" />
    
    <a href="<?php echo $plugin_url.'edit/'.$page_part_form->id; ?>"><?php echo $page_part_form->name; ?></a>
    
    <img class="handle" src="../images/drag.gif" alt="<?php echo __('Drag and Drop'); ?>" align="middle" />
    
    <div class="remove"><a href="<?php echo $plugin_url.'delete/'.$page_part_form->id; ?>" onclick="return confirm('<?php echo __('Are you sure you wish to delete'); ?> <?php echo $page_part_form->name; ?>?');"><img alt="<?php echo ('Remove page part form'); ?>" src="images/icon-remove.gif" /></a></div>
  </li>
<?php } ?>
</ul>