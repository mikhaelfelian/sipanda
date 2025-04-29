<?= $this->extend('admin-lte-3/layouts/main') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-1"></i> Instagram Posts - <?= esc($username) ?>
                        </h3>
                        <div class="card-tools">
                            <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary rounded-0">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($posts) || !is_array($posts)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i> No posts found for @<?= esc($username) ?>.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($posts as $post): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card rounded-0 h-100">
                                            <div class="card-header bg-light py-2">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= esc($profilePicUrl) ?>" 
                                                         alt="Profile Picture" 
                                                         class="rounded-circle mr-2" 
                                                         style="width: 36px; height: 36px;">
                                                    <div>
                                                        <div class="font-weight-bold"><?= esc($username) ?></div>
                                                        <div class="small text-muted">
                                                            <?= date('M d, Y', strtotime($post->getTimestamp())) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="position-relative">
                                                <img src="<?= esc($post->getImageUrl()) ?>" 
                                                     class="card-img-top" 
                                                     alt="Instagram post" 
                                                     style="height: 250px; object-fit: cover;">
                                                     
                                                <?php if ($post->isVideo()): ?>
                                                <div class="position-absolute" style="top: 10px; right: 10px;">
                                                    <span class="badge badge-danger p-2">
                                                        <i class="fas fa-video"></i> Video
                                                    </span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="card-body">
                                                <div class="mb-2">
                                                    <span class="mr-3"><i class="far fa-heart mr-1"></i> <?= number_format($post->getLikeCount()) ?></span>
                                                    <span><i class="far fa-comment mr-1"></i> <?= number_format($post->getCommentCount()) ?></span>
                                                </div>
                                                
                                                <?php if (!empty($post->getCaption())): ?>
                                                <p class="card-text">
                                                    <?php 
                                                    $caption = esc($post->getCaption());
                                                    echo (strlen($caption) > 150) ? substr($caption, 0, 150) . '...' : $caption; 
                                                    ?>
                                                </p>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($post->getHashtags())): ?>
                                                <div class="mt-2 small">
                                                    <?php foreach ($post->getHashtags() as $hashtag): ?>
                                                        <span class="badge badge-light mr-1 mb-1">#<?= esc($hashtag) ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="card-footer bg-white">
                                                <a href="<?= esc($post->getUrl()) ?>" 
                                                   class="btn btn-sm btn-outline-primary btn-block rounded-0" 
                                                   target="_blank">
                                                    <i class="fas fa-external-link-alt mr-1"></i> View on Instagram
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (isset($pagination) && $pagination): ?>
                                <div class="mt-4">
                                    <?= $pagination ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 