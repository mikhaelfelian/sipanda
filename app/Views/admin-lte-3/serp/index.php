<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Search Form Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Pencarian Google</h3>
                    </div>
                    <div class="card-body">
                        <?= form_open('serp/search', ['method' => 'post']) ?>
                            <div class="form-group">
                                <label for="query">Kata Kunci atau Kueri</label>
                                <input type="text" class="form-control rounded-0" id="query" name="query"
                                    placeholder="Masukkan kata kunci pencarian..." required minlength="3" maxlength="255">
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="deep_search" name="deep_search"
                                    value="1">
                                <label class="form-check-label" for="deep_search">Pencarian Mendalam (Tahun Terakhir)</label>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-0">Cari</button>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Recent Searches Card -->
                <div class="card rounded-0 mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Pencarian Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentSearches)): ?>
                            <ul class="list-group">
                                <?php foreach ($recentSearches as $search): ?>
                                    <li class="list-group-item rounded-0 d-flex justify-content-between align-items-center">
                                        <?= esc($search['keyword']) ?>
                                        <span
                                            class="badge badge-info badge-pill"><?= date('d M Y', strtotime($search['last_searched'])) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Tidak ada pencarian terbaru.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Popular Keywords Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Kata Kunci Populer</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($popularKeywords)): ?>
                            <ul class="list-group">
                                <?php foreach ($popularKeywords as $keyword): ?>
                                    <li class="list-group-item rounded-0 d-flex justify-content-between align-items-center">
                                        <?= esc($keyword['keyword']) ?>
                                        <span class="badge badge-success badge-pill"><?= $keyword['search_count'] ?? 0 ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Tidak ada kata kunci populer.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>