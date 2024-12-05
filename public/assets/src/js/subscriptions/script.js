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
            data: "user.name",
            name: "name",
        },
        {
            data: "user.email",
            name: "email",
        },
        {
            data: "user.mobile",
            name: "mobile",
        },
        {
            data: "subscription.name",
            name: "created_at",
            render: function (data, type, row) {
                console.log(row);
                return (
                    row.subscription.name +
                    " (" +
                    row.subscription_type.name +
                    ")"
                );
            },
        },
        {
            data: "subscription_plan.name",
            name: "subscription_plan",
        },
        {
            data: "amount",
            name: "amount",
        },
        {
            data: "status",
            name: "status",
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
            targets: [0],
            orderable: true,
            className: "text-center",
        },
        /*{
            defaultContent: " ",
            targets: [7],
            orderable: true,
            className: "text-center",
        },*/
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

function getSelectedDuration() {
    return $("#records_duration").val() || "";
}

function getSelectedUserStatus() {
    return $("#user_status").val() || "";
}

$("#customPageLength").on("change", function (e) {
    fetchDataToTable.page.len($(this).val()).draw();
});

$(document).on("click", ".cancel_subscription", function (e) {
    e.preventDefault();
    if (!confirm("Do you want to cancel subscription?")) {
        return;
    }

    let url = $(this).data("url");
    $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            if (response.status == true) {
                fetchDataToTable.draw();
            }
            alert(response.message);
        },
        error: function (err) {
            console.error(err);
        },
    });
});
