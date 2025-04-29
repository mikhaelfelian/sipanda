<?= $this->extend('admin-lte-3/layouts/main') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-1"></i> Instagram Profile Details
                        </h3>
                        <div class="card-tools">
                            <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary rounded-0">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($profile)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle mr-1"></i> Profile not found or unavailable.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="<?= esc($profile->getProfilePicUrl()) ?>" 
                                         alt="<?= esc($profile->getUsername()) ?>'s profile picture" 
                                         class="rounded-circle mb-3" style="width: 150px; height: 150px;">
                                    
                                    <h4>
                                        <?= esc($profile->getUsername()) ?>
                                        <?php if ($profile->isVerified()): ?>
                                            <i class="fas fa-check-circle text-primary ml-1" title="Verified Account"></i>
                                        <?php endif; ?>
                                    </h4>
                                    
                                    <?php if (!empty($profile->getFullName())): ?>
                                        <h5 class="text-muted"><?= esc($profile->getFullName()) ?></h5>
                                    <?php endif; ?>
                                    
                                    <div class="row mt-4">
                                        <div class="col-4">
                                            <div class="h4"><?= number_format($profile->getFollowersCount()) ?></div>
                                            <div class="text-muted">Followers</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4"><?= number_format($profile->getFollowingCount()) ?></div>
                                            <div class="text-muted">Following</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4"><?= number_format($profile->getMediaCount()) ?></div>
                                            <div class="text-muted">Posts</div>
                                        </div>
                                    </div>
                                    
                                    <a href="https://www.instagram.com/<?= esc($profile->getUsername()) ?>" 
                                       class="btn btn-primary btn-block rounded-0 mt-4" target="_blank">
                                        <i class="fab fa-instagram mr-1"></i> View on Instagram
                                    </a>
                                </div>
                                <div class="col-md-8">
                                    <div class="card rounded-0 mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0"><i class="fas fa-info-circle mr-1"></i> Profile Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if (!empty($profile->getBiography())): ?>
                                                <h6>Biography</h6>
                                                <p class="mb-4"><?= nl2br(esc($profile->getBiography())) ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($profile->getExternalUrl())): ?>
                                                <h6>Website</h6>
                                                <p class="mb-4">
                                                    <a href="<?= esc($profile->getExternalUrl()) ?>" target="_blank">
                                                        <?= esc($profile->getExternalUrl()) ?>
                                                    </a>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <h6>Account Information</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <th style="width: 150px;">Profile ID</th>
                                                    <td><?= esc($profile->getId()) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Account Type</th>
                                                    <td>
                                                        <?php if ($profile->isBusinessAccount()): ?>
                                                            <span class="badge badge-info">Business Account</span>
                                                        <?php elseif ($profile->isProfessionalAccount()): ?>
                                                            <span class="badge badge-info">Professional Account</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Personal Account</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php if ($profile->isPrivate()): ?>
                                                <tr>
                                                    <th>Privacy</th>
                                                    <td><span class="badge badge-warning">Private Account</span></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($profile->getCategoryName())): ?>
                                                <tr>
                                                    <th>Category</th>
                                                    <td><?= esc($profile->getCategoryName()) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($recentPosts) && is_array($recentPosts)): ?>
                                    <div class="card rounded-0">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0"><i class="fas fa-photo-video mr-1"></i> Recent Posts</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <?php foreach ($recentPosts as $post): ?>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="card rounded-0 h-100">
                                                            <img src="<?= esc($post->getImageUrl()) ?>" 
                                                                 class="card-img-top" 
                                                                 alt="Instagram post" 
                                                                 style="height: 150px; object-fit: cover;">
                                                            <div class="card-body p-2">
                                                                <div class="small text-muted mb-2">
                                                                    <i class="far fa-heart mr-1"></i> <?= number_format($post->getLikeCount()) ?>
                                                                    <i class="far fa-comment ml-2 mr-1"></i> <?= number_format($post->getCommentCount()) ?>
                                                                </div>
                                                                <p class="small mb-0 text-truncate">
                                                                    <?= esc(substr($post->getCaption(), 0, 50)) ?>
                                                                    <?= strlen($post->getCaption()) > 50 ? '...' : '' ?>
                                                                </p>
                                                            </div>
                                                            <div class="card-footer p-2 bg-white">
                                                                <a href="<?= esc($post->getUrl()) ?>" 
                                                                   class="btn btn-sm btn-outline-primary btn-block rounded-0" 
                                                                   target="_blank">
                                                                    <i class="fas fa-external-link-alt mr-1"></i> View
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                            <div class="text-center mt-3">
                                                <a href="<?= base_url('serp/instagram/posts/' . esc($profile->getUsername())) ?>" 
                                                   class="btn btn-info rounded-0">
                                                    <i class="fas fa-photo-video mr-1"></i> View All Posts
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 