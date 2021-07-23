<div class="button-group">
    <?php foreach ($actions as $action): ?>
        <a class="button <?=esc_attr($action['action'])?>" 
           href="<?=esc_url($action['url'])?>" 
           aria-label="<?=esc_attr($action['name'])?>" 
           title="<?=esc_attr($action['name'])?>"
        >
            <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> <?=esc_html($action['name'])?>
        </a>
    <?php endforeach; ?>
</div>
