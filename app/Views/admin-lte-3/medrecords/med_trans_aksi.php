<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-20
 * 
 * Medical Record Transaction Action View
 */
$csrf_token = csrf_token();
$csrf_hash = csrf_hash();
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">Medical Checkup</h3>
    </div>
    <div class="card-body">
        <!-- Patient Details -->
        <div class="row">
            <div class="col-3">
                <div class="text-center mb-4">
                    <img class="profile-user-img img-fluid img-circle"
                        src="<?= !empty($medrec->file_foto) ? base_url($medrec->file_foto) : base_url('assets/theme/admin-lte-3/dist/img/user4-128x128.jpg') ?>"
                        alt="Patient profile picture" style="width: 100px; height: 100px;">
                    <h4 class="mt-2 mb-0"><small><?= $medrec->nama_pasien ?></small></h4>
                    <p class="text-muted mb-0"><?= date('d-m-Y', strtotime($medrec->tgl_lahir)) ?></p>
                    <p class="text-muted"><?= $medrec->jns_klm == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                    <p class="mb-0">Poin: <?= $medrec->poin ?? '0' ?></p>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="<?= base_url('master/pasien/edit/' . $medrec->id_pasien) ?>"
                        class="btn btn-warning btn-sm flex-grow-1 mr-1">
                        <i class="fas fa-user-edit"></i> Ubah Pasien
                    </a>
                    <a href="<?= base_url('medrecords/patient/label/' . $medrec->id_pasien) ?>"
                        class="btn btn-info btn-sm flex-grow-1 mr-1" target="_blank">
                        <i class="fas fa-print"></i> Cetak Label
                    </a>
                    <a href="<?= base_url('medrecords/patient/cards/' . $medrec->id_pasien) ?>"
                        class="btn btn-success btn-sm flex-grow-1" target="_blank">
                        <i class="fas fa-id-card"></i> Kartu Pasien
                    </a>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="#" 
                        class="btn btn-info btn-app flex-grow-1 mr-1 rounded-0 btn-app">
                        <i class="fas fa-sync"></i> Posting
                    </a>
                    <a href="#"
                        class="btn btn-info btn-app flex-grow-1 mr-1 rounded-0 btn-app" target="_blank">
                        <i class="fas fa-print"></i> Cetak Nota
                    </a>
                    <a href="#"
                        class="btn btn-info btn-app flex-grow-1 rounded-0 btn-app" target="_blank">
                        <i class="fas fa-file-invoice-dollar"></i> Billing
                    </a>
                </div>
            </div>
            <div class="col-4">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tr>
                            <td style="width: 50px;"><small><b>TRX ID</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small><?php echo $medrec->id ?></small></td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>No. Register / Kunjungan</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small><?php echo $medrec->no_rm ?></small></td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>Penjamin</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small>Umum</small></td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>No. RM</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small><?php echo $medrec->no_pasien ?></small></td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>Poli</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small><?php echo $medrec->poli ?></small></td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>Petugas</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small><?php echo $medrec->no_pasien ?></small></td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>Dokter Utama</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small></small></td>
                        </tr>
                        <tr>
                            <td style="width: 100px;" colspan="3"><small><?php echo nbs(2) . $medrec->dokter ?></small>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>Tgl Masuk</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small><?php echo $medrec->tgl_masuk ?></small></td>
                        </tr>
                        <tr>
                            <td style="width: 50px;"><small><b>Tgl Selesai</b></small></td>
                            <td style="width: 2px;" class="text-center"><small>:</small></td>
                            <td style="width: 100px;"><small><?php echo $medrec->tgl_keluar ?></small></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-4">
                <table class="table table-sm">
                    <tr>
                        <td style="width: 75px;"><small>Suhu</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;"><small><?php echo $medrec->ttv_st ?> &deg;C</small></td>
                        <td style="width: 75px;"><small>BB</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;;"><small><?php echo $medrec->ttv_bb ?> Kg</small></td>
                    </tr>
                    <tr>
                        <td style="width: 75px;"><small>TB</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;"><small><?php echo $medrec->ttv_tb ?> Cm</small></td>
                        <td style="width: 75px;"><small>Nadi</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;"><small><?php echo $medrec->ttv_nadi ?> / Menit</small></td>
                    </tr>
                    <tr>
                        <td style="width: 75px;"><small>Sistole</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;"><small><?php echo $medrec->ttv_sistole ?> mmHg</small></td>
                        <td style="width: 75px;"><small>Diastole</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;;"><small><?php echo $medrec->ttv_diastole ?> mmHg</small></td>
                    </tr>
                    <tr>
                        <td style="width: 75px;"><small>Laju Nafas</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;"><small><?php echo $medrec->ttv_laju ?> / Menit</small></td>
                        <td style="width: 75px;"><small>Saturasi</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;"><small><?php echo $medrec->ttv_saturasi ?> %</small></td>
                    </tr>
                    <tr>
                        <td style="width: 75px;"><small>Nyeri</small></td>
                        <td style="width: 10px;" class="text-center"><small>:</small></td>
                        <td style="width: 80px;"><small><?php echo $medrec->ttv_skala ?></small></td>
                        <td style="width: 75px;"><small></small></td>
                        <td style="width: 10px;" class="text-center"><small></small></td>
                        <td style="width: 80px;"><small></small></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Existing Tabs Section -->
        <div class="row mt-4">
            <div class="col-sm-2">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-3 col-sm-2">
                <div class="nav flex-column nav-tabs h-100" id="vert-tabs" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="tab-pemeriksaan" data-toggle="pill" href="#content-pemeriksaan"
                        role="tab">
                        <i class="fas fa-stethoscope"></i> Pemeriksaan
                    </a>
                    <a class="nav-link" id="tab-tindakan" data-toggle="pill" href="#content-tindakan" role="tab">
                        <i class="fas fa-procedures"></i> Tindakan
                    </a>
                    <a class="nav-link" id="tab-rencana" data-toggle="pill" href="#content-rencana" role="tab">
                        <i class="fas fa-clipboard-list"></i> Rencana Awal
                    </a>
                    <a class="nav-link" id="tab-resep" data-toggle="pill" href="#content-resep" role="tab">
                        <i class="fas fa-prescription"></i> Resep
                    </a>
                    <a class="nav-link" id="tab-dokumen" data-toggle="pill" href="#content-dokumen" role="tab">
                        <i class="fas fa-file-medical"></i> Dokumen
                    </a>
                    <a class="nav-link" id="tab-penunjang" data-toggle="pill" href="#content-penunjang" role="tab">
                        <i class="fas fa-notes-medical"></i> Penunjang
                    </a>
                    <a class="nav-link" id="tab-radiologi" data-toggle="pill" href="#content-radiologi" role="tab">
                        <i class="fas fa-radiation"></i> Radiologi
                    </a>
                    <a class="nav-link" id="tab-laboratorium" data-toggle="pill" href="#content-laboratorium"
                        role="tab">
                        <i class="fas fa-flask"></i> Laboratorium
                    </a>
                    <a class="nav-link" id="tab-upload" data-toggle="pill" href="#content-upload" role="tab">
                        <i class="fas fa-upload"></i> Upload
                    </a>
                </div>
            </div>
            <div class="col-7 col-sm-9">
                <div class="tab-content" id="vert-tabs-tabContent">
                    <!-- Pemeriksaan Content -->
                    <div class="tab-pane fade show active" id="content-pemeriksaan" role="tabpanel">
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">
                                    DIAGNOSA ICD 10 - AN. <?= $medrec->nama_pasien ?>
                                    <small><i>(<?= usia($medrec->tgl_lahir) ?>)</i></small>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="search-icd">Diagnosa ICD 10</label>
                                    <div class="input-group">
                                        <select class="form-control select2" id="search-icd"
                                            data-placeholder="Isikan ICD 10 menggunakan bahasa inggris ...">
                                            <option value="">Pilih ICD 10...</option>
                                            <?php foreach ($sql_icd as $icd): ?>
                                                <option value="<?= $icd->id ?>" data-kode="<?= $icd->kode ?>"
                                                    data-icd="<?= $icd->icd ?>">
                                                    <?= $icd->kode ?> - <?= $icd->icd ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-info" type="button" id="btn-add-icd">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered" id="table-icd">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 50px">#</th>
                                                <th style="width: 150px">Kode</th>
                                                <th>Diagnosa</th>
                                                <th style="width: 100px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="icd-list">
                                            <!-- ICD items will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card rounded-0 mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    PEMERIKSAAN - <?= $medrec->nama_pasien ?>
                                    <small><i>(<?= usia($medrec->tgl_lahir) ?>)</i></small>
                                </h3>
                            </div>
                            <div class="card-body">
                                <form id="form-periksa">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="anamesa">Anamnesa</label>
                                                <textarea class="form-control rounded-0" id="anamesa"
                                                    placeholder="Isikan Anamnesa ..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="diagnosa">Diagnosa</label>
                                                <textarea class="form-control rounded-0" id="diagnosa"
                                                    placeholder="Isikan Diagnosa ..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pemeriksaan">Pemeriksaan</label>
                                                <textarea class="form-control rounded-0" id="pemeriksaan"
                                                    placeholder="Isikan Pemeriksaan ..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="program">Program</label>
                                                <textarea class="form-control rounded-0" id="program"
                                                    placeholder="Isikan Program (Khusus Dokter) ..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="keluhan">Keluhan</label>
                                                <textarea class="form-control rounded-0" id="keluhan"
                                                    placeholder="Isikan Keluhan ..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="alergi">Alergi</label>
                                                <textarea class="form-control rounded-0" id="alergi"
                                                    placeholder="Isikan Alergi ..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-0">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Other tab contents with similar structure -->
                    <div class="tab-pane fade" id="content-tindakan" role="tabpanel">
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">
                                    INPUT TINDAKAN & JASA - <?= $medrec->nama_pasien ?>
                                    <small><i>(<?= usia($medrec->tgl_lahir) ?>)</i></small>
                                </h3>
                            </div>
                            <div class="card-body">
                                <form id="form-tindakan" class="form-horizontal" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_trans" value="<?= $medrec->id ?>">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kode">Item</label>
                                                <select name="kode" id="kode" class="form-control select2 rounded-0"
                                                    required data-placeholder="Pilih Tindakan...">
                                                    <option value="">Pilih Tindakan...</option>
                                                    <?php foreach ($tindakan as $item): ?>
                                                        <option value="<?= $item->id ?>"
                                                            data-harga="<?= (float) $item->harga_jual ?>">
                                                            <?= $item->item ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ket">Ket</label>
                                                <input type="text" class="form-control rounded-0" id="ket" name="ket"
                                                    placeholder="Keterangan ..." autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jml">Jml</label>
                                                <input type="number" class="form-control rounded-0" id="jml" name="jml"
                                                    value="1" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="harga">Harga</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text rounded-0">Rp.</span>
                                                    </div>
                                                    <input type="text" class="form-control rounded-0 text-right"
                                                        id="harga" name="harga" value="0" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary float-right rounded-0">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card rounded-0">
                            <!-- Replace the empty card with this table -->
                            <div class="card rounded-0">
                                <div class="card-header">
                                    <h3 class="card-title">Item Jasa & Tindakan</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="table-tindakan">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px">No</th>
                                                    <th>Kode</th>
                                                    <th>Item</th>
                                                    <th>Keterangan</th>
                                                    <th style="width: 100px" class="text-center">Jumlah</th>
                                                    <th style="width: 100px" class="text-right">Harga</th>
                                                    <th style="width: 120px" class="text-right">Subtotal</th>
                                                    <th style="width: 80px" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-rencana" role="tabpanel">
                        <!-- Rencana content -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">Rencana Penatalaksanaan</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="rencana-penatalaksanaan">Rencana Penatalaksanaan *</label>
                                    <select class="form-control" id="rencana-penatalaksanaan"
                                        name="rencana_penatalaksanaan">
                                        <option value="">-- Pilih --</option>
                                        <option value="2">Rawat Inap</option>
                                        <option value="3">Pelayanan Laborat</option>
                                        <option value="4">Pelayanan radiologi</option>
                                        <option value="5">Medical Check Up</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="terapi-obat">Terapi Obat</label>
                                    <textarea class="form-control" id="terapi-obat" name="terapi_obat"
                                        rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="terapi-non-obat">Terapi Non Obat</label>
                                    <textarea class="form-control" id="terapi-non-obat" name="terapi_non_obat"
                                        rows="3"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary float-right rounded-0">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-resep" role="tabpanel">
                        <!-- Resep content -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="card rounded-0">
                                    <div class="card-header">
                                        <h3 class="card-title">Form Obat</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Hasil Data Yang Akan Dibuat</label>
                                            <select class="form-control">
                                                <option>obat Luar</option>
                                                <option>Proses Resep Didalam</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label>Item Obat</label>
                                                <button type="button" class="btn btn-info btn-sm" onclick="toastr.error('Error: Routes not found')">
                                                    <i class="fas fa-plus"></i> Buat Obat Racikan ?
                                                </button>
                                            </div>

                                            <div class="form-group">
                                                <label>Tampilkan Persediaan Obat ?</label>
                                                <select class="form-control">
                                                    <option>Tampilkan Semua Persediaan Obat</option>
                                                </select>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>Obat *</label>
                                                        <select class="form-control select2" id="obat" name="obat" style="width: 100%;">
                                                            <option value="">-- Pilih --</option>
                                                            <?php foreach ($items as $item): ?>
                                                                <option value="<?= $item->id ?>" 
                                                                        data-harga="<?= $item->harga_jual ?>"
                                                                        data-kode="<?= $item->kode ?>"
                                                                        data-nama="<?= $item->item ?>">
                                                                    <?= $item->kode ?> - <?= $item->item ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Jumlah Obat *</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Aturan Pemakaian Obat *</label>
                                                <select class="form-control">
                                                    <option>-- Pilih --</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Catatan Untuk Bagian Farmasi</label>
                                                <textarea class="form-control" rows="3"></textarea>
                                            </div>

                                            <button type="button" class="btn btn-success float-right">
                                                <i class="fas fa-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-dokumen" role="tabpanel">
                        <!-- Dokumen content -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">Dokumen</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label for="hasil" class="col-sm-3 col-form-label">Tipe</label>
                                            <div class="col-sm-9">
                                                <select id="tipe_surat" name="tipe_surat" class="form-control">
                                                    <option value="">[Tipe Surat]</option>
                                                    <option value="1">Surat Sehat</option>
                                                    <option value="2">Surat Sakit</option>
                                                    <option value="3">Surat Rawat Inap</option>
                                                    <option value="4">Surat Kontrol</option>
                                                    <option value="5">Surat Kelahiran</option>
                                                    <option value="6">Surat Kematian</option>
                                                    <option value="7">Surat Covid</option>
                                                    <option value="8">Surat Rujukan</option>
                                                    <option value="9">Surat Vaksin</option>
                                                    <option value="10">Surat Kehamilan</option>
                                                    <option value="14">Surat Layak Terbang</option>
                                                    <option value="13">Surat Ket. Bebas Narkoba</option>
                                                    <option value="15">Surat Ket. THT</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Surat Sehat Form -->
                                        <div id="1" class="divSurat">
                                            <div class="form-group row">
                                                <label for="inputTglSuratSehat" class="col-sm-3 col-form-label">Tgl
                                                    Surat</label>
                                                <div class="col-sm-9">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-alt"></i></span>
                                                        </div>
                                                        <?= form_input([
                                                            'id' => 'tgl_masuk_sht',
                                                            'name' => 'tgl_masuk',
                                                            'class' => 'form-control text-middle' . (!empty($hasError['pasien']) ? ' is-invalid' : ''),
                                                            'style' => 'vertical-align: middle;',
                                                            'placeholder' => 'Tgl Surat ...'
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputTinggi" class="col-sm-3 col-form-label">Tinggi</label>
                                                <div class="col-sm-3">
                                                    <?= form_input([
                                                        'id' => 'angka',
                                                        'name' => 'tb',
                                                        'class' => 'form-control' . (!empty($hasError['tinggi']) ? ' is-invalid' : ''),
                                                        'placeholder' => 'Tinggi ...'
                                                    ]) ?>
                                                </div>
                                                <label class="col-sm-6 col-form-label">cm</label>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputTD" class="col-sm-3 col-form-label">Tekanan
                                                    Darah</label>
                                                <div class="col-sm-3">
                                                    <?= form_input([
                                                        'name' => 'td',
                                                        'class' => 'form-control' . (!empty($hasError['darah']) ? ' is-invalid' : ''),
                                                        'placeholder' => 'Tekanan ...'
                                                    ]) ?>
                                                </div>
                                                <label class="col-sm-6 col-form-label">mmHg</label>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputBB" class="col-sm-3 col-form-label">Berat Badan</label>
                                                <div class="col-sm-3">
                                                    <?= form_input([
                                                        'id' => 'angka',
                                                        'name' => 'bb',
                                                        'class' => 'form-control' . (!empty($hasError['berat']) ? ' is-invalid' : ''),
                                                        'placeholder' => 'BB ...'
                                                    ]) ?>
                                                </div>
                                                <label class="col-sm-6 col-form-label">Kg</label>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Buta Warna</label>
                                                <div class="col-sm-9">
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-secondary active">
                                                            <input type="radio" name="bw" value="1" <?= isset($barang) && $barang->buta_warna == '1' ? 'checked' : '' ?>> +
                                                        </label>
                                                        <label class="btn btn-secondary">
                                                            <input type="radio" name="bw" value="0" <?= isset($barang) && $barang->buta_warna == '0' ? 'checked' : '' ?>> -
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label"></label>
                                                <div class="col-sm-9">
                                                    <?= form_input([
                                                        'name' => 'bw_ket',
                                                        'class' => 'form-control',
                                                        'placeholder' => 'Ket. Buta Warna ...'
                                                    ]) ?>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Hasil</label>
                                                <div class="col-sm-9">
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-secondary active">
                                                            <input type="radio" name="hasil" value="1"> Sehat
                                                        </label>
                                                        <label class="btn btn-secondary">
                                                            <input type="radio" name="hasil" value="0"> Tidak Sehat
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Surat Sakit Form -->
                                        <div id="2" class="divSurat">
                                            <!-- Similar pattern for Surat Sakit form -->
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="toastr.error('Error: Database connection failed')">
                                                        <i class="fas fa-save"></i> Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Continue with other divSurat sections -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-penunjang" role="tabpanel">
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">
                                    PEMERIKSAAN PENUNJANG - AN. <?= $medrec->nama_pasien ?? '' ?>
                                    <small><i>(<?= usia($medrec->tgl_lahir ?? '') ?>)</i></small>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- Left sidebar menu -->
                                        <div class="list-group">
                                            <a href="#" class="list-group-item list-group-item-action active d-flex justify-content-between align-items-center" data-toggle="tab" data-target="#tab-spirometri">
                                                <span><i class="fas fa-lungs"></i> Spirometri</span>
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-spirometri">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-toggle="tab" data-target="#tab-ekg">
                                                <span><i class="fas fa-heartbeat"></i> EKG</span>
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-ekg">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-toggle="tab" data-target="#tab-hrv">
                                                <span><i class="fas fa-wave-square"></i> HRV</span>
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-hrv">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-toggle="tab" data-target="#tab-audiometri">
                                                <span><i class="fas fa-assistive-listening-systems"></i> Audiometri</span>
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-audiometri">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <!-- Tab content -->
                                        <div class="tab-content">
                                            <!-- Spirometri tab -->
                                            <div class="tab-pane fade show active" id="tab-spirometri">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px">#</th>
                                                                <th>No.</th>
                                                                <th>Pemeriksaan Spirometri</th>
                                                                <th style="width: 100px">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="spirometri-list">
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- EKG tab -->
                                            <div class="tab-pane fade" id="tab-ekg">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px">#</th>
                                                                <th>No.</th>
                                                                <th>Pemeriksaan EKG</th>
                                                                <th style="width: 100px">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="ekg-list">
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- HRV tab -->
                                            <div class="tab-pane fade" id="tab-hrv">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px">#</th>
                                                                <th>No.</th>
                                                                <th>Pemeriksaan HRV</th>
                                                                <th style="width: 100px">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="hrv-list">
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Audiometri tab -->
                                            <div class="tab-pane fade" id="tab-audiometri">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px">#</th>
                                                                <th>No.</th>
                                                                <th>Pemeriksaan Audiometri</th>
                                                                <th style="width: 100px">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="audiometri-list">
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-radiologi" role="tabpanel">
                        <!-- Radiologi content -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">Data Radiologi</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Permintaan Radiologi</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-add-radiologi"
                                        onclick="toastr.error('SQL State Error: Database connection failed')">
                                        <i class="fas fa-plus"></i> Tambah Radiologi
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px">#</th>
                                                <th style="width: 150px">No.</th>
                                                <th>No. Sampel</th>
                                                <th style="width: 100px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="radiologi-list">
                                            <!-- Radiologi items will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-laboratorium" role="tabpanel">
                        <!-- Laboratorium content -->
                        <!-- Laboratorium content -->
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">Data Laboratorium</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Permintaan Laboratorium</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-add-lab"
                                        onclick="toastr.error('Error: Routes not found $pasien not found')">
                                        <i class="fas fa-plus"></i> Permintaan Lab
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px">#</th>
                                                <th style="width: 150px">No.</th>
                                                <th>Pemeriksaan</th>
                                                <th style="width: 150px">Tanggal</th>
                                                <th style="width: 150px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lab-list">
                                            <!-- Lab items will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="content-upload" role="tabpanel">
                        <div class="card rounded-0">
                            <div class="card-header">
                                <h3 class="card-title">Upload File</h3>
                            </div>
                            <div class="card-body">
                                <form id="uploadForm" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="judul">Title</label>
                                        <input type="text" id="judul" name="judul" class="form-control"
                                            placeholder="Enter file title">
                                    </div>
                                    <div class="form-group">
                                        <label for="keterangan">Description</label>
                                        <textarea id="keterangan" name="keterangan" class="form-control"
                                            placeholder="Enter file description"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Drag and drop a file here or click to select</label>
                                        <div id="dropZone" class="border border-primary rounded p-3 text-center"
                                            style="cursor: pointer;">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                            <p class="mb-0">Drop files here or click to browse</p>
                                            <input type="file" id="file" name="file" class="d-none"
                                                accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                    </div>
                                    <div id="fileInfo" class="mt-3">
                                        <!-- File information will be displayed here -->
                                    </div>
                                    <div class="progress mt-3 d-none">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100" style="width: 0%;">
                                            0%
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" id="uploadButton" class="btn btn-primary" disabled>
                                            <i class="fas fa-upload mr-1"></i> Upload File
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="<?= base_url('medrecords/rawat_jalan') ?>" class="btn btn-default btn-flat">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<?= $this->section('css') ?>
<style>
    .profile-user-img {
        border: 3px solid #adb5bd;
        margin: 0 auto;
        padding: 3px;
        object-fit: cover;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        // Initialize Bootstrap tabs
        $('#vert-tabs a:first').tab('show');

        // Format harga input with thousand separator
        $('#harga').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        // Initialize Select2
        $('#kode').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih Tindakan...',
            allowClear: true
        }).on('change', function () {
            var selectedOption = $(this).find('option:selected');
            var harga = selectedOption.data('harga');
            var satuan = selectedOption.data('satuan');
            $('#harga').val(harga || 0).trigger('input');
            // You can add a hidden input for satuan if needed
            // $('#satuan').val(satuan || '1');
        });

        // Configure toastr options
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };

        // Validate form
        function validateForm() {
            var isValid = true;
            var kode = $('#kode').val();
            var jml = $('#jml').val();
            var harga = $('#harga').val().replace(/[^\d]/g, '');

            if (!kode) {
                toastr.error('Pilih tindakan terlebih dahulu');
                isValid = false;
            }
            if (!jml || jml < 1) {
                toastr.error('Jumlah harus lebih dari 0');
                isValid = false;
            }
            if (!harga || harga < 1) {
                toastr.error('Harga harus lebih dari 0');
                isValid = false;
            }

            return isValid;
        }

        // Form submit handler
        $('#form-tindakan').on('submit', function (e) {
            e.preventDefault();

            // Validate form
            if (!validateForm()) {
                return false;
            }

            // Get form data
            var formData = new FormData(this);

            // Get unformatted harga value
            formData.set('harga', $('#harga').val().replace(/[^\d]/g, ''));

            // Show loading message
            toastr.info('Menyimpan data...');

            // Submit form via AJAX
            $.ajax({
                url: '<?= site_url('medrecords/cart_tindakan') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'text',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (responseText) {
                    try {
                        var response = JSON.parse(responseText);
                        if (response.success) {
                            toastr.success(response.message);
                            $('#form-tindakan')[0].reset();
                            $('#kode').val('').trigger('change');
                            loadTindakan();
                        } else {
                            toastr.error(response.message || 'Terjadi kesalahan saat menyimpan data');
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.log('Raw response:', responseText);
                        toastr.error('Terjadi kesalahan saat memproses response');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.log('Response Text:', xhr.responseText);

                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                    // Try to extract error message from HTML response
                    if (xhr.responseText.includes('Fatal error')) {
                        const matches = xhr.responseText.match(/<b>Fatal error<\/b>:(.*?)<br/);
                        if (matches && matches[1]) {
                            errorMessage = matches[1].trim();
                        }
                    }

                    toastr.error(errorMessage);
                }
            });
        });

        function loadTindakan() {
            $.ajax({
                url: '<?= site_url('publik/tindakan/' . $medrec->id) ?>',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var html = '';
                        var total = 0;

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function (item, index) {
                                total += parseInt(item.subtotal);
                                html += '<tr>' +
                                    '<td class="text-center">' + (index + 1) + '</td>' +
                                    '<td>' + item.kode + '</td>' +
                                    '<td>' + item.item + '</td>' +
                                    '<td>' + (item.keterangan || '-') + '</td>' +
                                    '<td class="text-center">' + parseFloat(item.jml) + '</td>' +
                                    '<td class="text-right">' + formatRupiah(item.harga) + '</td>' +
                                    '<td class="text-right">' + formatRupiah(item.subtotal) + '</td>' +
                                    '<td class="text-center">' +
                                    '<button type="button" class="btn btn-danger btn-sm" ' +
                                    'onclick="deleteTindakan(' + item.id + ')">' +
                                    '<i class="fas fa-trash"></i>' +
                                    '</button>' +
                                    '</td>' +
                                    '</tr>';
                            });

                            // Add total row
                            html += '<tr class="font-weight-bold">' +
                                '<td colspan="6" class="text-right">Total</td>' +
                                '<td class="text-right">' + formatRupiah(total) + '</td>' +
                                '<td></td>' +
                                '</tr>';
                        } else {
                            html = '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>';
                        }
                        $('#table-tindakan tbody').html(html);
                    } else {
                        toastr.error(response.message || 'Gagal memuat data tindakan');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error loading tindakan:', error);
                    toastr.error('Gagal memuat data tindakan');
                }
            });
        }

        function loadICD() {
            $.ajax({
                url: '<?= site_url('publik/icd/' . $medrec->id) ?>',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var html = '';

                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function (item, index) {
                                html += '<tr>' +
                                    '<td class="text-center">' + (index + 1) + '</td>' +
                                    '<td>' + item.kode + '</td>' +
                                    '<td>' + item.icd + '</td>' +
                                    '<td class="text-center">' +
                                    '<button type="button" class="btn btn-danger btn-sm" ' +
                                    'onclick="deleteICD(' + item.id + ')">' +
                                    '<i class="fas fa-trash"></i>' +
                                    '</button>' +
                                    '</td>' +
                                    '</tr>';
                            });
                        } else {
                            html = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
                        }
                        $('#table-icd tbody').html(html);
                    } else {
                        toastr.error(response.message || 'Gagal memuat data ICD');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error loading ICD:', error);
                    toastr.error('Gagal memuat data ICD');
                }
            });
        }

        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        function deleteTindakan(id) {
            if (confirm('Apakah Anda yakin ingin menghapus tindakan ini?')) {
                $.ajax({
                    url: '<?= site_url('medrecords/delete_tindakan/') ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            loadTindakan();
                        } else {
                            toastr.error(response.message || 'Gagal menghapus tindakan');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        toastr.error('Gagal menghapus tindakan');
                    }
                });
            }
            return false;
        }

        // Load tindakan on page load
        loadTindakan();
        loadICD();

        // Initialize Select2 for ICD dropdown
        $('#search-icd').select2({
            theme: 'bootstrap4',
            width: '100%',
            allowClear: true,
            placeholder: 'Isikan ICD 10 menggunakan bahasa inggris ...',
            language: {
                noResults: function () {
                    return "Data tidak ditemukan";
                }
            }
        });

        // Add ICD button click handler
        $('#btn-add-icd').on('click', function () {
            var selected = $('#search-icd').select2('data')[0];
            if (!selected || !selected.id) {
                toastr.error('Pilih diagnosa ICD terlebih dahulu');
                return;
            }

            var selectedOption = $('#search-icd option:selected');

            // Add to database via AJAX
            $.ajax({
                url: '<?= base_url('medrecords/cart_icd') ?>',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    med_id: '<?= $medrec->id ?>',
                    icd_id: selected.id,
                    kode: selectedOption.data('kode'),
                    icd: selectedOption.data('icd')
                },
                success: function (response) {
                    if (response.success) {
                        loadICD();
                        $('#search-icd').val('').trigger('change');
                        toastr.success('Diagnosa berhasil ditambahkan');
                    } else {
                        toastr.error(response.message || 'Gagal menambahkan diagnosa');
                    }
                },
                error: function (error) {
                    console.log('Error :', error);
                }
            });

        });

        $('#form-periksa').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url('medrecords/store_periksa/' . $medrec->id) ?>',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    id_user: '<?= $user->id ?>',
                    id_dokter: $('#id_dokter').val(),
                    diagnosa: $('#diagnosa').val(),
                    anamnesa: $('#anamesa').val(),
                    pemeriksaan: $('#pemeriksaan').val(),
                    program: $('#program').val(),
                    alergi: $('#alergi').val()
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error('Gagal menyimpan data');
                }
            });
        });

        // Get CSRF token from meta tag
        var csrf_name = '<?= csrf_token() ?>';
        var csrf_value = '<?= csrf_hash() ?>';

        var dropZone = $('#dropZone');
        var fileInput = $('#file');
        var fileInfo = $('#fileInfo');
        var progressBar = $('.progress');
        var uploadButton = $('#uploadButton');
        var selectedFile = null;

        // Handle file input change directly
        fileInput.on('change', function (e) {
            var file = this.files[0];
            if (file) {
                selectedFile = file;
                showFileInfo(file);
            }
        });

        // Handle drag and drop events
        dropZone
            .on('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('border-primary bg-light');
            })
            .on('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border-primary bg-light');
            })
            .on('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border-primary bg-light');

                var file = e.originalEvent.dataTransfer.files[0];
                if (file) {
                    selectedFile = file;
                    // Update the file input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput[0].files = dataTransfer.files;
                    showFileInfo(file);
                }
            });

        // Handle click on dropzone - use mousedown instead of click
        dropZone.on('mousedown', function (e) {
            e.preventDefault();
            e.stopPropagation();
            // Trigger file input click directly
            fileInput[0].click();
        });

        function showFileInfo(file) {
            // Validate file type
            var allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Invalid file type. Only JPG, PNG, and PDF files are allowed');
                resetUploadForm();
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5242880) {
                toastr.error('File size exceeds 5MB limit');
                resetUploadForm();
                return;
            }

            fileInfo.html(`
                <div class="alert alert-info">
                    <i class="fas fa-file mr-2"></i>
                    <strong>${file.name}</strong> (${formatFileSize(file.size)})
                    <button type="button" class="close" onclick="resetUploadForm()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);
            uploadButton.prop('disabled', false);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Handle upload button click
        uploadButton.on('mousedown', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!selectedFile) {
                toastr.error('Please select a file first');
                return;
            }
            uploadFile(selectedFile);
        });

        function uploadFile(file) {
            var formData = new FormData();
            formData.append('file', file);
            formData.append('judul', $('#judul').val() || file.name);
            formData.append('keterangan', $('#keterangan').val() || '');
            formData.append('status', '1');
            formData.append(csrf_name, csrf_value);

            progressBar.removeClass('d-none');
            uploadButton.prop('disabled', true);

            $.ajax({
                url: '<?= base_url('medrecords/upload/' . $medrec->id) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            $('.progress-bar').css('width', percent + '%').text(percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        resetUploadForm();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error('Upload failed: ' + error);
                },
                complete: function () {
                    uploadButton.prop('disabled', false);
                    progressBar.addClass('d-none');
                }
            });
        }

        function resetUploadForm() {
            selectedFile = null;
            fileInput.val('');
            fileInfo.empty();
            $('#judul').val('');
            $('#keterangan').val('');
            uploadButton.prop('disabled', true);
            progressBar.addClass('d-none');
            $('.progress-bar').css('width', '0%').text('0%');
        }

        // Make resetUploadForm available globally
        window.resetUploadForm = resetUploadForm;

        // Initialize select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih item pemeriksaan...',
            allowClear: true
        });

        // Handle item selection for Spirometri
        $('#item-spirometri').on('change', function() {
            var $selected = $(this).find('option:selected');
            if (!$selected.val()) return;

            var item = {
                id: $selected.val(),
                kode: $selected.data('kode'),
                nama: $selected.data('nama'),
                harga: $selected.data('harga')
            };

            // Add item to table
            var $tbody = $('#spirometri-list');
            var rowCount = $tbody.children().length + 1;
            
            var $row = $(`
                <tr data-id="${item.id}">
                    <td class="text-center">${rowCount}</td>
                    <td>
                        <strong>${item.kode}</strong><br>
                        ${item.nama}
                    </td>
                    <td class="text-right">
                        ${formatRupiah(item.harga)}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

            $tbody.append($row);
            $(this).val('').trigger('change');
        });

        // Handle delete button
        $(document).on('click', '.btn-delete', function() {
            $(this).closest('tr').remove();
            reorderTable('#spirometri-list');
        });

        // Helper functions
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        function reorderTable(tableId) {
            $(tableId + ' tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Handle add buttons
        $('#btn-add-spirometri, #btn-add-ekg, #btn-add-hrv, #btn-add-audiometri').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const type = $(this).closest('.list-group-item').data('target').replace('#tab-', '');
            showAddModal(type);
        });

        function showAddModal(type) {
            // Implementation will depend on your requirements
            toastr.info('Add ' + type + ' functionality to be implemented');
        }

        // Prevent button clicks from triggering tab change
        $('.list-group-item button').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>