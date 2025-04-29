<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-12
 * 
 * Radiologi Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/radiologi/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Radiologi</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Kategori <span class="text-danger">*</span></label>
                    <select name="kategori"
                        class="form-control select2 rounded-0 <?= validation_show_error('kategori') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategoris as $kategori): ?>
                            <option value="<?= $kategori->id ?>">
                                <?= esc($kategori->kategori) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('kategori') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Merk</label>
                    <select name="merk"
                        class="form-control select2 rounded-0 <?= validation_show_error('id_merk') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Merk</option>
                        <?php foreach ($merks as $merk): ?>
                            <option value="<?= $merk->id ?>" <?= old('id_merk') == $merk->id ? 'selected' : '' ?>>
                                <?= esc($merk->merk) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('id_merk') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Tindakan <span class="text-danger">*</span></label>
                    <?=
                        form_input([
                            'type' => 'text',
                            'name' => 'item',
                            'class' => 'form-control rounded-0 ' . (validation_show_error('item') ? 'is-invalid' : ''),
                            'placeholder' => 'Masukkan nama tindakan',
                            'value' => old('item')
                        ])
                        ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('item') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <?=
                        form_textarea([
                            'name' => 'item_kand',
                            'class' => 'form-control rounded-0 ' . (validation_show_error('item_kand') ? 'is-invalid' : ''),
                            'placeholder' => 'Masukkan keterangan tindakan',
                            'value' => old('item_kand'),
                            'rows' => 3
                        ])
                        ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('item_kand') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Harga Beli <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text rounded-0">Rp</span>
                                </div>
                                <?=
                                    form_input([
                                        'type' => 'text',
                                        'name' => 'harga_beli',
                                        'id' => 'harga',
                                        'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_beli') ? 'is-invalid' : ''),
                                        'placeholder' => 'Masukkan harga beli',
                                        'value' => old('harga_beli')
                                    ])
                                    ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('harga_beli') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Harga Jual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text rounded-0">Rp</span>
                                </div>
                                <?=
                                    form_input([
                                        'type' => 'text',
                                        'name' => 'harga_jual',
                                        'id' => 'harga',
                                        'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_jual') ? 'is-invalid' : ''),
                                        'placeholder' => 'Masukkan harga jual',
                                        'value' => old('harga_jual')
                                    ])
                                    ?>
                                <div class="invalid-feedback">
                                    <?= validation_show_error('harga_jual') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" class="form-control rounded-0 <?= validation_show_error('satuan') ? 'is-invalid' : '' ?>">
                                <option value="">Pilih Satuan</option>
                                <?php foreach ($satuans as $satuan): ?>
                                    <option value="<?= $satuan->id ?>" <?= old('satuanBesar') == $satuan->id ? 'selected' : '' ?>>
                                        <?= $satuan->satuanBesar ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?= validation_show_error('satuan') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4"><label class="control-label">Remunerasi</label></div>
                    <div class="col-lg-2"><label class="control-label">%</label></div>
                    <div class="col-lg-6"><label class="control-label">Rp</label></div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <select name="remun_tipe" class="form-control rounded-0">
                                <option value="">[Tipe]</option>
                                <option value="1">Persen</option>
                                <option value="2">Nominal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <input type="text" class="form-control rounded-0" id="remun_perc" name="remun_perc" value=""
                                placeholder="Masukkan %" oninput="validateNumber(this)" maxlength="3">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="text" class="form-control rounded-0 currency" id="remun_nom" name="remun_nom"
                                value="" placeholder="Masukkan nominal" onkeyup="formatCurrency(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4"><label class="control-label">Apresiasi</label></div>
                    <div class="col-lg-2"><label class="control-label">%</label></div>
                    <div class="col-lg-6"><label class="control-label">Rp</label></div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <select name="apres_tipe" class="form-control rounded-0">
                                <option value="">[Tipe]</option>
                                <option value="1">Persen</option>
                                <option value="2">Nominal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <input type="text" class="form-control rounded-0" id="apres_perc" name="apres_perc" value=""
                                placeholder="Masukkan %" oninput="validateNumber(this)" maxlength="3">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <input type="text" class="form-control rounded-0 currency" id="apres_nom" name="apres_nom"
                                value="" placeholder="Masukkan nominal" onkeyup="formatCurrency(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Stok</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status_stok" name="status_stok"
                                    value="1">
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
                                    id="statusAktif" <?= old('status', '1') == '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="statusAktif">
                                    Aktif
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="status" value="0"
                                    id="statusNonaktif" <?= old('status') == '0' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="statusNonaktif">
                                    Non Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/tindakan') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </div>
        <?= form_close() ?>
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

    // Initialize AutoNumeric for currency inputs
    $(document).ready(function () {
        $('input[id=harga]').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        // Initially disable all fields
        $('#remun_perc, #remun_nom, #apres_perc, #apres_nom').prop('disabled', true);

        // Handle type changes
        $('select[name="remun_tipe"]').on('change', handleRemunTipeChange);
        $('select[name="apres_tipe"]').on('change', handleApresTipeChange);

        // Handle harga_jual changes
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