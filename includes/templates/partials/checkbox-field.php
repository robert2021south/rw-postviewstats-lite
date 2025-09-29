<?php
/** @var string $option  */
/** @var string $value  */
/** @var string $desc */
?>
<label>
    <input type="hidden" name="<?php echo esc_attr($option); ?>" value="0" />
    <input type="checkbox" name="<?php echo esc_attr($option); ?>" value="1" <?php checked($value,'1'); ?>/>
</label>
<p class="description">
    <?php echo esc_html($desc); ?>
</p>