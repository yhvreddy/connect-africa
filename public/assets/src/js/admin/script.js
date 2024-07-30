let adminFetchListDataUrl = $("#adminFetchListDataUrl").val();
var adminFetchDataToTable = $("#adminFetchDataToTable").DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    scrollCollapse: true,
    scroller: true,
    responsive: true,
    ajax: {
        url: adminFetchListDataUrl,
        type: "get",
        data: function (d) {
            return $.extend({}, d, {
                length: $("#customPageLength").val() || "",
                search: $("#search").val() || "",
                duration: $("#records_duration").val() || "",
                status: getSelectedUserStatus(),
            });
        },
        dataSrc: function (json) {
            return json.data;
        },
    },
    columns: [
        { data: "sno", name: "sno" },
        { data: "name", name: "name" },
        { data: "email", name: "email" },
        { data: "movies", name: "movies" },
        { data: "shows", name: "shows" },
        { data: "events", name: "events" },
        { data: "date_added", name: "created_at" },
        { data: "actions", name: "actions" },
    ],
    searching: false,
    paging: true,
    info: true,
    columnDefs: [
        {
            defaultContent: " ",
            targets: [0, 3],
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
    var adminId = $(this).data("id");
    var action = $(this).data("action");

    var updateStatusUrl = $("#updateStatusUrl")
        .val()
        .replace(":adminId", adminId)
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
            adminFetchDataToTable.ajax.reload();
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
});

$("#territories").on("change", function () {
    adminFetchDataToTable.draw();
});

$(document).on("click", "#searchFilterButton", function (e) {
    e.preventDefault();
    adminFetchDataToTable.draw();
});

// Add this function to get the selected user status
function getSelectedUserStatus() {
    return $("#user_status").val() || "";
}

$("#customPageLength").on("change", function (e) {
    adminFetchDataToTable.page.len($(this).val()).draw();
});
