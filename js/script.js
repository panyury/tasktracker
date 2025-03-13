jQuery(document).ready(function ($) {
    // Виправлено натискання на "Дивитись детально"
    $(".tasktracker__details-toggle").click(function (e) {
        e.preventDefault(); // Запобігає переходу за посиланням
        var taskId = $(this).data("id");
        $("#details-" + taskId).slideToggle();
    });

    // AJAX-запит на зміну статусу
    $(".tasktracker__button").click(function () {
        var taskId = $(this).data("id");
        var newStatus = $(this).data("status");

        $.post(tasktrackerAjax.ajax_url, {
            action: "tasktracker_update_task_status",
            task_id: taskId,
            status: newStatus
        }, function (response) {
            if (response.success) {
                // alert("Статус оновлено: " + response.data.new_status);
                location.reload();
            } else {
                alert("Помилка: " + response.data.message);
            }
        }).fail(function () {
            alert("Помилка підключення до сервера");
        });
    });
});
