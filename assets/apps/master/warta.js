var table = $("#tabel").DataTable({
    processing: true,
    responsive: true,
    destroy: true,
    ajax: base_url + "warta/ajax_data_warta",
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

function list_konfirmasi(id) {
    $('#modal_warta').modal('show');
    $.ajax({
        url: base_url + 'warta/list_konfirmasi/' + id,
        type: 'GET',
        dataType: 'JSON',
        success: function(data){
            let html = '';
            data.forEach (function(value, key) {
                html += `<tr>
                            <td>${key+1}</td>
                            <td>${value.nij}</td>
                            <td>${value.nama}</td>
                            <td>${value.posisi}</td>
                            <td>${value.approval}</td>
                        </tr>`;
            });
            $('#dt_warta').html(html);

        },
        error: function(error) {
            console.log(error)
        }
    })
}