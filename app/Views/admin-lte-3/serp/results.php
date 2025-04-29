<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
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
                        <div
                            class="info-box bg-<?= $osintAnalysis['trust_score'] >= 70 ? 'success' : ($osintAnalysis['trust_score'] >= 40 ? 'warning' : 'danger') ?>">
                            <span class="info-box-icon"><i class="fas fa-shield-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Trust Score</span>
                                <span class="info-box-number"><?= $osintAnalysis['trust_score'] ?>%</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $osintAnalysis['trust_score'] ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Source Analysis</h5>
                            <div class="mt-4">
                                <h5>Source Analysis</h5>
                                <ul class="list-unstyled">
                                    <li>
                                        <i
                                            class="fas fa-<?= !empty($osintAnalysis['sources']['verified_sources']) ? 'check' : 'times' ?> text-<?= !empty($osintAnalysis['sources']['verified_sources']) ? 'success' : 'danger' ?>"></i>
                                        Verified Sources
                                    </li>
                                    <li>
                                        <i
                                            class="fas fa-<?= !empty($osintAnalysis['sources']['unverified_sources']) ? 'check' : 'times' ?> text-<?= !empty($osintAnalysis['sources']['unverified_sources']) ? 'success' : 'danger' ?>"></i>
                                        Unverified Sources
                                    </li>
                                    <li>
                                        <i
                                            class="fas fa-<?= (count($osintAnalysis['sources']['verified_sources'] ?? []) + count($osintAnalysis['sources']['unverified_sources'] ?? [])) > 1 ? 'check' : 'times' ?> text-<?= (count($osintAnalysis['sources']['verified_sources'] ?? []) + count($osintAnalysis['sources']['unverified_sources'] ?? [])) > 1 ? 'success' : 'danger' ?>"></i>
                                        Multiple Sources
                                    </li>
                                    <!-- Remove or implement recent_updates as needed -->
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Content Analysis</h5>
                            <?php
                            // Calculate content analysis summary
                            $hasProfessionalLanguage = false;
                            $hasSuspiciousKeywords = false;
                            $hasCredibleSources = false;

                            if (!empty($osintAnalysis['content']['safe_content'])) {
                                $hasProfessionalLanguage = true; // Assume safe_content means professional language
                                $hasCredibleSources = true;      // Assume safe_content means credible sources
                            }
                            if (!empty($osintAnalysis['content']['threat_indicators'])) {
                                $hasSuspiciousKeywords = true;   // If there are threat indicators, suspicious keywords exist
                            }
                            ?>
                            <ul class="list-unstyled">
                                <li>
                                    <i
                                        class="fas fa-<?= $hasProfessionalLanguage ? 'check' : 'times' ?> text-<?= $hasProfessionalLanguage ? 'success' : 'danger' ?>"></i>
                                    Professional Language
                                </li>
                                <li>
                                    <i
                                        class="fas fa-<?= $hasSuspiciousKeywords ? 'times' : 'check' ?> text-<?= $hasSuspiciousKeywords ? 'danger' : 'success' ?>"></i>
                                    No Suspicious Keywords
                                </li>
                                <li>
                                    <i
                                        class="fas fa-<?= $hasCredibleSources ? 'check' : 'times' ?> text-<?= $hasCredibleSources ? 'success' : 'danger' ?>"></i>
                                    Credible Sources
                                </li>
                            </ul>
                        </div>
                        <?php if (!empty($osintAnalysis['recommendations'])): ?>
                            <div class="mt-4">
                                <h5>Security Recommendations</h5>
                                <ul class="list-unstyled">
                                    <?php foreach ($osintAnalysis['recommendations'] as $recommendation): ?>
                                        <li><i class="fas fa-exclamation-triangle text-warning"></i> 
                                            <?= is_array($recommendation) ? esc(json_encode($recommendation)) : esc($recommendation) ?>
                                        </li>
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
                                    <h4><a href="<?= esc($result['link']) ?>" target="_blank"><?= esc($result['title']) ?></a>
                                    </h4>
                                    <p class="text-muted"><?= esc($result['link']) ?></p>
                                    <p>
                                        <?php
                                        if (isset($result['snippet'])) {
                                            if (is_array($result['snippet'])) {
                                                echo '<pre>' . print_r($result['snippet'], true) . '</pre>';
                                            } else {
                                                echo esc($result['snippet']);
                                            }
                                        } else {
                                            echo '<em>No snippet available.</em>';
                                        }
                                        ?>
                                    </p>
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
<?= $this->endSection() ?>