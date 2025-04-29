<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * ICD Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Detail ICD</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Kode</th>
                                <td><?= esc($icd->kode) ?></td>
                            </tr>
                            <tr>
                                <th>ICD</th>
                                <td><?= esc($icd->icd) ?></td>
                            </tr>
                            <tr>
                                <th>Diagnosa (EN)</th>
                                <td><?= esc($icd->diagnosa_en) ?></td>
                            </tr>
                            <tr>
                                <th>Diagnosa (ID)</th>
                                <td><?= esc($icd->diagnosa_id) ?></td>
                            </tr>
                            <tr>
                                <th>Dibuat</th>
                                <td><?= $icd->created_at ?></td>
                            </tr>
                            <tr>
                                <th>Diupdate</th>
                                <td><?= $icd->updated_at ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/icd') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div class="float-right">
                    <a href="<?= base_url("master/icd/edit/{$icd->id}") ?>" 
                       class="btn btn-warning rounded-0">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?= base_url("master/icd/delete/{$icd->id}") ?>"
                       class="btn btn-danger rounded-0"
                       onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 