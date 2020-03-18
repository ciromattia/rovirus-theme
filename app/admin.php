<?php

namespace App;

/**
 * Theme customizer
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
    // Add postMessage support
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->selective_refresh->add_partial('blogname', [
        'selector' => '.brand',
        'render_callback' => function () {
            bloginfo('name');
        }
    ]);
    $wp_customize->add_section('theme_section', [
        'title' => __('Opzioni del tema', 'sage'),
        'priority' => 30,
    ]);
    $wp_customize->add_setting('site_logo');
    $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'site_logo', [
        'label' => __('Site logo', 'sage'),
        'description' => 'Il logo del sito, dimensioni 120x84, meglio se in formato SVG (di default verrà usato quello di Rovirus)',
        'section' => 'title_tagline',
        'settings' => 'site_logo',
    ]));
    $wp_customize->add_setting('head_scripts');
    $wp_customize->add_control('head_scripts', [
        'label' => __('Script in head (e.g. Google Analytics)', 'sage'),
        'description' => 'Eventuali script da aggiungere nell\'head del sito, come lo snippet di Google Analytics, completi di tag &lt;script&gt;',
        'type' => 'textarea',
        'section' => 'title_tagline',
        'setting' => 'head_scripts',
        'priority' => 80
    ]);
});

/**
 * Customizer JS
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('sage/customizer.js', asset_path('scripts/customizer.js'), ['customize-preview'], null, true);
});

/**
 * Enqueue SVG javascript and stylesheet in admin
 */
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script('svg-media-preview', asset_path('/scripts/media-svg.js'), 'jquery');
    wp_localize_script('svg-media-preview', 'script_vars',
        array('AJAXurl' => admin_url('admin-ajax.php')));
});

/**
 * Ajax get_attachment_url_media_library
 */
add_action('wp_ajax_svg_get_attachment_url', function () {
    $attachmentID = isset($_REQUEST['attachmentID']) ? $_REQUEST['attachmentID'] : '';
    if ($attachmentID) {
        echo wp_get_attachment_url($attachmentID);
    }
    die();
});

/**
 * Choices are in a text area and are formatted like 'value : label', one per row
 */
function get_choices_option($choices_group)
{
    $choices = get_theme_mod('choices_' . $choices_group);
    $choices = explode("\n", $choices);
    $choices = array_map(function ($row) {
        return explode(' : ', $row);
    }, $choices);
    $out = [];
    foreach ($choices as $choice) {
        $out[trim($choice[0])] = trim($choice[1]);
    }
    // return the field
    return $out;
}
