<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return '&hellip;';
});

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment', 'embed'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__ . '\\filter_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    collect(['get_header', 'wp_head'])->each(function ($tag) {
        ob_start();
        do_action($tag);
        $output = ob_get_clean();
        remove_all_actions($tag);
        add_action($tag, function () use ($output) {
            echo $output;
        });
    });
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory() . '/index.php';
    }
    return $template;
}, PHP_INT_MAX);

/**
 * Render comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );

    $data = collect(get_body_class())->reduce(function ($data, $class) use ($comments_template) {
        return apply_filters("sage/template/{$class}/data", $data, $comments_template);
    }, []);

    $theme_template = locate_template(["views/{$comments_template}", $comments_template]);

    if ($theme_template) {
        echo template($theme_template, $data);
        return get_stylesheet_directory() . '/index.php';
    }

    return $comments_template;
}, 100);

/**
 * Filter excerpt length
 */
add_filter('excerpt_length', function () {
    return 20;
}, 999);

/* Set JPEG compression to 90 */
add_filter('jpeg_quality', function ($arg) {
    return 90;
});

/**
 * Allow widgets to evaluate shortcodes
 */
add_filter('widget_text', 'do_shortcode');

/**
 * Optimization: Add async to javascripts
 */
add_filter('script_loader_tag', function ($tag, $handle) {
    // add script handles to the array below
    $scripts_to_async = array('sage/main.js');
    foreach ($scripts_to_async as $async_script) {
        if ($async_script === $handle) {
            return str_replace(' src', ' async="async" src', $tag);
        }
    }
    return $tag;
}, 10, 2);

add_filter('navigation_markup_template', function ($template) {
    return '<nav class="navigation" role="navigation" aria-label="%2$s"><ul class="%1$s">%3$s</ul></nav>';
});

// enable block editor only for posts
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
    return $use_block_editor && 'post' === $post_type;
}, 10, 2);

// add .nav-item class to menu items (BS4)
add_filter('nav_menu_css_class', function ($classes, $item) {
    $classes[] = 'nav-item nav-link ';
    if (in_array('menu-item-has-children', $classes)) {
        $classes[] = 'dropdown';
    }
    return $classes;
}, 10, 2);

// add .nav-link class to menu links (BS4)
add_filter('nav_menu_link_attributes', function ($atts, $item, $args) {
    if ($args->theme_location === 'primary_navigation') {
        $atts['class'] = 'nav-link';
        if ($item->is_subitem) {
            $atts['class'] .= ' dropdown-toggle';
            $atts['data-toggle'] = 'dropdown';
            $atts['role'] = 'button';
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }
    }
    return $atts;
}, 10, 3);

