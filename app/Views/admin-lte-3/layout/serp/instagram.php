<?= $this->extend('admin-lte-3/layouts/main') ?>

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
                                    <i class="fas fa-user mr-1"></i> Search Profiles
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="hashtag-tab" data-toggle="pill" href="#hashtag-search" role="tab">
                                    <i class="fas fa-hashtag mr-1"></i> Search Hashtags
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
                                        <label for="profile-query">Username or Profile Name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text rounded-0 bg-light">@</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" id="profile-query" name="query"
                                                placeholder="Search for Instagram profiles..." required minlength="3" maxlength="255">
                                        </div>
                                        <small class="form-text text-muted">Enter a username or part of a name to find Instagram profiles</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-0">
                                        <i class="fas fa-search mr-1"></i> Search Profiles
                                    </button>
                                <?= form_close() ?>
                            </div>
                            
                            <!-- Hashtag Search Tab -->
                            <div class="tab-pane fade" id="hashtag-search" role="tabpanel">
                                <?= form_open('serp/instagram/hashtags', ['method' => 'post']) ?>
                                    <div class="form-group">
                                        <label for="hashtag-query">Instagram Hashtag</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text rounded-0 bg-light">#</span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" id="hashtag-query" name="query"
                                                placeholder="Search for Instagram hashtags..." required minlength="3" maxlength="255">
                                        </div>
                                        <small class="form-text text-muted">Enter a hashtag to see recent posts and statistics</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-0">
                                        <i class="fas fa-search mr-1"></i> Search Hashtags
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
                            <i class="fab fa-instagram mr-1"></i> Instagram OSINT Tool
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-primary">
                                    <span class="info-box-icon"><i class="fas fa-user"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Profile Analysis</span>
                                        <span class="info-box-number">Analyze user profiles</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Followers, posts, and engagement
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-gradient-success">
                                    <span class="info-box-icon"><i class="fas fa-hashtag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hashtag Analytics</span>
                                        <span class="info-box-number">Track trends and popularity</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Content analysis and metrics
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            This Instagram OSINT tool allows you to perform research on Instagram accounts and hashtags for information gathering purposes.
                            The data displayed is publicly available and is collected using the instagram-php-scraper library.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Recent Searches Card -->
                <div class="card rounded-0 mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Recent Searches</h3>
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
                            <p>No recent searches.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Popular Keywords Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Popular Keywords</h3>
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
                            <p>No popular keywords.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Instagram Tips Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Search Tips</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-info"></i> Search for partial usernames to find related accounts</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> For hashtags, omit the # symbol if you wish</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Instagram may rate-limit excessive searches</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Results are cached to improve performance</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 