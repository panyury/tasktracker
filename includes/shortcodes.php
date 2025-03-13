<?php

if (!defined('ABSPATH')) {
    exit;
}

// Підключаємо helpers.php для роботи з SVG
require_once plugin_dir_path(__FILE__) . 'helpers.php';

// // Мапа статусів
// function tasktracker_get_status_label($status) {
//     $statuses = [
//         'new' => ['label' => 'Нове', 'class' => 'task-status--new'],
//         'in_progress' => ['label' => 'В роботі', 'class' => 'task-status--in-progress'],
//         'pending_review' => ['label' => 'Очікує перевірки', 'class' => 'task-status--pending-review'],
//         'needs_revision' => ['label' => 'Потребує доопрацювання', 'class' => 'task-status--needs-revision'],
//         'completed' => ['label' => 'Завершено', 'class' => 'task-status--completed'],
//         'rejected' => ['label' => 'Відхилено', 'class' => 'task-status--rejected']
//     ];
//     return $statuses[$status] ?? ['label' => 'Невідомий статус', 'class' => 'task-status--unknown'];
// }

// Шорткод для відображення списку завдань
function tasktracker_list_shortcode($atts) {
    $selected_status = $_GET['task_status'] ?? '';

    ob_start();
    ?>
    <form method="GET" class="tasktracker__filter">
        <select name="task_status" id="task_status" class="tasktracker__filter-select">
            <option value="">Всі завдання</option>
            <option value="new" <?php selected($selected_status, 'new'); ?>>Нові</option>
            <option value="in_progress" <?php selected($selected_status, 'in_progress'); ?>>В роботі</option>
            <option value="pending_review" <?php selected($selected_status, 'pending_review'); ?>>Очікує перевірки</option>
            <option value="needs_revision" <?php selected($selected_status, 'needs_revision'); ?>>Потребує доопрацювання</option>
            <option value="completed" <?php selected($selected_status, 'completed'); ?>>Завершені</option>
            <option value="rejected" <?php selected($selected_status, 'rejected'); ?>>Відхилені</option>
        </select>
        <button type="submit" class="tasktracker__filter-button">
            <?php echo tasktracker_get_svg_icon('filter_list'); ?>
        </button>
    </form>
    <?php

    $query_args = [
        'post_type'      => 'tasktracker',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    ];

    if (!empty($selected_status)) {
        $query_args['meta_query'] = [['key' => 'task_status', 'value' => $selected_status]];
    }

    $query = new WP_Query($query_args);

    if ($query->have_posts()) {
        echo '<div class="tasktracker__list">';
        while ($query->have_posts()) {
            $query->the_post();
            $task_id = get_the_ID();
            $task_title = get_the_title();
            $task_short_desc = get_post_meta($task_id, 'task_short_desc', true);
            $task_desc = get_the_content();
            $task_conditions = get_post_meta($task_id, 'task_conditions', true);
            $task_status = get_post_meta($task_id, 'task_status', true);
            $task_reward = get_post_meta($task_id, 'task_reward', true);
            $status_data = tasktracker_get_status_label($task_status);
            
            echo '<div class="tasktracker__item">';
            echo '<span class="tasktracker__status ' . esc_attr($status_data['class']) . '">' . esc_html($status_data['label']) . '</span>';
            echo '<h3 class="tasktracker__title">' . esc_html($task_title) . '</h3>';
            echo '<p class="tasktracker__desc">' . esc_html($task_short_desc) . '</p>';
            echo '<a href="#" class="tasktracker__details-toggle" data-id="' . $task_id . '">Дивитись детально</a>';
            echo '<div class="tasktracker__details" id="details-' . $task_id . '" style="display: none;">';
            echo '<h4>Повний опис</h4><p>' . esc_html($task_desc) . '</p>';
            echo '<h4>Умови виконання</h4><p>' . esc_html($task_conditions) . '</p>';
            echo '<h4>Винагорода</h4><p>' . esc_html($task_reward) . ' умовних одиниць</p>';
            echo '</div>';
            
            echo '<div class="tasktracker__buttons">';
            if ($task_status === 'new') {
                echo '<button class="tasktracker__button tasktracker__button--play" data-id="' . $task_id . '" data-status="in_progress">'
                    . tasktracker_get_svg_icon('play_arrow') . '<span>Взяти в роботу</span></button>';
                echo '<button class="tasktracker__button tasktracker__button--reject" data-id="' . $task_id . '" data-status="rejected">'
                    . tasktracker_get_svg_icon('close') . '<span>Відхилити</span></button>';
            } elseif ($task_status === 'in_progress') {
                echo '<button class="tasktracker__button tasktracker__button--send" data-id="' . $task_id . '" data-status="pending_review">'
                    . tasktracker_get_svg_icon('send') . '<span>Відправити на перевірку</span></button>';
            } elseif ($task_status === 'needs_revision') {
                echo '<button class="tasktracker__button tasktracker__button--replay" data-id="' . $task_id . '" data-status="in_progress">'
                    . tasktracker_get_svg_icon('replay') . '<span>Повернути у роботу</span></button>';
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p class="tasktracker__empty">Завдань не знайдено.</p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode('tasktracker_list', 'tasktracker_list_shortcode');
