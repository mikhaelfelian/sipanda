<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/obat/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <a href="<?= base_url('master/obat/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="<?= base_url('master/obat/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                            <i class="fas fa-trash"></i> Sampah (<?= $trashCount ?>)
                        </a>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
             
            <div class="card-body table-responsive">
                <?= form_open(base_url('master/obat'), ['method' => 'get']) ?>
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
                        <tr>
                            <th></th>
                            <th>
                                <select name="kategori" class="form-control form-control-sm rounded-0">
                                    <option value="">- Kategori -</option>
                                <?php foreach($kategoriList ?? [] as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($selectedKategori ?? '') == $value ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th>
                                <select name="merk" class="form-control form-control-sm rounded-0">
                                    <option value="">- Merk -</option>
                                    <?php foreach($merkList as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($selectedMerk ?? '') == $value ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th>
                                <?= form_input([
                                    'name' => 'item',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'placeholder' => 'Filter item...'
                                ]) ?>
                            </th>
                            <th>
                                <?= form_input([
                                    'name' => 'harga_beli',
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
                        <?php foreach ($obat as $key => $row): ?>
                            <tr>
                                <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td><?= $row->kategori ?></td>
                                <td><?= $row->merk ?></td>
                                <td>
                                    <?= $row->item.br(); ?>
                                    <?php if (!empty($row->jenis)): ?>
                                        <small><i><?= $row->jenis ?></i></small><?=br(); ?>
                                    <?php endif; ?>
                                    <small><i><?= $row->kode ?></i></small><?=br(); ?>
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
                                        <a href="<?= base_url("master/obat/edit/$row->id") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/obat/delete/$row->id") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
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
                <?= form_close() ?>
            </div>
            <!-- /.card-body -->
            <?php if ($pager): ?>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        <?= $pager->links('obat', 'adminlte_pagination') ?>
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