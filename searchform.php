<?php

$unique_id = wp_unique_id('search-form-');
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo esc_attr($unique_id); ?>" class="visually-hidden">
        <?php esc_html_e('Search for:', 'wheellab'); ?>
    </label>
    <input
        type="search"
        id="<?php echo esc_attr($unique_id); ?>"
        class="search-field"
        placeholder="<?php esc_attr_e('Search…', 'wheellab'); ?>"
        value="<?php echo get_search_query(); ?>"
        name="s"
    >
    <button type="submit" class="search-submit">
        <?php esc_html_e('Search', 'wheellab'); ?>
    </button>
</form>
