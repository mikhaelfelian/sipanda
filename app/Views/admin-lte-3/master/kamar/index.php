<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Kamar Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/kamar/create') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/kamar', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode</th>
                        <th width="20%">Nama Kamar</th>
                        <th width="15%">Jumlah Terisi</th>
                        <th width="15%">Kapasitas</th>
                        <th width="15%">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="4">
                            <?= form_input([
                                'name' => 'search',
                                'value' => $search,
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari data...'
                            ]) ?>
                        </th>
                        <th>
                            <?= form_dropdown(
                                'status',
                                [
                                    '' => 'Semua Status',
                                    '1' => 'Aktif',
                                    '0' => 'Tidak Aktif'
                                ],
                                $status,
                                'class="form-control form-control-sm rounded-0"'
                            ) ?>
                        </th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($kamars)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($kamars as $kamar):
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?>.</td>
                                <td><?= esc($kamar->kode) ?></td>
                                <td><?= esc($kamar->kamar) ?></td>
                                <td><?= esc($kamar->jml) ?></td>
                                <td><?= esc($kamar->jml_max) ?></td>
                                <td><?= $getStatusLabel($kamar->status) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/kamar/edit/{$kamar->id}") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/kamar/delete/{$kamar->id}") ?>"
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
        <?= $pager->links('kamar', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?> 