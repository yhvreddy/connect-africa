let showFetchListDataUrl = $("#showFetchListDataUrl").val();
var showFetchDataToTable = $("#showFetchDataToTable").DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    scrollCollapse: true,
    scroller: true,
    responsive: true,
    ajax: {
        url: showFetchListDataUrl,
        type: "get",
        data: function (d) {
            return $.extend({}, d, {
                length: $("#customPageLength").val() || "",
                search: $("#search").val() || "",
                duration: getSelectedDuration() || "", // Pass selected duration
                isAdminRequest: $("#isRequestAdmin").val() || "",
                admin_id: $("#admin_id").val() || "",
            });
        },
        dataSrc: function (json) {
            return json.data;
        },
    },
    columns: [
        { data: "sno", name: "sno" },

        {
            data: "poster_image",
            name: "poster_image",
            render: function (data, type, row) {
                if (type === "display" && data) {
                    return (
                        '<img src="' +
                        data +
                        '" alt="Poster Image" style="max-width: 100px; max-height: 150px;">'
                    );
                } else {
                    return "-";
                }
            },
        },
        { data: "title", name: "title" },
        { data: "year", name: "year" },
        { data: "date_added", name: "created_at" },
        { data: "actions", name: "actions" },
    ],
    searching: false,
    paging: true,
    info: true,
    columnDefs: [
        {
            defaultContent: " ",
            targets: [0],
            orderable: true,
            className: "text-center",
        },
    ],
    order: [[0, "desc"]],
    pageLength: $("#customPageLength").val(),
    lengthChange: false,
});

$(document).on("click", ".btn-activate", function (e) {
    e.preventDefault();
    var showId = $(this).data("id");
    var action = $(this).data("action");

    var updateStatusUrl = $("#updateStatusUrl")
        .val()
        .replace(":showId", showId)
        .replace(":action", action);

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: updateStatusUrl,
        type: "PUT",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
        success: function (response) {
            // Reload the DataTable after successful update
            showFetchDataToTable.ajax.reload();
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
});

$(document).on("click", "#searchFilterButton", function (e) {
    e.preventDefault();
    showFetchDataToTable.draw();
});

function getSelectedDuration() {
    return $("#records_duration").val() || "";
}

$("#customPageLength").on("change", function (e) {
    showFetchDataToTable.page.len($(this).val()).draw();
});
