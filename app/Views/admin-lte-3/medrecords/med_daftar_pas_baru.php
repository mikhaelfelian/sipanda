<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-07
 * 
 * Medical Record Patient Registration View
 */
?>
<div class="row">
    <div class="col-md-3">
        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title">Foto Pasien</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <!-- Camera preview -->
                <div id="camera-preview" style="width:100%; height:200px;"></div>

                <!-- Captured photo will be shown here -->
                <div id="camera-result" style="display:none; width:100%; height:240px;">
                    <img id="photo" style="width:100%; height:100%; object-fit:cover;">
                    <input type="hidden" name="foto_pasien" id="foto_pasien">
                </div>
            </div>
            <div class="card-footer p-0">
                <button type="button" onclick="takeSnapshot()" class="btn btn-primary btn-flat btn-block"><i
                        class="fa fa-camera"></i> Ambil Gambar
                </button>
            </div>
        </div>
        <div class="card card-default rounded-0">
            <div class="card-header">
                <h3 class="card-title">Foto Identitas</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <!-- Camera preview for KTP -->
                <div id="camera-preview-ktp" style="width:100%; height:200px;"></div>

                <!-- Captured KTP photo will be shown here -->
                <div id="camera-result-ktp" style="display:none; width:100%; height:240px;">
                    <img id="photo-ktp" style="width:100%; height:100%; object-fit:cover;">
                    <input type="hidden" name="foto_ktp" id="foto_ktp">
                </div>
            </div>
            <div class="card-footer p-0">
                <button type="button" onclick="takeKtpSnapshot()" class="btn btn-primary btn-flat btn-block"><i
                        class="fa fa-camera"></i> Ambil Gambar
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Pasien</h3>
                <div class="card-tools">
                    <?= anchor('pasien', '<i class="fas fa-times"></i>', ['class' => 'btn btn-tool rounded-0']) ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <?= form_label('NIK', 'nik') ?>
                            <?= form_input([
                                'type' => 'text',
                                'class' => 'form-control rounded-0 ' . (session('validation_errors.nik') ? 'is-invalid' : ''),
                                'id' => 'nik',
                                'name' => 'nik',
                                'maxlength' => 16,
                                'placeholder' => 'Masukkan NIK',
                                'value' => old('nik')
                            ]) ?>
                            <?php if (session('validation_errors.nik')): ?>
                                <div class="invalid-feedback">
                                    <?= session('validation_errors.nik') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_gelar">Gelar <span class="text-danger">*</span></label>
                                    <select name="id_gelar" id="id_gelar"
                                        class="form-control rounded-0 <?= session('validation_errors.id_gelar') ? 'is-invalid' : '' ?>">
                                        <option value="">Pilih Gelar</option>
                                        <?php foreach ($gelars as $gelar): ?>
                                            <option value="<?= $gelar->id ?>" <?= old('id_gelar') == $gelar->id ? 'selected' : '' ?>>
                                                <?= esc($gelar->gelar) ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                    <?php if (session('validation_errors.id_gelar')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('validation_errors.id_gelar') ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Nama Lengkap <span class="text-danger">*</span>', 'nama') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0 ' . (session('validation_errors.nama') ? 'is-invalid' : ''),
                                        'id' => 'nama',
                                        'name' => 'nama',
                                        'value' => old('nama'),
                                        'placeholder' => 'Masukkan nama lengkap'
                                    ]) ?>
                                    <?php if (session('validation_errors.nama')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('validation_errors.nama') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jns_klm">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jns_klm" id="jns_klm"
                                        class="form-control rounded-0 <?= session('validation_errors.jns_klm') ? 'is-invalid' : '' ?>">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" <?= old('jns_klm') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= old('jns_klm') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                    <?php if (session('validation_errors.jns_klm')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('validation_errors.jns_klm') ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Tempat Lahir <span class="text-danger">*</span>', 'tmp_lahir') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0 ' . (session('validation_errors.tmp_lahir') ? 'is-invalid' : ''),
                                        'id' => 'tmp_lahir',
                                        'name' => 'tmp_lahir',
                                        'value' => old('tmp_lahir'),
                                        'placeholder' => 'Masukkan tempat lahir'
                                    ]) ?>
                                    <?php if (session('validation_errors.tmp_lahir')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('validation_errors.tmp_lahir') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Tanggal Lahir <span class="text-danger">*</span>', 'tgl_lahir') ?>
                                    <?= form_input([
                                        'type' => 'date',
                                        'class' => 'form-control rounded-0 ' . (session('validation_errors.tgl_lahir') ? 'is-invalid' : ''),
                                        'id' => 'tgl_lahir',
                                        'name' => 'tgl_lahir',
                                        'value' => old('tgl_lahir'),
                                        'placeholder' => 'yyyy-mm-dd'
                                    ]) ?>
                                    <?php if (session('validation_errors.tgl_lahir')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('validation_errors.tgl_lahir') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Alamat <span class="text-danger">*</span>', 'alamat') ?>
                                    <?= form_textarea([
                                        'class' => 'form-control rounded-0 ' . (session('validation_errors.alamat') ? 'is-invalid' : ''),
                                        'id' => 'alamat',
                                        'name' => 'alamat',
                                        'rows' => 3,
                                        'value' => old('alamat'),
                                        'placeholder' => 'Masukkan alamat lengkap'
                                    ]) ?>
                                    <?php if (session('validation_errors.alamat')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('validation_errors.alamat') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Alamat Domisili', 'alamat_domisili') ?>
                                    <?= form_textarea([
                                        'class' => 'form-control rounded-0',
                                        'id' => 'alamat_domisili',
                                        'name' => 'alamat_domisili',
                                        'rows' => 3,
                                        'placeholder' => 'Masukkan alamat domisili'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= form_label('RT', 'rt') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'rt',
                                        'name' => 'rt',
                                        'maxlength' => 3,
                                        'placeholder' => 'RT'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= form_label('RW', 'rw') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'rw',
                                        'name' => 'rw',
                                        'maxlength' => 3,
                                        'placeholder' => 'RW'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <?= form_label('Kelurahan', 'kelurahan') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'kelurahan',
                                        'name' => 'kelurahan',
                                        'placeholder' => 'Masukkan kelurahan'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Kecamatan', 'kecamatan') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'kecamatan',
                                        'name' => 'kecamatan',
                                        'placeholder' => 'Masukkan kecamatan'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Kota', 'kota') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'kota',
                                        'name' => 'kota',
                                        'placeholder' => 'Masukkan kota'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('No HP', 'no_hp') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'no_hp',
                                        'name' => 'no_hp',
                                        'placeholder' => 'Masukkan nomor HP'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= form_label('Pekerjaan', 'pekerjaan') ?>
                                    <?= form_input([
                                        'type' => 'text',
                                        'class' => 'form-control rounded-0',
                                        'id' => 'pekerjaan',
                                        'name' => 'pekerjaan',
                                        'placeholder' => 'Masukkan pekerjaan'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Poli -->
                        <div class="form-group">
                            <label>Poli <span class="text-danger">*</span></label>
                            <select name="id_poli" class="form-control select2 rounded-0" required>
                                <option value="">Pilih Poli</option>
                                <?php foreach ($poliModel->findAll() as $poli): ?>
                                    <option value="<?= $poli->id ?>"><?= esc($poli->poli) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Penjamin -->
                        <div class="form-group">
                            <label>Penjamin <span class="text-danger">*</span></label>
                            <select name="tipe_bayar" class="form-control rounded-0" required>
                                <option value="">Pilih Penjamin</option>
                                <?php foreach ($penjaminModel->findAll() as $penjamin): ?>
                                    <option value="<?= $penjamin->id ?>"><?= esc($penjamin->penjamin) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipe -->
                        <div class="form-group">
                            <label>Tipe <span class="text-danger">*</span></label>
                            <select name="tipe_rawat" class="form-control rounded-0" required>
                                <option value="">Pilih Tipe</option>
                                <option value="1">Rawat Jalan</option>
                                <option value="3">Laboratorium</option>
                                <option value="4">Radiologi</option>
                            </select>
                        </div>

                        <!-- Tanggal Daftar -->
                        <div class="form-group">
                            <label>Tgl Daftar <span class="text-danger">*</span></label>
                            <?= form_input([
                                'type' => 'date',
                                'name' => 'tgl_masuk',
                                'class' => 'form-control rounded-0',
                                'required' => true,
                                'value' => date('Y-m-d')
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <?= anchor('master/pasien', '<i class="fas fa-arrow-left mr-2"></i>Kembali', [
                    'class' => 'btn btn-default rounded-0 float-left'
                ]) ?>
                <?= form_submit('submit', 'Simpan', [
                    'class' => 'btn btn-primary rounded-0'
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Handle Toastr notifications from flash data
        <?php if (session()->getFlashdata('toastr')): ?>
            <?php $toastr = session()->getFlashdata('toastr'); ?>
            <?php if (!empty($toastr['message']['foto_pasien'])): ?>
                toastr.error('<?= $toastr['message']['foto_pasien'] ?>', 'Validasi Error');
            <?php endif; ?>
            <?php if (!empty($toastr['message']['foto_ktp'])): ?>
                toastr.error('<?= $toastr['message']['foto_ktp'] ?>', 'Validasi Error');
            <?php endif; ?>
        <?php endif; ?>

        // Form submission handling
        $('form').on('submit', function (e) {
            if (!$('#foto_pasien').val()) {
                e.preventDefault();
                toastr.error('Foto pasien harus diunggah', 'Validasi Error');
            }
            if (!$('#foto_ktp').val()) {
                e.preventDefault();
                toastr.error('Foto KTP harus diunggah', 'Validasi Error');
            }
        });
    });
</script>
<?= $this->endSection() ?>