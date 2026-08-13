<?php
/**
 * Block: Table Section
 * Registered as: acf/table-section
 * Source: WheelLab Website (Figma) — node 527:28785 ("Table example").
 *
 * Architecture (see acf-json/group_table_section.json for the full
 * reasoning): two repeaters — `columns` (just a header label per
 * column) and `rows`, where each row has its own `cells` repeater (one
 * WYSIWYG cell per column, same order). A nested repeater is the only
 * way to keep both dimensions independently admin-editable — number of
 * columns AND number of rows — with plain ACF fields.
 *
 * That nesting only works smoothly if a mismatched cell count (an
 * admin adding an extra cell, or forgetting one) can never break the
 * table: rendering below is index-based and defensive — a row with
 * fewer cells than columns just prints empty <td>s for the rest, and
 * any cells beyond the column count are ignored, instead of shifting
 * every cell after the gap sideways into the wrong column.
 *
 * Cell content is WYSIWYG (not plain text) because the source design
 * mixes plain values ("200K"), comma lists ("Kyiv, Kharkiv, Lviv…"),
 * and inline links to different URLs within the same cell ("Augmented
 * Pixels, Petcube, Grammarly…") — one rich-text field covers all three
 * without extra field-type branching per column.
 *
 * Assets: build/css/sections/table_section.min.css
 */

$columns = get_field('columns') ?: [];
$rows    = get_field('rows') ?: [];

if (!$columns || !$rows) {
    return;
}

$class  = 'table-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>
<div class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="table-section__scroll">
        <table class="table-section__table">
            <thead>
                <tr>
                    <?php foreach ($columns as $column) : ?>
                        <th class="table-section__head" scope="col"><?php echo esc_html($column['label']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row) :
                    $cells = $row['cells'] ?: [];
                    ?>
                    <tr class="table-section__row">
                        <?php foreach ($columns as $i => $column) :
                            $content = $cells[$i]['content'] ?? '';
                            ?>
                            <td class="table-section__cell"><?php echo wp_kses_post($content); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
