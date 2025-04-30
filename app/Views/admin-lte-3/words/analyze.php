<?php
/**
 * Text Sentiment Analysis View
 * 
 * This view displays the sentiment analysis results for a text
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if (session()->has('message')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= session('message') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= session('error') ?>
            </div>
        <?php endif; ?>

        <!-- Analyze Text Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Analyze Text Sentiment</h3>
            </div>
            <div class="card-body">
                <!-- Using GET method form -->
                <form action="<?= base_url('words/analyze') ?>" method="get">
                    <div class="form-group">
                        <label for="text">Text to Analyze</label>
                        <textarea class="form-control" id="text" name="text" rows="4" required><?= esc($text ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="language">Language</label>
                        <select class="form-control" id="language" name="language">
                            <option value="en" <?= ($language ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                            <option value="id" <?= ($language ?? 'en') === 'id' ? 'selected' : '' ?>>Indonesian</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Analyze Sentiment</button>
                </form>
            </div>
        </div>

        <?php if (isset($analysis)): ?>
        <!-- Analysis Results Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Analysis Results</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Classification</span>
                                <span class="info-box-number">
                                    <?php
                                    $sentiment = $analysis['classification'];
                                    $icon = 'far fa-meh';
                                    $color = 'text-secondary';
                                    
                                    if ($sentiment === 'positive') {
                                        $icon = 'far fa-smile';
                                        $color = 'text-success';
                                    } elseif ($sentiment === 'negative') {
                                        $icon = 'far fa-frown';
                                        $color = 'text-danger';
                                    }
                                    ?>
                                    <i class="<?= $icon ?> <?= $color ?> mr-2"></i>
                                    <span class="<?= $color ?> text-uppercase font-weight-bold"><?= ucfirst($sentiment) ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sentiment Score</span>
                                <span class="info-box-number">
                                    <?php
                                    $totalScore = $analysis['total_score'];
                                    $color = 'text-secondary';
                                    
                                    if ($totalScore > 0) {
                                        $color = 'text-success';
                                    } elseif ($totalScore < 0) {
                                        $color = 'text-danger';
                                    }
                                    ?>
                                    <span class="<?= $color ?> font-weight-bold"><?= $totalScore ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Positive Words (<?= count($analysis['positive_words']) ?>)</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Word</th>
                                            <th>Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($analysis['positive_words'] as $word): ?>
                                            <tr>
                                                <td><?= esc($word['word']) ?></td>
                                                <td><?= $word['weight'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($analysis['positive_words'])): ?>
                                            <tr>
                                                <td colspan="2" class="text-center">No positive words found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title">Negative Words (<?= count($analysis['negative_words']) ?>)</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Word</th>
                                            <th>Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($analysis['negative_words'] as $word): ?>
                                            <tr>
                                                <td><?= esc($word['word']) ?></td>
                                                <td><?= $word['weight'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($analysis['negative_words'])): ?>
                                            <tr>
                                                <td colspan="2" class="text-center">No negative words found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Summary</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 200px">Text</th>
                                            <td><?= esc($analysis['text']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Word Count</th>
                                            <td><?= $analysis['word_count'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Positive Score</th>
                                            <td class="text-success"><?= $analysis['positive_score'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Negative Score</th>
                                            <td class="text-danger"><?= $analysis['negative_score'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Score</th>
                                            <td class="<?= $color ?> font-weight-bold"><?= $analysis['total_score'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Classification</th>
                                            <td class="<?= $color ?> font-weight-bold">
                                                <i class="<?= $icon ?> mr-2"></i> <?= ucfirst($analysis['classification']) ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?> 