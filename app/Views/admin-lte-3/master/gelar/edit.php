<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2024-01-13
 * 
 * Gelar Edit View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/gelar/update/' . $gelar['id']) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Gelar</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Gelar <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'gelar',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('gelar') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan gelar',
                        'value' => old('gelar', $gelar['gelar'])
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('gelar') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'keterangan',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('keterangan') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan keterangan',
                        'value' => old('keterangan', $gelar['keterangan'])
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('keterangan') ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/gelar') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?> 