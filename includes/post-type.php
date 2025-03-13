<?php

if (!defined('ABSPATH')) {
    exit;
}

// Реєстрація кастомного типу запису "Завдання"
function tasktracker_register_post_type() {
    $labels = array(
        'name'               => 'Завдання',
        'singular_name'      => 'Завдання',
        'menu_name'          => 'Завдання',
        'name_admin_bar'     => 'Завдання',
        'add_new'            => 'Додати нове',
        'add_new_item'       => 'Додати нове завдання',
        'new_item'           => 'Нове завдання',
        'edit_item'          => 'Редагувати завдання',
        'view_item'          => 'Переглянути завдання',
        'all_items'          => 'Всі завдання',
        'search_items'       => 'Шукати завдання',
        'not_found'          => 'Завдання не знайдені',
        'not_found_in_trash' => 'Завдання в кошику не знайдені'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'tasktracker'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor')
    );

    register_post_type('tasktracker', $args);
}
add_action('init', 'tasktracker_register_post_type');
