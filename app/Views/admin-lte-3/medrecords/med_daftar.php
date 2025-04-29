<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-06
 * 
 * Patient Registration View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>

<?php if (!isset($_GET['tipe_pas'])): ?>
    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>&nbsp;</h3>
                    <p>PASIEN LAMA</p>
                </div>
                <div class="icon">

                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="<?= base_url('medrecords/daftar?tipe_pas=1') ?>" class="small-box-footer">DAFTAR PASIEN LAMA <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>&nbsp;</h3>
                    <p>PASIEN BARU</p>


                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <a href="<?= base_url('medrecords/daftar?tipe_pas=2') ?>" class="small-box-footer">DAFTAR PASIEN BARU <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <!-- ./col -->
<?php else: ?>
    <?php
    switch ($_GET['tipe_pas']) {
        case '1':
            $id_pasien = $_GET['id_pasien'] ?? null;

            if (isset($id_pasien)) {
                echo form_open('medrecords/reg/store');
                echo form_hidden('id_pasien', $pasien->id);
                echo form_hidden('tipe_pas', $_GET['tipe_pas']);
                echo view('admin-lte-3/medrecords/med_daftar_pas_lama', ['id_pasien' => $id_pasien]);
                echo form_close();
            } else {
                echo view('admin-lte-3/medrecords/med_daftar_pas_cari');
            }
            break;
        case '2':
            echo form_open('medrecords/reg/store');
            echo view('admin-lte-3/medrecords/med_daftar_pas_baru');
            echo form_close();
            break;

        default:
            $title = 'Foto Pasien';
            break;
    }
?>
<?php endif; ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });

    <?php if (isset($_GET['tipe_pas'])): ?>
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
    <?php endif ?>
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>