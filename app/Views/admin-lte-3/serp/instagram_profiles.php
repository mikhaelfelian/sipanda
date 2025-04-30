<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-1"></i> Hasil Pencarian Profil Instagram untuk "<?= esc($query) ?>"
                        </h3>
                        <div class="card-tools">
                            <a href="<?= base_url('serp/instagram') ?>" class="btn btn-sm btn-outline-secondary rounded-0">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Pencarian
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($profiles)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i> Tidak ditemukan profil yang cocok dengan "<?= esc($query) ?>".
                                <p class="mb-0 mt-2">Hal ini mungkin disebabkan oleh:</p>
                                <ul class="mb-0">
                                    <li>Pembatasan akses dari Instagram (coba lagi dalam beberapa menit)</li>
                                    <li>Kata pencarian terlalu umum</li>
                                    <li>Tidak ada profil yang cocok</li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($profiles as $profile): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card rounded-0 h-100">
                                            <div class="card-body text-center">
                                                <img src="<?= esc($profile->getProfilePicUrl()) ?>" 
                                                     alt="Foto profil <?= esc($profile->getUsername()) ?>" 
                                                     class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                                                
                                                <h5 class="card-title">
                                                    <?= esc($profile->getUsername()) ?>
                                                    <?php if (method_exists($profile, 'isVerified') && $profile->isVerified()): ?>
                                                        <i class="fas fa-check-circle text-primary ml-1" title="Akun Terverifikasi"></i>
                                                    <?php elseif (isset($profile->isVerified) && is_callable($profile->isVerified) && $profile->isVerified()): ?>
                                                        <i class="fas fa-check-circle text-primary ml-1" title="Akun Terverifikasi"></i>
                                                    <?php endif; ?>
                                                </h5>
                                                
                                                <?php if (method_exists($profile, 'getFullName') && $profile->getFullName()): ?>
                                                    <p class="text-muted"><?= esc($profile->getFullName()) ?></p>
                                                <?php elseif (isset($profile->getFullName) && is_callable($profile->getFullName) && $profile->getFullName()): ?>
                                                    <p class="text-muted"><?= esc($profile->getFullName()) ?></p>
                                                <?php endif; ?>
                                                
                                                <?php 
                                                $biography = '';
                                                if (method_exists($profile, 'getBiography') && $profile->getBiography()) {
                                                    $biography = $profile->getBiography();
                                                } elseif (isset($profile->biography)) {
                                                    $biography = $profile->biography;
                                                }
                                                
                                                if (!empty($biography)): 
                                                ?>
                                                    <p class="small mt-2">
                                                        <?= nl2br(esc(substr($biography, 0, 150) . (strlen($biography) > 150 ? '...' : ''))) ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <div class="row text-center mt-3">
                                                    <div class="col-4">
                                                        <div class="font-weight-bold">
                                                            <?php
                                                            $followers = 0;
                                                            if (method_exists($profile, 'getFollowersCount')) {
                                                                $followers = $profile->getFollowersCount();
                                                            } elseif (isset($profile->getFollowersCount) && is_callable($profile->getFollowersCount)) {
                                                                $followers = $profile->getFollowersCount();
                                                            } elseif (isset($profile->followersCount)) {
                                                                $followers = $profile->followersCount;
                                                            }
                                                            echo number_format($followers);
                                                            ?>
                                                        </div>
                                                        <div class="small text-muted">Pengikut</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="font-weight-bold">
                                                            <?php
                                                            $following = 0;
                                                            if (method_exists($profile, 'getFollowingCount')) {
                                                                $following = $profile->getFollowingCount();
                                                            } elseif (isset($profile->getFollowingCount) && is_callable($profile->getFollowingCount)) {
                                                                $following = $profile->getFollowingCount();
                                                            } elseif (isset($profile->followingCount)) {
                                                                $following = $profile->followingCount;
                                                            }
                                                            echo number_format($following);
                                                            ?>
                                                        </div>
                                                        <div class="small text-muted">Mengikuti</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="font-weight-bold">
                                                            <?php
                                                            $media = 0;
                                                            if (method_exists($profile, 'getMediaCount')) {
                                                                $media = $profile->getMediaCount();
                                                            } elseif (isset($profile->getMediaCount) && is_callable($profile->getMediaCount)) {
                                                                $media = $profile->getMediaCount();
                                                            } elseif (isset($profile->mediaCount)) {
                                                                $media = $profile->mediaCount;
                                                            }
                                                            echo number_format($media);
                                                            ?>
                                                        </div>
                                                        <div class="small text-muted">Postingan</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-white border-top">
                                                <a href="<?= base_url('serp/instagram/profile/' . esc($profile->getUsername())) ?>" 
                                                   class="btn btn-info btn-sm rounded-0 mr-1">
                                                    <i class="fas fa-search mr-1"></i> Lihat Detail
                                                </a>
                                                <a href="https://www.instagram.com/<?= esc($profile->getUsername()) ?>" 
                                                   class="btn btn-primary btn-sm rounded-0" target="_blank">
                                                    <i class="fab fa-instagram mr-1"></i> Lihat di Instagram
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