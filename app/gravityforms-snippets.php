<?php

/* --------------------------------------------------------------
* app/admin.php
-------------------------------------------------------------- */

/**
 * Add All Gravityforms Capabilities To Editor Role
 */
add_action('init', function () {
    $role = get_role('editor');
    $role->add_cap('gform_full_access');
    // $role -> remove_cap('gform_full_access');
});

/**
 * Hide Gravityforms Spinner
 */
add_filter('gform_ajax_spinner_url', function () {
    return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
});

/**
 * Enable 'Hide labels' Option
 */
add_filter('gform_enable_field_label_visibility_settings', '__return_true');

/**
 * Set Tabindex to 0 On All Gravityforms
 */
add_filter('gform_tabindex', function () {
    return 0;
});

/**
 * Allow Gravity Forms play nice with footer JS
 */
// Force Gravity Forms to init scripts in the footer and ensure that the DOM is loaded before scripts are executed
add_filter('gform_init_scripts_footer', '__return_true');
add_filter('gform_cdata_open', function ($content = '') {
    if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
        return $content;
    }
    $content = 'document.addEventListener( "DOMContentLoaded", function() {';
    return $content;
}, 1);

add_filter('gform_cdata_close', function ($content = '') {
    if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
        return $content;
    }
    $content = '}, false );';
    return $content;
}, 99);

add_filter( 'gform_confirmation_anchor', '__return_true' );

/**
 * Modify Gravity Forms select for Bootstrap 4
 */
add_filter('gform_field_content', function ($field_content, $field) {
    if (!is_admin()) {
        // Add 'custom - select' class to select
        if ($field->type === 'select') {
            $field_content = preg_replace('!(<select .*class=\'.* gfield_select)!', '$1 custom-select', $field_content);
        }
        if (is_a($field, 'GF_Field_FileUpload')) {
            if (strpos($field_content, 'custom-file') > 0) {
                // we already did the customization, return
                return $field_content;
            }
            // add classes
            $field_content = str_replace(
                ['ginput_container_fileupload', "type='file' class='"],
                ['custom-file ginput_container_fileupload', "type='file' class='custom-file-input "],
                $field_content
            );

            $field_content = preg_replace('!(<div class=\'[\w\s]*validation_message)!',
                "<label class='custom-file-label'>" . __('Scegli il file...', 'sage') . '</label>$1',
                $field_content
            );
        }
    }
    return $field_content;
}, 10, 5);

/**
 * Modify Gravity Forms radio for Bootstrap 4
 */
add_filter('gform_field_choice_markup_pre_render', function ($choice_markup, $choice, $field, $value) {
    if ($field->type == 'radio') {
        preg_match('/(<li[^>]+>)(<input[^>]+>)<label[^>]+>(\w+)<\/label><\/li>/iu',
            $choice_markup, $matches);

        $choice_markup = $matches[1] . '<label class="custom-control custom-radio">' .
            str_replace('type=', 'class=\'custom-control-input\' type=', $matches[2]) .
            '<span class="custom-control-indicator"></span><span class="custom-control-description">' .
            $matches[3] . '</span></label></li>';
    }

    return $choice_markup;
}, 10, 4);

/**
 * Remove gravity form basic style
 */
add_filter('pre_option_rg_gforms_disable_css', '__return_true');
