<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Search Form Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Google Maps Search</h3>
                    </div>
                    <div class="card-body">
                        <?= form_open('serp/maps/search', ['method' => 'post']) ?>
                            <div class="form-group">
                                <label for="query">What are you looking for?</label>
                                <input type="text" class="form-control rounded-0" id="query" name="query"
                                    placeholder="Enter business, address, or place..." required minlength="3" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control rounded-0" id="location" name="location"
                                    placeholder="City, region, or address (default: Indonesia)">
                                <small class="form-text text-muted">Leave empty to search all of Indonesia</small>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-0">Search Maps</button>
                        <?= form_close() ?>
                    </div>
                </div>
                <!-- Map Placeholder (for a future feature) -->
                <div class="card rounded-0 mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Preview Map</h3>
                    </div>
                    <div class="card-body text-center bg-light">
                        <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <i class="fas fa-map-marked-alt fa-5x text-muted mb-3"></i>
                                <p class="text-muted">Search to display map results</p>
                            </div>
                        </div>
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
                <!-- Search Tips Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Search Tips</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-info"></i> Use specific business names for exact matches</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Try adding a city name for better results</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Search for categories like "restaurants" or "hotels"</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 