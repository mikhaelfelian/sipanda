<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Search Form Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Pencarian Google Maps</h3>
                    </div>
                    <div class="card-body">
                        <?= form_open('serp/maps/search', ['method' => 'post']) ?>
                            <div class="form-group">
                                <label for="query">Apa yang Anda cari?</label>
                                <input type="text" class="form-control rounded-0" id="query" name="query"
                                    placeholder="Masukkan bisnis, alamat, atau tempat..." required minlength="3" maxlength="255"
                                    value="<?= set_value('query', $this->request->getGet('query') ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="location">Lokasi</label>
                                <input type="text" class="form-control rounded-0" id="location" name="location"
                                    placeholder="Kota, wilayah, atau alamat (default: Indonesia)"
                                    value="<?= set_value('location', $this->request->getGet('location') ?? '') ?>">
                                <small class="form-text text-muted">Biarkan kosong untuk mencari di seluruh Indonesia</small>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-0">Cari di Maps</button>
                        <?= form_close() ?>
                    </div>
                </div>
                <!-- Map Placeholder (for a future feature) -->
                <div class="card rounded-0 mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Pratinjau Peta</h3>
                    </div>
                    <div class="card-body text-center bg-light">
                        <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <i class="fas fa-map-marked-alt fa-5x text-muted mb-3"></i>
                                <p class="text-muted">Lakukan pencarian untuk menampilkan hasil peta</p>
                            </div>
                        </div>
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
                <!-- Trending Searches Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Trending di Google</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($trendingSearches)): ?>
                            <?php if (ENVIRONMENT === 'development'): ?>
                            <div class="small text-muted mb-2">
                                Found <?= count($trendingSearches) ?> trending items
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex flex-wrap">
                                <?php foreach ($trendingSearches as $trend): ?>
                                    <div class="dropdown mr-2 mb-2">
                                        <a href="<?= site_url('serp/maps/search?query=' . urlencode(is_array($trend) ? $trend['title'] : $trend)) ?>" 
                                           class="badge badge-info p-2 dropdown-toggle" id="trend-<?= md5(is_array($trend) ? $trend['title'] : $trend) ?>"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?= esc(is_array($trend) ? $trend['title'] : $trend) ?>
                                        </a>
                                        <?php 
                                        $relatedQueries = [];
                                        if (is_array($trend) && isset($trend['related_queries']) && !empty($trend['related_queries'])): 
                                            $relatedQueries = $trend['related_queries'];
                                        endif;
                                        
                                        if (!empty($relatedQueries)): 
                                        ?>
                                        <div class="dropdown-menu p-2" aria-labelledby="trend-<?= md5(is_array($trend) ? $trend['title'] : $trend) ?>">
                                            <h6 class="dropdown-header bg-light">Trend Breakdown</h6>
                                            <?php foreach ($relatedQueries as $related): ?>
                                            <a class="dropdown-item py-1" href="<?= site_url('serp/maps/search?query=' . urlencode($related)) ?>">
                                                <?= esc($related) ?>
                                            </a>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>Tidak ada trending search tersedia saat ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Search Tips Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Tips Pencarian</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-info"></i> Gunakan nama bisnis spesifik untuk hasil yang tepat</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Coba tambahkan nama kota untuk hasil yang lebih baik</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Cari berdasarkan kategori seperti "restoran" atau "hotel"</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 