$(function () {
    var basictable = $('.js-basic-example').DataTable();

    //Exportable table
    var table = $('.js-exportable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    // Custom filter function
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var selectedStore = $('#storeFilter').val();
            var storeColumnIndex = 2; // Assuming column index 1 contains store names
            console.log('Selected Store:', selectedStore);
            console.log('Row Store Value:', data[storeColumnIndex]);
            // If no store is selected or the selected store matches the current row, return true
            return !selectedStore || data[storeColumnIndex] === selectedStore;
        }
    );
    // Event listener for the store filter dropdown
    $('#storeFilter').on('change', function() {
        console.log($('#storeFilter').val());
        table.draw();
    });
});