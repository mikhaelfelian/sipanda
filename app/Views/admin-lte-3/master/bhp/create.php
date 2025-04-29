<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-12
 * 
 * BHP Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/bhp/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Tambah BHP</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Kategori <span class="text-danger">*</span></label>
                    <select name="kategori"
                        class="form-control select2 rounded-0 <?= validation_show_error('kategori') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategoris as $kategori): ?>
                            <option value="<?= $kategori->id ?>" <?= old('kategori') == $kategori->id ? 'selected' : '' ?>>
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
                    <label>Nama BHP <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'item',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('item') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan nama BHP',
                        'value' => old('item')
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
                        'value' => old('item_kand'),
                        'rows' => 3
                    ]) ?>
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
                                <?= form_input([
                                    'type' => 'text',
                                    'name' => 'harga_beli',
                                    'id' => 'harga',
                                    'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_beli') ? 'is-invalid' : ''),
                                    'placeholder' => 'Masukkan harga beli',
                                    'value' => old('harga_beli')
                                ]) ?>
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
                                <?= form_input([
                                    'type' => 'text',
                                    'name' => 'harga_jual',
                                    'id' => 'harga',
                                    'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_jual') ? 'is-invalid' : ''),
                                    'placeholder' => 'Masukkan harga jual',
                                    'value' => old('harga_jual')
                                ]) ?>
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
                                    <option value="<?= $satuan->id ?>" <?= old('satuan') == $satuan->id ? 'selected' : '' ?>>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Stok</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status_stok" name="status_stok"
                                    value="1" <?= old('status_stok') == '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="status_stok">Stockable</label>
                            </div>
                            <small class="form-text text-muted">Aktifkan jika di centang maka akan mengurangi stok.</small>
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
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Initialize AutoNumeric
    $(document).ready(function () {
        $('input[id=harga]').autoNumeric('init', {
            aSep: '.',
            aDec: ',',
            aForm: true,
            vMax: '999999999',
            vMin: '-999999999'
        });
    });
</script>
<?= $this->endSection() ?> 