<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-21
 * 
 * Supplier Trash View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/supplier') ?>" class="btn btn-sm btn-secondary rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-sm btn-danger rounded-0" onclick="restoreAll()">
                    <i class="fas fa-trash-restore"></i> Pulihkan Semua
                </button>
                <button type="button" class="btn btn-sm btn-danger rounded-0" onclick="deleteAllPermanent()">
                    <i class="fas fa-trash"></i> Hapus Permanen Semua
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/supplier/trash', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="35%">Nama</th>
                        <th width="15%" class="text-center">Tipe</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>
                            <?= form_input([
                                'name' => 'search',
                                'value' => $search,
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari...'
                            ]) ?>
                        </th>
                        <th></th>
                        <th class="text-center">
                            <?= form_dropdown(
                                'tipe',
                                [
                                    '' => '- Semua -',
                                    '1' => 'Instansi',
                                    '2' => 'Personal'
                                ],
                                $selectedTipe,
                                'class="form-control form-control-sm rounded-0"'
                            ) ?>
                        </th>
                        <th></th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($suppliers)): ?>
                        <?php $no = 1; foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($supplier->kode) ?></td>
                                <td><?= esc($supplier->nama) ?></td>
                                <td class="text-center"><?= $getTipeLabel($supplier->tipe) ?></td>
                                <td class="text-center"><?= $getStatusLabel($supplier->status) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/supplier/restore/{$supplier->id}") ?>"
                                            class="btn btn-success btn-sm rounded-0" title="Pulihkan">
                                            <i class="fas fa-trash-restore"></i>
                                        </a>
                                        <a href="<?= base_url("master/supplier/delete-permanent/{$supplier->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0" title="Hapus Permanen"
                                            onclick="return confirm('Data akan dihapus secara permanen. Lanjutkan?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('suppliers', 'adminlte_pagination') ?>
    </div>
</div>

<?= $this->section('js') ?>
<script>
function restoreAll() {
    if (confirm('Pulihkan semua data?')) {
        window.location.href = '<?= base_url("master/supplier/restore-all") ?>';
    }
}

function deleteAllPermanent() {
    if (confirm('Semua data akan dihapus secara permanen. Lanjutkan?')) {
        window.location.href = '<?= base_url("master/supplier/delete-all-permanent") ?>';
    }
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 