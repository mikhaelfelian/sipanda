<?= $this->extend('admin-lte-3/layouts/main') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-1"></i> Instagram Profile Results for "<?= esc($query) ?>"
                        </h3>
                        <div class="card-tools">
                            <a href="<?= base_url('serp/instagram') ?>" class="btn btn-sm btn-outline-secondary rounded-0">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Search
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($profiles)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> No profiles found matching "<?= esc($query) ?>".
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($profiles as $profile): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card rounded-0 h-100">
                                            <div class="card-body text-center">
                                                <img src="<?= esc($profile->getProfilePicUrl()) ?>" 
                                                     alt="<?= esc($profile->getUsername()) ?>'s profile picture" 
                                                     class="rounded-circle mb-3" style="width: 100px; height: 100px;">
                                                
                                                <h5 class="card-title">
                                                    <?= esc($profile->getUsername()) ?>
                                                    <?php if ($profile->isVerified()): ?>
                                                        <i class="fas fa-check-circle text-primary ml-1" title="Verified Account"></i>
                                                    <?php endif; ?>
                                                </h5>
                                                
                                                <?php if (!empty($profile->getFullName())): ?>
                                                    <p class="text-muted"><?= esc($profile->getFullName()) ?></p>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($profile->getBiography())): ?>
                                                    <p class="small mt-2">
                                                        <?= nl2br(esc(substr($profile->getBiography(), 0, 150) . (strlen($profile->getBiography()) > 150 ? '...' : ''))) ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <div class="row text-center mt-3">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold"><?= number_format($profile->getFollowersCount()) ?></div>
                                                        <div class="small text-muted">Followers</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="font-weight-bold"><?= number_format($profile->getFollowingCount()) ?></div>
                                                        <div class="small text-muted">Following</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="font-weight-bold"><?= number_format($profile->getMediaCount()) ?></div>
                                                        <div class="small text-muted">Posts</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-white border-top">
                                                <a href="<?= base_url('serp/instagram/profile/' . esc($profile->getUsername())) ?>" 
                                                   class="btn btn-info btn-sm rounded-0 mr-1">
                                                    <i class="fas fa-search mr-1"></i> View Details
                                                </a>
                                                <a href="https://www.instagram.com/<?= esc($profile->getUsername()) ?>" 
                                                   class="btn btn-primary btn-sm rounded-0" target="_blank">
                                                    <i class="fab fa-instagram mr-1"></i> View on Instagram
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