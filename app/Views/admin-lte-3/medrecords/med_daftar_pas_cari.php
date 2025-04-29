<div class="col-md-12">
    <div class="card card-default rounded-0">

        <div class="card-header">
            <h3 class="card-title">Cari Pasien</h3>
        </div>
        <div class="card-body">
            <?= form_open('medrecords/daftar?tipe_pas={$tipe_pas}', ['method' => 'get']) ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">No. RM</th>
                        <th class="text-left">Nama</th>
                        <th class="text-center">L/P</th>
                        <th class="text-center">No HP</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="text-left">
                            <?= form_input(['name' => 'no_rm', 'placeholder' => 'No. RM ...', 'value' => $search, 'class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-left">
                            <?= form_input(['name' => 'pasien', 'placeholder' => 'Pasien ...', 'value' => $search, 'class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-center">
                            <?= form_dropdown('jns_klm', ['' => '- Pilih -', 'L' => 'Laki-laki', 'P' => 'Perempuan'], $search, ['class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-center">
                            <?= form_input(['name' => 'no_hp', 'placeholder' => 'No HP ...', 'value' => $search, 'class' => 'form-control form-control rounded-0']) ?>
                        </th>
                        <th class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary rounded-0">
                                <i class="fas fa-filter"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pasiens)): ?>
                        <?php
                        $start = ($page - 1) * $perPage + 1;
                        foreach ($pasiens as $index => $pasien):
                            ?>
                            <tr>
                                <td class="text-center"><?= $start + $index ?>.</td>
                                <td class="text-left"><?= esc($pasien->kode) ?></td>
                                <td class="text-left">
                                    <b><?= esc($pasien->nama) ?></b><?= br() ?>
                                    <small><?= esc($pasien->nik) ?></small><?= br() ?>
                                    <small><?= usia_lkp($pasien->tgl_lahir) ?></small><?= br() ?>
                                    <small><i><?= esc($pasien->alamat) ?></i></small>
                                </td>
                                <td class="text-center"><?= jns_klm($pasien->jns_klm) ?></td>
                                <td class="text-center"><?= esc($pasien->no_hp) ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url("medrecords/daftar?tipe_pas={$tipe_pas}&id_pasien={$pasien->id}") ?>"
                                            class="btn btn-info btn-sm rounded-0">
                                            <i class="fas fa-check-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Add pagination links -->
            <?php if ($pager): ?>
                <div class="mt-2">
                    <?= $pager->links('pasien', 'adminlte_pagination') ?>
                </div>
            <?php endif; ?>
            <?= form_close() ?>
        </div>
    </div>
</div>