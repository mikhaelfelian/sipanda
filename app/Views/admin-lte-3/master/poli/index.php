<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Poli Index View
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
                        <a href="<?= base_url('master/poli/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <?= form_open(base_url('master/poli'), ['method' => 'get']) ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Poli</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>
                                <?= form_input([
                                    'name' => 'search',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'placeholder' => 'Search poli/kode...',
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
                        <?php foreach ($poli as $key => $row): ?>
                            <tr>
                                <td class="text-center"><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td width="10%"><?= esc($row->kode) ?></td>
                                <td width="45%"><?= esc($row->poli) ?></td>
                                <td width="20%"><?= esc($row->keterangan) ?></td>
                                <td>
                                    <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                        <?= ($row->status == '1') ? 'Aktif' : 'Non-Aktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/poli/edit/$row->id") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/poli/delete/$row->id") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($poli)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
                <?= form_close() ?>
            </div>
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('poli', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 