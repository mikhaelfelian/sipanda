<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Penjamin Index View
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
                        <a href="<?= base_url('master/penjamin/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <?= form_open(base_url('master/penjamin'), ['method' => 'get']) ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Penjamin</th>
                            <th>Persentase</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th colspan="2">
                                <?= form_input([
                                    'name' => 'search',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'placeholder' => 'Search penjamin/kode...',
                                    'value' => $search
                                ]) ?>
                            </th>
                            <th></th>
                            <th>
                                <select name="status" class="form-control form-control-sm rounded-0">
                                    <option value="">- Status -</option>
                                    <option value="1" <?= ($selectedStatus ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= ($selectedStatus ?? '') === '0' ? 'selected' : '' ?>>Non-Aktif</option>
                                </select>
                            </th>
                            <th>
                                <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penjamin as $key => $row): ?>
                            <tr>
                                <td class="text-center"><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td width="15%"><?= esc($row->kode) ?></td>
                                <td width="35%"><?= esc($row->penjamin) ?></td>
                                <td width="15%"><?= (float)$row->persen ?>%</td>
                                <td>
                                    <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                        <?= ($row->status == '1') ? 'Aktif' : 'Non-Aktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/penjamin/edit/$row->id") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/penjamin/delete/$row->id") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($penjamin)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
                <?= form_close() ?>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('penjamin', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 