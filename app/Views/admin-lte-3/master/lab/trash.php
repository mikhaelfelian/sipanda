<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-13
 * 
 * Lab Trash View
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
                        <a href="<?= base_url('master/lab') ?>" class="btn btn-sm btn-default rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <?= form_open(base_url('master/lab/trash'), ['method' => 'get']) ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Item</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Dihapus pada</th>
                            <th width="100">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm rounded-0" name="item" 
                                    value="<?= esc($search) ?>" placeholder="Cari...">
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="<?= base_url('master/lab/trash') ?>" class="btn btn-sm btn-secondary rounded-0">
                                    <i class="fas fa-times"></i>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lab as $key => $row): ?>
                            <tr>
                                <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td><?= $row->kode ?></td>
                                <td>
                                    <?= $row->item.br(); ?>
                                    <?php if (!empty($row->item_alias)): ?>
                                        <small class="text-muted"><i>Alias: <?= $row->item_alias ?></i></small>
                                    <?php endif ?>
                                    <?php if (!empty($row->item_kand)): ?>
                                        <?=br();?>
                                        <small class="text-muted"><i>Keterangan: <?= $row->item_kand ?></i></small>
                                    <?php endif ?>
                                </td>
                                <td><?= format_angka_rp($row->harga_jual) ?></td>
                                <td>
                                    <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                        <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td><?= $row->deleted_at ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/lab/restore/$row->id") ?>"
                                            class="btn btn-success btn-sm rounded-0"
                                            onclick="return confirm('Pulihkan data ini?')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="<?= base_url("master/lab/delete_permanent/$row->id") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Hapus permanen data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($lab)): ?>
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
                        <?= $pager->links('lab', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 