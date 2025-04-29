<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-08
 * 
 * Medical Record Transaction Form View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">Form Medical Checkup</h3>
    </div>
    <?= form_open('medrecords/trans/store', ['id' => 'form-medtrans']) ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <!-- Nama Pasien -->
                <div class="form-group">
                    <?= form_label('Nama Pasien<span class="text-danger">*</span>', 'nama_pasien') ?>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'nama_pasien',
                        'id' => 'nama_pasien',
                        'class' => 'form-control rounded-0',
                        'value' => $pasien->nama_pgl ?? '',
                        'readonly' => true
                    ]) ?>
                    <?= form_hidden('id_pasien', $pasien->id ?? '') ?>
                    <?= form_hidden('id_dft', $daftar->id ?? '') ?>
                </div>

                <!-- Tipe -->
                <div class="form-group">
                    <?= form_label('Tipe<span class="text-danger">*</span>', 'tipe') ?>
                    <?= form_dropdown('tipe', [
                        '' => '- Tipe -',
                        '1' => 'Rawat Jalan',
                        '3' => 'Laboratorium',
                        '4' => 'Radiologi'
                    ], $daftar->tipe ?? '', ['class' => 'form-control rounded-0', 'required' => true]) ?>
                </div>

                <!-- Klinik -->
                <div class="form-group">
                    <?= form_label('Klinik<span class="text-danger">*</span>', 'id_poli') ?>
                    <?= form_dropdown('id_poli', ['' => '- Pilih Klinik -'] + array_column($poliModel->findAll(), 'poli', 'id'), 
                        $daftar->id_poli ?? '', [
                        'class' => 'form-control rounded-0',
                        'required' => true
                    ]) ?>
                </div>

                <!-- Dokter -->
                <div class="form-group">
                    <?= form_label('Dokter<span class="text-danger">*</span>', 'id_dokter') ?>
                    <select name="id_dokter" class="form-control rounded-0" required>
                        <option value="">- Pilih Dokter -</option>
                        <?php foreach ($dokters as $dokter): ?>
                            <option value="<?= $dokter->id ?>"><?= esc($dokter->nama_pgl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Petugas -->
                <div class="form-group">
                    <?= form_label('Petugas', 'petugas') ?>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'petugas',
                        'class' => 'form-control rounded-0',
                        'value' => $user->first_name . ' ' . $user->last_name,
                        'readonly' => true
                    ]) ?>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Keluhan -->
                <div class="form-group">
                    <?= form_label('Keluhan', 'keluhan') ?>
                    <?= form_textarea([
                        'name' => 'keluhan',
                        'class' => 'form-control rounded-0',
                        'rows' => 3,
                        'placeholder' => 'Isikan keluhan pasien...'
                    ]) ?>
                </div>

                <!-- Vital Signs -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Suhu Tubuh', 'ttv_st') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_st',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Suhu tubuh...'
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Berat Badan', 'ttv_bb') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_bb',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Berat badan...'
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Tinggi Badan', 'ttv_tb') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_tb',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Tinggi badan...'
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Nadi', 'ttv_nadi') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_nadi',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Nadi...'
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Sistole', 'ttv_sistole') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_sistole',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Sistole...'
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Diastole', 'ttv_diastole') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_diastole',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Diastole...'
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Laju Napas', 'ttv_laju') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_laju',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Laju napas...'
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= form_label('Saturasi', 'ttv_saturasi') ?>
                            <?= form_input([
                                'type' => 'text',
                                'name' => 'ttv_saturasi',
                                'class' => 'form-control rounded-0',
                                'placeholder' => 'Saturasi...'
                            ]) ?>
                        </div>
                    </div>
                </div>

                <!-- Skala Nyeri -->
                <div class="form-group">
                    <?= form_label('Skala Nyeri', 'ttv_skala') ?>
                    <?= form_dropdown('ttv_skala', [
                        '' => '- Pilih Skala -',
                        '0' => 'Skala 0',
                        '1' => 'Skala 1',
                        '2' => 'Skala 2',
                        '3' => 'Skala 3',
                        '4' => 'Skala 4',
                        '5' => 'Skala 5'
                    ], '', ['class' => 'form-control rounded-0']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <?= anchor('medrecords/antrian', '<i class="fas fa-arrow-left mr-2"></i>Kembali', [
            'class' => 'btn btn-default rounded-0 float-left'
        ]) ?>
        <button type="submit" class="btn btn-primary rounded-0">
            <i class="fas fa-save mr-2"></i>Simpan
        </button>
    </div>
    <?= form_close() ?>
</div>

<script>
function deleteTindakan(id) {
    if (confirm('Apakah anda yakin ingin menghapus tindakan ini?')) {
        $.ajax({
            url: '<?= base_url('publik/deleteTindakan') ?>/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove the row
                    $('#row-tindakan-' + id).fadeOut(function() {
                        $(this).remove();
                    });
                    
                    // Show success message
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Gagal menghapus data');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                toastr.error('Error: ' + error);
            }
        });
    }
    return false;
}
</script>

<?= $this->endSection() ?> 