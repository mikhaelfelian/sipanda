<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/tindakan/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <a href="<?= base_url('master/tindakan/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="<?= base_url('master/tindakan/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                            <i class="fas fa-trash"></i> Sampah (<?= $trashCount ?>)
                        </a>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
             
            <div class="card-body table-responsive">
                <?= form_open(base_url('master/tindakan'), ['method' => 'get']) ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Item</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <?= form_input([
                                    'name' => 'item',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'placeholder' => 'Filter item...'
                                ]) ?>
                            </th>
                            <th>
                                <?= form_input([
                                    'name' => 'harga_jual',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'placeholder' => 'Filter harga...'
                                ]) ?>
                            </th>
                            <th>
                                <?= form_dropdown('status', [
                                    '' => 'Semua',
                                    '1' => 'Aktif',
                                    '0' => 'Tidak Aktif'
                                ], $selectedStatus ?? '', ['class' => 'form-control form-control-sm rounded-0']) ?>
                            </th>
                            <th>
                                <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tindakan as $key => $row): ?>
                            <tr>
                                <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td>
                                    <?= $row->item.br(); ?>
                                    <small><i><?= $row->kode ?></i></small>
                                    <?php if (!empty($row->item_alias)): ?>
                                        <?=br();?>
                                        <small class="text-muted"><i>Alias: <?= $row->item_alias ?></i></small>
                                    <?php endif ?>
                                </td>
                                <td><?= format_angka_rp($row->harga_jual) ?></td>
                                <td>
                                    <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                        <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/tindakan/edit/$row->id") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/tindakan/delete/$row->id") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($tindakan)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
                <?= form_close() ?>
            </div>
            <!-- /.card-body -->
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('tindakan', 'adminlte_pagination') ?>
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