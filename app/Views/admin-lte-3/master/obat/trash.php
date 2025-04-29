<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/obat') ?>" class="btn btn-sm btn-secondary rounded-0">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="col-md-6">
                        <?= form_open('', ['method' => 'get', 'class' => 'float-right']) ?>
                        <div class="input-group input-group-sm">
                            <?= form_input([
                                'name' => 'keyword',
                                'class' => 'form-control rounded-0',
                                'value' => $keyword ?? '',
                                'placeholder' => 'Cari...'
                            ]) ?>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary rounded-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Item</th>
                            <th>Harga Beli</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($obat as $key => $row): ?>
                            <tr>
                                <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td><?= $row->kategori ?></td>
                                <td><?= $row->merk ?></td>
                                <td>
                                    <small><i><?= $row->kode ?></i></small><?=br(); ?>
                                    <?= $row->item.br(); ?>
                                    <small><b><?= format_angka_rp($row->harga_jual) ?></b></small>
                                    <?php if (!empty($row->item_alias)): ?>
                                        <?=br();?>
                                        <small class="text-muted"><i>Alias: <?= $row->item_alias ?></i></small>
                                    <?php endif ?>
                                    <?php if (!empty($row->item_kand)): ?>
                                        <?=br();?>
                                        <small class="text-muted"><i>Kandungan: <?= $row->item_kand ?></i></small>
                                    <?php endif ?>
                                    <?= isStockable($row->status_stok) ?>
                                </td>
                                <td><?= format_angka_rp($row->harga_beli) ?></td>
                                <td>
                                    <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                        <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/obat/restore/$row->id") ?>"
                                            class="btn btn-success btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin memulihkan data ini?')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="<?= base_url("master/obat/delete_permanent/$row->id") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus permanen data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($obat)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('default', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<?= $this->endSection() ?> 