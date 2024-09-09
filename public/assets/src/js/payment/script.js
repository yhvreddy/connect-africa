let fetchDataToTableUrl = $("#fetchDataToTableUrl").val();
var fetchDataToTable = $("#fetchDataToTable").DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    scrollCollapse: true,
    scroller: true,
    responsive: true,
    ajax: {
        url: fetchDataToTableUrl,
        type: "get",
        data: function (d) {
            return $.extend({}, d, {
                length: $("#customPageLength").val() || "",
                username: $("#username").val() || "",
                payment_id: $("#paymentRef").val() || "",
                duration: $("#records_duration").val() || "",
                country_id: $("#country_id").val() || "",
            });
        },
        dataSrc: function (json) {
            return json.data;
        },
    },
    columns: [
        {
            data: "sno",
            name: "sno",
        },
        {
            data: "created_date",
            name: "created_date",
        },
        {
            data: "user_name",
            name: "user_name",
        },
        {
            data: "response_data.total",
            name: "amount",
            render: function (data, type, row) {
                return "$" + data / 100 + ".00";
            },
        },
        {
            data: "price_id",
            name: "price_id",
            render: function (data, type, row) {
                return row.response_data.id;
            },
        },
        {
            data: "actions",
            name: "actions",
            orderable: false, // Disable ordering for this column
            searchable: false, // Disable searching for this column
        },
    ],
    searching: false,
    paging: true,
    info: true,
    columnDefs: [
        {
            defaultContent: " ",
            targets: [0, 5],
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
    var userId = $(this).data("id");
    var action = $(this).data("action");

    var updateStatusUrl = $("#updateStatusUrl")
        .val()
        .replace(":userId", userId)
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
            fetchDataToTable.ajax.reload();
        },
        error: function (xhr, status, error) {
            console.error(error);
        },
    });
});

$("#territories").on("change", function () {
    fetchDataToTable.draw();
});

$(document).on("click", "#searchFilterButton", function (e) {
    e.preventDefault();
    fetchDataToTable.draw();
});

$("#customPageLength").on("change", function (e) {
    fetchDataToTable.page.len($(this).val()).draw();
});
