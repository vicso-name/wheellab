<?php

$unique_id  = wp_unique_id('site-search-');
$results_id = $unique_id . '-results';
$status_id  = $unique_id . '-status';
?>
<form
    role="search"
    method="get"
    class="search-form site-search-form"
    action="<?php echo esc_url(home_url('/')); ?>"
    data-live-search
>
    <label for="<?php echo esc_attr($unique_id); ?>" class="visually-hidden">
        <?php esc_html_e('Search the site:', 'wheellab'); ?>
    </label>

    <div class="site-search-form__controls">
        <input
            type="search"
            id="<?php echo esc_attr($unique_id); ?>"
            class="search-field site-search-form__input"
            placeholder="<?php esc_attr_e('Search services, case studies, insights…', 'wheellab'); ?>"
            value="<?php echo esc_attr(get_search_query()); ?>"
            name="s"
            autocomplete="off"
            aria-controls="<?php echo esc_attr($results_id); ?>"
            aria-describedby="<?php echo esc_attr($status_id); ?>"
        >
        <button type="submit" class="search-submit site-search-form__submit">
            <?php esc_html_e('Search', 'wheellab'); ?>
        </button>
    </div>

    <div
        class="site-search-form__status body-s"
        id="<?php echo esc_attr($status_id); ?>"
        role="status"
        aria-live="polite"
        data-search-status
        hidden
    ></div>

    <div
        class="site-search-form__results"
        id="<?php echo esc_attr($results_id); ?>"
        data-search-results
        hidden
    >
        <div class="site-search-form__items" data-search-items></div>
        <a class="site-search-form__all button-text-m" href="#" data-search-all hidden></a>
    </div>
</form>
