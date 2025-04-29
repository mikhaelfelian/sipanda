<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-14
 * 
 * Pasien Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-3">
        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                        src="<?= !empty($pasien->file_foto) ? base_url($pasien->file_foto) : base_url('assets/theme/admin-lte-3/dist/img/user4-128x128.jpg') ?>"
                        alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= esc($pasien->nama_pgl) ?></h3>
                <p class="text-muted text-center"><?= esc($pasien->kode) ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>NIK</b> <a class="float-right"><?= esc($pasien->nik) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Jenis Kelamin</b> <a class="float-right"><?= jns_klm($pasien->jns_klm) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Tanggal Lahir</b> <a class="float-right"><?= usia_lkp($pasien->tgl_lahir) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>No. HP</b> <a class="float-right"><?= esc($pasien->no_hp) ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Poin</b> <a class="float-right">0</a>
                    </li>
                </ul>
                <div class="d-flex justify-content-between mt-3">
                    <?php if ($hasUser): ?>
                        <a href="<?= base_url('master/pasien/reset_user/' . $pasien->id) ?>" 
                           class="btn btn-warning rounded-0"
                           onclick="return confirm('Apakah anda yakin ingin mereset user ini?')">
                            <i class="fas fa-sync-alt"></i> Reset User »
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('master/pasien/create_user/' . $pasien->id) ?>" 
                           class="btn btn-primary rounded-0">
                            <i class="fas fa-user-plus"></i> Buat User »
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($pasien->file_foto)): ?>
                        <a href="<?= base_url('master/pasien/delete_photo/' . $pasien->id) ?>" 
                           class="btn btn-danger rounded-0"
                           onclick="return confirm('Apakah anda yakin ingin menghapus foto ini?')">
                            <i class="fas fa-times"></i> Hapus Foto »
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- KTP Image -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">KTP</h3>
            </div>
            <div class="card-body box-profile">
                <img src="<?= !empty($pasien->file_ktp) ? base_url($pasien->file_ktp) : base_url('assets/theme/admin-lte-3/dist/img/photo1.png') ?>"
                    class="img-fluid" alt="KTP">
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#detail" data-toggle="tab">Detail</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="detail">
                        <strong>Alamat</strong>
                        <p class="text-muted"><?= esc($pasien->alamat) ?></p>
                        <hr>

                        <strong>Alamat Domisili</strong>
                        <p class="text-muted"><?= esc($pasien->alamat_domisili) ?></p>
                        <hr>

                        <strong>RT/RW</strong>
                        <p class="text-muted"><?= esc($pasien->rt) ?>/<?= esc($pasien->rw) ?></p>
                        <hr>

                        <strong>Kelurahan</strong>
                        <p class="text-muted"><?= esc($pasien->kelurahan) ?></p>
                        <hr>

                        <strong>Kecamatan</strong>
                        <p class="text-muted"><?= esc($pasien->kecamatan) ?></p>
                        <hr>

                        <strong>Kota</strong>
                        <p class="text-muted"><?= esc($pasien->kota) ?></p>
                        <hr>

                        <strong>Pekerjaan</strong>
                        <p class="text-muted"><?= esc($pasien->pekerjaan) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>