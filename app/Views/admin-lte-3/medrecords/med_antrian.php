<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-07
 * 
 * Medical Record Queue View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="card rounded-0">
    <div class="card-header">
        <h3 class="card-title">Data Pendaftaran Pasien</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Tgl</th>
                        <th>Antrian</th>
                        <th>Pasien</th>
                        <th>L / P</th>
                        <th>Tgl Lahir</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($antrians)): ?>
                        <?php
                        $no = ($perPage * ($currentPage - 1)) + 1;
                        foreach ($antrians as $dft):
                            ?>
                            <tr>
                                <td></td>
                                <td class="text-center"><?php echo $no++ ?>.</td>
                                <td>
                                    <span class="mailbox-read-time float-left"><?php echo tgl_indo8($dft->tgl_masuk) ?></span>
                                </td>
                                <td><?php echo format_nomor(3, $dft->no_urut) ?></td>
                                <td>
                                    <b><?php echo $dft->nama_pgl ?></b><br />
                                    <small><?php echo strtoupper($dft->alamat) ?></small>

                                </td>
                                <td><?php echo jns_klm($dft->jns_klm) ?></td>
                                <td><?php echo tgl_indo3($dft->tgl_lahir) ?></td>
                                <td style="width: 250px;">
                                    <?php if ($dft->status_akt == '0'): ?>
                                        <?= anchor(
                                            base_url('medrecords/daftar/konfirm/' . $dft->id),
                                            '<i class="fa fa-check"></i> Konfirm »',
                                            [
                                                'class' => 'btn btn-danger btn-flat btn-xs',
                                                'style' => 'width: 80px;',
                                                'onclick' => "return confirm('Anda belum mengisi form GENERAL CONSENT, lanjutkan ?')"
                                            ]
                                        ) ?><br>
                                        <?php else: ?>
                                            <?= anchor(
                                                base_url('medrecords/trans/create/' . $dft->id),
                                                '<i class="fa fa-shopping-cart"></i> Input »',
                                                [
                                                    'class' => 'btn btn-warning btn-flat btn-xs',
                                                    'style' => 'width: 80px;'
                                                ]
                                            ) ?>
                                        <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <?php //$pager->links('antrian', 'adminlte_pagination') ?>
    </div>
</div>
<?= $this->endSection() ?>