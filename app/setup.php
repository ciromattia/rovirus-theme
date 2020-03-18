<?php

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);
    wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}, 100);

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Menu principale', 'sage')
    ]);

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    add_editor_style(asset_path('styles/main.css'));

    /**
     * Enable excerpt support for pages
     */
    add_post_type_support('page', 'excerpt');

    /**
     * Hide ACF backend in production and to non-admins
     */
    add_filter('acf/settings/show_admin', function ($show) {
        return defined('WP_ENV') && WP_ENV != 'production' && current_user_can('manage_options');
    });
}, 20);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ];
    register_sidebar([
            'name' => __('Footer', 'sage'),
            'id' => 'sidebar-footer'
        ] + $config);
});

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    sage('blade')->share('post', $post);
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

    /**
     * Add Blade to Sage container
     */
    sage()->singleton('sage.blade', function (Container $app) {
        $cachePath = config('view.compiled');
        if (!file_exists($cachePath)) {
            wp_mkdir_p($cachePath);
        }
        (new BladeProvider($app))->register();
        return new Blade($app['view']);
    });

    /**
     * Create @asset() Blade directive
     */
    sage('blade')->compiler()->directive('asset', function ($asset) {
        return "<?= " . __NAMESPACE__ . "\\asset_path({$asset}); ?>";
    });
});

/**
 * SoberWP Intervention
 * Check the modules here: https://github.com/soberwp/intervention#modules
 */

if (function_exists('\Sober\Intervention\intervention')) {
    // add theme options page
    // https://github.com/soberwp/intervention/blob/master/.github/add-acf-page.md
    // https://www.advancedcustomfields.com/resources/acf_add_options_page/
    \Sober\Intervention\intervention('add-acf-page', [
        'page_title' => __('Theme general settings', 'sage'),
        'menu_title' => __('Theme Settings', 'sage'),
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
        'autoload' => true,
        'redirect' => false
    ]);

    // add SVG support
    \Sober\Intervention\intervention('add-svg-support');

    \Sober\Intervention\intervention('remove-emoji');

    // removes danger zone for non-admins
    // https://github.com/soberwp/intervention/blob/master/.github/remove-menu-items.md
    \Sober\Intervention\intervention('remove-menu-items', 'all-non-admin');

    // removes toolbar for non-admins
    // https://github.com/soberwp/intervention/blob/master/.github/remove-toolbar-frontend.md
    \Sober\Intervention\intervention('remove-toolbar-frontend', 'all-non-admin');

    // Updates to 40 posts per page. (WordPress default is 20)
    // https://github.com/soberwp/intervention/blob/master/.github/update-pagination.md
    \Sober\Intervention\intervention('update-pagination');
}

include 'customPosts.php';
include 'gravityforms-snippets.php';
