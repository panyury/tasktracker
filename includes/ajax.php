<?php

if (!defined('ABSPATH')) {
    exit;
}

// Обробка AJAX-запиту для оновлення статусу завдання
function tasktracker_update_task_status() {
    if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
        wp_send_json_error(array('message' => 'Некоректні дані'));
    }

    $task_id = intval($_POST['task_id']);
    $new_status = sanitize_text_field($_POST['status']);
    $current_status = get_post_meta($task_id, 'task_status', true);

    // Дозволені переходи статусів
    $allowed_transitions = array(
        'new'            => array('in_progress', 'rejected'),  // Виконавець може прийняти або відхилити
        'in_progress'    => array('pending_review'),          // Виконавець може відправити на перевірку
        'needs_revision' => array('in_progress'),            // Виконавець може повернути завдання в роботу
    );

    // Перевіряємо, чи дозволено змінювати статус
    if (!isset($allowed_transitions[$current_status]) || !in_array($new_status, $allowed_transitions[$current_status])) {
        wp_send_json_error(array('message' => 'Зміна статусу недозволена'));
    }

    // Оновлюємо статус завдання
    update_post_meta($task_id, 'task_status', $new_status);

    wp_send_json_success(array('message' => 'Статус оновлено', 'new_status' => $new_status));
}

// Реєструємо AJAX-обробник (доступний без авторизації)
add_action('wp_ajax_tasktracker_update_task_status', 'tasktracker_update_task_status');
add_action('wp_ajax_nopriv_tasktracker_update_task_status', 'tasktracker_update_task_status');
