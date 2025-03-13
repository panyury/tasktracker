<?php

if (!defined('ABSPATH')) {
    exit;
}

// Підключаємо helpers.php для роботи з SVG
require_once plugin_dir_path(__FILE__) . 'helpers.php';

// Додаємо сторінку для тестових завдань
function tasktracker_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=tasktracker',
        'Тестові завдання',
        'Тестові завдання',
        'manage_options',
        'tasktracker_test_tasks',
        'tasktracker_test_tasks_page'
    );
}
add_action('admin_menu', 'tasktracker_add_admin_menu');

// Функція створення тестових завдань
function tasktracker_test_tasks_page() {
    ?>
    <div class="wrap">
        <h1>Додавання тестових завдань</h1>
        <p>Натисніть кнопку нижче, щоб додати 5 тестових завдань.</p>
        <form method="post">
            <input type="hidden" name="tasktracker_generate_test_tasks" value="1">
            <?php submit_button('Створити тестові завдання'); ?>
        </form>
    </div>
    <?php

    if (isset($_POST['tasktracker_generate_test_tasks'])) {
        tasktracker_add_test_tasks();
        echo '<div class="updated"><p>Тестові завдання успішно додані!</p></div>';
    }
}

// Додавання тестових завдань
function tasktracker_add_test_tasks() {
    $test_tasks = array(
        array(
            'title' => 'Розробити REST API',
            'short_desc' => 'Створити API для роботи з завданнями.',
            'desc' => 'Необхідно створити REST API для управління завданнями, включаючи отримання списку, оновлення статусів та видалення.',
            'conditions' => 'API повинен підтримувати авторизацію та обмеження за ролями.',
            'reward' => 10
        ),
        array(
            'title' => 'Додати фільтрацію',
            'short_desc' => 'Додати фільтр за статусами завдань.',
            'desc' => 'Реалізувати можливість фільтрації списку завдань за різними статусами у фронтенді.',
            'conditions' => 'Фільтрація має працювати без перезавантаження сторінки (AJAX).',
            'reward' => 8
        ),
        array(
            'title' => 'Оновити UI',
            'short_desc' => 'Додати нові стилі для інтерфейсу.',
            'desc' => 'Переробити дизайн списку завдань, покращити візуальне сприйняття та адаптивність.',
            'conditions' => 'UI має бути адаптивним і відповідати сучасним стандартам UX/UI.',
            'reward' => 7
        ),
        array(
            'title' => 'Оптимізувати базу',
            'short_desc' => 'Оптимізація запитів до бази даних.',
            'desc' => 'Перевірити та оптимізувати SQL-запити для зменшення навантаження на сервер.',
            'conditions' => 'Запити мають працювати швидко, без надмірних запитів до бази.',
            'reward' => 9
        ),
        array(
            'title' => 'Додати тестування',
            'short_desc' => 'Написати юніт-тести для плагіна.',
            'desc' => 'Створити тестовий набір для перевірки основних функцій плагіна.',
            'conditions' => 'Має бути покрито не менше 80% коду.',
            'reward' => 6
        ),
        // array(
        //     'title' => 'Виправити баги',
        //     'short_desc' => 'Виправити всі виявлені баги.',
        //     'desc' => 'Проаналізувати лог-файли, знайти та виправити всі помилки в коді.',
        //     'conditions' => 'Жоден відомий баг не повинен залишитися у фінальній версії.',
        //     'reward' => 5
        // ),
    );

    foreach ($test_tasks as $task) {
        $task_id = wp_insert_post(array(
            'post_title'   => $task['title'],
            'post_content' => $task['desc'],
            'post_status'  => 'publish',
            'post_type'    => 'tasktracker',
        ));

        if ($task_id) {
            update_post_meta($task_id, 'task_short_desc', $task['short_desc']);
            update_post_meta($task_id, 'task_desc', $task['desc']);
            update_post_meta($task_id, 'task_conditions', $task['conditions']);
            update_post_meta($task_id, 'task_status', 'new');
            update_post_meta($task_id, 'task_reward', $task['reward']);
            update_post_meta($task_id, 'task_revision_count', 0);
        }
    }
}

// Додаємо мета-бокси
function tasktracker_add_meta_boxes() {
    add_meta_box('tasktracker_details_box', 'Деталі завдання', 'tasktracker_details_meta_box', 'tasktracker', 'normal', 'high');
    add_meta_box('tasktracker_status_box', 'Статус завдання', 'tasktracker_status_meta_box', 'tasktracker', 'side', 'high');
}
add_action('add_meta_boxes', 'tasktracker_add_meta_boxes');

// Відображення деталей завдання
function tasktracker_details_meta_box($post) {
    $status = get_post_meta($post->ID, 'task_status', true);
    $short_desc = get_post_meta($post->ID, 'task_short_desc', true);
    $conditions = get_post_meta($post->ID, 'task_conditions', true);
    $reward = get_post_meta($post->ID, 'task_reward', true);
    // $revision_count = get_post_meta($post->ID, 'task_revision_count', true);

    $readonly = (!empty($status) && $status !== 'new') ? 'readonly' : '';

    ?>
    <p>
        <label for="task_short_desc">Короткий опис:</label><br>
        <textarea name="task_short_desc" id="task_short_desc" value="" style="width:100%; height: 100px;" <?php echo $readonly; ?>><?php echo esc_attr($short_desc); ?></textarea>
    </p>
    <p>
        <label for="task_short_desc">Умови завдання:</label><br>
        <textarea name="task_conditions" id="task_conditions" value="" style="width:100%; height: 100px;" <?php echo $readonly; ?>><?php echo esc_attr($conditions); ?></textarea>
    </p>
    <p>
        <label for="task_reward">Винагорода:</label><br>
        <input type="number" name="task_reward" id="task_reward" value="<?php echo esc_attr($reward); ?>" min="0" <?php echo $readonly; ?>>
    </p>
    <?php
}

// Функція для збереження метаданих завдання
function tasktracker_save_meta_box_data($post_id) {
    // Перевіряємо, чи це правильний тип запису
    if (get_post_type($post_id) !== 'tasktracker') {
        return;
    }

    // Перевіряємо, чи є дані у POST-запиті
    if (array_key_exists('task_short_desc', $_POST)) {
        update_post_meta($post_id, 'task_short_desc', sanitize_text_field($_POST['task_short_desc']));
    }
    if (array_key_exists('task_conditions', $_POST)) {
        update_post_meta($post_id, 'task_conditions', sanitize_textarea_field($_POST['task_conditions']));
    }
    if (array_key_exists('task_reward', $_POST)) {
        update_post_meta($post_id, 'task_reward', intval($_POST['task_reward']));
    }

    // Якщо це новий запис, встановлюємо статус та кількість повернень
    if (!metadata_exists('post', $post_id, 'task_status')) {
        update_post_meta($post_id, 'task_status', 'new');
    }
    if (!metadata_exists('post', $post_id, 'task_revision_count')) {
        update_post_meta($post_id, 'task_revision_count', 0);
    }
}
add_action('save_post', 'tasktracker_save_meta_box_data');

// Відображення кнопок керування статусом
function tasktracker_status_meta_box($post) {
    $status = get_post_meta($post->ID, 'task_status', true);
    $status_data = tasktracker_get_status_label($status);
    $revision_count = get_post_meta($post->ID, 'task_revision_count', true);
    ?>
    <p>
        <span class="dashicons dashicons-tag"></span>
        Поточний статус:
        <strong><?php echo esc_html($status_data['label']); ?></strong>
    </p>
    <p>
        <span class="dashicons dashicons-update"></span>
        Кількість повернень:
        <strong><?php echo $revision_count; ?></strong>
    </p>
    
    <?php if ($status === 'pending_review') : ?>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" class="button button-primary tasktracker-change-status" data-status="completed" data-id="<?php echo esc_attr($post->ID); ?>">Прийняти</button>
            <button type="button" class="button button-secondary tasktracker-change-status" data-status="needs_revision" data-id="<?php echo esc_attr($post->ID); ?>">Повернути</button>
        </div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.tasktracker-change-status').forEach(button => {
                button.addEventListener('click', function () {
                    var taskId = this.getAttribute('data-id');
                    var newStatus = this.getAttribute('data-status');

                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'tasktracker_admin_update_status',
                            task_id: taskId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // alert('Статус оновлено: ' + data.data.new_status);
                            location.reload();
                        } else {
                            alert('Помилка: ' + data.data.message);
                        }
                    })
                    .catch(() => alert('Помилка підключення до сервера'));
                });
            });
        });
    </script>
    <?php
}

// Обробка AJAX-запиту для зміни статусу
function tasktracker_admin_update_status() {
    if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
        wp_send_json_error(['message' => 'Некоректні дані']);
    }

    $task_id = intval($_POST['task_id']);
    $new_status = sanitize_text_field($_POST['status']);
    $current_status = get_post_meta($task_id, 'task_status', true);

    $allowed_transitions = [
        'pending_review' => ['completed', 'needs_revision'],
    ];

    if (!isset($allowed_transitions[$current_status]) || !in_array($new_status, $allowed_transitions[$current_status])) {
        wp_send_json_error(['message' => 'Зміна статусу недозволена']);
    }

    update_post_meta($task_id, 'task_status', $new_status);

    if ($new_status === 'needs_revision') {
        $revision_count = get_post_meta($task_id, 'task_revision_count', true);
        update_post_meta($task_id, 'task_revision_count', intval($revision_count) + 1);
    }

    wp_send_json_success(['message' => 'Статус оновлено', 'new_status' => $new_status]);
}
add_action('wp_ajax_tasktracker_admin_update_status', 'tasktracker_admin_update_status');

// Додаємо нові колонки в адмінку (список завдань)
function tasktracker_add_custom_columns($columns) {
    $columns['task_status'] = 'Статус';
    $columns['task_revision_count'] = 'Повернення';
    return $columns;
}
add_filter('manage_tasktracker_posts_columns', 'tasktracker_add_custom_columns');

// Відображення значень у кастомних колонках
function tasktracker_custom_column_content($column, $post_id) {
    if ($column == 'task_status') {
        $status = get_post_meta($post_id, 'task_status', true);
        $status_data = tasktracker_get_status_label($status);
        echo esc_html($status ? $status_data['label'] : 'Не вказано');
    }
    if ($column == 'task_revision_count') {
        $revision_count = get_post_meta($post_id, 'task_revision_count', true);
        echo intval($revision_count);
    }
}
add_action('manage_tasktracker_posts_custom_column', 'tasktracker_custom_column_content', 10, 2);

// Додаємо можливість сортувати таблицю за статусом та кількістю повернень
function tasktracker_sortable_columns($columns) {
    $columns['task_status'] = 'task_status';
    $columns['task_revision_count'] = 'task_revision_count';
    return $columns;
}
add_filter('manage_edit-tasktracker_sortable_columns', 'tasktracker_sortable_columns');

// Фільтрація сортування
function tasktracker_custom_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($orderby = $query->get('orderby')) {
        if ($orderby === 'task_status') {
            $query->set('meta_key', 'task_status');
            $query->set('orderby', 'meta_value');
        } elseif ($orderby === 'task_revision_count') {
            $query->set('meta_key', 'task_revision_count');
            $query->set('orderby', 'meta_value_num');
        }
    }
}
add_action('pre_get_posts', 'tasktracker_custom_orderby');
