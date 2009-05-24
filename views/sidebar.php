<p class="button" id="<?php echo $css_id_prefix; ?>buttons">
<a href="<?php echo $plugin_url; ?>add/" id="<?php echo $css_id_prefix; ?>add">
  <img src="../../frog/plugins/page_part_forms/images/new_page_part_form.png" align="middle" alt="<?php echo __("New Page Part Form"); ?>" /><?php echo __("New Page Part Form"); ?></a>
</p>

<div class="box">
<h2><?php echo __('About page part form'); ?></h2>

<p>
<?php echo __('Page part forms fills the gap between the generic structure and a custom interface. A form can be created that is shown instead of the frog build in tab view. The form does not only contain basic text field, but allows e.g. selections and date fields. This enables frog to edit simple composite content parts.'); ?>
</p>
<p>
<?php echo __('Right now, a <a href="http://www.yaml.org/">YAML</a> syntax is used to define the form. A basic example for the syntax:'); ?>
</p>
<pre>
<?php echo __('body:
  title: "The review text"
  type: page_part
rating:
  title: "My Rating"
  type: select
  limt: 1
  values:
    - good
    - bad'); ?>
</pre>
<p>
<?php echo __('See the <a href="http://wiki.github.com/them/frog_page_part_forms">wiki</a> for more examples.'); ?>
</p>

</div>