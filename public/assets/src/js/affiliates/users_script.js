let fetchListDataUrl = $("#fetchListDataUrl").val();
let requestType = $("#requestType").val();
let requestAffiliateId = $("#requestAffiliate").val();
var fetchDataToTable = $("#fetchDataToTable").DataTable({
    processing: true,
    serverSide: true,
    lengthChange: false,
    scrollCollapse: true,
    scroller: true,
    responsive: true,
    ajax: {
        url:
            fetchListDataUrl +
            "?requestType=" +
            requestType +
            "&id=" +
            requestAffiliateId,
        type: "get",
        data: function (d) {
            return $.extend({}, d, {
                length: $("#customPageLength").val() || "",
                search: $("#search").val() || "",
                duration: $("#records_duration").val() || "",
                status: $("#user_status").val() || "",
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
            targets: [0, 4, 6],
            orderable: true,
            className: "text-center",
        },
    ],
    order: [[0, "desc"]],
    pageLength: $("#customPageLength").val(),
    lengthChange: false,
});

$(document).on("click", "#searchFilterButton", function (e) {
    e.preventDefault();
    fetchDataToTable.draw();
});

$("#customPageLength").on("change", function (e) {
    fetchDataToTable.page.len($(this).val()).draw();
});
