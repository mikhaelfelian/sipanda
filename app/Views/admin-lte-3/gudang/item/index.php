<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-01
 * 
 * Stock Items Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-boxes mr-1"></i>
            Data Item Stok
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive mt-3">
            <form action="<?= base_url('stock/items') ?>" method="GET">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-left">Kategori</th>
                            <th class="text-left">Merk</th>
                            <th class="text-left">Item</th>
                            <th class="text-right">Harga Beli</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <select class="form-control form-control-sm rounded-0" name="filter_kategori">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategoris as $kategori): ?>
                                        <option value="<?= $kategori->id ?>" 
                                                <?= isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == $kategori->id ? 'selected' : '' ?>>
                                                    <?= $kategori->kategori ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm rounded-0" name="filter_merk">
                                    <option value="">Pilih Merk</option>
                                    <?php foreach ($merks as $merk): ?>
                                        <option value="<?= $merk->id ?>"
                                                <?= isset($_GET['filter_merk']) && $_GET['filter_merk'] == $merk->id ? 'selected' : '' ?>>
                                                    <?= $merk->merk ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm rounded-0" 
                                       placeholder="Item" name="filter_item"
                                       value="<?= isset($_GET['filter_item']) ? htmlspecialchars($_GET['filter_item']) : '' ?>">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm rounded-0" 
                                       placeholder="Harga Beli" name="filter_harga"
                                       value="<?= isset($_GET['filter_harga']) ? htmlspecialchars($_GET['filter_harga']) : '' ?>">
                            </th>
                            <th></th>
                            <th class="text-center">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                    <a href="<?= base_url('stock/items') ?>" class="btn btn-sm btn-secondary rounded-0">
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php
                            $no = 1;
                            foreach ($items as $item):
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-left"><?= $item->kategori ?></td>
                                    <td class="text-left"><?= $item->merk ?></td>
                                    <td class="text-left">
                                        <?= strtoupper($item->kode) ?><br />
                                        <?= strtoupper($item->item) ?><br />
                                        <small><b><?= format_angka_rp($item->harga_beli) ?></b></small><br />
                                        <?php if (!empty($item->item_kand)) { ?>
                                            <small><i>(<?php echo strtolower($item->item_kand) ?>)</i></small><br />
                                        <?php } ?>
                                        <small><i><?php echo $item->item_alias ?></i></small>
                                        <?php if ($item->status_stok == '1'): ?>
                                            <br /><span class="badge badge-success">Stockable</span>
                                        <?php else: ?>
                                            <br /><span class="badge badge-warning">Non Stockable</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right"><?= format_angka_rp($item->harga_beli) ?></td>
                                    <td class="text-center">
                                        <?= $itemStockModel->getTotalStockByItem($item->id) ?? 0 ?>
                                        <?= $item->nama_satuan ?? '' ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?= base_url('stock/items/detail/' . $item->id) ?>"
                                               class="btn btn-info btn-sm rounded-0" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-5">
                <div class="dataTables_info" role="status" aria-live="polite">
                    Showing <?= ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1 ?> to 
                    <?= min($pager->getCurrentPage() * $pager->getPerPage(), $pager->getTotal()) ?> of 
                    <?= $pager->getTotal() ?> entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers">
                    <?= $pager->links() ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>