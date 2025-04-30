<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Instagram Search Card -->
                <div class="card rounded-0">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-justified" id="instagram-search-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab" data-toggle="pill" href="#profile-search" role="tab">
                                    <i class="fas fa-user mr-1"></i> Cari Profil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="hashtag-tab" data-toggle="pill" href="#hashtag-search" role="tab">
                                    <i class="fas fa-hashtag mr-1"></i> Cari Hashtag
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="hashtag-suggestions-tab" data-toggle="pill" href="#hashtag-suggestions" role="tab">
                                    <i class="fas fa-lightbulb mr-1"></i> Saran Hashtags
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Profile Search Tab -->
                            <div class="tab-pane fade show active" id="profile-search" role="tabpanel">
                                <?= form_open('serp/instagram/profiles', ['method' => 'post']) ?>
                                    <div class="form-group">
                                        <label for="profile-query">Username atau Nama Profil</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text rounded-0 bg-light">@</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" id="profile-query" name="query"
                                                placeholder="Cari profil Instagram..." required minlength="3" maxlength="255">
                                        </div>
                                        <small class="form-text text-muted">Masukkan username atau bagian dari nama untuk menemukan profil Instagram</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-0">
                                        <i class="fas fa-search mr-1"></i> Cari Profil
                                    </button>
                                <?= form_close() ?>
                            </div>
                            
                            <!-- Hashtag Search Tab -->
                            <div class="tab-pane fade" id="hashtag-search" role="tabpanel">
                                <?= form_open('serp/instagram/hashtags', ['method' => 'post']) ?>
                                    <div class="form-group">
                                        <label for="hashtag-query">Hashtag Instagram</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text rounded-0 bg-light">#</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" id="hashtag-query" name="query"
                                                placeholder="Cari hashtag Instagram..." required minlength="3" maxlength="255">
                                        </div>
                                        <small class="form-text text-muted">Masukkan hashtag untuk melihat postingan terbaru dan statistik</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-0">
                                        <i class="fas fa-search mr-1"></i> Cari Hashtag
                                    </button>
                                <?= form_close() ?>
                            </div>
                            
                            <!-- Hashtag Suggestions Tab -->
                            <div class="tab-pane fade" id="hashtag-suggestions" role="tabpanel">
                                <?= form_open('serp/instagram/hashtag-suggestions', ['method' => 'post']) ?>
                                    <div class="form-group">
                                        <label for="topic-query">Topik atau Niche</label>
                                        <input type="text" class="form-control rounded-0" id="topic-query" name="topic"
                                            placeholder="Masukkan topik atau niche..." required minlength="3" maxlength="255">
                                        <small class="form-text text-muted">Masukkan topik untuk mendapatkan saran hashtag populer yang relevan</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select class="form-control rounded-0" name="category">
                                            <option value="general">Umum</option>
                                            <option value="business">Bisnis</option>
                                            <option value="lifestyle">Gaya Hidup</option>
                                            <option value="travel">Perjalanan</option>
                                            <option value="food">Makanan</option>
                                            <option value="fashion">Fashion</option>
                                            <option value="photography">Fotografi</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-0">
                                        <i class="fas fa-lightbulb mr-1"></i> Dapatkan Saran Hashtag
                                    </button>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Instagram Preview Card -->
                <div class="card rounded-0 mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-1"></i> Alat OSINT Instagram
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-primary">
                                    <span class="info-box-icon"><i class="fas fa-user"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Analisis Profil</span>
                                        <span class="info-box-number">Analisis profil pengguna</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Pengikut, postingan, dan keterlibatan
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-success">
                                    <span class="info-box-icon"><i class="fas fa-hashtag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Analitik Hashtag</span>
                                        <span class="info-box-number">Lacak tren dan popularitas</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Analisis konten dan metrik
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            Alat OSINT Instagram ini memungkinkan Anda melakukan penelitian pada akun dan hashtag Instagram untuk tujuan pengumpulan informasi.
                            Data yang ditampilkan tersedia untuk umum dan dikumpulkan menggunakan pustaka instagram-php-scraper.
                        </p>
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
                                        <a href="#" onclick="setSearchQuery('<?= esc(is_array($trend) ? $trend['title'] : $trend) ?>')" 
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
                                            <a class="dropdown-item py-1" href="#" onclick="setSearchQuery('<?= esc($related) ?>'); return false;">
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
                
                <!-- Instagram Tips Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Tips Pencarian</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-info"></i> Cari sebagian username untuk menemukan akun terkait</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Untuk hashtag, hilangkan simbol # jika Anda mau</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Instagram mungkin membatasi pencarian yang berlebihan</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Hasil dicache untuk meningkatkan kinerja</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<script>
function setSearchQuery(keyword) {
    // Set the active tab to profile search by default
    $('#profile-tab').tab('show');
    
    // Set the value in profile search input
    $('#profile-query').val(keyword);
    
    // Also set in hashtag search input without the hashtag symbol
    $('#hashtag-query').val(keyword.replace('#', ''));
    
    // Scroll to the search form
    $('html, body').animate({
        scrollTop: $("#instagram-search-tabs").offset().top - 100
    }, 500);
}
</script> 