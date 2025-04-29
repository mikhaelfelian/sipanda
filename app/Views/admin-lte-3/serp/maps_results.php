<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <!-- Search Form Row -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-search mr-2"></i>Maps Search Results for "<?= esc($query) ?>"
                            <?php if ($location && $location !== 'Indonesia'): ?>
                                in <?= esc($location) ?>
                            <?php endif; ?>
                        </h3>
                        <div class="card-tools">
                            <a href="<?= site_url('serp/maps') ?>" class="btn btn-tool" title="New Search">
                                <i class="fas fa-sync"></i> New Search
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Map Display -->
            <div class="col-md-8">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Map View</h3>
                    </div>
                    <div class="card-body p-0">
                        <!-- If we have a map in the results, display it -->
                        <?php if (!empty($mapResults) && isset($mapResults[0]['gps_coordinates']['latitude'])): ?>
                            <?php 
                                $latitude = $mapResults[0]['gps_coordinates']['latitude'];
                                $longitude = $mapResults[0]['gps_coordinates']['longitude'];
                            ?>
                            <div id="map" style="height: 500px; width: 100%;"></div>
                            
                            <!-- Google Maps JavaScript -->
                            <script>
                                function initMap() {
                                    const center = { lat: <?= $latitude ?>, lng: <?= $longitude ?> };
                                    const map = new google.maps.Map(document.getElementById("map"), {
                                        zoom: 13,
                                        center: center,
                                    });

                                    // Add markers for each result
                                    <?php foreach ($mapResults as $index => $result): ?>
                                        <?php if (isset($result['gps_coordinates']['latitude'])): ?>
                                            const marker<?= $index ?> = new google.maps.Marker({
                                                position: { 
                                                    lat: <?= $result['gps_coordinates']['latitude'] ?>, 
                                                    lng: <?= $result['gps_coordinates']['longitude'] ?> 
                                                },
                                                map: map,
                                                title: "<?= esc($result['title'] ?? 'Location') ?>"
                                            });

                                            const infowindow<?= $index ?> = new google.maps.InfoWindow({
                                                content: `<div class="info-window">
                                                    <h5><?= esc($result['title'] ?? 'Location') ?></h5>
                                                    <p><?= esc($result['address'] ?? '') ?></p>
                                                    <?php if (isset($result['rating'])): ?>
                                                        <p>Rating: <?= $result['rating'] ?>/5</p>
                                                    <?php endif; ?>
                                                </div>`
                                            });

                                            marker<?= $index ?>.addListener("click", () => {
                                                infowindow<?= $index ?>.open({
                                                    anchor: marker<?= $index ?>,
                                                    map,
                                                });
                                            });
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                }
                            </script>
                            <script async defer
                                src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap">
                            </script>
                        <?php else: ?>
                            <div class="text-center bg-light" style="height: 500px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <i class="fas fa-map-marked-alt fa-5x text-muted mb-3"></i>
                                    <p class="text-muted">No map data available</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Search Results Listing -->
            <div class="col-md-4">
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?= count($mapResults) ?> Result<?= count($mapResults) != 1 ? 's' : '' ?> Found
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($mapResults)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($mapResults as $index => $result): ?>
                                    <div class="list-group-item list-group-item-action flex-column align-items-start">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?= esc($result['title'] ?? 'Unknown') ?></h5>
                                            <?php if (isset($result['rating'])): ?>
                                                <small class="text-muted">
                                                    <?= $result['rating'] ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                    (<?= $result['reviews'] ?? 0 ?>)
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if (isset($result['address'])): ?>
                                            <p class="mb-1 small">
                                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> 
                                                <?= esc($result['address']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($result['hours'])): ?>
                                            <p class="mb-1 small">
                                                <i class="far fa-clock text-info mr-1"></i>
                                                <?= esc($result['hours']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($result['phone'])): ?>
                                            <p class="mb-1 small">
                                                <i class="fas fa-phone text-success mr-1"></i>
                                                <?= esc($result['phone']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($result['website'])): ?>
                                            <p class="mb-0">
                                                <a href="<?= esc($result['website']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-globe mr-1"></i> Website
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info m-3">
                                No results found. Try a different search term or location.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?> 