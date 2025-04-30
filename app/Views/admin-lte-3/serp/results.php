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
                                    <p class="text-muted"><i class="far fa-clock"></i> 
                                        <?php 
                                        if (isset($result['formatted_date'])) {
                                            // Use pre-formatted date if available
                                            echo tgl_indo8($result['formatted_date']);
                                        } elseif (isset($result['date'])) {
                                            // Format date if available in the result
                                            echo tgl_indo8(date('Y-m-d H:i:s', strtotime($result['date'])));
                                        } elseif (isset($result['published_date'])) {
                                            // Alternate field for date in news results
                                            echo tgl_indo8(date('Y-m-d H:i:s', strtotime($result['published_date'])));
                                        } elseif (isset($result['source'])) {
                                            // If source is available but no date, show source
                                            echo 'Sumber: ' . esc($result['source']);
                                        } else {
                                            // Fallback to current time
                                            echo tgl_indo8(date('Y-m-d H:i:s'));
                                        }
                                        ?>
                                    </p>
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
                                    <button class="btn btn-info btn-analyze" data-title="<?= isset($result['title']) ? esc($result['title']) : '' ?>" data-snippet="<?= isset($result['snippet']) ? esc(is_array($result['snippet']) ? json_encode($result['snippet']) : $result['snippet']) : '' ?>">
                                        Analisis
                                    </button>
                                    <div class="analyze-result mt-2"></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <p>Tidak ada hasil ditemukan.</p>
                            <?php endif; ?>
                        </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-export-all-pdf">
                            <i class="fas fa-file-pdf"></i> Ekspor ke PDF
                        </button>
                        <button class="btn btn-secondary btn-export-text">
                            <i class="fas fa-file-alt"></i> Ekspor ke Teks
                        </button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<script>
$(document).on('click', '.btn-analyze', function() {
    var title = $(this).data('title') || '';
    var snippet = $(this).data('snippet') || '';
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

// Export all results to a single PDF
$('.btn-export-all-pdf').click(function() {
    // Show loading state
    var $btn = $(this);
    var originalHtml = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Generating PDF...').attr('disabled', true);
    
    // Collect all search results
    var results = [];
    $('.search-result').each(function() {
        var $result = $(this);
        var title = $result.find('h4 a').text();
        var link = $result.find('h4 a').attr('href');
        var snippet = '';
        
        // Get published date from timestamp display
        var dateText = $result.find('p').eq(1).text().trim();
        var date = '';
        if (dateText) {
            // Try to extract the date part if it's not a source label
            if (dateText.indexOf('Sumber:') === -1) {
                date = dateText;
            }
        }
        
        // Get snippet text, avoiding the <em> tag if there's no snippet
        var $snippetP = $result.find('p').eq(2);
        if ($snippetP.find('em').length > 0 && $snippetP.text().trim() === 'Tidak ada cuplikan tersedia.') {
            snippet = 'No snippet available';
        } else {
            snippet = $snippetP.text();
        }
        
        // Get analysis results if available
        var sentiment = '';
        var viral = '';
        var $analyzeResult = $result.find('.analyze-result');
        
        if ($analyzeResult.find('.badge-info').length > 0) {
            sentiment = $analyzeResult.find('.badge-info').text().replace('Sentimen: ', '');
            viral = $analyzeResult.find('.badge-warning').text().replace('Prediksi Viral: ', '');
        }
        
        results.push({
            title: title,
            link: link,
            snippet: snippet,
            sentiment: sentiment,
            viral: viral,
            date: date
        });
    });
    
    // Send all results to generate PDF
    $.ajax({
        url: '<?= site_url('serp/exportAllResultsPdf') ?>',
        type: 'POST',
        data: {
            query: '<?= $query ?>',
            results: JSON.stringify(results),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                // Create a download link for the PDF
                var pdfData = 'data:application/pdf;base64,' + response.pdf;
                var link = document.createElement('a');
                link.href = pdfData;
                link.download = 'search_results_report.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show success message using toastr
                toastr.success('PDF berhasil dibuat');
            } else {
                // Show error message using toastr
                toastr.error(response.message || 'Gagal membuat PDF');
            }
        },
        error: function() {
            // Show error message using toastr
            toastr.error('Gagal meng-export PDF. Silakan coba lagi.');
        },
        complete: function() {
            // Reset button state
            $btn.html(originalHtml).attr('disabled', false);
        }
    });
});

// Export results to formatted text
$('.btn-export-text').click(function() {
    // Show loading state
    var $btn = $(this);
    var originalHtml = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Generating Text...').attr('disabled', true);
    
    // Collect all search results
    var results = [];
    $('.search-result').each(function() {
        var $result = $(this);
        var title = $result.find('h4 a').text();
        var link = $result.find('h4 a').attr('href');
        var snippet = '';
        
        // Get published date from timestamp display
        var dateText = $result.find('p').eq(1).text().trim();
        var date = '';
        if (dateText) {
            // Try to extract the date part if it's not a source label
            if (dateText.indexOf('Sumber:') === -1) {
                date = dateText;
            }
        }
        
        // Get snippet text, avoiding the <em> tag if there's no snippet
        var $snippetP = $result.find('p').eq(2);
        if ($snippetP.find('em').length > 0 && $snippetP.text().trim() === 'Tidak ada cuplikan tersedia.') {
            snippet = 'No snippet available';
        } else {
            snippet = $snippetP.text();
        }
        
        // Get analysis results if available
        var sentiment = '';
        var viral = '';
        var $analyzeResult = $result.find('.analyze-result');
        
        if ($analyzeResult.find('.badge-info').length > 0) {
            sentiment = $analyzeResult.find('.badge-info').text().replace('Sentimen: ', '');
            viral = $analyzeResult.find('.badge-warning').text().replace('Prediksi Viral: ', '');
        }
        
        results.push({
            title: title,
            link: link,
            snippet: snippet,
            sentiment: sentiment,
            viral: viral,
            date: date
        });
    });
    
    // Send all results to generate text report
    $.ajax({
        url: '<?= site_url('serp/exportToText') ?>',
        type: 'POST',
        data: {
            query: '<?= $query ?>',
            results: JSON.stringify(results),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                // Create a blob and download the text file
                var today = new Date();
                var blob = new Blob([response.text], { type: 'text/plain;charset=utf-8' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'laporan_patroli_cyber_' + today.toISOString().slice(0, 10) + '.txt';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show success message using toastr
                toastr.success('Teks laporan berhasil dibuat');
            } else {
                // Show error message using toastr
                toastr.error(response.message || 'Gagal membuat laporan teks');
            }
        },
        error: function() {
            // Show error message using toastr
            toastr.error('Gagal meng-export teks. Silakan coba lagi.');
        },
        complete: function() {
            // Reset button state
            $btn.html(originalHtml).attr('disabled', false);
        }
    });
});
</script>
<?= $this->endSection() ?>