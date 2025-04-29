<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('master/radiologi/create') ?>" class="btn btn-sm btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Tambah Data
                        </a>
                        <a href="<?= base_url('master/radiologi/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-sm btn-success rounded-0">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="<?= base_url('master/radiologi/trash') ?>" class="btn btn-sm btn-danger rounded-0">
                            <i class="fas fa-trash"></i> Sampah (<?= $trashCount ?>)
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <?= form_open(base_url('master/radiologi'), ['method' => 'get']) ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Item</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <select name="kategori" class="form-control form-control-sm rounded-0">
                                    <option value="">- Kategori -</option>
                                    <?php foreach($kategoriList as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($selectedKategori ?? '') == $value ? 'selected' : '' ?>>
                                            <?= esc($label) ?>
                                        </option>
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
                                    'placeholder' => 'Filter item...',
                                    'value' => service('request')->getGet('item')
                                ]) ?>
                            </th>
                            <th>
                                <?= form_input([
                                    'name' => 'harga_jual',
                                    'class' => 'form-control form-control-sm rounded-0',
                                    'placeholder' => 'Filter harga...',
                                    'value' => service('request')->getGet('harga_jual')
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
                        <?php foreach ($radiologi as $key => $row): ?>
                            <tr>
                                <td><?= (($currentPage - 1) * $perPage) + $key + 1 ?></td>
                                <td><?= $row->kategori ?? '' ?></td>
                                <td><?= $row->merk ?? '' ?></td>
                                <td>
                                    <?= $row->item.br(); ?>
                                    <small><i><?= $row->kode ?></i></small><?=br(); ?>
                                    <small><b><?= format_angka_rp($row->harga_jual) ?></b></small>
                                    <?php if (!empty($row->item_alias)): ?>
                                        <?=br();?>
                                        <small class="text-muted"><i>Alias: <?= $row->item_alias ?></i></small>
                                    <?php endif ?>
                                    <?php if (!empty($row->item_kand)): ?>
                                        <?=br();?>
                                        <small class="text-muted"><i>Keterangan: <?= $row->item_kand ?></i></small>
                                    <?php endif ?>
                                    <?= isStockable($row->status_stok) ?>
                                </td>
                                <td><?= format_angka_rp($row->harga_jual) ?></td>
                                <td>
                                    <span class="badge badge-<?= ($row->status == '1') ? 'success' : 'danger' ?>">
                                        <?= ($row->status == '1') ? 'Aktif' : 'Tidak Aktif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/radiologi/edit/$row->id") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/radiologi/delete/$row->id") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        <?php if (empty($radiologi)): ?>
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
                        <?= $pager->links('radiologi', 'adminlte_pagination') ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 