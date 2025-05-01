<?php
/**
 * X.com OSINT View
 * 
 * This view provides a user interface for X.com (formerly Twitter) OSINT analysis.
 *
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */
?>
<?= $this->extend($this->theme->getThemePath() . '/layout/page_layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fab fa-twitter mr-2"></i> X.com OSINT</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                    <li class="breadcrumb-item active">X.com OSINT</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Tabs -->
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="xosint-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="profile-tab" data-toggle="pill" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
                            <i class="fas fa-user mr-1"></i> Profile Analysis
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="search-tab" data-toggle="pill" href="#search" role="tab" aria-controls="search" aria-selected="false">
                            <i class="fas fa-search mr-1"></i> Tweet Search
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="trends-tab" data-toggle="pill" href="#trends" role="tab" aria-controls="trends" aria-selected="false">
                            <i class="fas fa-chart-line mr-1"></i> Trending Topics
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="xosint-tabs-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h3 class="card-title">Profile Analysis</h3>
                                    </div>
                                    <div class="card-body">
                                        <form id="profile-form">
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">@</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                                                </div>
                                                <small class="form-text text-muted">Enter X.com username without @</small>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-search"></i> Analyze Profile
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Recent Searches -->
                                <div class="card">
                                    <div class="card-header bg-secondary">
                                        <h3 class="card-title">Recent Searches</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush" id="recent-searches">
                                            <?php if (!empty($recent_searches)) : ?>
                                                <?php foreach ($recent_searches as $search) : ?>
                                                    <li class="list-group-item">
                                                        <a href="#" class="search-item" data-username="<?= esc($search['query']) ?>">
                                                            @<?= esc($search['query']) ?>
                                                        </a>
                                                        <span class="float-right text-muted">
                                                            <?= date('d M Y', strtotime($search['timestamp'])) ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <li class="list-group-item text-center">No recent searches</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div id="profile-results" class="d-none">
                                    <div class="card">
                                        <div class="card-header bg-primary">
                                            <h3 class="card-title">Profile Information</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" id="export-profile-pdf">
                                                    <i class="fas fa-file-pdf"></i> Export PDF
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body" id="profile-info-container">
                                            <!-- Profile information will be loaded here -->
                                            <div class="text-center">
                                                <i class="fas fa-spinner fa-spin fa-3x"></i>
                                                <p class="mt-2">Loading profile information...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Initial Instructions -->
                                <div id="profile-instructions">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fab fa-twitter fa-4x text-primary mb-3"></i>
                                            <h4>X.com Profile Analysis</h4>
                                            <p>Enter a X.com username to analyze their profile and tweets.</p>
                                            <ul class="text-left">
                                                <li>View profile information and statistics</li>
                                                <li>Analyze tweet patterns and engagement</li>
                                                <li>Identify hashtags and mentions</li>
                                                <li>Export analysis to PDF</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Tab -->
                    <div class="tab-pane fade" id="search" role="tabpanel" aria-labelledby="search-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h3 class="card-title">Tweet Search</h3>
                                    </div>
                                    <div class="card-body">
                                        <form id="search-form">
                                            <div class="form-group">
                                                <label for="search-query">Search Query</label>
                                                <input type="text" class="form-control" id="search-query" name="query" placeholder="Enter keyword, hashtag, or phrase" required>
                                                <small class="form-text text-muted">Use hashtags (#) or keywords to search for tweets</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="search-limit">Maximum Results</label>
                                                <select class="form-control" id="search-limit" name="limit">
                                                    <option value="10">10 tweets</option>
                                                    <option value="25">25 tweets</option>
                                                    <option value="50" selected>50 tweets</option>
                                                    <option value="100">100 tweets</option>
                                                    <option value="200">200 tweets</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Search Tweets
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div id="search-results" class="d-none">
                                    <div class="card">
                                        <div class="card-header bg-primary">
                                            <h3 class="card-title">Search Results</h3>
                                        </div>
                                        <div class="card-body" id="search-results-container">
                                            <!-- Search results will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trends Tab -->
                    <div class="tab-pane fade" id="trends" role="tabpanel" aria-labelledby="trends-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-primary">
                                        <h3 class="card-title">Trending Topics</h3>
                                    </div>
                                    <div class="card-body">
                                        <form id="trends-form">
                                            <div class="form-group">
                                                <label for="country-code">Country</label>
                                                <select class="form-control" id="country-code" name="country">
                                                    <option value="">Worldwide</option>
                                                    <option value="ID">Indonesia</option>
                                                    <option value="US">United States</option>
                                                    <option value="GB">United Kingdom</option>
                                                    <option value="JP">Japan</option>
                                                    <option value="BR">Brazil</option>
                                                    <option value="AU">Australia</option>
                                                    <option value="CA">Canada</option>
                                                    <option value="FR">France</option>
                                                    <option value="DE">Germany</option>
                                                    <option value="IN">India</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-chart-line"></i> Get Trends
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div id="trends-results" class="d-none">
                                    <div class="card">
                                        <div class="card-header bg-primary">
                                            <h3 class="card-title">Trending Topics</h3>
                                        </div>
                                        <div class="card-body" id="trends-results-container">
                                            <!-- Trends will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // Profile Analysis Form
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        var username = $('#username').val().trim();
        
        // Show loading
        $('#profile-instructions').addClass('d-none');
        $('#profile-results').removeClass('d-none');
        $('#profile-info-container').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading profile information...</p></div>');
        
        $.ajax({
            url: '<?= base_url('serp/xosint/profile') ?>',
            type: 'POST',
            data: { username: username },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Render profile information
                    renderProfileInfo(response.data);
                } else {
                    $('#profile-info-container').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + response.message + '</div>');
                }
            },
            error: function() {
                $('#profile-info-container').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error connecting to the server. Please try again.</div>');
            }
        });
    });
    
    // Recent search item click
    $(document).on('click', '.search-item', function(e) {
        e.preventDefault();
        var username = $(this).data('username');
        $('#username').val(username);
        $('#profile-form').submit();
    });
    
    // Export profile to PDF
    $('#export-profile-pdf').on('click', function() {
        var username = $('#username').val().trim();
        
        if (!username) {
            alert('Please search for a profile first');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Generating PDF',
            html: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '<?= base_url('serp/xosint/export-profile-pdf') ?>',
            type: 'POST',
            data: { username: username },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    // Create download link
                    var link = document.createElement('a');
                    link.href = 'data:application/pdf;base64,' + response.pdf;
                    link.download = 'x_profile_' + username + '.pdf';
                    link.click();
                    
                    // Success message
                    Toastify({
                        text: "PDF generated successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                    }).showToast();
                } else {
                    // Error message
                    Toastify({
                        text: response.message || "Failed to generate PDF",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                    }).showToast();
                }
            },
            error: function() {
                Swal.close();
                Toastify({
                    text: "Error connecting to the server",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        });
    });
    
    // Render profile information
    function renderProfileInfo(data) {
        var profile = data.profile;
        var analysis = data.analysis;
        
        var html = '<div class="row">';
        
        // Profile header
        html += '<div class="col-md-12">';
        html += '<div class="d-flex align-items-center mb-3">';
        
        // Profile image
        if (profile.profileImageUrl) {
            html += '<img src="' + profile.profileImageUrl + '" alt="Profile Image" class="img-circle mr-3" style="width: 80px; height: 80px;">';
        } else {
            html += '<div class="img-circle mr-3 bg-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;"><i class="fas fa-user fa-3x"></i></div>';
        }
        
        // Name and username
        html += '<div>';
        html += '<h4 class="mb-0">' + (profile.displayName || 'Unknown') + ' ';
        if (profile.verified) {
            html += '<i class="fas fa-check-circle text-primary" title="Verified"></i>';
        }
        html += '</h4>';
        html += '<p class="text-muted mb-0">@' + profile.username + '</p>';
        
        // Account creation
        if (profile.created) {
            var created = new Date(profile.created);
            html += '<small class="text-muted">Joined ' + created.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + '</small>';
        }
        
        html += '</div>';
        html += '</div>';
        
        // Bio
        if (profile.bio) {
            html += '<p>' + profile.bio + '</p>';
        }
        
        // Location and URL
        if (profile.location || profile.url) {
            html += '<p class="mb-2">';
            if (profile.location) {
                html += '<i class="fas fa-map-marker-alt mr-1"></i> ' + profile.location + ' ';
            }
            if (profile.url) {
                html += '<i class="fas fa-link mr-1"></i> <a href="' + profile.url + '" target="_blank">' + profile.url + '</a>';
            }
            html += '</p>';
        }
        
        html += '</div>';
        
        // Stats cards
        html += '<div class="col-md-4">';
        html += '<div class="info-box bg-primary">';
        html += '<span class="info-box-icon"><i class="fas fa-users"></i></span>';
        html += '<div class="info-box-content">';
        html += '<span class="info-box-text">Followers</span>';
        html += '<span class="info-box-number">' + (profile.followersCount ? profile.followersCount.toLocaleString() : '0') + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        html += '<div class="col-md-4">';
        html += '<div class="info-box bg-info">';
        html += '<span class="info-box-icon"><i class="fas fa-user-friends"></i></span>';
        html += '<div class="info-box-content">';
        html += '<span class="info-box-text">Following</span>';
        html += '<span class="info-box-number">' + (profile.friendsCount ? profile.friendsCount.toLocaleString() : '0') + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        html += '<div class="col-md-4">';
        html += '<div class="info-box bg-success">';
        html += '<span class="info-box-icon"><i class="fab fa-twitter"></i></span>';
        html += '<div class="info-box-content">';
        html += '<span class="info-box-text">Tweets</span>';
        html += '<span class="info-box-number">' + (profile.statusesCount ? profile.statusesCount.toLocaleString() : '0') + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        // Tweet activity (if available)
        if (analysis && analysis.tweetActivity) {
            html += '<div class="col-md-12 mt-3">';
            html += '<h5 class="mb-3">Tweet Activity Analysis</h5>';
            html += '<div class="row">';
            
            html += '<div class="col-md-3">';
            html += '<div class="small-box bg-info">';
            html += '<div class="inner">';
            html += '<h3>' + analysis.tweetActivity.withMedia + '</h3>';
            html += '<p>Media Tweets</p>';
            html += '</div>';
            html += '<div class="icon">';
            html += '<i class="fas fa-image"></i>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="col-md-3">';
            html += '<div class="small-box bg-success">';
            html += '<div class="inner">';
            html += '<h3>' + analysis.tweetActivity.withLinks + '</h3>';
            html += '<p>Link Tweets</p>';
            html += '</div>';
            html += '<div class="icon">';
            html += '<i class="fas fa-link"></i>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="col-md-3">';
            html += '<div class="small-box bg-warning">';
            html += '<div class="inner">';
            html += '<h3>' + analysis.tweetActivity.withHashtags + '</h3>';
            html += '<p>Hashtag Tweets</p>';
            html += '</div>';
            html += '<div class="icon">';
            html += '<i class="fas fa-hashtag"></i>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="col-md-3">';
            html += '<div class="small-box bg-danger">';
            html += '<div class="inner">';
            html += '<h3>' + analysis.tweetActivity.avgLikes + '</h3>';
            html += '<p>Avg. Likes</p>';
            html += '</div>';
            html += '<div class="icon">';
            html += '<i class="fas fa-heart"></i>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            html += '</div>';
            
            // Top hashtags
            if (analysis.topHashtags && Object.keys(analysis.topHashtags).length > 0) {
                html += '<div class="col-md-6 mt-3">';
                html += '<div class="card">';
                html += '<div class="card-header">';
                html += '<h3 class="card-title">Top Hashtags</h3>';
                html += '</div>';
                html += '<div class="card-body p-0">';
                html += '<ul class="list-group list-group-flush">';
                
                for (var tag in analysis.topHashtags) {
                    html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    html += '#' + tag;
                    html += '<span class="badge badge-primary badge-pill">' + analysis.topHashtags[tag] + '</span>';
                    html += '</li>';
                }
                
                html += '</ul>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            }
            
            // Top mentions
            if (analysis.topMentions && Object.keys(analysis.topMentions).length > 0) {
                html += '<div class="col-md-6 mt-3">';
                html += '<div class="card">';
                html += '<div class="card-header">';
                html += '<h3 class="card-title">Top Mentions</h3>';
                html += '</div>';
                html += '<div class="card-body p-0">';
                html += '<ul class="list-group list-group-flush">';
                
                for (var mention in analysis.topMentions) {
                    html += '<li class="list-group-item d-flex justify-content-between align-items-center">';
                    html += '@' + mention;
                    html += '<span class="badge badge-primary badge-pill">' + analysis.topMentions[mention] + '</span>';
                    html += '</li>';
                }
                
                html += '</ul>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            }
        }
        
        html += '</div>';
        
        $('#profile-info-container').html(html);
    }
});
</script>
<?= $this->endSection() ?> 