<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <!-- Post Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fab fa-instagram mr-2"></i>Detail Postingan Instagram
                        </h3>
                        <div class="card-tools">
                            <a href="javascript:history.back()" class="btn btn-tool" title="Kembali">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Post Content -->
        <div class="row">
            <!-- Post Media -->
            <div class="col-md-8">
                <div class="card rounded-0">
                    <div class="card-body p-0">
                        <!-- Media Display -->
                        <div class="text-center">
                            <?php if ($media->isVideo()): ?>
                                <?php if ($media->getVideoUrl()): ?>
                                    <video class="img-fluid" controls style="max-height: 600px;">
                                        <source src="<?= esc($media->getVideoUrl()) ?>" type="video/mp4">
                                        Browser Anda tidak mendukung tag video.
                                    </video>
                                <?php else: ?>
                                    <img src="<?= esc($media->getImageHighResolutionUrl()) ?>" 
                                         alt="Postingan Instagram" 
                                         class="img-fluid" 
                                         style="max-height: 600px;">
                                    <div class="mt-2">
                                        <span class="badge badge-danger">Video (hanya pratinjau)</span>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <img src="<?= esc($media->getImageHighResolutionUrl()) ?>" 
                                     alt="Postingan Instagram" 
                                     class="img-fluid" 
                                     style="max-height: 600px;">
                            <?php endif; ?>
                        </div>
                        
                        <!-- Post Details -->
                        <div class="p-4 border-top">
                            <!-- Author Info -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?= esc($media->getOwner()->getProfilePicUrl()) ?>" 
                                     alt="<?= esc($media->getOwner()->getUsername()) ?>" 
                                     class="img-circle mr-3" 
                                     style="width: 40px; height: 40px;">
                                <div>
                                    <h5 class="mb-0">
                                        <a href="<?= site_url('serp/instagram/profile/' . $media->getOwner()->getUsername()) ?>" 
                                           class="text-dark">
                                            <?= esc($media->getOwner()->getUsername()) ?>
                                        </a>
                                        <?php if ($media->getOwner()->isVerified()): ?>
                                            <i class="fas fa-check-circle text-primary ml-1" data-toggle="tooltip" title="Akun Terverifikasi"></i>
                                        <?php endif; ?>
                                    </h5>
                                    <?php if ($media->getOwner()->getFullName()): ?>
                                        <small class="text-muted"><?= esc($media->getOwner()->getFullName()) ?></small>
                                    <?php endif; ?>
                                </div>
                                <a href="https://instagram.com/<?= esc($media->getOwner()->getUsername()) ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-secondary btn-sm ml-auto rounded-0">
                                    <i class="fas fa-external-link-alt mr-1"></i> Profil
                                </a>
                            </div>
                            
                            <!-- Caption -->
                            <?php if ($media->getCaption()): ?>
                                <div class="mt-3">
                                    <p><?= nl2br(esc($media->getCaption())) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Post Stats -->
                            <div class="row mt-4">
                                <div class="col-md-4 col-6">
                                    <div class="text-center">
                                        <h4><?= number_format($media->getLikesCount()) ?></h4>
                                        <small class="text-muted">SUKA</small>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="text-center">
                                        <h4><?= number_format($media->getCommentsCount()) ?></h4>
                                        <small class="text-muted">KOMENTAR</small>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12 mt-3 mt-md-0">
                                    <div class="text-center">
                                        <h4><?= $media->getCreatedTime()->format('d M Y') ?></h4>
                                        <small class="text-muted">TANGGAL POSTING</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location -->
                            <?php if ($media->getLocation() && $media->getLocation()->getName()): ?>
                                <div class="mt-4">
                                    <i class="fas fa-map-marker-alt text-danger mr-2"></i> 
                                    <?= esc($media->getLocation()->getName()) ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Hashtags -->
                            <?php 
                                $caption = $media->getCaption(); 
                                $hashtags = [];
                                if ($caption) {
                                    preg_match_all('/#([a-zA-Z0-9_]+)/', $caption, $matches);
                                    $hashtags = $matches[1] ?? [];
                                }
                            ?>
                            <?php if (!empty($hashtags)): ?>
                                <div class="mt-3">
                                    <?php foreach ($hashtags as $hashtag): ?>
                                        <a href="<?= site_url('serp/instagram/hashtags') ?>" 
                                           class="badge badge-light p-2 mr-2 mb-2" 
                                           onclick="event.preventDefault(); document.getElementById('hashtag-form-<?= $hashtag ?>').submit();">
                                            #<?= esc($hashtag) ?>
                                        </a>
                                        <form id="hashtag-form-<?= $hashtag ?>" action="<?= site_url('serp/instagram/hashtags') ?>" method="post" style="display: none;">
                                            <input type="hidden" name="query" value="<?= esc($hashtag) ?>">
                                            <?= csrf_field() ?>
                                        </form>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- External Link -->
                            <div class="mt-4">
                                <a href="<?= esc($media->getLink()) ?>" target="_blank" class="btn btn-primary rounded-0">
                                    <i class="fas fa-external-link-alt mr-1"></i> Lihat di Instagram
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Comments Section -->
            <div class="col-md-4">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Komentar (<?= number_format($media->getCommentsCount()) ?>)</h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($comments)): ?>
                            <div class="direct-chat-messages" style="height: 600px;">
                                <?php foreach ($comments as $comment): ?>
                                    <div class="direct-chat-msg mb-3 p-3">
                                        <div class="direct-chat-infos mb-2">
                                            <div class="d-flex align-items-center">
                                                <img class="direct-chat-img mr-2" 
                                                     src="<?= esc($comment->getOwner()->getProfilePicUrl()) ?>" 
                                                     alt="<?= esc($comment->getOwner()->getUsername()) ?>">
                                                <div>
                                                    <a href="<?= site_url('serp/instagram/profile/' . $comment->getOwner()->getUsername()) ?>" 
                                                       class="direct-chat-name text-dark font-weight-bold">
                                                        <?= esc($comment->getOwner()->getUsername()) ?>
                                                    </a>
                                                    <div class="direct-chat-timestamp text-muted small">
                                                        <?= $comment->getCreatedAt()->format('d M Y H:i') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="direct-chat-text bg-light">
                                            <?= nl2br(esc($comment->getText())) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada komentar untuk ditampilkan</p>
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
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Hasil
                </a>
                <a href="<?= site_url('serp/instagram') ?>" class="btn btn-outline-primary rounded-0">
                    <i class="fas fa-search mr-1"></i> Pencarian Instagram Baru
                </a>
            </div>
        </div>
    </div>
</section>

<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?= $this->endSection() ?> 