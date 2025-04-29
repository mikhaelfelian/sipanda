$(document).ready(function() {
    // Load tindakan details
    function loadTindakan(id) {
        $.ajax({
            url: baseUrl + 'publik/getTindakan',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            cache: false,
            success: function(response) {
                if (response.success) {
                    const tindakan = response.data;
                    
                    // Populate form fields
                    $('#tindakan-form input[name="id"]').val(tindakan.id);
                    $('#tindakan-form input[name="id_item"]').val(tindakan.id_item);
                    $('#tindakan-form input[name="item"]').val(tindakan.item_nama);
                    $('#tindakan-form input[name="keterangan"]').val(tindakan.keterangan);
                    $('#tindakan-form input[name="jml"]').val(tindakan.jml);
                    $('#tindakan-form input[name="satuan"]').val(tindakan.satuan);
                    
                    // Show the form modal if using one
                    $('#modal-edit-tindakan').modal('show');
                } else {
                    toastr.error(response.message || 'Gagal memuat data tindakan');
                }
            },
            error: function(xhr, status, error) {
                console.error('XHR:', xhr.responseText);
                toastr.error('Error loading tindakan: ' + error);
            }
        });
    }

    // Handle edit tindakan button click
    $(document).on('click', '.btn-edit-tindakan', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        loadTindakan(id);
    });

    // Handle delete tindakan
    $(document).on('click', '.btn-delete-tindakan', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (confirm('Apakah anda yakin ingin menghapus tindakan ini?')) {
            $.ajax({
                url: baseUrl + 'publik/deleteTindakan',
                type: 'POST',
                data: { 
                    id: id,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success('Tindakan berhasil dihapus');
                        // Reload tindakan list or remove row
                        $('#row-tindakan-' + id).remove();
                    } else {
                        toastr.error(response.message || 'Gagal menghapus tindakan');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Error: ' + error);
                    console.error(xhr.responseText);
                }
            });
        }
    });

    // Initialize form elements
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Format number inputs
    $('.number-format').number(true, 2);
}); 