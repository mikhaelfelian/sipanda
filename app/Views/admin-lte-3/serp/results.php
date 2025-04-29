<?= $this->extend('admin-lte-3/layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Search Results for "<?= esc($query) ?>"</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- OSINT Analysis Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">OSINT Analysis</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box bg-<?= $osintAnalysis['trust_score'] >= 70 ? 'success' : ($osintAnalysis['trust_score'] >= 40 ? 'warning' : 'danger') ?>">
                                <span class="info-box-icon"><i class="fas fa-shield-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Trust Score</span>
                                    <span class="info-box-number"><?= $osintAnalysis['trust_score'] ?>%</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $osintAnalysis['trust_score'] ?>%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h5>Source Analysis</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-<?= $osintAnalysis['source_verification']['trusted_domains'] ? 'check' : 'times' ?> text-<?= $osintAnalysis['source_verification']['trusted_domains'] ? 'success' : 'danger' ?>"></i> Trusted Domains</li>
                                    <li><i class="fas fa-<?= $osintAnalysis['source_verification']['multiple_sources'] ? 'check' : 'times' ?> text-<?= $osintAnalysis['source_verification']['multiple_sources'] ? 'success' : 'danger' ?>"></i> Multiple Sources</li>
                                    <li><i class="fas fa-<?= $osintAnalysis['source_verification']['recent_updates'] ? 'check' : 'times' ?> text-<?= $osintAnalysis['source_verification']['recent_updates'] ? 'success' : 'danger' ?>"></i> Recent Updates</li>
                                </ul>
                            </div>

                            <div class="mt-4">
                                <h5>Content Analysis</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-<?= $osintAnalysis['content_analysis']['professional_language'] ? 'check' : 'times' ?> text-<?= $osintAnalysis['content_analysis']['professional_language'] ? 'success' : 'danger' ?>"></i> Professional Language</li>
                                    <li><i class="fas fa-<?= $osintAnalysis['content_analysis']['suspicious_keywords'] ? 'times' : 'check' ?> text-<?= $osintAnalysis['content_analysis']['suspicious_keywords'] ? 'danger' : 'success' ?>"></i> No Suspicious Keywords</li>
                                    <li><i class="fas fa-<?= $osintAnalysis['content_analysis']['credible_sources'] ? 'check' : 'times' ?> text-<?= $osintAnalysis['content_analysis']['credible_sources'] ? 'success' : 'danger' ?>"></i> Credible Sources</li>
                                </ul>
                            </div>

                            <?php if (!empty($osintAnalysis['security_recommendations'])): ?>
                            <div class="mt-4">
                                <h5>Security Recommendations</h5>
                                <ul class="list-unstyled">
                                    <?php foreach ($osintAnalysis['security_recommendations'] as $recommendation): ?>
                                    <li><i class="fas fa-exclamation-triangle text-warning"></i> <?= esc($recommendation) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- SERP Results -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Search Results</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($results)): ?>
                                <?php foreach ($results as $result): ?>
                                    <div class="search-result mb-4">
                                        <h4><a href="<?= esc($result['link']) ?>" target="_blank"><?= esc($result['title']) ?></a></h4>
                                        <p class="text-muted"><?= esc($result['link']) ?></p>
                                        <p><?= esc($result['snippet']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No results found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>