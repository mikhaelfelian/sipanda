<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Alat Analisis Sentimen</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="text-to-analyze">Masukkan teks untuk dianalisis</label>
                            <textarea class="form-control rounded-0" id="text-to-analyze" rows="5"
                                placeholder="Masukkan teks di sini..."></textarea>
                        </div>
                        <button type="button" class="btn btn-primary rounded-0" id="analyze-button">Analisis Sentimen</button>

                        <div class="mt-4" id="result-container" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>Hasil Analisis</h4>
                                <button type="button" class="btn btn-success rounded-0" id="export-pdf-button">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box rounded-0">
                                        <span class="info-box-icon bg-info rounded-0"><i class="fas fa-balance-scale"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sentimen</span>
                                            <span class="info-box-number" id="sentiment-result">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box rounded-0">
                                        <span class="info-box-icon bg-success rounded-0"><i class="fas fa-smile"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Skor Positif</span>
                                            <span class="info-box-number" id="positive-score">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box rounded-0">
                                        <span class="info-box-icon bg-danger rounded-0"><i class="fas fa-frown"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Skor Negatif</span>
                                            <span class="info-box-number" id="negative-score">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card rounded-0">
                                        <div class="card-header">
                                            <h3 class="card-title">Kata-kata Positif</h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group rounded-0" id="positive-words-list"></ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card rounded-0">
                                        <div class="card-header">
                                            <h3 class="card-title">Kata-kata Negatif</h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group rounded-0" id="negative-words-list"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Store the analysis results
        let analysisResults = null;
        
        $('#analyze-button').click(function () {
            const text = $('#text-to-analyze').val().trim();

            if (text === '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Silakan masukkan teks untuk dianalisis'
                });
                return;
            }

            // Show loading state
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Menganalisis...').attr('disabled', true);

            $.ajax({
                url: '<?= base_url('serp/sentiment/analyze') ?>',
                type: 'POST',
                data: {
                    text: text
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Store results for PDF export
                        analysisResults = response;
                        analysisResults.text = text;
                        
                        // Display the results
                        let sentimentText = response.sentiment.toUpperCase();
                        if (sentimentText === 'POSITIVE') sentimentText = 'POSITIF';
                        if (sentimentText === 'NEGATIVE') sentimentText = 'NEGATIF';
                        if (sentimentText === 'NEUTRAL') sentimentText = 'NETRAL';
                        
                        $('#sentiment-result').text(sentimentText);
                        $('#sentiment-result').removeClass('text-success text-danger text-warning')
                            .addClass(response.sentiment === 'positive' ? 'text-success' :
                                (response.sentiment === 'negative' ? 'text-danger' : 'text-warning'));

                        $('#positive-score').text(response.positiveScore);
                        $('#negative-score').text(response.negativeScore);

                        // Display word lists
                        $('#positive-words-list').empty();
                        $('#negative-words-list').empty();

                        if (response.positiveWords && response.positiveWords.length > 0) {
                            response.positiveWords.forEach(function (word) {
                                $('#positive-words-list').append('<li class="list-group-item rounded-0">' + word + '</li>');
                            });
                        } else {
                            $('#positive-words-list').append('<li class="list-group-item rounded-0">Tidak ditemukan kata-kata positif</li>');
                        }

                        if (response.negativeWords && response.negativeWords.length > 0) {
                            response.negativeWords.forEach(function (word) {
                                $('#negative-words-list').append('<li class="list-group-item rounded-0">' + word + '</li>');
                            });
                        } else {
                            $('#negative-words-list').append('<li class="list-group-item rounded-0">Tidak ditemukan kata-kata negatif</li>');
                        }

                        // Show results container
                        $('#result-container').show();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message || 'Terjadi kesalahan'
                        });
                    }
                },
                error: function () {
                    Toast.fire({
                        icon: 'error',
                        title: 'Gagal menganalisis teks. Silakan coba lagi.'
                    });
                },
                complete: function () {
                    // Reset button state
                    $('#analyze-button').html('Analisis Sentimen').attr('disabled', false);
                }
            });
        });
        
        // PDF Export button handler
        $('#export-pdf-button').click(function() {
            if (!analysisResults) {
                Toast.fire({
                    icon: 'error',
                    title: 'Tidak ada hasil analisis untuk diekspor'
                });
                return;
            }
            
            // Show loading state
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Generating PDF...').attr('disabled', true);
            
            // Prepare data for PDF export
            const data = {
                text: analysisResults.text,
                sentiment: analysisResults.sentiment,
                positiveScore: analysisResults.positiveScore,
                negativeScore: analysisResults.negativeScore,
                positiveWords: JSON.stringify(analysisResults.positiveWords),
                negativeWords: JSON.stringify(analysisResults.negativeWords)
            };
            
            // Send request to generate PDF
            $.ajax({
                url: '<?= base_url('serp/sentiment/export-pdf') ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Create a download link for the PDF
                        const pdfData = 'data:application/pdf;base64,' + response.pdf;
                        const link = document.createElement('a');
                        link.href = pdfData;
                        link.download = 'sentiment_analysis_report.pdf';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        Toast.fire({
                            icon: 'success',
                            title: 'PDF berhasil dibuat'
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message || 'Gagal membuat PDF'
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Gagal meng-export PDF. Silakan coba lagi.'
                    });
                },
                complete: function() {
                    // Reset button state
                    $('#export-pdf-button').html('<i class="fas fa-file-pdf"></i> Export PDF').attr('disabled', false);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>