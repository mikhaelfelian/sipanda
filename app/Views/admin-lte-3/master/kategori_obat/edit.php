<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-08
 * 
 * KategoriObat Edit View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= form_open("master/jenis/update/{$kategori_obat->id}", ['method' => 'POST']) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Jenis</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <?= form_label('Jenis <span class="text-danger">*</span>', 'jenis') ?>

                    <?= form_input([
                        'name' => 'jenis',
                        'id' => 'jenis',
                        'class' => 'form-control rounded-0 ' . (session('validation_errors.jenis') ? 'is-invalid' : ''),
                        'value' => old('jenis', $kategori_obat->jenis),
                        'placeholder' => 'Masukkan jenis obat'
                    ]) ?>
                    <?php if (session('validation_errors.jenis')): ?>
                        <div class="invalid-feedback">
                            <?= session('validation_errors.jenis') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <?= form_label('Keterangan', 'keterangan') ?>
                    <?= form_textarea([
                        'name' => 'keterangan',
                        'id' => 'keterangan',
                        'class' => 'form-control rounded-0',
                        'rows' => 3,
                        'value' => old('keterangan', $kategori_obat->keterangan),
                        'placeholder' => 'Masukkan keterangan'
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= form_label('Status', 'status') ?>
                    <div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <?= form_radio([
                                'name' => 'status',
                                'id' => 'statusAktif',
                                'value' => '1',
                                'checked' => old('status', $kategori_obat->status) == '1',
                                'class' => 'custom-control-input'
                            ]) ?>
                            <?= form_label('Aktif', 'statusAktif', ['class' => 'custom-control-label']) ?>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <?= form_radio([
                                'name' => 'status',
                                'id' => 'statusNonAktif',
                                'value' => '0',
                                'checked' => old('status', $kategori_obat->status) == '0',
                                'class' => 'custom-control-input'
                            ]) ?>
                            <?= form_label('Non-Aktif', 'statusNonAktif', ['class' => 'custom-control-label']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/jenis') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>