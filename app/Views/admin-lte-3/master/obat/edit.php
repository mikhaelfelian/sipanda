<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-5">
        <?= form_open('master/obat/update/' . $obat->id) ?>
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Form Edit Obat</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Kategori <span class="text-danger">*</span></label>
                    <select name="id_kategori"
                        class="form-control select2 rounded-0 <?= validation_show_error('id_kategori') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k->id ?>" <?= old('id_kategori', $obat->id_kategori) == $k->id ? 'selected' : '' ?>>
                                <?= $k->kategori ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('id_kategori') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Merk <span class="text-danger">*</span></label>
                    <select name="id_merk"
                        class="form-control select2 rounded-0 <?= validation_show_error('id_merk') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Merk</option>
                        <?php foreach ($merk as $m): ?>
                            <option value="<?= $m->id ?>" <?= old('id_merk', $obat->id_merk) == $m->id ? 'selected' : '' ?>>
                                <?= $m->merk ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('id_merk') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="jenis_obat">Jenis Obat</label>
                    <select name="jenis" id="jenis"
                        class="form-control select2 rounded-0 <?= validation_show_error('id_kategori_obat') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Jenis Obat</option>
                        <?php foreach ($jenis as $jenis): ?>
                            <option value="<?= $jenis->id ?>"><?= $jenis->jenis ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('id_kategori_obat') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Obat <span class="text-danger">*</span></label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'item',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('item') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan nama obat',
                        'value' => old('item', $obat->item)
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('item') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alias</label>
                    <?= form_input([
                        'type' => 'text',
                        'name' => 'item_alias',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('item_alias') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan nama alias obat',
                        'value' => old('item_alias', $obat->item_alias)
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('item_alias') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Kandungan</label>
                    <?= form_textarea([
                        'name' => 'item_kand',
                        'class' => 'form-control rounded-0 ' . (validation_show_error('item_kand') ? 'is-invalid' : ''),
                        'placeholder' => 'Masukkan kandungan obat',
                        'value' => old('item_kand', $obat->item_kand),
                        'rows' => 3
                    ]) ?>
                    <div class="invalid-feedback">
                        <?= validation_show_error('item_kand') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Satuan <span class="text-danger">*</span></label>
                    <select name="id_satuan"
                        class="form-control rounded-0 <?= validation_show_error('id_satuan') ? 'is-invalid' : '' ?>">
                        <option value="">Pilih Satuan</option>
                        <?php foreach ($satuan as $s): ?>
                            <option value="<?= $s->id ?>" <?= old('id_satuan', $obat->id_satuan) == $s->id ? 'selected' : '' ?>>
                                <?= $s->satuanBesar ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= validation_show_error('id_satuan') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Harga Beli <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text rounded-0">Rp</span>
                        </div>
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'harga',
                            'name' => 'harga_beli',
                            'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_beli') ? 'is-invalid' : ''),
                            'placeholder' => 'Masukkan harga beli',
                            'value' => old('harga_beli', $obat->harga_beli)
                        ]) ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('harga_beli') ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Harga Jual <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text rounded-0">Rp</span>
                        </div>
                        <?= form_input([
                            'type' => 'text',
                            'name' => 'harga_jual',
                            'id' => 'harga',
                            'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_jual') ? 'is-invalid' : ''),
                            'placeholder' => 'Masukkan harga jual',
                            'value' => old('harga_jual', $obat->harga_jual)
                        ]) ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('harga_jual') ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Harga Eceran Tertinggi <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text rounded-0">Rp</span>
                        </div>
                        <?= form_input([
                            'type' => 'text',
                            'name' => 'harga_het',
                            'id' => 'harga',
                            'class' => 'form-control rounded-0 autonumeric ' . (validation_show_error('harga_jual') ? 'is-invalid' : ''),
                            'placeholder' => 'Masukkan harga het',
                            'value' => old('harga_het', $obat->harga_het)
                        ]) ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('harga_het') ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status Stok</label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="status_stok" name="status_stok"
                            value="1" <?= old('status_stok', $obat->status_stok) == '1' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="status_stok">Stockable</label>
                    </div>
                    <small class="form-text text-muted">Aktifkan jika di centang maka akan mengurangi stok.</small>
                </div>

                <div class="form-group">
                    <label>Tipe Racikan</label><br />
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label
                            class="btn btn-secondary <?= old('status_racikan', $obat->status_racikan) == '0' ? 'active' : '' ?>">
                            <input type="radio" name="status_racikan" value="0" <?= old('status_racikan', $obat->status_racikan) == '0' ? 'checked' : '' ?>> Non
                        </label>
                        <label
                            class="btn btn-secondary <?= old('status_racikan', $obat->status_racikan) == '1' ? 'active' : '' ?>">
                            <input type="radio" name="status_racikan" value="1" <?= old('status_racikan', $obat->status_racikan) == '1' ? 'checked' : '' ?>> Racikan
                        </label>
                    </div>
                    <small class="form-text text-muted">Racikan hanya dapat di inputkan jika di pilih.</small>
                </div>

                <div class="form-group">
                    <label>Status <span class="text-danger">*</span></label>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="1" id="statusAktif"
                            <?= old('status', $obat->status) == '1' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusAktif">
                            Aktif
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" name="status" value="0" id="statusNonaktif"
                            <?= old('status', $obat->status) == '0' ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="statusNonaktif">
                            Non Aktif
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer text-left">
                <a href="<?= base_url('master/obat') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-0 float-right">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
    <div class="col-7">
        <div class="card rounded-0">
            <div class="card-header">
                <h3 class="card-title">Item Referensi</h3>
            </div>
            <div class="card-body">
                <?= form_open('master/obat/item_ref_save/' . $obat->id, ['id' => 'formItemRef']) ?>
                <?= form_input([
                    'type' => 'hidden',
                    'id' => 'id_item_ref',
                    'name' => 'id_item_ref'
                ]) ?>
                <?= form_input([
                    'type' => 'hidden',
                    'id' => 'id',
                    'name' => 'id_item',
                    'value' => $obat->id
                ]) ?>
                <div class="row mb-3">
                    <div class="col-5">
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'item_ref',
                            'name' => 'item_ref',
                            'class' => 'form-control rounded-0' . (validation_show_error('item_ref') ? ' is-invalid' : ''),
                            'placeholder' => 'Item'
                        ]) ?>
                        <?php if (validation_show_error('item_ref')): ?>
                            <div class="invalid-feedback">
                                <?= validation_show_error('item_ref') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-2">
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'jml',
                            'name' => 'jml',
                            'class' => 'form-control rounded-0' . (validation_show_error('jumlah') ? ' is-invalid' : ''),
                            'placeholder' => 'Jumlah'
                        ]) ?>
                        <?php if (validation_show_error('jumlah')): ?>
                            <div class="invalid-feedback">
                                <?= validation_show_error('jumlah') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-3">
                        <?= form_input([
                            'type' => 'text',
                            'id' => 'harga_item_ref',
                            'name' => 'harga_item_ref',
                            'class' => 'form-control rounded-0' . (validation_show_error('harga_item_reff') ? ' is-invalid' : ''),
                            'placeholder' => 'Harga',
                            'readonly' => true
                        ]) ?>
                        <?php if (validation_show_error('harga_item_reff')): ?>
                            <div class="invalid-feedback">
                                <?= validation_show_error('harga_item_reff') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="fas fa-plus"></i> Simpan
                        </button>
                    </div>
                </div>
                <?= form_close() ?>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th class="text-left">Item</th>
                                <th width="15%" class="text-right">Jumlah</th>
                                <th width="20%" class="text-right">Harga</th>
                                <th width="20%" class="text-right">Subtotal</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($item_refs)): ?>
                                <?php $no = 1;
                                foreach ($item_refs as $ref): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?>.</td>
                                        <td><?= esc($ref->item) ?></td>
                                        <td class="text-center"><?= (int) $ref->jml ?></td>
                                        <td class="text-right"><?= format_angka_rp($ref->harga) ?></td>
                                        <td class="text-right"><?= format_angka_rp($ref->subtotal) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('master/obat/item_ref_delete/' . $ref->id) ?>"
                                                class="btn btn-danger btn-sm rounded-0" onclick="return confirm('Hapus?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Initialize AutoNumeric for currency inputs
    $(function () {
        $('input[id=harga], input[id=jml]').autoNumeric({ aSep: '.', aDec: ',', aPad: false });

        // Initialize autocomplete for item reference
        $("#item_ref").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?= base_url('publik/items') ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            select: function (event, ui) {
                var $itemrow = $(this).closest('tr');

                //Populate the input fields from the returned values
                $itemrow.find('#id_item_ref').val(ui.item.id);
                $('#id_item_ref').val(ui.item.id);
                $('#item_ref').val(ui.item.item);
                $('#harga_item_ref').val(ui.item.harga_jual);
                $('#jml').val(1);
                $('#jml').focus();
                return false;
            }

            // Format the list menu output of the autocomplete
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + item.label + "</a>")
                .appendTo(ul);
        };
    });
</script>
<?= $this->endSection() ?>