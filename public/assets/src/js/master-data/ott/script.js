let ottFetchListDataUrl = $("#ottFetchListDataUrl").val();
var ottFetchDataToTable = $("#ottFetchDataToTable").DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    scrollCollapse: true,
    scroller: true,
    responsive: true,
    ajax: {
        url: ottFetchListDataUrl,
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
        { 
            data: "image", 
            name: "image",
            render: function(data, type, row) {
                if (type === 'display' && data) {
                    return '<img src="' + data + '" alt="Image" style="max-width: 100px; max-height: 150px;">';
                } else {
                    return '';
                }
            }
        },
        { data: "title", name: "title" },
        { data: "actions", name: "actions" },
    ],
    searching: false,
    paging: true,
    info: true,
    columnDefs: [
        {
            defaultContent: " ",
            targets: [0, 2],
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
    var genreId = $(this).data('id'); 
    var action = $(this).data('action');

    var updateStatusUrl = $("#updateStatusUrl").val().replace(':genreId', genreId).replace(':action', action);

    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: updateStatusUrl,
        type: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function (response) {
            // Reload the DataTable after successful update
            ottFetchDataToTable.ajax.reload();
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
});

$("#territories").on("change", function() {
    ottFetchDataToTable.draw();
});

$(document).on("click", "#searchFilterButton", function (e) {
    e.preventDefault();
    ottFetchDataToTable.draw();
});

// Add this function to get the selected user status
function getSelectedUserStatus() {
    return $("#user_status").val() || "";
}

$("#customPageLength").on("change", function (e) {
    ottFetchDataToTable.page.len($(this).val()).draw();
});

