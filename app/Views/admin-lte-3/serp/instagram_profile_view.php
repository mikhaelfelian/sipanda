<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="row">
                            <!-- Profile Picture Column -->
                            <div class="col-md-3 text-center">
                                <img src="<?= esc($account->getProfilePicUrl()) ?>" 
                                     alt="<?= esc($account->getUsername()) ?>" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #f8f9fa;">
                                     
                                <?php if ($account->isVerified()): ?>
                                    <div class="mt-2">
                                        <span class="badge badge-primary rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle mr-1"></i> Verified
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <a href="https://instagram.com/<?= esc($account->getUsername()) ?>" target="_blank" class="btn btn-outline-primary btn-sm btn-block rounded-0">
                                        <i class="fas fa-external-link-alt mr-1"></i> Open on Instagram
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Profile Info Column -->
                            <div class="col-md-9">
                                <h2 class="mb-0"><?= esc($account->getUsername()) ?></h2>
                                <?php if ($account->getFullName()): ?>
                                    <h5 class="text-muted"><?= esc($account->getFullName()) ?></h5>
                                <?php endif; ?>
                                
                                <!-- Stats Badges -->
                                <div class="row mt-4 mb-3">
                                    <div class="col-md-4">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content text-center">
                                                <span class="info-box-number"><?= number_format($account->getFollowedByCount()) ?></span>
                                                <span class="info-box-text">Followers</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content text-center">
                                                <span class="info-box-number"><?= number_format($account->getFollowsCount()) ?></span>
                                                <span class="info-box-text">Following</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-light">
                                            <div class="info-box-content text-center">
                                                <span class="info-box-number"><?= number_format($account->getMediaCount()) ?></span>
                                                <span class="info-box-text">Posts</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Biography -->
                                <?php if ($account->getBiography()): ?>
                                    <div class="mt-3">
                                        <h6 class="font-weight-bold">Bio</h6>
                                        <p class="text-muted"><?= nl2br(esc($account->getBiography())) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- External URL -->
                                <?php if ($account->getExternalUrl()): ?>
                                    <div class="mt-3">
                                        <h6 class="font-weight-bold">Website</h6>
                                        <a href="<?= esc($account->getExternalUrl()) ?>" target="_blank" class="text-primary">
                                            <?= esc($account->getExternalUrl()) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Additional Info -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Account Analytics</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-bullseye text-info mr-2"></i> Engagement Rate: 
                                                <?php 
                                                    $engagementRate = ($account->getMediaCount() > 0 && $account->getFollowedByCount() > 0) 
                                                        ? round(($account->getMediaCount() / $account->getFollowedByCount()) * 100, 2) 
                                                        : 0;
                                                    echo $engagementRate . '%';
                                                ?>
                                            </li>
                                            <li class="mt-2"><i class="fas fa-chart-bar text-success mr-2"></i> Follower Ratio: 
                                                <?php 
                                                    $followerRatio = ($account->getFollowsCount() > 0) 
                                                        ? round($account->getFollowedByCount() / $account->getFollowsCount(), 2) 
                                                        : 0;
                                                    echo $followerRatio;
                                                ?>
                                            </li>
                                            <li class="mt-2"><i class="fas fa-clock text-warning mr-2"></i> Last Updated: <?= date('M d, Y H:i') ?></li>
                                        </ul>
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
                                No recent posts by this user were found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back buttons -->
        <div class="row mt-3 mb-4">
            <div class="col-12 text-center">
                <a href="javascript:history.back()" class="btn btn-default rounded-0 mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Results
                </a>
                <a href="<?= site_url('serp/instagram') ?>" class="btn btn-outline-primary rounded-0">
                    <i class="fas fa-search mr-1"></i> New Instagram Search
                </a>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 