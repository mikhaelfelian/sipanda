<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- OSINT Analysis Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Analisis OSINT</h3>
                    </div>
                    <div class="card-body">
                        <div
                            class="info-box bg-<?= $osintAnalysis['trust_score'] >= 70 ? 'success' : ($osintAnalysis['trust_score'] >= 40 ? 'warning' : 'danger') ?>">
                            <span class="info-box-icon"><i class="fas fa-shield-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Skor Kepercayaan</span>
                                <span class="info-box-number"><?= $osintAnalysis['trust_score'] ?>%</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $osintAnalysis['trust_score'] ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Analisis Sumber</h5>
                            <div class="mt-4">
                                <h5>Analisis Sumber</h5>
                                <ul class="list-unstyled">
                                    <li>
                                        <i
                                            class="fas fa-<?= !empty($osintAnalysis['sources']['verified_sources']) ? 'check' : 'times' ?> text-<?= !empty($osintAnalysis['sources']['verified_sources']) ? 'success' : 'danger' ?>"></i>
                                        Sumber Terverifikasi
                                    </li>
                                    <li>
                                        <i
                                            class="fas fa-<?= !empty($osintAnalysis['sources']['unverified_sources']) ? 'check' : 'times' ?> text-<?= !empty($osintAnalysis['sources']['unverified_sources']) ? 'success' : 'danger' ?>"></i>
                                        Sumber Tidak Terverifikasi
                                    </li>
                                    <li>
                                        <i
                                            class="fas fa-<?= (count($osintAnalysis['sources']['verified_sources'] ?? []) + count($osintAnalysis['sources']['unverified_sources'] ?? [])) > 1 ? 'check' : 'times' ?> text-<?= (count($osintAnalysis['sources']['verified_sources'] ?? []) + count($osintAnalysis['sources']['unverified_sources'] ?? [])) > 1 ? 'success' : 'danger' ?>"></i>
                                        Beberapa Sumber
                                    </li>
                                    <!-- Remove or implement recent_updates as needed -->
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Analisis Konten</h5>
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
                                    Bahasa Profesional
                                </li>
                                <li>
                                    <i
                                        class="fas fa-<?= $hasSuspiciousKeywords ? 'times' : 'check' ?> text-<?= $hasSuspiciousKeywords ? 'danger' : 'success' ?>"></i>
                                    Tidak Ada Kata Kunci Mencurigakan
                                </li>
                                <li>
                                    <i
                                        class="fas fa-<?= $hasCredibleSources ? 'check' : 'times' ?> text-<?= $hasCredibleSources ? 'success' : 'danger' ?>"></i>
                                    Sumber Terpercaya
                                </li>
                            </ul>
                        </div>
                        <?php if (!empty($osintAnalysis['recommendations'])): ?>
                            <div class="mt-4">
                                <h5>Rekomendasi Keamanan</h5>
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
                        <h3 class="card-title">Hasil Pencarian</h3>
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
                                            echo '<em>Tidak ada cuplikan tersedia.</em>';
                                        }
                                        ?>
                                    </p>
                                    <button class="btn btn-info btn-analyze" data-title="<?= esc($result['title']) ?>" data-snippet="<?= esc(is_array($result['snippet']) ? json_encode($result['snippet']) : $result['snippet']) ?>">
                                        Analisis
                                    </button>
                                    <div class="analyze-result mt-2"></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Tidak ada hasil ditemukan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
$(document).on('click', '.btn-analyze', function() {
    var title = $(this).data('title');
    var snippet = $(this).data('snippet');
    var text = title + ' ' + snippet;
    var resultDiv = $(this).next('.analyze-result');
    resultDiv.html('Menganalisis...');
    
    $.ajax({
        url: '<?= site_url('serp/analyzeNews') ?>',
        type: 'POST',
        data: {
            text: text,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(data) {
            resultDiv.html(
                '<span class="badge badge-info">Sentimen: ' + data.sentiment + '</span> ' +
                '<span class="badge badge-warning">Prediksi Viral: ' + data.viral + '</span>'
            );
        },
        error: function(xhr) {
            resultDiv.html('<span class="badge badge-danger">Error: ' + xhr.status + ' ' + xhr.statusText + '</span>');
        }
    });
});
</script>
<?= $this->endSection() ?>