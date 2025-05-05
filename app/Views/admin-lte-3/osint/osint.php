<?= $this->extend('admin-lte-3/layout/page_layout') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title; ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                    <li class="breadcrumb-item active"><?= $title; ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline card-tabs">
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="osint-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-x" data-toggle="pill" href="#content-x" role="tab" aria-controls="content-x" aria-selected="true">
                                    <i class="fab fa-twitter"></i> X.com (Twitter)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-instagram" data-toggle="pill" href="#content-instagram" role="tab" aria-controls="content-instagram" aria-selected="false">
                                    <i class="fab fa-instagram"></i> Instagram
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-facebook" data-toggle="pill" href="#content-facebook" role="tab" aria-controls="content-facebook" aria-selected="false">
                                    <i class="fab fa-facebook"></i> Facebook
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-linkedin" data-toggle="pill" href="#content-linkedin" role="tab" aria-controls="content-linkedin" aria-selected="false">
                                    <i class="fab fa-linkedin"></i> LinkedIn
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="osint-tabsContent">
                            <div class="tab-pane fade show active" id="content-x" role="tabpanel" aria-labelledby="tab-x">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">X.com Profile Search</h3>
                                            </div>
                                            <div class="card-body">
                                                <form id="x-profile-form">
                                                    <div class="form-group">
                                                        <label for="x-username">Username:</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">@</span>
                                                            </div>
                                                            <input type="text" class="form-control" id="x-username" name="username" placeholder="Enter X.com username">
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-block" id="btn-search-profile">
                                                        <i class="fas fa-search"></i> Search Profile
                                                    </button>
                                                </form>

                                                <div class="mt-4">
                                                    <h5>Recent Searches</h5>
                                                    <div class="list-group">
                                                        <?php if (!empty($recent_searches)) : ?>
                                                            <?php foreach ($recent_searches as $search) : ?>
                                                                <a href="#" class="list-group-item list-group-item-action recent-search" data-query="<?= esc($search->search_query) ?>">
                                                                    @<?= esc($search->search_query) ?>
                                                                </a>
                                                            <?php endforeach; ?>
                                                        <?php else : ?>
                                                            <div class="list-group-item">No recent searches</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Profile Results</h3>
                                            </div>
                                            <div class="card-body">
                                                <div id="profile-results">
                                                    <div class="text-center">
                                                        <p>Enter a username to search for a X.com profile</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="content-instagram" role="tabpanel" aria-labelledby="tab-instagram">
                                <div class="text-center">
                                    <h3>Instagram OSINT</h3>
                                    <p>This feature is coming soon</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="content-facebook" role="tabpanel" aria-labelledby="tab-facebook">
                                <div class="text-center">
                                    <h3>Facebook OSINT</h3>
                                    <p>This feature is coming soon</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="content-linkedin" role="tabpanel" aria-labelledby="tab-linkedin">
                                <div class="text-center">
                                    <h3>LinkedIn OSINT</h3>
                                    <p>This feature is coming soon</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Profile search form submission
    $('#x-profile-form').on('submit', function(e) {
        e.preventDefault();
        
        const username = $('#x-username').val().trim();
        if (!username) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a username'
            });
            return;
        }
        
        // Show loading
        $('#profile-results').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Loading profile...</p></div>');
        
        // Submit search request
        $.ajax({
            url: '<?= base_url('osint/x/profile') ?>',
            type: 'POST',
            data: {
                username: username
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayProfile(response.data);
                } else {
                    $('#profile-results').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function(xhr, status, error) {
                $('#profile-results').html('<div class="alert alert-danger">An error occurred while processing your request</div>');
                console.error(error);
            }
        });
    });
    
    // Click on recent search
    $('.recent-search').on('click', function(e) {
        e.preventDefault();
        const query = $(this).data('query');
        $('#x-username').val(query);
        $('#x-profile-form').submit();
    });
    
    // Function to display profile results
    function displayProfile(data) {
        const profile = data.profile;
        const analysis = data.analysis;
        
        let html = `
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="${profile.profileImageUrl || '<?= base_url('assets/img/no-profile.png') ?>'}" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                    <h4>@${profile.username}</h4>
                    <p>${profile.displayName || ''}</p>
                    ${profile.verified ? '<span class="badge badge-primary"><i class="fas fa-check-circle"></i> Verified</span>' : ''}
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <p>${profile.description || 'No bio provided'}</p>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 text-center">
                            <h5>${profile.followersCount || 0}</h5>
                            <span>Followers</span>
                        </div>
                        <div class="col-4 text-center">
                            <h5>${profile.followingCount || 0}</h5>
                            <span>Following</span>
                        </div>
                        <div class="col-4 text-center">
                            <h5>${profile.statusesCount || 0}</h5>
                            <span>Tweets</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p><i class="fas fa-map-marker-alt"></i> ${profile.location || 'Not specified'}</p>
                        <p><i class="fas fa-link"></i> ${profile.url ? `<a href="${profile.url}" target="_blank">${profile.url}</a>` : 'No website provided'}</p>
                        <p><i class="fas fa-calendar-alt"></i> Joined: ${profile.createdAt ? new Date(profile.createdAt).toLocaleDateString() : 'Unknown'}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h5>Profile Analysis</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Activity</h6>
                            </div>
                            <div class="card-body">
                                <p>Average tweets per day: ${analysis.tweetsPerDay || 'N/A'}</p>
                                <p>Average likes received: ${analysis.averageLikes || 'N/A'}</p>
                                <p>Average retweets received: ${analysis.averageRetweets || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Top Hashtags</h6>
                            </div>
                            <div class="card-body">
                                ${renderHashtags(analysis.topHashtags)}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-success" id="btn-export-pdf">
                        <i class="fas fa-file-pdf"></i> Export Profile Analysis
                    </button>
                </div>
            </div>
        `;
        
        $('#profile-results').html(html);
        
        // Set up export button
        $('#btn-export-pdf').on('click', function() {
            exportProfilePdf(profile.username);
        });
    }
    
    // Function to render hashtags
    function renderHashtags(hashtags) {
        if (!hashtags || hashtags.length === 0) {
            return '<p>No hashtags found</p>';
        }
        
        let html = '<div class="d-flex flex-wrap">';
        hashtags.forEach(tag => {
            html += `<span class="badge badge-info m-1">#${tag.tag} (${tag.count})</span>`;
        });
        html += '</div>';
        
        return html;
    }
    
    // Function to export profile to PDF
    function exportProfilePdf(username) {
        window.location.href = `<?= base_url('osint/x/export-profile/') ?>${username}`;
    }
});
</script>
<?= $this->endSection() ?> 