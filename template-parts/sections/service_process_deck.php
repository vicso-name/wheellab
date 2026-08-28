<?php
/**
 * Block: Service Process Deck
 * Registered as: acf/service-process-deck
 * Source: WheelLab Website (Figma) — node 806:11700 ("Frame").
 *
 * A fanned card deck: one active card sits flat/sharp/opaque in front;
 * the rest recede via rotation (-4°/step), translateX (-18%/step — close
 * to Figma's own literal per-card X position, X-ONLY though: the source
 * data's Y offset cascaded the whole stack diagonally downward, which is
 * what actually caused an earlier "fan too open" look, not the X
 * component — X alone reproduces the reference's fully-legible peeking
 * badge without that diagonal drift/gap), blur, and a light opacity fade
 * (-0.08/step, subtler than an earlier pass's -0.16 — that one dimmed
 * the very next card's badge down to an unreadable corner fragment,
 * confirmed against the reference, which shows every peeking card's
 * badge as a whole legible (if progressively softer) shape, not a
 * sliver). Blur only starts from offset 2 (1.5px/step from there) — the
 * card immediately behind active (offset 1) stays fully sharp per the
 * reference, and the overall recession reads as gentler than an earlier
 * pass's flat 2px/step from offset 1. Capped at 4 steps of visible
 * recession (a 5th+ card just renders at that same capped look, fully
 * hidden behind the ones in front of it either way). Rotation is
 * negative in CSS on purpose — Figma's own inspector shows these as
 * positive (16°/12°/8°/4°), but
 * Figma's rotation sign convention is the opposite of CSS's; the actual
 * exported code (confirmed via get_design_context) is `-rotate-16` etc.,
 * i.e. counter-clockwise in CSS terms.
 *
 * Clicking any peeking card brings it to the front; the deck also
 * auto-advances through the steps in order every 3.5s (paused on
 * hover/focus, restarted on manual click) — see service_process_deck.js.
 * Desktop-only interaction: below $small (576px) this drops to a plain
 * stacked list, no fan/rotation/click/autoplay, no sourced mobile frame
 * for this block but matching the same "drop the fancy interaction
 * below 576px" decision already made twice on this page
 * (service_feature_cards' slider, service_comparison_section's drag
 * reveal) per explicit instruction on those.
 *
 * The decorative corner texture (two rotated fragments of the same
 * illustration service_feature_cards uses, cropped down to angular line
 * fragments via a large mask-size — that's what reads as a "circuit
 * board" pattern) reuses assets/img/services/process-deck-texture.png.
 * Figma's own per-card mask-position differs slightly for each of the 5
 * cards (its own auto-layout crop math) — not reproduced 1:1 since
 * that's Figma-instance-specific, not something arbitrary admin-added
 * steps could carry; every card here uses the same fixed crop instead.
 *
 * Numbers (01, 02, …) are derived from each step's position, not a
 * manual field — no reordering-desync risk.
 *
 * Assets: build/css/sections/service_process_deck.min.css
 *         build/js/sections/service_process_deck.min.js
 */

$title = get_field('title') ?: '';
$steps = get_field('steps') ?: [];

$steps = array_values(array_filter($steps, static function ($step) {
    return !empty($step['title']) && !empty($step['description']);
}));

if (!$title || count($steps) < 2) {
    return;
}

$class  = 'service-process-deck';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <h2 class="service-process-deck__title"><?php echo esc_html($title); ?></h2>
    </div>

    <div class="container">
        <div class="service-process-deck__stage">
            <div class="service-process-deck__deck" style="--deck-count: <?php echo (int) count($steps); ?>;">
                <?php foreach ($steps as $i => $step) :
                    $number = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
                ?>
                    <button
                        type="button"
                        class="service-process-deck__card<?php echo $i === 0 ? ' is-active' : ''; ?>"
                        style="--deck-offset: <?php echo (int) $i; ?>;"
                        data-index="<?php echo (int) $i; ?>"
                        aria-label="<?php echo esc_attr(sprintf(/* translators: 1: step number, 2: step title */ __('Step %1$d: %2$s', 'wheellab'), $i + 1, $step['title'])); ?>"
                    >
                        <span class="service-process-deck__card-inner">
                            <span class="service-process-deck__card-texture service-process-deck__card-texture--top" aria-hidden="true"></span>
                            <span class="service-process-deck__card-texture service-process-deck__card-texture--bottom" aria-hidden="true"></span>

                            <span class="service-process-deck__card-head">
                                <span class="service-process-deck__card-top">
                                    <span class="service-process-deck__card-number h3"><?php echo esc_html($number); ?></span>
                                </span>

                                <span class="service-process-deck__card-text">
                                    <span class="service-process-deck__card-heading h3"><?php echo esc_html($step['title']); ?></span>
                                    <span class="service-process-deck__card-description body-m"><?php echo nl2br(esc_html($step['description'])); ?></span>
                                </span>
                            </span>

                            <span class="service-process-deck__card-bottom">
                                <span class="service-process-deck__card-number service-process-deck__card-number--ghost h3"><?php echo esc_html($number); ?></span>
                            </span>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <ul class="service-process-deck__list">
            <?php foreach ($steps as $i => $step) :
                $number = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            ?>
                <li class="service-process-deck__list-item">
                    <span class="service-process-deck__list-number h3"><?php echo esc_html($number); ?></span>
                    <span class="service-process-deck__list-text">
                        <span class="service-process-deck__list-heading h3"><?php echo esc_html($step['title']); ?></span>
                        <span class="service-process-deck__list-description body-m"><?php echo nl2br(esc_html($step['description'])); ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
