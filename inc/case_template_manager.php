<?php

defined('ABSPATH') || exit;

add_action('init', function () {
    register_post_type('case_template', [
        'labels' => [
            'name'          => __('Case Templates', 'wheellab'),
            'singular_name' => __('Case Template', 'wheellab'),
            'edit_item'     => __('Edit Case Block Template', 'wheellab'),
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => false,
        'show_in_rest'  => true,
        'supports'      => ['title', 'editor', 'custom-fields'],
        'template_lock' => false,
    ]);
});

add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=case_study',
        __('Block Template Settings', 'wheellab'),
        __('Block Template', 'wheellab'),
        'manage_options',
        'case-block-template',
        'wheellab_render_case_template_settings_page'
    );
});

function wheellab_render_case_template_settings_page(): void {
    $template_post_id = wheellab_get_or_create_case_template_post();
    $edit_link        = get_edit_post_link($template_post_id, 'raw');

    if (
        isset($_POST['wheellab_case_template_nonce']) &&
        wp_verify_nonce($_POST['wheellab_case_template_nonce'], 'wheellab_case_template_save')
    ) {
        if (isset($_POST['action_reset'])) {
            wp_update_post([
                'ID'           => $template_post_id,
                'post_content' => wheellab_get_case_default_template_content(),
            ]);
            echo '<div class="notice notice-success"><p>' . esc_html__('Template reset to default.', 'wheellab') . '</p></div>';
        } elseif (isset($_POST['action_backfill'])) {
            $updated = wheellab_backfill_empty_case_posts();
            echo '<div class="notice notice-success"><p>' . sprintf(
                esc_html__('Done. %d case post(s) updated with the block template.', 'wheellab'),
                $updated
            ) . '</p></div>';
        } elseif (isset($_POST['action_fix_edit_mode'])) {
            $updated = wheellab_fix_edit_mode_on_case_posts();
            echo '<div class="notice notice-success"><p>' . sprintf(
                esc_html__('Done. Edit mode fixed on %d case post(s).', 'wheellab'),
                $updated
            ) . '</p></div>';
        }
    }

    $empty_count    = wheellab_count_empty_case_posts();
    $fix_mode_count = wheellab_count_case_posts_needing_edit_mode();
    ?>

    <div class="wrap">
        <h1><?php esc_html_e('Case Studies — Block Template Settings', 'wheellab'); ?></h1>

        <div class="card" style="max-width: 720px; margin-top: 20px;">
            <h2><?php esc_html_e('Default Block Template', 'wheellab'); ?></h2>
            <p class="description">
                <?php esc_html_e('Define which Gutenberg blocks are pre-loaded when creating a new Case Study post. Edit the template in the block editor.', 'wheellab'); ?>
            </p>

            <table class="form-table" role="presentation" style="margin-top: 16px;">
                <tr>
                    <th scope="row"><?php esc_html_e('Edit template', 'wheellab'); ?></th>
                    <td>
                        <a href="<?php echo esc_url($edit_link); ?>" class="button button-primary" target="_blank">
                            <?php esc_html_e('Open Template in Block Editor →', 'wheellab'); ?>
                        </a>
                        <p class="description" style="margin-top: 8px;">
                            <?php esc_html_e('Add, remove, or reorder blocks. Changes apply to all NEW case posts only.', 'wheellab'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Reset template', 'wheellab'); ?></th>
                    <td>
                        <form method="post">
                            <?php wp_nonce_field('wheellab_case_template_save', 'wheellab_case_template_nonce'); ?>
                            <button type="submit" name="action_reset" value="1" class="button button-secondary"
                                    onclick="return confirm('<?php esc_attr_e('Reset template to default? This cannot be undone.', 'wheellab'); ?>')">
                                <?php esc_html_e('Reset to Default', 'wheellab'); ?>
                            </button>
                            <p class="description" style="margin-top: 8px;">
                                <?php esc_html_e('Restore the built-in block order in the template editor.', 'wheellab'); ?>
                            </p>
                        </form>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($empty_count > 0) : ?>
        <div class="card" style="max-width: 720px; margin-top: 20px;">
            <h2><?php esc_html_e('Apply Template to Existing Posts', 'wheellab'); ?></h2>
            <p>
                <?php printf(
                    esc_html__('%d case post(s) have no content yet and will receive the block template.', 'wheellab'),
                    $empty_count
                ); ?>
                <?php esc_html_e('Posts that already have content will not be touched.', 'wheellab'); ?>
            </p>
            <form method="post">
                <?php wp_nonce_field('wheellab_case_template_save', 'wheellab_case_template_nonce'); ?>
                <button type="submit" name="action_backfill" value="1" class="button button-primary"
                        onclick="return confirm('<?php esc_attr_e('Apply template to all empty case posts? This cannot be undone.', 'wheellab'); ?>')">
                    <?php printf(esc_html__('Apply Template to %d Post(s)', 'wheellab'), $empty_count); ?>
                </button>
            </form>
        </div>
        <?php else : ?>
        <div class="card" style="max-width: 720px; margin-top: 20px;">
            <p style="margin: 0;">✅ <?php esc_html_e('All existing case posts already have content. Nothing to migrate.', 'wheellab'); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($fix_mode_count > 0) : ?>
        <div class="card" style="max-width: 720px; margin-top: 20px;">
            <h2><?php esc_html_e('Fix Block Edit Mode', 'wheellab'); ?></h2>
            <p>
                <?php printf(
                    esc_html__('%d case post(s) have blocks displayed in Preview mode instead of Edit mode.', 'wheellab'),
                    $fix_mode_count
                ); ?>
            </p>
            <form method="post">
                <?php wp_nonce_field('wheellab_case_template_save', 'wheellab_case_template_nonce'); ?>
                <button type="submit" name="action_fix_edit_mode" value="1" class="button button-primary">
                    <?php printf(esc_html__('Fix Edit Mode on %d Post(s)', 'wheellab'), $fix_mode_count); ?>
                </button>
            </form>
        </div>
        <?php endif; ?>

        <div class="card" style="max-width: 720px; margin-top: 20px; background: #fff8e1; border-left: 4px solid #f0b429;">
            <h3 style="margin-top: 0;"><?php esc_html_e('How it works', 'wheellab'); ?></h3>
            <ul style="list-style: disc; padding-left: 20px;">
                <li><?php esc_html_e('Click "Open Template in Block Editor" and arrange the blocks you want.', 'wheellab'); ?></li>
                <li><?php esc_html_e('All NEW case posts will start with these blocks pre-loaded.', 'wheellab'); ?></li>
                <li><?php esc_html_e('Posts that already have content are never touched automatically.', 'wheellab'); ?></li>
                <li><?php esc_html_e('Use "Reset to Default" to restore the original block order in the editor.', 'wheellab'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

function wheellab_get_or_create_case_template_post(): int {
    $post_id = (int) get_option('wheellab_case_template_post_id', 0);

    if ($post_id && get_post_status($post_id) !== false) {
        return $post_id;
    }

    $post_id = wp_insert_post([
        'post_title'   => __('Case — Default Block Template', 'wheellab'),
        'post_type'    => 'case_template',
        'post_status'  => 'publish',
        'post_content' => wheellab_get_case_default_template_content(),
    ]);

    update_option('wheellab_case_template_post_id', $post_id);

    return $post_id;
}

function wheellab_get_case_default_template_content(): string {
    return <<<'BLOCKS'
<!-- wp:acf/case-study-hero {"mode":"edit"} /-->
<!-- wp:acf/case-study-about-section {"mode":"edit"} /-->
<!-- wp:acf/case-study-quote-section {"mode":"edit"} /-->
<!-- wp:acf/case-study-showcase-section {"mode":"edit"} /-->
<!-- wp:acf/cta-banner-section {"mode":"edit"} /-->
<!-- wp:acf/case-study-tabs-section {"mode":"edit"} /-->
<!-- wp:acf/case-study-screens-section {"mode":"edit"} /-->
<!-- wp:acf/case-study-what-we-did-section {"mode":"edit"} /-->
<!-- wp:acf/stats-showcase-section {"mode":"edit"} /-->
<!-- wp:acf/case-study-section {"mode":"edit"} /-->
<!-- wp:acf/contact-section {"mode":"edit"} /-->
BLOCKS;
}

function wheellab_case_blocks_to_template_array(string $content): array {
    $parsed   = parse_blocks($content);
    $template = [];

    foreach ($parsed as $block) {
        if (empty($block['blockName'])) {
            continue;
        }

        $entry = [$block['blockName'], array_merge($block['attrs'] ?? [], ['mode' => 'edit'])];

        if (!empty($block['innerBlocks'])) {
            $inner = [];
            foreach ($block['innerBlocks'] as $ib) {
                if (!empty($ib['blockName'])) {
                    $inner[] = [$ib['blockName'], $ib['attrs'] ?? []];
                }
            }
            $entry[] = $inner;
        }

        $template[] = $entry;
    }

    return $template;
}

add_filter('register_post_type_args', function (array $args, string $post_type): array {
    if ($post_type !== 'case_study') {
        return $args;
    }

    $content = wheellab_get_case_default_template_content();
    $tid     = (int) get_option('wheellab_case_template_post_id', 0);

    if ($tid) {
        $post = get_post($tid);
        if ($post && !empty($post->post_content)) {
            $content = $post->post_content;
        }
    }

    $args['template']      = wheellab_case_blocks_to_template_array($content);
    $args['template_lock'] = false;

    return $args;
}, 20, 2);

add_filter('allowed_block_types_all', function ($allowed, $ctx) {
    if (isset($ctx->post) && $ctx->post->post_type === 'case_template') {
        return true;
    }
    return $allowed;
}, 10, 2);

add_filter('wp_insert_post_data', function (array $data): array {
    if ($data['post_type'] === 'case_template' && !empty($data['post_content'])) {
        $data['post_content'] = wheellab_inject_edit_mode($data['post_content']);
    }
    return $data;
}, 10, 1);

add_action('admin_init', function (): void {
    $tid = (int) get_option('wheellab_case_template_post_id', 0);
    if (!$tid) return;

    $post = get_post($tid);
    if (!$post || empty($post->post_content)) return;

    if (strpos($post->post_content, '"mode":"edit"') === false) {
        wp_update_post([
            'ID'           => $tid,
            'post_content' => wheellab_inject_edit_mode($post->post_content),
        ]);
    }
});

add_filter('user_has_cap', function (array $allcaps, array $caps, array $args): array {
    if (
        isset($args[0]) &&
        in_array($args[0], ['edit_post', 'delete_post'], true)
    ) {
        $post_id = $args[2] ?? 0;
        if ($post_id && get_post_type($post_id) === 'case_template') {
            if (empty($allcaps['manage_options'])) {
                $allcaps[$caps[0]] = false;
            }
        }
    }
    return $allcaps;
}, 10, 3);

function wheellab_inject_edit_mode(string $content): string {
    $blocks = parse_blocks($content);

    $add_edit_mode = static function (array $blocks) use (&$add_edit_mode): array {
        foreach ($blocks as &$block) {
            if (!empty($block['blockName']) && strpos($block['blockName'], 'acf/') === 0) {
                $block['attrs']['mode'] = 'edit';
            }
            if (!empty($block['innerBlocks'])) {
                $block['innerBlocks'] = $add_edit_mode($block['innerBlocks']);
            }
        }
        return $blocks;
    };

    return serialize_blocks($add_edit_mode($blocks));
}

function wheellab_get_empty_case_post_ids(): array {
    $posts = get_posts([
        'post_type'      => 'case_study',
        'post_status'    => ['publish', 'draft', 'pending', 'future'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    return array_filter($posts, function (int $id): bool {
        $post = get_post($id);
        return $post && trim($post->post_content) === '';
    });
}

function wheellab_count_empty_case_posts(): int {
    return count(wheellab_get_empty_case_post_ids());
}

function wheellab_count_case_posts_needing_edit_mode(): int {
    $posts = get_posts([
        'post_type'      => 'case_study',
        'post_status'    => ['publish', 'draft', 'pending', 'future'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    $count = 0;
    foreach ($posts as $id) {
        $post = get_post($id);
        if (
            $post &&
            strpos($post->post_content, 'wp:acf/') !== false &&
            strpos($post->post_content, '"mode":"edit"') === false
        ) {
            $count++;
        }
    }

    return $count;
}

function wheellab_fix_edit_mode_on_case_posts(): int {
    $posts = get_posts([
        'post_type'      => 'case_study',
        'post_status'    => ['publish', 'draft', 'pending', 'future'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    $updated = 0;
    foreach ($posts as $id) {
        $post = get_post($id);
        if (
            !$post ||
            strpos($post->post_content, 'wp:acf/') === false ||
            strpos($post->post_content, '"mode":"edit"') !== false
        ) {
            continue;
        }

        wp_update_post([
            'ID'           => $id,
            'post_content' => wheellab_inject_edit_mode($post->post_content),
        ]);
        $updated++;
    }

    return $updated;
}

function wheellab_backfill_empty_case_posts(): int {
    $content = wheellab_get_case_default_template_content();
    $tid     = (int) get_option('wheellab_case_template_post_id', 0);

    if ($tid) {
        $tpost = get_post($tid);
        if ($tpost && !empty($tpost->post_content)) {
            $content = $tpost->post_content;
        }
    }

    $content = wheellab_inject_edit_mode($content);

    $updated = 0;
    foreach (wheellab_get_empty_case_post_ids() as $id) {
        wp_update_post([
            'ID'           => $id,
            'post_content' => $content,
        ]);
        $updated++;
    }

    return $updated;
}
