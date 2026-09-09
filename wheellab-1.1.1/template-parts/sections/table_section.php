<?php

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
