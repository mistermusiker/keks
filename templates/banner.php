<?php
/**
 * Keks Cookie Banner Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$banner_text = keks()->get_banner_text();
$privacy_page_id = get_option('keks_privacy_page_id', 0);
$privacy_url = $privacy_page_id ? get_permalink($privacy_page_id) : '';
$privacy_title = $privacy_page_id ? get_the_title($privacy_page_id) : '';
$categories = keks()->get_categories();
$granular_mode = get_option('keks_granular_mode', '1') === '1';
$show_imprint_link = keks()->show_imprint_link();
$imprint_page_id = get_option('keks_imprint_page_id', 0);
$imprint_url = $imprint_page_id ? get_permalink($imprint_page_id) : '';
$imprint_title = $imprint_page_id ? get_the_title($imprint_page_id) : '';
$require_consent = get_option('keks_require_consent', '1') === '1';
?>
<div id="keks-banner" class="keks-banner keks-hidden" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr(keks_t('banner_aria_label')); ?>">
    <div class="keks-container">
        <div class="keks-content">
            <div class="keks-text">
                <p><?php echo esc_html($banner_text); ?></p>
            </div>

            <?php if ($granular_mode && count($categories) > 1) : ?>
                <div class="keks-categories">
                    <?php foreach ($categories as $key => $category) : ?>
                        <label class="keks-category <?php echo $category['required'] ? 'keks-category-required' : ''; ?>">
                            <input type="checkbox"
                                   name="keks_category_<?php echo esc_attr($key); ?>"
                                   data-category="<?php echo esc_attr($key); ?>"
                                   <?php echo $category['required'] ? 'checked disabled' : ''; ?>>
                            <span class="keks-category-checkbox"></span>
                            <span class="keks-category-info">
                                <strong class="keks-category-name"><?php echo esc_html($category['name']); ?></strong>
                                <?php if ($category['required']) : ?>
                                    <span class="keks-category-badge"><?php echo esc_html(keks_t('banner_always_active')); ?></span>
                                <?php endif; ?>
                                <span class="keks-category-desc"><?php echo esc_html($category['description']); ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($privacy_url || ($show_imprint_link && $imprint_url)) : ?>
            <div class="keks-footer">
                <?php if ($privacy_url) : ?>
                    <a href="<?php echo esc_url($privacy_url); ?>" class="keks-privacy-link"><?php echo esc_html($privacy_title); ?></a>
                <?php endif; ?>
                <?php if ($show_imprint_link && $imprint_url) : ?>
                    <?php if ($privacy_url) : ?><span class="keks-link-separator">|</span><?php endif; ?>
                    <a href="<?php echo esc_url($imprint_url); ?>" class="keks-privacy-link"><?php echo esc_html($imprint_title); ?></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="keks-buttons">
            <?php if ($granular_mode && count($categories) > 1) : ?>
                <button type="button" class="keks-btn keks-btn-save">
                    <?php echo esc_html(keks_t('banner_save_selection')); ?>
                </button>
            <?php endif; ?>
            <?php if (!$require_consent) : ?>
                <button type="button" class="keks-btn keks-btn-reject">
                    <?php echo esc_html(keks_t('banner_reject_all')); ?>
                </button>
            <?php endif; ?>
            <button type="button" class="keks-btn keks-btn-accept">
                <?php echo esc_html(keks_t('banner_accept_all')); ?>
            </button>
        </div>
    </div>
</div>
