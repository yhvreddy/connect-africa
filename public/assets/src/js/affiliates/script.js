let fetchListDataUrl = $("#fetchListDataUrl").val();
var fetchDataToTable = $("#fetchDataToTable").DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    scrollCollapse: true,
    scroller: true,
    responsive: true,
    ajax: {
        url: fetchListDataUrl,
        type: "get",
        data: function (d) {
            return $.extend({}, d, {
                length: $("#customPageLength").val() || "",
                search: $("#search").val() || "",
                duration: getSelectedDuration(),
                status: getSelectedUserStatus(),
            });
        },
        dataSrc: function (json) {
            return json.data;
        },
    },
    columns: [
        { data: "sno", name: "sno" },
        { data: "email", name: "email" },
        { data: "name", name: "name" },
        { data: "joined", name: "joined" },
        { data: "referred", name: "referred" },
        { data: "status", name: "status" },
        { data: "access_code", name: "access_code" },
        { data: "actions", name: "actions" },
    ],
    searching: false,
    paging: true,
    info: true,
    columnDefs: [
        {
            defaultContent: " ",
            targets: [0, 4, 6, 7],
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
    var affiliateId = $(this).data('id'); 
    var action = $(this).data('action');

    var updateStatusUrl = $("#updateStatusUrl").val().replace(':affiliateId', affiliateId).replace(':action', action);

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


$(document).on("click", "#searchFilterButton", function (e) {
    e.preventDefault();
    fetchDataToTable.draw();
});

function getSelectedDuration() {
    return $("#records_duration").val() || "";
}

function getSelectedUserStatus() {
    return $("#user_status").val() || "";
}

$("#customPageLength").on("change", function (e) {
    fetchDataToTable.page.len($(this).val()).draw();
});
