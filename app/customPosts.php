<?php
/** Place here your custom post definitions and logic (e.g. changes to the main query) */

namespace App;

/*** Custom posts ***/
add_action('init', function () {
});

// Change query for custom posts
add_action('pre_get_posts', function ($query) {
    if (is_admin()) {
        return;
    }
});
