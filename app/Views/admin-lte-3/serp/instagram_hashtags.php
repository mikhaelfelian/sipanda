<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <!-- Search Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-2"></i>Hashtag Search Results for #<?= esc($query) ?>
                        </h3>
                        <div class="card-tools">
                            <a href="<?= site_url('serp/instagram') ?>" class="btn btn-tool" title="New Search">
                                <i class="fas fa-sync"></i> New Search
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hashtag Stats -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Hashtag Statistics</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-gradient-info">
                                    <span class="info-box-icon"><i class="fas fa-hashtag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hashtag</span>
                                        <span class="info-box-number">#<?= esc($query) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-gradient-success">
                                    <span class="info-box-icon"><i class="fas fa-photo-video"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Media Count</span>
                                        <span class="info-box-number"><?= number_format($mediaCount) ?> posts</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-gradient-warning">
                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Popularity</span>
                                        <span class="info-box-number">
                                            <?php if ($mediaCount > 1000000): ?>
                                                Very High
                                            <?php elseif ($mediaCount > 100000): ?>
                                                High
                                            <?php elseif ($mediaCount > 10000): ?>
                                                Medium
                                            <?php else: ?>
                                                Low
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-gradient-danger">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Last Updated</span>
                                        <span class="info-box-number"><?= date('M d, Y H:i') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Posts -->
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Recent Posts</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($medias)): ?>
                            <div class="row">
                                <?php foreach ($medias as $media): ?>
                                    <div class="col-md-4 col-sm-6 mb-4">
                                        <div class="card rounded-0 h-100">
                                            <div class="card-body p-0">
                                                <!-- Image -->
                                                <div class="position-relative">
                                                    <img src="<?= esc($media->getImageHighResolutionUrl()) ?>"
                                                        alt="Instagram Post"
                                                        class="img-fluid w-100"
                                                        style="max-height: 250px; object-fit: cover;">
                                                    
                                                    <!-- Overlay with stats -->
                                                    <div class="position-absolute" style="bottom: 0; right: 0; left: 0; padding: 10px; background: rgba(0,0,0,0.5);">
                                                        <div class="d-flex justify-content-around text-white">
                                                            <div>
                                                                <i class="fas fa-heart"></i> <?= number_format($media->getLikesCount()) ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-comment"></i> <?= number_format($media->getCommentsCount()) ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-calendar-alt"></i> <?= $media->getCreatedTime()->format('M d, Y') ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Caption -->
                                                <div class="p-3">
                                                    <?php if ($media->getCaption()): ?>
                                                        <p class="small text-muted mb-2">
                                                            <?= esc(mb_strimwidth($media->getCaption(), 0, 100, '...')) ?>
                                                        </p>
                                                    <?php else: ?>
                                                        <p class="small text-muted font-italic mb-2">No caption</p>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Author -->
                                                    <div class="d-flex align-items-center mt-2">
                                                        <a href="<?= site_url('serp/instagram/profile/' . $media->getOwner()->getUsername()) ?>" class="d-flex align-items-center text-decoration-none">
                                                            <img src="<?= esc($media->getOwner()->getProfilePicUrl()) ?>"
                                                                alt="<?= esc($media->getOwner()->getUsername()) ?>"
                                                                class="img-circle mr-2"
                                                                style="width: 30px; height: 30px;">
                                                            <span class="text-muted">@<?= esc($media->getOwner()->getUsername()) ?></span>
                                                        </a>
                                                    </div>
                                                    
                                                    <!-- Action buttons -->
                                                    <div class="mt-3">
                                                        <a href="<?= site_url('serp/instagram/post/' . $media->getShortCode()) ?>"
                                                            class="btn btn-primary btn-sm btn-block rounded-0">
                                                            <i class="fas fa-search mr-1"></i> View Details
                                                        </a>
                                                        
                                                        <a href="<?= esc($media->getLink()) ?>"
                                                            target="_blank"
                                                            class="btn btn-outline-secondary btn-sm btn-block rounded-0 mt-2">
                                                            <i class="fas fa-external-link-alt mr-1"></i> Open on Instagram
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if ($media->isVideo()): ?>
                                                <div class="ribbon-wrapper ribbon-lg">
                                                    <div class="ribbon bg-danger">
                                                        Video
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> No posts found!</h5>
                                No recent posts with the hashtag #<?= esc($query) ?> were found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back to search button -->
        <div class="row mt-3 mb-4">
            <div class="col-12 text-center">
                <a href="<?= site_url('serp/instagram') ?>" class="btn btn-default rounded-0">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Instagram Search
                </a>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 