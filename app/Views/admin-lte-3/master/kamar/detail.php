<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Kamar Detail View
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Detail Kamar</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Kode</th>
                                <td><?= esc($kamar->kode) ?></td>
                            </tr>
                            <tr>
                                <th>Nama Kamar</th>
                                <td><?= esc($kamar->kamar) ?></td>
                            </tr>
                            <tr>
                                <th>Jumlah Terisi</th>
                                <td><?= esc($kamar->jml) ?></td>
                            </tr>
                            <tr>
                                <th>Kapasitas</th>
                                <td><?= esc($kamar->jml_max) ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><?= $kamar->status === '1' ? 'Aktif' : 'Tidak Aktif' ?></td>
                            </tr>
                            <tr>
                                <th>Dibuat</th>
                                <td><?= $kamar->created_at ?></td>
                            </tr>
                            <tr>
                                <th>Diupdate</th>
                                <td><?= $kamar->updated_at ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/kamar') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div class="float-right">
                    <a href="<?= base_url("master/kamar/edit/{$kamar->id}") ?>" 
                       class="btn btn-warning rounded-0">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <?php if ($kamar->jml == 0): ?>
                        <a href="<?= base_url("master/kamar/delete/{$kamar->id}") ?>"
                           class="btn btn-danger rounded-0"
                           onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 