<?= $this->extend('admin-lte-3/layouts/main') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-1"></i> Instagram Profile
                        </h3>
                        <div class="card-tools">
                            <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary rounded-0">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($profile)): ?>
                            <div class="alert alert-warning m-3">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Profile not found.
                            </div>
                        <?php else: ?>
                            <!-- Profile Header -->
                            <div class="p-3 bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-sm-3 text-center">
                                        <img src="<?= esc($profile->getProfilePicUrl()) ?>" 
                                             alt="<?= esc($profile->getUsername()) ?> profile picture" 
                                             class="rounded-circle img-thumbnail" 
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-10 col-sm-9">
                                        <div class="d-flex align-items-center mb-2">
                                            <h4 class="mb-0"><?= esc($profile->getUsername()) ?></h4>
                                            <?php if ($profile->isVerified()): ?>
                                                <span class="badge badge-primary ml-2" title="Verified Account">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($profile->isPrivate()): ?>
                                                <span class="badge badge-warning ml-2" title="Private Account">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mt-2 mb-3">
                                            <div class="mr-4">
                                                <span class="font-weight-bold"><?= number_format($profile->getPostCount()) ?></span>
                                                <span class="text-muted">posts</span>
                                            </div>
                                            <div class="mr-4">
                                                <span class="font-weight-bold"><?= number_format($profile->getFollowerCount()) ?></span>
                                                <span class="text-muted">followers</span>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold"><?= number_format($profile->getFollowingCount()) ?></span>
                                                <span class="text-muted">following</span>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <?php if (!empty($profile->getFullName())): ?>
                                                <p class="font-weight-bold mb-1"><?= esc($profile->getFullName()) ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($profile->getBiography())): ?>
                                                <p class="mb-1"><?= nl2br(esc($profile->getBiography())) ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($profile->getExternalUrl())): ?>
                                                <p class="mb-1">
                                                    <a href="<?= esc($profile->getExternalUrl()) ?>" target="_blank">
                                                        <?= esc($profile->getExternalUrl()) ?>
                                                    </a>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="p-3 border-top">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <a href="<?= base_url('serp/instagram/profile/' . esc($profile->getUsername()) . '/details') ?>" 
                                           class="btn btn-info btn-block rounded-0">
                                            <i class="fas fa-info-circle mr-1"></i> View Detailed Profile
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <a href="<?= base_url('serp/instagram/profile/' . esc($profile->getUsername()) . '/posts') ?>" 
                                           class="btn btn-primary btn-block rounded-0">
                                            <i class="fas fa-th mr-1"></i> View All Posts
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Posts Preview -->
                            <?php if (!$profile->isPrivate() && !empty($recentPosts)): ?>
                                <div class="p-3 border-top">
                                    <h5 class="mb-3">Recent Posts</h5>
                                    <div class="row">
                                        <?php foreach (array_slice($recentPosts, 0, 6) as $post): ?>
                                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                                <a href="<?= esc($post->getUrl()) ?>" target="_blank" class="d-block position-relative">
                                                    <img src="<?= esc($post->getImageUrl()) ?>" 
                                                         alt="Post" 
                                                         class="img-fluid" 
                                                         style="height: 120px; width: 100%; object-fit: cover;">
                                                    
                                                    <?php if ($post->isVideo()): ?>
                                                        <span class="position-absolute badge badge-danger" style="top: 5px; right: 5px;">
                                                            <i class="fas fa-video"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <div class="position-absolute d-flex justify-content-between w-100 px-2" 
                                                         style="bottom: 5px; left: 0; right: 0; font-size: 0.7rem; color: white; text-shadow: 0 0 3px rgba(0,0,0,0.8);">
                                                        <span><i class="fas fa-heart"></i> <?= number_format($post->getLikeCount()) ?></span>
                                                        <span><i class="fas fa-comment"></i> <?= number_format($post->getCommentCount()) ?></span>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="text-center mt-2">
                                        <a href="<?= base_url('serp/instagram/profile/' . esc($profile->getUsername()) . '/posts') ?>" 
                                           class="btn btn-sm btn-outline-secondary rounded-0">
                                            <i class="fas fa-images mr-1"></i> View All Posts
                                        </a>
                                    </div>
                                </div>
                            <?php elseif ($profile->isPrivate()): ?>
                                <div class="p-3 border-top">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-lock mr-1"></i> This account is private. Posts cannot be viewed.
                                    </div>
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