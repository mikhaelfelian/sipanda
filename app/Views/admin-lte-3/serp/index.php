<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Search Form Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Pencarian Multi Platform</h3>
                    </div>
                    <div class="card-body">
                        <?= form_open('serp/search', ['method' => 'post']) ?>
                            <div class="form-group">
                                <label for="query">Kata Kunci atau Kueri</label>
                                <input type="text" class="form-control rounded-0" id="query" name="query"
                                    placeholder="Masukkan kata kunci pencarian..." required minlength="3" maxlength="255">
                            </div>
                            
                            <div class="form-group">
                                <label for="engine">Platform Pencarian</label>
                                <select class="form-control rounded-0" id="engine" name="engine">
                                    <option value="google">Google Web</option>
                                    <option value="google_news">Google News</option>
                                    <option value="google_images">Google Images</option>
                                    <option value="google_scholar">Google Scholar</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="twitter">Twitter/X</option>
                                    <option value="reddit">Reddit</option>
                                    <option value="bing">Bing</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="country">Negara</label>
                                <select class="form-control rounded-0" id="country" name="country">
                                    <option value="id" selected>Indonesia</option>
                                </select>
                            </div>
                            
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="deep_search" name="deep_search">
                                <label class="form-check-label" for="deep_search">Pencarian Mendalam (Hasil Lebih Luas)</label>
                            </div>
                            
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="use_ai" name="use_ai">
                                <label class="form-check-label" for="use_ai">Gunakan AI untuk Analisis Hasil</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary rounded-0">Cari</button>
                        <?= form_close() ?>
                    </div>
                </div>
                
                <!-- Search Tips Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Tips Pencarian</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Tips Umum</h5>
                                <ul>
                                    <li>Gunakan kata kunci yang spesifik untuk hasil yang lebih relevan</li>
                                    <li>Pencarian mendalam akan memberikan hasil yang lebih lengkap</li>
                                    <li>Aktivasi mode analisis AI untuk mendapatkan wawasan tambahan</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Tips Platform Khusus</h5>
                                <ul>
                                    <li><strong>Google News</strong>: Cocok untuk berita terkini</li>
                                    <li><strong>YouTube</strong>: Untuk mencari konten video</li>
                                    <li><strong>Twitter/X</strong>: Untuk mencari trending topics dan diskusi terkini</li>
                                    <li><strong>Reddit</strong>: Untuk diskusi mendalam dan komunitas</li>
                                </ul>
                            </div>
                        </div>
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