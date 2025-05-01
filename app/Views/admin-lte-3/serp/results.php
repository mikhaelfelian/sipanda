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
$(document).ready(function() {
    // Analyze button click
    $('.btn-analyze').on('click', function() {
        var title = $(this).data('title');
        var snippet = $(this).data('snippet');
        var button = $(this);
        var resultDiv = button.siblings('.analyze-result');

        // Check if analysis already done
        if (resultDiv.html() !== '') {
            resultDiv.toggle();
            return;
        }

        // Show loading
        resultDiv.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Sedang menganalisis...</div>');

        $.ajax({
            url: '<?= base_url('serp/sentiment/analyze') ?>',
            type: 'POST',
            data: {
                text: title + ' ' + snippet
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var sentiment = response.sentiment;
                    var badgeClass = sentiment === 'positive' ? 'success' : (sentiment === 'negative' ? 'danger' : 'warning');
                    var sentimentText = sentiment === 'positive' ? 'Positif' : (sentiment === 'negative' ? 'Negatif' : 'Netral');
                    
                    var html = '<div class="card">';
                    html += '<div class="card-header bg-' + badgeClass + '">';
                    html += '<h5 class="mb-0">Sentimen: <span class="badge badge-light">' + sentimentText + '</span></h5>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    html += '<div class="row">';
                    
                    // Scores
                    html += '<div class="col-md-6">';
                    html += '<p><strong>Skor Positif:</strong> <span class="badge badge-success">' + response.positiveScore + '%</span></p>';
                    html += '<p><strong>Skor Negatif:</strong> <span class="badge badge-danger">' + response.negativeScore + '%</span></p>';
                    html += '</div>';
                    
                    // Words lists
                    html += '<div class="col-md-6">';
                    if (response.positiveWords && response.positiveWords.length > 0) {
                        html += '<p><strong>Kata Positif:</strong> ';
                        response.positiveWords.forEach(function(word) {
                            html += '<span class="badge badge-light mr-1">' + word + '</span>';
                        });
                        html += '</p>';
                    }
                    
                    if (response.negativeWords && response.negativeWords.length > 0) {
                        html += '<p><strong>Kata Negatif:</strong> ';
                        response.negativeWords.forEach(function(word) {
                            html += '<span class="badge badge-light mr-1">' + word + '</span>';
                        });
                        html += '</p>';
                    }
                    html += '</div>';
                    
                    html += '</div>'; // row
                    html += '</div>'; // card-body
                    html += '</div>'; // card
                    
                    resultDiv.html(html);
                } else {
                    resultDiv.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                resultDiv.html('<div class="alert alert-danger">Terjadi kesalahan saat menganalisis teks.</div>');
            }
        });
    });

    // Export all to PDF
    $('.btn-export-all-pdf').on('click', function() {
        var searchKeyword = '<?= isset($keyword) ? esc($keyword) : "-" ?>';
        var searchParam = '<?= isset($searchTool) ? esc($searchTool) : "-" ?>';
        var osintAnalysisData = <?= json_encode($osintAnalysis) ?>;
        var aiAnalysisData = <?= !empty($aiAnalysis) && $useAI ? json_encode($aiAnalysis) : 'null' ?>;
        var results = <?= json_encode($results) ?>;

        // Show loading
        Swal.fire({
            title: 'Sedang Menyiapkan PDF',
            html: 'Mohon tunggu...',
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false
        });

        $.ajax({
            url: '<?= base_url('serp/export-pdf') ?>',
            type: 'POST',
            data: {
                search_keyword: searchKeyword,
                search_param: searchParam,
                osint_analysis: JSON.stringify(osintAnalysisData),
                ai_analysis: JSON.stringify(aiAnalysisData),
                search_results: JSON.stringify(results)
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    // Create download link
                    var link = document.createElement('a');
                    link.href = response.file_url;
                    link.download = response.file_name;
                    link.click();

                    // Success message
                    Toastify({
                        text: "PDF berhasil dibuat!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                    }).showToast();
                } else {
                    // Error message
                    Toastify({
                        text: response.message || "Gagal membuat PDF",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                    }).showToast();
                }
            },
            error: function() {
                Swal.close();
                Toastify({
                    text: "Terjadi kesalahan saat membuat PDF",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        });
    });

    // Export to Text
    $('.btn-export-text').on('click', function() {
        var searchKeyword = '<?= isset($keyword) ? esc($keyword) : "-" ?>';
        var searchParam = '<?= isset($searchTool) ? esc($searchTool) : "-" ?>';
        var osintAnalysisData = <?= json_encode($osintAnalysis) ?>;
        var aiAnalysisData = <?= !empty($aiAnalysis) && $useAI ? json_encode($aiAnalysis) : 'null' ?>;
        var results = <?= json_encode($results) ?>;
        
        // Create text content
        var textContent = "LAPORAN PENCARIAN SERP\n";
        textContent += "==============================\n\n";
        textContent += "Kata Kunci: " + searchKeyword + "\n";
        textContent += "Parameter Pencarian: " + searchParam + "\n";
        textContent += "Tanggal Ekspor: " + new Date().toLocaleString() + "\n\n";
        
        // OSINT Analysis
        textContent += "ANALISIS OSINT\n";
        textContent += "==============================\n";
        textContent += "Skor Kepercayaan: " + osintAnalysisData.trust_score + "%\n\n";
        
        // AI Analysis if available
        if (aiAnalysisData) {
            textContent += "ANALISIS AI\n";
            textContent += "==============================\n";
            
            if (aiAnalysisData.analysis) {
                textContent += aiAnalysisData.analysis + "\n\n";
            } else {
                if (aiAnalysisData.topik_utama || aiAnalysisData.topikUtama) {
                    textContent += "Topik Utama: " + (aiAnalysisData.topik_utama || aiAnalysisData.topikUtama) + "\n";
                }
                
                if (aiAnalysisData.sub_topik || aiAnalysisData.subTopik) {
                    textContent += "Sub-topik Relevan:\n";
                    var subTopics = aiAnalysisData.sub_topik || aiAnalysisData.subTopik || [];
                    if (typeof subTopics === 'string') {
                        textContent += "- " + subTopics + "\n";
                    } else if (Array.isArray(subTopics)) {
                        subTopics.forEach(function(topic) {
                            textContent += "- " + (typeof topic === 'object' ? JSON.stringify(topic) : topic) + "\n";
                        });
                    }
                }
                
                if (aiAnalysisData.maksud_pengguna || aiAnalysisData.maksudPengguna || aiAnalysisData.intent) {
                    textContent += "Maksud Pengguna: " + (aiAnalysisData.maksud_pengguna || aiAnalysisData.maksudPengguna || aiAnalysisData.intent) + "\n";
                }
            }
            textContent += "\n";
        }
        
        // Search results
        textContent += "HASIL PENCARIAN\n";
        textContent += "==============================\n\n";
        if (results && results.length > 0) {
            results.forEach(function(result, index) {
                textContent += (index + 1) + ". " + result.title + "\n";
                textContent += "   URL: " + result.link + "\n";
                if (result.snippet) {
                    textContent += "   Cuplikan: " + (typeof result.snippet === 'object' ? JSON.stringify(result.snippet) : result.snippet) + "\n";
                }
                textContent += "\n";
            });
        } else {
            textContent += "Tidak ada hasil ditemukan.\n";
        }

        // Create and download text file
        var blob = new Blob([textContent], { type: 'text/plain' });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'laporan_pencarian_' + searchKeyword.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '_' + new Date().getTime() + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        // Success message
        Toastify({
            text: "Teks berhasil diekspor!",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#28a745",
        }).showToast();
    });
});
</script>
<?= $this->endSection() ?>