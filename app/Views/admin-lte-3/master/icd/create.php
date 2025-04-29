<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * ICD Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open('master/icd/store') ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Data ICD</h3>
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
                                'placeholder' => 'Kode ICD...',
                                'value' => old('kode')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('kode') ?>
                            </div>
                        </div>

                        <!-- ICD -->
                        <div class="form-group">
                            <label>ICD <span class="text-danger">*</span></label>
                            <?= form_input([
                                'name' => 'icd',
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('icd') ? 'is-invalid' : ''),
                                'placeholder' => 'ICD...',
                                'value' => old('icd')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('icd') ?>
                            </div>
                        </div>

                        <!-- Diagnosa (EN) -->
                        <div class="form-group">
                            <label>Diagnosa (EN) <span class="text-danger">*</span></label>
                            <?= form_textarea([
                                'name' => 'diagnosa_en',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('diagnosa_en') ? 'is-invalid' : ''),
                                'rows' => 3,
                                'placeholder' => 'Diagnosa dalam bahasa Inggris...',
                                'value' => old('diagnosa_en')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('diagnosa_en') ?>
                            </div>
                        </div>

                        <!-- Diagnosa (ID) -->
                        <div class="form-group">
                            <label>Diagnosa (ID) <span class="text-danger">*</span></label>
                            <?= form_textarea([
                                'name' => 'diagnosa_id',
                                'class' => 'form-control rounded-0 ' . ($validation->hasError('diagnosa_id') ? 'is-invalid' : ''),
                                'rows' => 3,
                                'placeholder' => 'Diagnosa dalam bahasa Indonesia...',
                                'value' => old('diagnosa_id')
                            ]) ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('diagnosa_id') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/icd') ?>" class="btn btn-default rounded-0">
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