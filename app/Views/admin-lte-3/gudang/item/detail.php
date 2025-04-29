<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-29
 * 
 * Stock Item Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- Title and Breadcrumbs -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Stok Detail</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><?= anchor('dashboard', 'Dashboard') ?></li>
                    <li class="breadcrumb-item"><?= anchor('stock/items', 'Data Stok') ?></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <!-- Data Item Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Data Item</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Kode</label>
                            <?= form_input([
                                'class' => 'form-control rounded-0',
                                'value' => esc($item->kode ?? ''),
                                'readonly' => true
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label>Item</label>
                            <?= form_input([
                                'class' => 'form-control rounded-0',
                                'value' => esc($item->item ?? ''),
                                'readonly' => true
                            ]) ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <?= form_input([
                                        'class' => 'form-control rounded-0',
                                        'value' => ($itemStockModel->getTotalStockByItem($item->id) ?? 0),
                                        'readonly' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <?= form_input([
                                        'class' => 'form-control rounded-0',
                                        'value' => esc($item->satuan ?? 'PCS'),
                                        'readonly' => true
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <?= anchor('stock/items', '<i class="fas fa-arrow-left mr-2"></i>Kembali', [
                            'class' => 'btn btn-default rounded-0'
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <!-- Data Stok Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-warehouse mr-2"></i>Data Stok Per Gudang</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Gudang</th>
                                        <th class="text-center">Stok</th>
                                        <th colspan="2" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stockDetails as $stock): ?>
                                        <tr>
                                            <td><?= esc($stock->gudang) ?></td>
                                            <td class="text-center">
                                                <?= form_open('stock/items/update/' . $stock->id, ['class' => 'd-inline-flex align-items-center']) ?>
                                                <?= form_input([
                                                    'type' => 'number',
                                                    'name' => 'jumlah',
                                                    'class' => 'form-control form-control-sm rounded-0 text-center mr-2',
                                                    'value' => $stock->jml,
                                                    'style' => 'width: 100px;'
                                                ]) ?>
                                                <span
                                                    class="mr-2"><?= isset($item->nama_satuan) ? $item->nama_satuan : 'PCS' ?></span>
                                                <?= form_button([
                                                    'type' => 'submit',
                                                    'class' => 'btn btn-primary btn-sm rounded-0',
                                                    'title' => 'Update Stok',
                                                    'content' => '<i class="fas fa-save"></i>'
                                                ]) ?>
                                                <?= form_close() ?>
                                            </td>
                                            <td>
                                                <?php if (isset($stock->status_gd) && $stock->status_gd == '1'): ?>
                                                    <span class="badge badge-success">Utama</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <?php if (isset($item->status_item) && $item->status_item == '1'): ?>
                    <!-- Data Batch Card -->
                    <div class="card rounded-0">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Data Batch</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode Batch</th>
                                            <th>Tgl ED</th>
                                            <th class="text-center">Stok</th>
                                            <th class="text-center">Sisa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($batches as $batch): ?>
                                            <tr>
                                                <td><?= esc($batch->kode_batch) ?></td>
                                                <td><?= tgl_indo($batch->tgl_ed) ?></td>
                                                <td class="text-center"><?= $batch->jml ?></td>
                                                <td class="text-center"><?= $batch->jml_sisa ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title"><i class="fas fa-history mr-2"></i>Riwayat Stok</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Gudang</th>
                                        <th class="text-center">Jml</th>
                                        <th>Satuan</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <select class="form-control form-control-sm rounded-0" id="filter-gudang">
                                                <option value="">Semua Gudang</option>
                                                <?php foreach ($gudangs as $gudang): ?>
                                                    <option value="<?= $gudang->id ?>"><?= $gudang->gudang ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th>
                                            <input type="text" class="form-control form-control-sm rounded-0"
                                                id="filter-keterangan" placeholder="Keterangan">
                                        </th>
                                        <th>
                                            <select class="form-control form-control-sm rounded-0" id="filter-status">
                                                <option value="">Semua Status</option>
                                                <option value="1">Stok Masuk Pembelian</option>
                                                <option value="2">Stok Masuk</option>
                                                <option value="3">Stok Masuk Retur Jual</option>
                                                <option value="4">Stok Keluar Penjualan</option>
                                                <option value="5">Stok Keluar Retur Beli</option>
                                                <option value="6">SO</option>
                                                <option value="7">Stok Keluar</option>
                                                <option value="8">Mutasi Antar Gudang</option>
                                            </select>
                                        </th>
                                        <th>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary btn-sm rounded-0"
                                                    id="btn-filter">
                                                    <i class="fas fa-search"></i> Cari
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm rounded-0"
                                                    id="btn-reset">
                                                    <i class="fas fa-undo"></i> Reset
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($itemHists as $hist): ?>
                                        <tr>
                                            <td><?= $hist->gudang ?></td>
                                            <td class="text-center"><?= $hist->jml ?></td>
                                            <td><?= $hist->satuan ?></td>
                                            <td><?= $hist->keterangan ?></td>
                                            <td>
                                                <?php 
                                                    $status = statusHist($hist->status);
                                                    echo "<span class='badge badge-{$status['badge']}'>{$status['label']}</span>";
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($hist->status == '2' || $hist->status == '7'): ?>
                                                    <a href="<?= base_url('stock/items/delete_hist/' . $hist->id) ?>"
                                                        class="btn btn-danger btn-sm rounded-0"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <span class="row-count mt-2 text-muted" id="row-count"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>