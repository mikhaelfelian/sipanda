<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bug mr-2"></i> Instagram API Debug
                        </h3>
                        <div class="card-tools">
                            <a href="<?= site_url('serp/instagram') ?>" class="btn btn-tool">
                                <i class="fas fa-arrow-left"></i> Back to Instagram Search
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>Test Results</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Test</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($results['tests'])): ?>
                                        <?php foreach ($results['tests'] as $test): ?>
                                        <tr>
                                            <td><?= esc($test['name']) ?></td>
                                            <td>
                                                <?php if (isset($test['status']) && $test['status'] === 'success'): ?>
                                                    <span class="text-success"><i class="fas fa-check-circle mr-1"></i> <?= esc($test['result']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> <?= esc($test['result']) ?></span>
                                                    <?php if (isset($test['solution'])): ?>
                                                        <div class="mt-2 p-2 bg-light">
                                                            <strong>Solution:</strong> <?= esc($test['solution']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="text-center">No test results available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            <h4>Debug Information</h4>
                            <div class="alert alert-info">
                                <p>The "Undefined array key Set-Cookie" error occurs when the Instagram API response doesn't include the expected cookie headers. This can happen for several reasons:</p>
                                <ul>
                                    <li>Instagram has changed their API response format</li>
                                    <li>Instagram is rate-limiting the requests</li>
                                    <li>The IP address is being blocked by Instagram</li>
                                </ul>
                                <p>The error handling we've implemented tries to work around this issue by:</p>
                                <ol>
                                    <li>Detecting the Set-Cookie error</li>
                                    <li>Creating mock cookies to satisfy the requirement</li>
                                    <li>Gracefully handling failures and showing limited results</li>
                                </ol>
                            </div>
                            
                            <div class="mt-3">
                                <h5>Technical Details</h5>
                                <p>The error occurs in the <code>instagram-php-scraper</code> library when it tries to access the <code>Set-Cookie</code> key in the response headers array, but that key doesn't exist.</p>
                                <pre class="bg-light p-3"><?php
                                if (isset($results['tests'][1]['status']) && $results['tests'][1]['status'] === 'error') {
                                    echo esc($results['tests'][1]['result']);
                                } else {
                                    echo "No error detected in the current test.";
                                }
                                ?></pre>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="<?= site_url('serp/instagram/debug') ?>" class="btn btn-primary">
                                <i class="fas fa-sync mr-1"></i> Run Tests Again
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 