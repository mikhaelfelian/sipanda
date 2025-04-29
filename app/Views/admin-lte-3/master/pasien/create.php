<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-14
 * 
 * Pasien Create View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<?= form_open('pasien/store', ['method' => 'POST']) ?>

<div class="row">
    <div class="col-md-3">
        <div class="card card-default">
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
        <div class="card card-default">
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
    <div class="col-9">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Pasien</h3>
                <div class="card-tools">
                    <?= anchor('pasien', '<i class="fas fa-times"></i>', ['class' => 'btn btn-tool rounded-0']) ?>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <?= form_label('Nomor RM <span class="text-danger">*</span>', 'no_rm') ?>
                    <?= form_input([
                        'type'     => 'text',
                        'class'    => 'form-control rounded-0',
                        'id'       => 'no_rm',
                        'name'     => 'no_rm',
                        'value'    => $pasien,
                        'readonly' => true,
                        'required' => true
                    ]) ?>
                </div>

                <div class="form-group">
                    <?= form_label('NIK', 'nik') ?>
                    <?= form_input([
                        'type'        => 'text',
                        'class'       => 'form-control rounded-0 ' . (session('validation_errors.nik') ? 'is-invalid' : ''),
                        'id'          => 'nik',
                        'name'        => 'nik',
                        'maxlength'   => 16,
                        'placeholder' => 'Masukkan NIK',
                        'value'       => old('nik')
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
                            <?= form_label('Gelar <span class="text-danger">*</span>', 'id_gelar') ?>
                            <?= form_dropdown(
                                'id_gelar',
                                ['' => 'Pilih Gelar'] + array_column($gelars, 'gelar', 'id'),
                                old('id_gelar'),
                                'class="form-control select2 rounded-0 ' . (session('validation_errors.id_gelar') ? 'is-invalid' : '') . '"'
                            ) ?>
                            <?php if (session('validation_errors.id_gelar')): ?>
                                <div class="invalid-feedback">
                                    <?= session('validation_errors.id_gelar') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Nama Lengkap <span class="text-danger">*</span>', 'nama') ?>
                            <?= form_input([
                                'type'        => 'text',
                                'class'       => 'form-control rounded-0 ' . (session('validation_errors.nama') ? 'is-invalid' : ''),
                                'id'          => 'nama',
                                'name'        => 'nama',
                                'value'       => old('nama'),
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
                            <?= form_label('Jenis Kelamin <span class="text-danger">*</span>', 'jns_klm') ?>
                            <?= form_dropdown(
                                'jns_klm',
                                ['' => 'Pilih Jenis Kelamin', 'L' => 'Laki-laki', 'P' => 'Perempuan'],
                                old('jns_klm'),
                                'class="form-control rounded-0 ' . (session('validation_errors.jns_klm') ? 'is-invalid' : '') . '"'
                            ) ?>
                            <?php if (session('validation_errors.jns_klm')): ?>
                                <div class="invalid-feedback">
                                    <?= session('validation_errors.jns_klm') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Tempat Lahir <span class="text-danger">*</span>', 'tmp_lahir') ?>
                            <?= form_input([
                                'type'        => 'text',
                                'class'       => 'form-control rounded-0 ' . (session('validation_errors.tmp_lahir') ? 'is-invalid' : ''),
                                'id'          => 'tmp_lahir',
                                'name'        => 'tmp_lahir',
                                'value'       => old('tmp_lahir'),
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
                                'type'        => 'date',
                                'class'       => 'form-control rounded-0 ' . (session('validation_errors.tgl_lahir') ? 'is-invalid' : ''),
                                'id'          => 'tgl_lahir',
                                'name'        => 'tgl_lahir',
                                'value'       => '08/17/1945',
                                // 'placeholder' => 'yyyy-mm-dd'
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
                                'name'        => 'alamat',
                                'class'       => 'form-control rounded-0 ' . (session('validation_errors.alamat') ? 'is-invalid' : ''),
                                'rows'        => 3,
                                'value'       => old('alamat'),
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
                                'type'        => 'text',
                                'class'       => 'form-control rounded-0',
                                'id'          => 'no_hp',
                                'name'        => 'no_hp',
                                'placeholder' => 'Masukkan nomor HP'
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Pekerjaan', 'pekerjaan') ?>
                            <?= form_input([
                                'type'        => 'text',
                                'class'       => 'form-control rounded-0',
                                'id'          => 'pekerjaan',
                                'name'        => 'pekerjaan',
                                'placeholder' => 'Masukkan pekerjaan'
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
<?= form_close() ?>

<?= $this->section('js') ?>
<script>
    // Photos Patient Handling
    // Initialize camera when page loads
    window.addEventListener('load', function () {
        // Check if browser supports getUserMedia
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    const video = document.createElement('video');
                    video.style.width = '100%';
                    video.style.height = '100%';
                    video.style.objectFit = 'cover';
                    video.autoplay = true;

                    // Add video element to preview div
                    document.getElementById('camera-preview').appendChild(video);
                    video.srcObject = stream;

                    // Store stream globally to stop it later
                    window.stream = stream;
                })
                .catch(function (error) {
                    console.error("Camera error:", error);
                    alert("Could not access camera. Please check permissions.");
                });
        } else {
            alert("Sorry, your browser does not support camera access");
        }
    });

    // Function to capture photo
    function takeSnapshot() {
        const preview = document.getElementById('camera-preview');
        const result = document.getElementById('camera-result');
        const video = preview.querySelector('video');

        if (!video) return;

        // Create canvas to capture frame
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw video frame to canvas
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0);

        // Convert to base64
        const imageData = canvas.toDataURL('image/png');

        // Show captured photo
        document.getElementById('photo').src = imageData;
        document.getElementById('foto_pasien').value = imageData;

        // Hide preview, show result
        preview.style.display = 'none';
        result.style.display = 'block';

        // Stop camera stream
        if (window.stream) {
            window.stream.getTracks().forEach(track => track.stop());
        }
    }


    // KTP Camera handling
    let ktpCamera = null;

    // Initialize camera when page loads
    window.addEventListener('load', function () {
        // Request camera access
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                // Create video element
                let video = document.createElement('video');
                video.style.width = '100%';
                video.style.height = '100%';
                video.autoplay = true;

                // Add video stream to preview
                video.srcObject = stream;
                document.getElementById('camera-preview-ktp').appendChild(video);

                ktpCamera = video;
            })
            .catch(function (err) {
                console.error("Camera error: ", err);
                alert("Could not access camera");
            });
    });

    // Take photo function
    function takeKtpSnapshot() {
        if (!ktpCamera) {
            alert('Camera not ready');
            return;
        }

        // Create canvas
        let canvas = document.createElement('canvas');
        canvas.width = ktpCamera.videoWidth;
        canvas.height = ktpCamera.videoHeight;

        // Draw video frame to canvas
        let context = canvas.getContext('2d');
        context.drawImage(ktpCamera, 0, 0);

        // Convert to base64
        let imageData = canvas.toDataURL('image/png');

        // Show result
        document.getElementById('photo-ktp').src = imageData;
        document.getElementById('foto_ktp').value = imageData;

        // Hide preview, show result
        document.getElementById('camera-preview-ktp').style.display = 'none';
        document.getElementById('camera-result-ktp').style.display = 'block';
    }

    // Stop camera when leaving page
    window.addEventListener('beforeunload', function () {
        if (ktpCamera && ktpCamera.srcObject) {
            ktpCamera.srcObject.getTracks().forEach(track => track.stop());
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>