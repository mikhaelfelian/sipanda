<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-14
 * 
 * Pasien Trash View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/pasien') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
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
                </thead>
                <tbody>
                    <?php if (!empty($pasiens)): ?>
                        <?php
                        $start = ($currentPage - 1) * $perPage + 1;
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
                                        <a href="<?= base_url("master/pasien/restore/{$pasien->id}") ?>"
                                            class="btn btn-info btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin memulihkan data ini?')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="<?= base_url("master/pasien/delete_permanent/{$pasien->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('PERHATIAN! Data yang dihapus tidak dapat dikembalikan. Lanjutkan?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('pasien', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?> 