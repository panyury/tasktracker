<?php
/**
 * Plugin Name: TaskTracker
 * Description: Управління завданнями з кастомними статусами та винагородами.
 * Version: 1.0
 * Author: Yury Egoroff
 */

if (!defined('ABSPATH')) {
    exit; // Захист від прямого доступу
}

// Підключення файлів
require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';

// Підключення CSS і JS
function tasktracker_enqueue_assets() {
    wp_enqueue_style('tasktracker-css', plugin_dir_url(__FILE__) . 'css/styles.css');
    wp_enqueue_script('tasktracker-js', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
    wp_localize_script('tasktracker-js', 'tasktrackerAjax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'tasktracker_enqueue_assets');
