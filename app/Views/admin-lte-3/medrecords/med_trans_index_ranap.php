<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-08
 * 
 * Medical Record Transaction List View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">Rawat Inap</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>ID</th>
                        <th>Pasien</th>
                        <th>L / P</th>
                        <th>Tgl Lahir</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($medrecs)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($medrecs as $med):
                            ?>
                            <tr>
                                <td></td>
                                <td class="text-center"><?php echo $no ?>.</td>
                                <td>
                                    <?php echo anchor('medrecords/trans/detail/' . $med->id, '#' . $med->no_rm) ?><br />
                                    <span class="mailbox-read-time float-left"><?php echo tgl_indo8($med->tgl_masuk) ?></span><br />
                                    <small><b><?= tipeRawat($med->tipe) ?></b></small>
                                </td>
                                <td>
                                    <b><?php echo $med->pasien ?></b><br />
                                    <small><?php echo strtoupper($med->pasien_alamat) ?></small><br/>                                    
                                    <small><b><?php echo $med->poli ?></b></small><br/>
                                    <small><i><?php echo $med->dokter ?></i></small>
                                </td>
                                <td><?php echo jns_klm($med->jns_klm) ?></td>
                                <td><?php echo tgl_indo3($med->tgl_lahir) ?></td>
                                <td style="width: 150px;">
                                    <?= anchor(
                                        base_url('medrecords/aksi/' . $med->id),
                                        '<i class="fas fa-folder"></i> Aksi &raquo;',
                                        [
                                            'class' => 'btn btn-primary btn-sm btn-flat',
                                            'style' => 'width: 80px;'
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                            <?php $no++ ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <?= $pager->links('default', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?>