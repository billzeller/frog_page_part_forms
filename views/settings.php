<h1><?php echo __('Page Part Forms settings'); ?></h1>
<h2><?php echo __('Delete Page Part Forms table'); ?></h2>

<a href="#" id="<?php echo $css_id_prefix; ?>delete-table-link"><?php echo __("Delete Page Part Forms table"); ?></a>

<div id="<?php echo $css_id_prefix; ?>delete-table" style="display: none;">
  <?php echo __('Once you delete your table, there is no going back. Please be certain.'); ?>
  <form id="<?php echo $css_id_prefix; ?>delete-table-form" action="<?echo $plugin_url;?>cleanup" method="post">
    <input type="submit" id="cleanup" value="<?php echo __("Delete Page Part Forms table"); ?>"/>
  </form>
</div>
<script type="text/javascript">
//<![CDATA[
(function($) {
  // Document load
  $(function() {
    $('#<?php echo $css_id_prefix; ?>delete-table-link').click(function() {
      // Show confirmation
      $('#<?php echo $css_id_prefix; ?>delete-table').css('display', 'block');
      return false;
    });
  });
})(jQuery);
//]]>
</script>