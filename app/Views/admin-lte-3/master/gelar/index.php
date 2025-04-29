<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2024-01-13
 * 
 * Gelar Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/gelar/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <?= form_open(base_url('master/gelar'), ['method' => 'get']) ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Gelar</th>
                            <th>Keterangan</th>
                            <th width="100">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <?= form_input([
                                    'name' => 'search',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'placeholder' => 'Filter gelar/keterangan...',
                                    'value' => $search
                                ]) ?>
                            </th>
                            <th></th>
                            <th>
                                <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gelar as $key => $row): ?>
                            <tr>
                                <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td><?= esc($row->gelar) ?></td>
                                <td><?= esc($row->keterangan) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/gelar/edit/{$row->id}") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/gelar/delete/{$row->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($gelar)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
                <?= form_close() ?>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('gelar', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 