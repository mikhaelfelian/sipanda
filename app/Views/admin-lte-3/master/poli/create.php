<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Poli Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/poli/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Poli</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Poli <span class="text-danger">*</span></label>
                    <?= form_input([
                        'name' => 'poli',
                        'id' => 'poli',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('poli') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan nama poli',
                        'value' => old('poli')
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('poli') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <?= form_textarea([
                        'name' => 'keterangan',
                        'id' => 'keterangan',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('keterangan') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan keterangan',
                        'rows' => 3,
                        'value' => old('keterangan')
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('keterangan') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Post Location</label>
                    <?= form_input([
                        'name' => 'post_location',
                        'id' => 'post_location',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('post_location') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan post location',
                        'value' => old('post_location')
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('post_location') ?>
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
                <a href="<?= base_url('master/poli') ?>" class="btn btn-default rounded-0">
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