<?php defined('ABSPATH') or die("Cannot access pages directly."); ?>

<div class="wdt-rating-notice notice notice-success">
    <div class="wdt-float-left">
        <img class="wdt-icon-rating" src="<?php echo WDT_ROOT_URL ?>assets/img/logo-large.png" alt="">
    </div>
    <div class="wdt-float-left">
        <h1 class="wdt-rating-heading"><?php _e("Leave a Review?", "wpdatatables") ?></h1>
        <p class="wdt-rating-massage"><?php _e("We hope you've enjoyed using wpDataTables Lite. Would you consider
            leaving us a review on WordPress.org?", "wpdatatables") ?></p>
    </div>
    <div class="wdt-dismiss-dash">
        <span class="wdt-dismiss dashicons dashicons-dismiss"></span>
    </div>
    <ul class="wdt-rating-buttons">
        <li><a href="https://wordpress.org/support/plugin/wpdatatables/reviews/?rate=5&filter=5#new-post"
               class="wdt-rating-button wdt-first-btn" target="_new"
               ><?php _e("Sure! I Like wpDataTables Lite", "wpdatatables") ?></a>
        </li>
        <li><a href="javascript:void(0);" class="wdt-rating-button wdt-hide-rating wdt-other-btn"><?php _e("I've already left a review", "wpdatatables") ?></a></li>
        <li><a href="javascript:void(0);" class="wdt-rating-button wdt-other-btn wdt-dismiss"><?php _e("Maybe Later", "wpdatatables") ?></a></li>
        <li><a href="javascript:void(0);" class="wdt-rating-button wdt-hide-rating wdt-other-btn"><?php _e("Never show again", "wpdatatables") ?></a></li>
    </ul>
</div>