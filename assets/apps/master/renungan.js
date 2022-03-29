var table = $("#tabel").DataTable({
    processing: true,
    responsive: true,
    destroy: true,
    ajax: base_url + "master/ajax_data_renungan",
    order: [
        [0, 'asc']
    ],
    language: {
        aria: {
            sortAscending: ": activate to sort column ascending",
            sortDescending: ": activate to sort column descending"
        },
        emptyTable: "No data available in table",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "No entries found",
        infoFiltered: "(filtered1 from _MAX_ total entries)",
        lengthMenu: "_MENU_ entries",
        search: "Search:",
        zeroRecords: "No matching records found"
    },
    lengthMenu: [
        [5, 10, 15, 20, -1],
        [5, 10, 15, 20, "All"]
    ],
    pageLength: 10,
});