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

                    <?php if (!empty($aiAnalysis) && $useAI): ?>
                    <!-- AI Analysis Card -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-robot mr-2"></i> Analisis AI</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($aiAnalysis['error'])): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle"></i> <?= $aiAnalysis['error'] ?>
                                </div>
                            <?php elseif (isset($aiAnalysis['analysis'])): ?>
                                <div class="alert alert-info">
                                    <?= nl2br(esc($aiAnalysis['analysis'])) ?>
                                </div>
                            <?php else: ?>
                                <?php if (isset($aiAnalysis['topik_utama']) || isset($aiAnalysis['topikUtama'])): ?>
                                    <div class="mb-3">
                                        <h5><i class="fas fa-file-alt mr-1"></i> Topik Utama</h5>
                                        <p><?= esc($aiAnalysis['topik_utama'] ?? $aiAnalysis['topikUtama']) ?></p>
                                        
                                        <?php if (isset($aiAnalysis['sub_topik']) || isset($aiAnalysis['subTopik'])): ?>
                                            <h6 class="mt-2">Sub-topik Relevan:</h6>
                                            <ul>
                                                <?php 
                                                $subTopics = $aiAnalysis['sub_topik'] ?? $aiAnalysis['subTopik'] ?? [];
                                                if (is_string($subTopics)) {
                                                    echo "<li>" . esc($subTopics) . "</li>";
                                                } else if (is_array($subTopics)) {
                                                    foreach ($subTopics as $topic): 
                                                ?>
                                                    <li><?= is_array($topic) ? esc(json_encode($topic)) : esc($topic) ?></li>
                                                <?php 
                                                    endforeach; 
                                                }
                                                ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($aiAnalysis['kata_kunci_tambahan']) || isset($aiAnalysis['kataKunciTambahan'])): ?>
                                    <div class="mb-3">
                                        <h5><i class="fas fa-search-plus mr-1"></i> Kata Kunci Tambahan</h5>
                                        <div class="mt-2">
                                            <?php 
                                            $keywords = $aiAnalysis['kata_kunci_tambahan'] ?? $aiAnalysis['kataKunciTambahan'] ?? [];
                                            if (is_string($keywords)) {
                                                echo "<span class='badge badge-info mr-1'>" . esc($keywords) . "</span>";
                                            } else if (is_array($keywords)) {
                                                foreach ($keywords as $keyword): 
                                            ?>
                                                <span class="badge badge-info mr-1"><?= is_array($keyword) ? esc(json_encode($keyword)) : esc($keyword) ?></span>
                                            <?php 
                                                endforeach; 
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($aiAnalysis['maksud_pengguna']) || isset($aiAnalysis['maksudPengguna']) || isset($aiAnalysis['intent'])): ?>
                                    <div class="mb-3">
                                        <h5><i class="fas fa-bullseye mr-1"></i> Kemungkinan Maksud Pengguna</h5>
                                        <p><?= esc($aiAnalysis['maksud_pengguna'] ?? $aiAnalysis['maksudPengguna'] ?? $aiAnalysis['intent'] ?? '') ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($aiAnalysis['kategori']) || isset($aiAnalysis['domain'])): ?>
                                    <div class="mb-3">
                                        <h5><i class="fas fa-tag mr-1"></i> Kategori/Domain</h5>
                                        <span class="badge badge-primary"><?= esc($aiAnalysis['kategori'] ?? $aiAnalysis['domain'] ?? '') ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($aiAnalysis['bias']) || isset($aiAnalysis['potensi_bias'])): ?>
                                    <div class="mb-3">
                                        <h5><i class="fas fa-balance-scale mr-1"></i> Potensi Bias</h5>
                                        <p class="text-muted"><?= esc($aiAnalysis['bias'] ?? $aiAnalysis['potensi_bias'] ?? '') ?></p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-8">
                    <!-- SERP Results -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Hasil Pencarian <?= esc(ucfirst(str_replace('_', ' ', $engine))) ?></h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($results)): ?>
                                <?php foreach ($results as $result): ?>
                                    <div class="search-result mb-4">
                                        <?php if ($engine === 'google_images'): ?>
                                            <!-- Image result display -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <img src="<?= esc($result['thumbnail'] ?? $result['original']) ?>" class="img-fluid" alt="<?= esc($result['title'] ?? 'Image') ?>">
                                                </div>
                                                <div class="col-md-8">
                                                    <h4><a href="<?= esc($result['original'] ?? $result['link']) ?>" target="_blank"><?= esc($result['title'] ?? 'Image') ?></a></h4>
                                                    <p class="text-muted"><?= esc($result['source'] ?? $result['link'] ?? '') ?></p>
                                                    <?php if (!empty($result['snippet'])): ?>
                                                        <p><?= esc($result['snippet']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php elseif ($engine === 'youtube'): ?>
                                            <!-- YouTube result display -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <img src="<?= esc($result['thumbnail'] ?? '') ?>" class="img-fluid" alt="<?= esc($result['title'] ?? 'Video') ?>">
                                                </div>
                                                <div class="col-md-8">
                                                    <h4><a href="<?= esc($result['link']) ?>" target="_blank"><?= esc($result['title']) ?></a></h4>
                                                    <p class="text-muted">
                                                        <?php if (!empty($result['channel'])): ?>
                                                            <i class="fab fa-youtube"></i> <?= esc($result['channel']) ?>
                                                        <?php endif; ?>
                                                        <?php if (!empty($result['published_date'])): ?>
                                                            <span class="ml-2"><i class="far fa-clock"></i> <?= esc($result['published_date']) ?></span>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if (!empty($result['snippet'])): ?>
                                                        <p><?= esc($result['snippet']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php elseif ($engine === 'twitter'): ?>
                                            <!-- Twitter/X result display -->
                                            <div class="card border-primary">
                                                <div class="card-header bg-light">
                                                    <strong>@<?= esc($result['username'] ?? '') ?></strong>
                                                    <?php if (!empty($result['published_date'] ?? $result['formatted_date'])): ?>
                                                        <span class="float-right text-muted"><i class="far fa-clock"></i> <?= esc($result['published_date'] ?? $result['formatted_date']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <p><?= esc($result['title'] ?? $result['text'] ?? $result['snippet'] ?? '') ?></p>
                                                    
                                                    <?php if (!empty($result['link'])): ?>
                                                        <a href="<?= esc($result['link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fab fa-twitter"></i> View Tweet
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php elseif ($engine === 'reddit'): ?>
                                            <!-- Reddit result display -->
                                            <div class="card border-danger">
                                                <div class="card-header bg-light">
                                                    <strong><?= esc($result['subreddit'] ?? '') ?></strong>
                                                    <?php if (!empty($result['published_date'])): ?>
                                                        <span class="float-right text-muted"><i class="far fa-clock"></i> <?= esc($result['published_date']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <h4><a href="<?= esc($result['link']) ?>" target="_blank"><?= esc($result['title']) ?></a></h4>
                                                    <?php if (!empty($result['snippet'])): ?>
                                                        <p><?= esc($result['snippet']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Default result display for Google, Bing, News, and Scholar -->
                                            <h4><a href="<?= esc($result['link']) ?>" target="_blank"><?= esc($result['title']) ?></a></h4>
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
                                        <?php endif; ?>
                                        
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
$(document).ready(function() {
    // Store analysis results
    const analysisResults = [];

    // Handle analyze button clicks
    $('.btn-analyze').on('click', function() {
        const button = $(this);
        const title = button.data('title');
        const snippet = button.data('snippet');
        const resultContainer = button.next('.analyze-result');

        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menganalisis...');

        // Send AJAX request to analyze content
        $.ajax({
            url: '<?= base_url('serp/analyzeNews') ?>',
            method: 'POST',
            data: {
                text: snippet,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                // Store result
                const analysisResult = {
                    title: title,
                    snippet: snippet,
                    sentiment: response.sentiment,
                    viral: response.viral
                };
                analysisResults.push(analysisResult);

                // Build HTML for display
                let html = '<div class="alert alert-';
                if (response.sentiment === 'positive') {
                    html += 'success';
                } else if (response.sentiment === 'negative') {
                    html += 'danger';
                } else {
                    html += 'warning';
                }
                html += '">';
                html += '<p class="mb-1"><strong>Sentimen:</strong> ' + response.sentiment + '</p>';
                html += '<p class="mb-0"><strong>Prediksi Viral:</strong> ' + response.viral + '</p>';
                html += '</div>';

                // Display result
                resultContainer.html(html);

                // Re-enable button
                button.prop('disabled', false).html('Analisis');
            },
            error: function(xhr, status, error) {
                console.error('Error analyzing content:', error);
                resultContainer.html('<div class="alert alert-danger">Gagal menganalisis konten</div>');
                button.prop('disabled', false).html('Analisis');
            }
        });
    });

    // Handle PDF export
    $('.btn-export-all-pdf').on('click', function() {
        const button = $(this);
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        // Prepare data for export based on engine type
        const resultsData = [];
        const engine = '<?= $engine ?>'; // Get engine type from PHP

        $('.search-result').each(function() {
            const result = $(this);
            let title, link, date, snippet, source = '';
            
            // Extract data based on engine type
            if (engine === 'google_images') {
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                source = result.find('.text-muted').text().trim();
                snippet = '';
            } else if (engine === 'youtube') {
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                source = result.find('.text-muted').text().trim();
                snippet = result.find('p:not(.text-muted)').text().trim();
            } else if (engine === 'twitter') {
                title = result.find('.card-body p').first().text().trim();
                link = result.find('.btn-outline-primary').attr('href') || '';
                date = result.find('.float-right.text-muted').text().trim();
                source = '@' + result.find('.card-header strong').text().trim();
                snippet = title;
            } else if (engine === 'reddit') {
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                date = result.find('.float-right.text-muted').text().trim();
                source = result.find('.card-header strong').text().trim();
                snippet = result.find('.card-body p').text().trim();
            } else {
                // Default extraction for Google, Bing, News, Scholar
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                date = result.find('.text-muted i.far.fa-clock').parent().text().trim();
                snippet = result.find('p:not(.text-muted)').text().trim();
            }
            
            // Find matching analysis result if available
            const analysisResult = analysisResults.find(item => item.title === title);
            
            resultsData.push({
                title: title || 'Untitled',
                link: link || '#',
                date: date || '',
                source: source || '',
                snippet: snippet || '',
                sentiment: analysisResult ? analysisResult.sentiment : null,
                viral: analysisResult ? analysisResult.viral : null
            });
        });

        // Send AJAX request to generate PDF
        $.ajax({
            url: '<?= base_url('serp/exportAllResultsPdf') ?>',
            method: 'POST',
            data: {
                query: '<?= esc($query) ?>',
                engine: '<?= esc($engine) ?>',
                results: JSON.stringify(resultsData),
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Create download link
                    const link = document.createElement('a');
                    link.href = 'data:application/pdf;base64,' + response.pdf;
                    link.download = 'hasil_pencarian_<?= url_title($query, '_', true) ?>_<?= date('Ymd_His') ?>.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Show success toast
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'PDF berhasil dibuat dan diunduh',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal membuat PDF'
                    });
                }
                
                // Re-enable button
                button.prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Ekspor ke PDF');
            },
            error: function(xhr, status, error) {
                console.error('Error exporting to PDF:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal membuat PDF: ' + error
                });
                button.prop('disabled', false).html('<i class="fas fa-file-pdf"></i> Ekspor ke PDF');
            }
        });
    });

    // Handle text export
    $('.btn-export-text').on('click', function() {
        const button = $(this);
        
        // Disable button and show loading state
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        // Prepare data for export
        const resultsData = [];
        const engine = '<?= $engine ?>'; // Get engine type from PHP

        $('.search-result').each(function() {
            const result = $(this);
            let title, link, date, snippet, source = '';
            
            // Extract data based on engine type
            if (engine === 'google_images') {
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                source = result.find('.text-muted').text().trim();
                snippet = '';
            } else if (engine === 'youtube') {
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                source = result.find('.text-muted').text().trim();
                snippet = result.find('p:not(.text-muted)').text().trim();
            } else if (engine === 'twitter') {
                title = result.find('.card-body p').first().text().trim();
                link = result.find('.btn-outline-primary').attr('href') || '';
                date = result.find('.float-right.text-muted').text().trim();
                source = '@' + result.find('.card-header strong').text().trim();
                snippet = title;
            } else if (engine === 'reddit') {
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                date = result.find('.float-right.text-muted').text().trim();
                source = result.find('.card-header strong').text().trim();
                snippet = result.find('.card-body p').text().trim();
            } else {
                // Default extraction for Google, Bing, News, Scholar
                title = result.find('h4 a').text();
                link = result.find('h4 a').attr('href');
                date = result.find('.text-muted i.far.fa-clock').parent().text().trim();
                snippet = result.find('p:not(.text-muted)').text().trim();
            }
            
            // Find matching analysis result if available
            const analysisResult = analysisResults.find(item => item.title === title);
            
            resultsData.push({
                title: title || 'Untitled',
                link: link || '#',
                date: date || '',
                source: source || '',
                snippet: snippet || '',
                sentiment: analysisResult ? analysisResult.sentiment : null,
                viral: analysisResult ? analysisResult.viral : null
            });
        });

        // Send AJAX request to generate text
        $.ajax({
            url: '<?= base_url('serp/exportToText') ?>',
            method: 'POST',
            data: {
                query: '<?= esc($query) ?>',
                engine: '<?= esc($engine) ?>',
                results: JSON.stringify(resultsData),
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Create a textarea to copy text
                    const textarea = document.createElement('textarea');
                    textarea.value = response.text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    
                    // Create a download link for text file
                    const blob = new Blob([response.text], { type: 'text/plain' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'laporan_pencarian_<?= url_title($query, '_', true) ?>_<?= date('Ymd_His') ?>.txt';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Show success toast
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Laporan teks berhasil dibuat dan disalin ke clipboard',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal membuat laporan teks'
                    });
                }
                
                // Re-enable button
                button.prop('disabled', false).html('<i class="fas fa-file-alt"></i> Ekspor ke Teks');
            },
            error: function(xhr, status, error) {
                console.error('Error exporting to text:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal membuat laporan teks: ' + error
                });
                button.prop('disabled', false).html('<i class="fas fa-file-alt"></i> Ekspor ke Teks');
            }
        });
    });
    
    // Add console logging for debugging
    console.log('Engine type:', '<?= $engine ?>');
    console.log('Query:', '<?= esc($query) ?>');
    console.log('Results count:', $('.search-result').length);
    
    // Log CSRF token existence for debugging
    console.log('CSRF token exists:', typeof <?= csrf_token() ?> !== 'undefined');
});
</script>
<?= $this->endSection() ?>