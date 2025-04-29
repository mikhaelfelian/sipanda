<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * ICD Index View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= base_url('master/icd/create') ?>" class="btn btn-sm btn-primary rounded-0">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
                <a href="<?= base_url('master/icd/export') ?>?<?= $_SERVER['QUERY_STRING'] ?>"
                    class="btn btn-sm btn-success rounded-0">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= form_open('master/icd', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="15%">ICD</th>
                        <th width="30%">Diagnosa (EN)</th>
                        <th width="30%">Diagnosa (ID)</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th colspan="4">
                            <?= form_input([
                                'name' => 'search',
                                'value' => $search,
                                'class' => 'form-control form-control-sm rounded-0',
                                'placeholder' => 'Cari data...'
                            ]) ?>
                        </th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($icds)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($icds as $icd):
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?>.</td>
                                <td><?= esc($icd->kode) ?></td>
                                <td><?= esc($icd->icd) ?></td>
                                <td><?= esc($icd->diagnosa_en) ?></td>
                                <td><?= esc($icd->diagnosa_id) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("master/icd/edit/{$icd->id}") ?>"
                                            class="btn btn-warning btn-sm rounded-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url("master/icd/delete/{$icd->id}") ?>"
                                            class="btn btn-danger btn-sm rounded-0"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('icd', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?> 