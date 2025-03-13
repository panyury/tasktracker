<?php

if (!defined('ABSPATH')) {
    exit;
}

// Функція для отримання SVG-іконки з папки `svg/`
function tasktracker_get_svg_icon($icon_name) {
    $svg_path = plugin_dir_path(__FILE__) . '../svg/' . $icon_name . '.svg';
    if (file_exists($svg_path)) {
        return file_get_contents($svg_path);
    }
    return ''; // Якщо файл не знайдено, повертаємо порожній рядок
}

// Мапа статусів
function tasktracker_get_status_label($status) {
    $statuses = [
        'new' => ['label' => 'Нове', 'class' => 'task-status--new'],
        'in_progress' => ['label' => 'В роботі', 'class' => 'task-status--in-progress'],
        'pending_review' => ['label' => 'Очікує перевірки', 'class' => 'task-status--pending-review'],
        'needs_revision' => ['label' => 'Потребує доопрацювання', 'class' => 'task-status--needs-revision'],
        'completed' => ['label' => 'Завершено', 'class' => 'task-status--completed'],
        'rejected' => ['label' => 'Відхилено', 'class' => 'task-status--rejected']
    ];
    return $statuses[$status] ?? ['label' => 'Невідомий статус', 'class' => 'task-status--unknown'];
}
