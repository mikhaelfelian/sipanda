<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-12
 * 
 * BHP Edit View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-5">
        <?= form_open('master/bhp/update/' . $bhp->id) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit BHP</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Kode</label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'kode',
                        'class' => 'form-control rounded-0',
                        'value' => $bhp->kode,
                        'readonly' => true
                    ]) ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select name="kategori"
                                class="form-control select2 rounded-0 <?= validation_show_error('kategori') ? 'is-invalid' : '' ?>">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategoris as $kategori): ?>
                                    <option value="<?= $kategori->id ?>" <?= old('kategori', $bhp->id_kategori) == $kategori->id ? 'selected' : '' ?>>
                                        <?= esc($kategori->kategori) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?= validation_show_error('kategori') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Merk</label>
                            <select name="merk"
                                class="form-control select2 rounded-0 <?= validation_show_error('id_merk') ? 'is-invalid' : '' ?>">
                                <option value="">Pilih Merk</option>
                                <?php foreach ($merks as $merk): ?>
                                    <option value="<?= $merk->id ?>" <?= old('id_merk', $bhp->id_merk) == $merk->id ? 'selected' : '' ?>>
                                        <?= esc($merk->merk) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?= validation_show_error('id_merk') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama BHP <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'item',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('item') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan nama BHP',
                        'value' => old('item', $bhp->item)
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('item') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name' => 'item_kand',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('item_kand') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan keterangan BHP',
                        'value' => old('item_kand', $bhp->item_kand),
                        'rows' => 3
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('item_kand') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Satuan <span class="text-danger">*</span></label>
                    <select name="satuan"
                        class="form-control rounded-0 <?= validation_show_error('satuan') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Satuan</option>
                        <?php foreach ($satuans as $satuan): ?>
                            <option value="<?= $satuan->id ?>" <?= old('satuan', $bhp->id_satuan) == $satuan->id ? 'selected' : '' ?>>
                                <?= $satuan->satuanBesar ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('satuan') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Harga Beli <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text rounded-0">Rp</span>
                                </div>
                                <?= form_input([
                                    'type' => 'text',
                                    'name' => 'harga_beli',
                                    'id' => 'harga',
                                    'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_beli') ? 'is-invalid' : ''),
                                    'placeholder' => 'Masukkan harga beli',
                                    'value' => old('harga_beli', $bhp->harga_beli)
                                ]) ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('harga_beli') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Harga Jual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text rounded-0">Rp</span>
                                </div>
                                <?= form_input([
                                    'type' => 'text',
                                    'name' => 'harga_jual',
                                    'id' => 'harga',
                                    'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_jual') ? 'is-invalid' : ''),
                                    'placeholder' => 'Masukkan harga jual',
                                    'value' => old('harga_jual', $bhp->harga_jual)
                                ]) ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('harga_jual') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Stok</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status_stok" name="status_stok"
                                    value="1" <?= old('status_stok', $bhp->status_stok) == '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="status_stok">Stockable</label>
                            </div>
                            <small class="form-text text-muted">Aktifkan jika di centang maka akan mengurangi
                                stok.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="status" value="1"
                                    id="statusAktif" <?= old('status', $bhp->status) == '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="statusAktif">
                                    Aktif
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="status" value="0"
                                    id="statusNonaktif" <?= old('status', $bhp->status) == '0' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="statusNonaktif">
                                    Non Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/bhp') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>

    <div class="col-7">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Item Referensi</h3>
            </div>
            <div class="card-body">
                <?= form_open('master/bhp/item_ref_save/' . $bhp->id, ['id' => 'formItemRef']) ?>
                <?= form_input([
                    'type' => 'hidden',
                    'id' => 'id_item_ref',
                    'name' => 'id_item_ref'
                ]) ?>
                <?= form_input([
                    'type' => 'hidden',
                    'id' => 'id',
                    'name' => 'id_item',
                    'value' => $bhp->id
                ]) ?>
                <div class="row mb-3">
                    <div class="col-5">
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'item_ref',
                            'name' => 'item_ref',
                            'class' => 'form-control rounded-0' . (validation_show_error('item_ref') ? ' is-invalid' : ''),
                            'placeholder' => 'Item'
                        ]) ?>
                        <?php if (validation_show_error('item_ref')): ?>
                            <div class="invalid-feedback">
                                <?= validation_show_error('item_ref') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-2">
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'jml',
                            'name' => 'jml',
                            'class' => 'form-control rounded-0' . (validation_show_error('jumlah') ? ' is-invalid' : ''),
                            'placeholder' => 'Jumlah'
                        ]) ?>
                        <?php if (validation_show_error('jumlah')): ?>
                            <div class="invalid-feedback">
                                <?= validation_show_error('jumlah') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3">
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'harga_item_ref',
                            'name' => 'harga_item_ref',
                            'class' => 'form-control rounded-0' . (validation_show_error('harga_item_reff') ? ' is-invalid' : ''),
                            'placeholder' => 'Harga',
                            'readonly' => true
                        ]) ?>
                        <?php if (validation_show_error('harga_item_reff')): ?>
                            <div class="invalid-feedback">
                                <?= validation_show_error('harga_item_reff') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Simpan
                        </button>
                    </div>
                </div>
                <?= form_close() ?>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th class="text-left">Item</th>
                                <th width="15%" class="text-right">Jumlah</th>
                                <th width="20%" class="text-right">Harga</th>
                                <th width="20%" class="text-right">Subtotal</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($item_refs)): ?>
                                <?php $no = 1;
                                foreach ($item_refs as $ref): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?>.</td>
                                        <td><?= esc($ref->item) ?></td>
                                        <td class="text-center"><?= (int) $ref->jml ?></td>
                                        <td class="text-right"><?= format_angka_rp($ref->harga) ?></td>
                                        <td class="text-right"><?= format_angka_rp($ref->subtotal) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('master/bhp/item_ref_delete/' . $ref->id) ?>"
                                                class="btn btn-danger btn-sm rounded-0" onclick="return confirm('Hapus?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    function formatCurrency(input) {
        // Remove non-digit characters
        let value = input.value.replace(/\D/g, '');

        // Convert to number and format
        if (value !== '') {
            value = parseInt(value);
            value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Update input value
        input.value = value;
    }

    function validateNumber(input) {
        // Remove any non-digit characters except decimal point
        input.value = input.value.replace(/[^\d]/g, '');

        // Ensure value is between 0 and 100
        let value = parseInt(input.value);
        if (value > 100) {
            input.value = '100';
        }

        // If this is remun_perc, calculate remun_nom
        if (input.id === 'remun_perc') {
            calculateRemunNom();
        }
    }

    function calculateRemunNom() {
        const hargaJual = parseInt($('#harga').val().replace(/\./g, '') || 0);
        const remunPerc = parseInt($('#remun_perc').val() || 0);

        if (hargaJual && remunPerc) {
            const remunNom = Math.round((hargaJual * remunPerc) / 100);
            $('#remun_nom').val(remunNom.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        }
    }

    function calculateApresNom() {
        const hargaJual = parseInt($('#harga').val().replace(/\./g, '') || 0);
        const apresPerc = parseInt($('#apres_perc').val() || 0);

        if (hargaJual && apresPerc) {
            const apresNom = Math.round((hargaJual * apresPerc) / 100);
            $('#apres_nom').val(apresNom.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        }
    }

    function calculateRemunPerc() {
        const hargaJual = parseInt($('#harga').val().replace(/\./g, '') || 0);
        const remunNom = parseInt($('#remun_nom').val().replace(/\./g, '') || 0);

        if (hargaJual && remunNom) {
            const remunPerc = Math.round((remunNom * 100) / hargaJual);
            const finalPerc = Math.min(remunPerc, 100);
            $('#remun_perc').val(finalPerc);
        }
    }

    function calculateApresPerc() {
        const hargaJual = parseInt($('#harga').val().replace(/\./g, '') || 0);
        const apresNom = parseInt($('#apres_nom').val().replace(/\./g, '') || 0);

        if (hargaJual && apresNom) {
            const apresPerc = Math.round((apresNom * 100) / hargaJual);
            const finalPerc = Math.min(apresPerc, 100);
            $('#apres_perc').val(finalPerc);
        }
    }

    function handleRemunTipeChange() {
        const remunTipe = $('select[name="remun_tipe"]').val();

        // Reset fields
        $('#remun_perc, #remun_nom').val('').prop('disabled', true);

        if (remunTipe === '1') { // Persen
            $('#remun_perc').prop('disabled', false);
            $('#remun_nom').prop('disabled', true);
        } else if (remunTipe === '2') { // Nominal
            $('#remun_perc').prop('disabled', true);
            $('#remun_nom').prop('disabled', false);
        }
    }

    function handleApresTipeChange() {
        const apresTipe = $('select[name="apres_tipe"]').val();

        // Reset fields
        $('#apres_perc, #apres_nom').val('').prop('disabled', true);

        if (apresTipe === '1') { // Persen
            $('#apres_perc').prop('disabled', false);
            $('#apres_nom').prop('disabled', true);
        } else if (apresTipe === '2') { // Nominal
            $('#apres_perc').prop('disabled', true);
            $('#apres_nom').prop('disabled', false);
        }
    }

    // Initialize AutoNumeric
    $(document).ready(function () {
        // Initialize AutoNumeric
        $('input[id=harga]').autoNumeric({ aSep: '.', aDec: ',', aPad: false });
        $('input[id=harga], input[id=jml]').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        // Initialize autocomplete for item reference
        $("#item_ref").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?= base_url('publik/items') ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                var $itemrow = $(this).closest('tr');

                //Populate the input fields from the returned values
                $itemrow.find('#id_item_ref').val(ui.item.id);
                $('#id_item_ref').val(ui.item.id);
                $('#item_ref').val(ui.item.item);
                $('#harga_item_ref').val(ui.item.harga_jual);
                $('#jml').val(1);
                $('#jml').focus();
                return false;
            }
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + item.label + "</a>")
                .appendTo(ul);
        };

        // Initially disable all fields
        $('#remun_perc, #remun_nom, #apres_perc, #apres_nom').prop('disabled', true);

        // Handle type changes
        $('select[name="remun_tipe"]').on('change', handleRemunTipeChange);
        $('select[name="apres_tipe"]').on('change', handleApresTipeChange);

        // Handle harga changes
        $('#harga').on('keyup', function () {
            const remunTipe = $('select[name="remun_tipe"]').val();
            const apresTipe = $('select[name="apres_tipe"]').val();

            if (remunTipe === '1') calculateRemunNom();
            if (remunTipe === '2') calculateRemunPerc();
            if (apresTipe === '1') calculateApresNom();
            if (apresTipe === '2') calculateApresPerc();
        });

        // Handle percentage inputs
        $('#remun_perc').on('input', function () {
            if ($('select[name="remun_tipe"]').val() === '1') {
                calculateRemunNom();
            }
        });

        $('#apres_perc').on('input', function () {
            if ($('select[name="apres_tipe"]').val() === '1') {
                calculateApresNom();
            }
        });

        // Handle nominal inputs
        $('#remun_nom').on('keyup', function () {
            if ($('select[name="remun_tipe"]').val() === '2') {
                calculateRemunPerc();
            }
        });

        $('#apres_nom').on('keyup', function () {
            if ($('select[name="apres_tipe"]').val() === '2') {
                calculateApresPerc();
            }
        });

        // Add keypress validation for percentage fields
        $('#remun_perc, #apres_perc').on('keypress', function (e) {
            // Allow only numbers (0-9)
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
            }

            // Prevent input if current value is 100 and trying to add more digits
            if (this.value === '100' || (this.value.length === 2 && parseInt(this.value + e.key) > 100)) {
                e.preventDefault();
            }
        });
    });
</script>
<?= $this->endSection() ?>