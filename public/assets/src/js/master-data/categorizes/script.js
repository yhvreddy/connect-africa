let categorizeFetchListDataUrl = $("#categorizeFetchListDataUrl").val();
console.log(categorizeFetchListDataUrl);
var fetchDataToTable = $("#fetchDataToTable").DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    scrollCollapse: true,
    scroller: true,
    responsive: true,
    ajax: {
        url: categorizeFetchListDataUrl,
        type: "get",
        data: function (d) {
            return $.extend({}, d, {
                length: $("#customPageLength").val() || "",
                search: $("#search").val() || "",
                status: getSelectedUserStatus(),
            });
        },
        dataSrc: function (json) {
            return json.data;
        },
    },
    columns: [
        { data: "sno", name: "sno" },
        { data: "title", name: "title" },
        { data: "type", name: "type" },
        { data: "is_in_menu", name: "is_in_menu" },
        { data: "is_show_frontend", name: "is_show_frontend" },
        { data: "actions", name: "actions" },
    ],
    searching: false,
    paging: true,
    info: true,
    columnDefs: [
        {
            defaultContent: " ",
            targets: [0, 3, 4],
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
    var categorizeId = $(this).data('id');
    var action = $(this).data('action');

    var updateStatusUrl = $("#updateStatusUrl").val().replace(':categorizeId', categorizeId).replace(':action', action);

    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: updateStatusUrl,
        type: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function (response) {
            // Reload the DataTable after successful update
            fetchDataToTable.ajax.reload();
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
});

$("#territories").on("change", function() {
    fetchDataToTable.draw();
});

$(document).on("click", "#searchFilterButton", function (e) {
    e.preventDefault();
    fetchDataToTable.draw();
});

// Add this function to get the selected user status
function getSelectedUserStatus() {
    return $("#user_status").val() || "";
}

$("#customPageLength").on("change", function (e) {
    fetchDataToTable.page.len($(this).val()).draw();
});

