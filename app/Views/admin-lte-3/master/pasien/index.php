<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-14
 * 
 * Pasien Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/pasien/create') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
                <a href="<?= base_url('master/pasien/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>"
                    class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="<?= base_url('master/pasien/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                    <i class="fas fa-trash"></i> Sampah
                    <span class="badge badge-light"><?= $trashCount ?></span>
                </a>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive mt-3">
            <?= form_open('master/pasien', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">No. RM</th>
                        <th class="text-left">Nama</th>
                        <th class="text-center">L/P</th>
                        <th class="text-center">No HP</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="text-left">
                            <?= form_input(['name' => 'no_rm', 'placeholder' => 'No. RM ...', 'value' => $search, 'class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-left">
                            <?= form_input(['name' => 'pasien', 'placeholder' => 'Pasien ...', 'value' => $search, 'class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-center">
                            <?= form_dropdown('jns_klm', ['' => '- Pilih -', 'L' => 'Laki-laki', 'P' => 'Perempuan'], $search, ['class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-center">
                            <?= form_input(['name' => 'no_hp', 'placeholder' => 'No HP ...', 'value' => $search, 'class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-filter"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pasiens)): ?>
                        <?php
                        $start = ($page - 1) * $perPage + 1;
                        foreach ($pasiens as $index => $pasien):
                            ?>
                            <tr>
                                <td class="text-center"><?= $start + $index ?>.</td>
                                <td class="text-left"><?= esc($pasien->kode) ?></td>
                                <td class="text-left">
                                    <b><?= esc($pasien->nama) ?></b><?= br() ?>
                                    <small><?= esc($pasien->nik) ?></small><?= br() ?>
                                    <small><?= usia_lkp($pasien->tgl_lahir) ?></small><?= br() ?>
                                    <small><i><?= esc($pasien->alamat) ?></i></small>
                                </td>
                                <td class="text-center"><?= jns_klm($pasien->jns_klm) ?></td>
                                <td class="text-center"><?= esc($pasien->no_hp) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/pasien/detail/{$pasien->id}") ?>"
                                            class="btn btn-info btn-sm rounded-0">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url("master/pasien/edit/{$pasien->id}") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/pasien/delete/{$pasien->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('pasien', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?>