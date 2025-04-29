<?= $this->extend('admin-lte-3/layouts/main') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-1"></i> Instagram Hashtag Results for "#<?= esc($query) ?>"
                        </h3>
                        <div class="card-tools">
                            <a href="<?= base_url('serp/instagram') ?>" class="btn btn-sm btn-outline-secondary rounded-0">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Search
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($hashtags)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> No hashtags found matching "#<?= esc($query) ?>".
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($hashtags as $hashtag): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card rounded-0 h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">#<?= esc($hashtag->getName()) ?></h5>
                                                <p class="card-text">
                                                    <i class="fas fa-image mr-1"></i> 
                                                    <?= number_format($hashtag->getMediaCount()) ?> posts
                                                </p>
                                                <?php if (!empty($hashtag->getTopPosts())): ?>
                                                    <div class="row mt-3">
                                                        <?php foreach (array_slice($hashtag->getTopPosts(), 0, 3) as $post): ?>
                                                            <div class="col-4">
                                                                <a href="<?= base_url('serp/instagram/post/' . esc($post->getShortcode())) ?>" 
                                                                   target="_blank">
                                                                    <img src="<?= esc($post->getThumbnailSrc()) ?>" 
                                                                         alt="#<?= esc($hashtag->getName()) ?> post" 
                                                                         class="img-fluid">
                                                                </a>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-footer bg-white border-top">
                                                <a href="https://www.instagram.com/explore/tags/<?= esc($hashtag->getName()) ?>" 
                                                   class="btn btn-primary btn-sm btn-block rounded-0" target="_blank">
                                                    <i class="fas fa-hashtag mr-1"></i> View on Instagram
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 