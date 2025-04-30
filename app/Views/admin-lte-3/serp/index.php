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
                            <div class="form-group">
                                <label for="country">Negara</label>
                                <select class="form-control rounded-0" id="country" name="country">
                                    <option value="id" selected>Indonesia</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="region">Provinsi</label>
                                <select class="form-control rounded-0" id="region" name="region">
                                    <option value="">Semua Provinsi</option>
                                    <option value="aceh">Aceh</option>
                                    <option value="sumatera_utara">Sumatera Utara</option>
                                    <option value="sumatera_barat">Sumatera Barat</option>
                                    <option value="riau">Riau</option>
                                    <option value="kepulauan_riau">Kepulauan Riau</option>
                                    <option value="jambi">Jambi</option>
                                    <option value="sumatera_selatan">Sumatera Selatan</option>
                                    <option value="bangka_belitung">Kepulauan Bangka Belitung</option>
                                    <option value="bengkulu">Bengkulu</option>
                                    <option value="lampung">Lampung</option>
                                    <option value="jakarta">DKI Jakarta</option>
                                    <option value="banten">Banten</option>
                                    <option value="jawa_barat">Jawa Barat</option>
                                    <option value="jawa_tengah">Jawa Tengah</option>
                                    <option value="yogyakarta">DI Yogyakarta</option>
                                    <option value="jawa_timur">Jawa Timur</option>
                                    <option value="bali">Bali</option>
                                    <option value="nusa_tenggara_barat">Nusa Tenggara Barat</option>
                                    <option value="nusa_tenggara_timur">Nusa Tenggara Timur</option>
                                    <option value="kalimantan_barat">Kalimantan Barat</option>
                                    <option value="kalimantan_tengah">Kalimantan Tengah</option>
                                    <option value="kalimantan_selatan">Kalimantan Selatan</option>
                                    <option value="kalimantan_timur">Kalimantan Timur</option>
                                    <option value="kalimantan_utara">Kalimantan Utara</option>
                                    <option value="sulawesi_utara">Sulawesi Utara</option>
                                    <option value="gorontalo">Gorontalo</option>
                                    <option value="sulawesi_tengah">Sulawesi Tengah</option>
                                    <option value="sulawesi_barat">Sulawesi Barat</option>
                                    <option value="sulawesi_selatan">Sulawesi Selatan</option>
                                    <option value="sulawesi_tenggara">Sulawesi Tenggara</option>
                                    <option value="maluku">Maluku</option>
                                    <option value="maluku_utara">Maluku Utara</option>
                                    <option value="papua_barat">Papua Barat</option>
                                    <option value="papua">Papua</option>
                                    <option value="papua_selatan">Papua Selatan</option>
                                    <option value="papua_tengah">Papua Tengah</option>
                                    <option value="papua_pegunungan">Papua Pegunungan</option>
                                </select>
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
                <!-- Suggestion Keywords Card -->
                <div class="card rounded-0 mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Saran Kata Kunci</h3>
                    </div>
                    <div class="card-body">
                        <div class="suggestion-keywords">
                            <?php 
                            // Using dynamic trending searches from Google Trends API instead of static suggestions
                            if (!empty($trendingSearches)): 
                                // Add debug info for development purposes
                                if (ENVIRONMENT === 'development'): 
                            ?>
                                <div class="small text-muted mb-2">
                                    Found <?= count($trendingSearches) ?> trending items
                                </div>
                                <?php endif; ?>

                                <div class="d-flex flex-wrap mb-3">
                                    <?php foreach ($trendingSearches as $trend): ?>
                                        <a href="<?= site_url('serp/result?q=' . urlencode(is_array($trend) ? $trend['title'] : $trend)) ?>" 
                                           class="badge badge-primary p-2 mr-2 mb-2">
                                            <?= esc(is_array($trend) ? $trend['title'] : $trend) ?>
                                        </a>
                                        
                                        <?php 
                                        $relatedQueries = [];
                                        if (is_array($trend) && isset($trend['related_queries']) && !empty($trend['related_queries'])): 
                                            $relatedQueries = $trend['related_queries'];
                                        endif;
                                        
                                        if (!empty($relatedQueries)): 
                                            foreach ($relatedQueries as $related): 
                                        ?>
                                            <a class="badge badge-secondary p-2 mr-2 mb-2" href="<?= site_url('serp/result?q=' . urlencode($related)) ?>">
                                                <?= esc($related) ?>
                                            </a>
                                        <?php 
                                            endforeach;
                                        endif; 
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <?php 
                                // Fallback to static suggestions if trending searches are not available
                                $suggestionKeywords = [
                                    'berita terkini', 'hoax', 'radikalisme', 'terorisme', 
                                    'keamanan siber', 'keamanan nasional', 'pemilu', 
                                    'demonstrasi', 'konflik sosial', 'bencana alam'
                                ];
                                ?>
                                <div class="d-flex flex-wrap">
                                    <?php foreach ($suggestionKeywords as $keyword): ?>
                                        <a href="<?= site_url('serp/result?q=' . urlencode($keyword)) ?>" 
                                           class="badge badge-primary mr-2 mb-2 p-2">
                                            <?= esc($keyword) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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
                        <h3 class="card-title">Kata Kunci Terpopuler</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($popularKeywords)): ?>
                            <ul class="list-group">
                                <?php 
                                // Sort keywords by search count in descending order
                                usort($popularKeywords, function($a, $b) {
                                    return ($b['search_count'] ?? 0) - ($a['search_count'] ?? 0);
                                });
                                
                                // Display keywords
                                foreach ($popularKeywords as $keyword): 
                                ?>
                                    <li class="list-group-item rounded-0 d-flex justify-content-between align-items-center">
                                        <a href="<?= site_url('serp/result?q=' . urlencode($keyword['keyword'])) ?>">
                                            <?= esc($keyword['keyword']) ?>
                                        </a>
                                        <span class="badge badge-success badge-pill"><?= $keyword['search_count'] ?? 0 ?> pencarian</span>
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