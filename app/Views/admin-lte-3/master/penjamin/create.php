<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Penjamin Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/penjamin/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Penjamin</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Penjamin <span class="text-danger">*</span></label>
                    <?= form_input([
                        'name' => 'penjamin',
                        'id' => 'penjamin',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('penjamin') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan nama penjamin',
                        'value' => old('penjamin')
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('penjamin') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Persentase (%)</label>
                    <?= form_input([
                        'type' => 'number',
                        'name' => 'persen',
                        'id' => 'persen',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('persen') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan persentase',
                        'value' => old('persen', '0'),
                        'min' => '0',
                        'max' => '100',
                        'step' => '0.01'
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('persen') ?>
                    </div>
                </div>

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
                    <?php if (validation_show_error('status')): ?>
                        <small class="text-danger">
                            <?= validation_show_error('status') ?>
                        </small>
                    <?php endif ?>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/penjamin') ?>" class="btn btn-default rounded-0">
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