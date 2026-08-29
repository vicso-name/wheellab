<?php
/**
 * Block: Service Reel Section
 * Registered as: acf/service-reel-section
 * Source: WheelLab Website (Figma) — node 806:11659 ("Frame"), the
 * "See it in motion" video showcase. The chapter thumbnail strip below
 * the player (Figma's "Navigation dots") seeks the video to a timestamp
 * on click — implemented uniformly across all three supported sources
 * (self-hosted file, YouTube, Vimeo) via their respective JS APIs:
 * plain video.currentTime for <video>, the YouTube IFrame API's
 * seekTo(), and the Vimeo Player SDK's setCurrentTime(). See
 * service_reel_section.js for the actual player wiring — this template
 * only renders the right markup per source and each chapter's computed
 * timestamp in seconds (wheellab_parse_video_timestamp(), accepting
 * either "mm:ss" or a plain seconds value in the ACF field).
 *
 * Assets: build/css/sections/service_reel_section.min.css
 *         build/js/sections/service_reel_section.min.js
 */

$eyebrow     = get_field('eyebrow')     ?: '';
$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$note_text   = get_field('note_text')   ?: '';
$video_type  = get_field('video_type')  ?: 'file';
$video_file  = get_field('video_file');
$video_poster = get_field('video_poster');
$video_url   = get_field('video_url')   ?: '';
$chapters    = get_field('chapters')    ?: [];

$has_video = ($video_type === 'file' && !empty($video_file['url']))
    || (in_array($video_type, ['youtube', 'vimeo'], true) && $video_url);

if (!$title || !$has_video) {
    return;
}

$class  = 'service-reel-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$info_icon_url = esc_url(wheellab_asset_url('assets/img/icons/info-fill.svg'));

$youtube_embed_id = '';
$vimeo_embed_id   = '';
if ($video_type === 'youtube') {
    $youtube_embed_id = wheellab_extract_youtube_id($video_url);
} elseif ($video_type === 'vimeo') {
    $vimeo_embed_id = wheellab_extract_vimeo_id($video_url);
}

$chapters = array_values(array_filter(array_map(function ($chapter) {
    if (empty($chapter['image']['url']) || $chapter['timestamp'] === '') {
        return null;
    }
    return [
        'url'     => $chapter['image']['url'],
        'alt'     => $chapter['image']['alt'] ?? '',
        'label'   => $chapter['label'] ?: '',
        'seconds' => wheellab_parse_video_timestamp($chapter['timestamp']),
    ];
}, $chapters)));
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="service-reel-section__header">
            <?php if ($eyebrow) : ?>
                <p class="service-reel-section__eyebrow body-m"><?php echo esc_html($eyebrow); ?></p>
            <?php endif; ?>

            <h2 class="service-reel-section__title"><?php echo esc_html($title); ?></h2>

            <?php if ($description) : ?>
                <p class="service-reel-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
            <?php endif; ?>

            <?php if ($note_text) : ?>
                <div class="service-reel-section__note">
                    <img class="svg service-reel-section__note-icon" src="<?php echo $info_icon_url; ?>" alt="">
                    <p class="service-reel-section__note-text body-s"><?php echo esc_html($note_text); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="service-reel-section__player-wrap">
            <?php if ($video_type === 'file') : ?>
                <div class="service-reel-section__player" data-reel-player data-video-type="file">
                    <video
                        class="service-reel-section__video"
                        data-reel-video
                        controls
                        playsinline
                        <?php echo !empty($video_poster['url']) ? 'poster="' . esc_url($video_poster['url']) . '"' : ''; ?>
                    >
                        <source src="<?php echo esc_url($video_file['url']); ?>" type="<?php echo esc_attr($video_file['mime_type'] ?? 'video/mp4'); ?>">
                    </video>
                </div>
            <?php elseif ($video_type === 'youtube' && $youtube_embed_id) :
                $youtube_src = 'https://www.youtube-nocookie.com/embed/' . $youtube_embed_id . '?enablejsapi=1&rel=0&origin=' . rawurlencode(home_url());
            ?>
                <div class="service-reel-section__player" data-reel-player data-video-type="youtube">
                    <iframe
                        class="service-reel-section__video"
                        data-reel-video
                        src="<?php echo esc_url($youtube_src); ?>"
                        title="<?php echo esc_attr($title); ?>"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
            <?php elseif ($video_type === 'vimeo' && $vimeo_embed_id) :
                $vimeo_src = 'https://player.vimeo.com/video/' . $vimeo_embed_id;
            ?>
                <div class="service-reel-section__player" data-reel-player data-video-type="vimeo">
                    <iframe
                        class="service-reel-section__video"
                        data-reel-video
                        src="<?php echo esc_url($vimeo_src); ?>"
                        title="<?php echo esc_attr($title); ?>"
                        allow="autoplay; fullscreen; picture-in-picture; clipboard-write"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
            <?php endif; ?>

            <?php if ($chapters) : ?>
                <div class="service-reel-section__chapters">
                    <?php foreach ($chapters as $index => $chapter) : ?>
                        <button
                            type="button"
                            class="service-reel-section__chapter<?php echo $index === 0 ? ' is-active' : ''; ?>"
                            data-reel-chapter
                            data-timestamp="<?php echo (int) $chapter['seconds']; ?>"
                            aria-label="<?php echo esc_attr($chapter['label'] ?: sprintf(__('Jump to %s', 'wheellab'), gmdate($chapter['seconds'] >= 3600 ? 'H:i:s' : 'i:s', $chapter['seconds']))); ?>"
                        >
                            <img src="<?php echo esc_url($chapter['url']); ?>" alt="<?php echo esc_attr($chapter['alt']); ?>" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
