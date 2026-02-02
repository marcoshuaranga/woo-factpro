<?php

defined('ABSPATH') || exit;

?>

<div class="button-group">
    <?php foreach ($actions as $action): ?>
        <a class="button <?php echo esc_attr($action['action']); ?>"
           href="<?php echo esc_url($action['url']); ?>"
           aria-label="<?php echo esc_attr($action['name']); ?>"
           title="<?php echo esc_attr($action['name']); ?>"
        >
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> <?php echo esc_html($action['name']); ?>
        </a>
    <?php endforeach; ?>
</div>
