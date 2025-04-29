<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Kamar Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/kamar/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Data Kamar</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Kode -->
                        <div class="form-group">
                            <label>Kode <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'kode',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('kode') ? 'is-invalid' : ''),
                                'placeholder' => 'Kode kamar...',
                                'value' => old('kode', $kode)
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('kode') ?>
                            </div>
                        </div>

                        <!-- Nama Kamar -->
                        <div class="form-group">
                            <label>Nama Kamar <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'kamar',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('kamar') ? 'is-invalid' : ''),
                                'placeholder' => 'Nama kamar...',
                                'value' => old('kamar')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('kamar') ?>
                            </div>
                        </div>

                        <!-- Kapasitas -->
                        <div class="form-group">
                            <label>Kapasitas <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'jml_max',
                                'type' => 'number',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('jml_max') ? 'is-invalid' : ''),
                                'placeholder' => 'Kapasitas kamar...',
                                'value' => old('jml_max'),
                                'min' => '1'
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('jml_max') ?>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <?= form_dropdown(
                                'status',
                                [
                                    '1' => 'Aktif',
                                    '0' => 'Tidak Aktif'
                                ],
                                old('status', '1'),
                                'class="form-control rounded-0 ' . ($validation->hasError('status') ? 'is-invalid' : '') . '"'
                            ) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('status') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/kamar') ?>" class="btn btn-default rounded-0">
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