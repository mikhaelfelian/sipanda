<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Search Form Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Pencarian Google Maps</h3>
                    </div>
                    <div class="card-body">
                        <?= form_open('serp/maps/search', ['method' => 'post']) ?>
                            <div class="form-group">
                                <label for="query">Apa yang Anda cari?</label>
                                <input type="text" class="form-control rounded-0" id="query" name="query"
                                    placeholder="Masukkan bisnis, alamat, atau tempat..." required minlength="3" maxlength="255"
                                    value="<?= set_value('query', isset($query) ? $query : (service('request')->getGet('query') ?? '')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="location">Lokasi</label>
                                <input type="text" class="form-control rounded-0" id="location" name="location"
                                    placeholder="Kota, wilayah, atau alamat (default: Indonesia)"
                                    value="<?= set_value('location', isset($location) ? $location : (service('request')->getGet('location') ?? '')) ?>">
                                <small class="form-text text-muted">Biarkan kosong untuk mencari di seluruh Indonesia</small>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-0">Cari di Maps</button>
                        <?= form_close() ?>
                    </div>
                </div>
                <!-- Map Placeholder (for a future feature) -->
                <div class="card rounded-0 mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Peta Indonesia</h3>
                    </div>
                    <div class="card-body p-0">
                        <!-- Default map showing Indonesia -->
                        <div id="default-map" style="height: 300px; width: 100%;"></div>
                        
                        <!-- Google Maps JavaScript -->
                        <script>
                            // Function to search at a specific location
                            function searchAtLocation(coordinates) {
                                const queryInput = document.getElementById('map-search-query');
                                const query = queryInput.value.trim();
                                
                                if (query) {
                                    // Redirect to search with both query and coordinates
                                    window.location.href = `<?= site_url('serp/maps/search') ?>?query=${encodeURIComponent(query)}&coordinates=${encodeURIComponent(coordinates)}`;
                                } else {
                                    // Show alert if no query is entered
                                    alert('Silakan masukkan kata kunci pencarian terlebih dahulu');
                                    queryInput.focus();
                                }
                            }
                            
                            function initDefaultMap() {
                                // Default coordinates for Indonesia (centered on Java)
                                const indonesia = { lat: -2.4833, lng: 117.8902 };
                                
                                // Create map centered on Indonesia
                                const map = new google.maps.Map(document.getElementById("default-map"), {
                                    zoom: 5,
                                    center: indonesia,
                                    mapTypeId: 'roadmap',
                                    mapTypeControl: true,
                                    mapTypeControlOptions: {
                                        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                                    }
                                });
                                
                                // Add ability to search by clicking on map
                                const searchInfoWindow = new google.maps.InfoWindow();
                                
                                // Add click listener to map
                                map.addListener("click", (mapsMouseEvent) => {
                                    // Close any open info windows
                                    searchInfoWindow.close();
                                    
                                    // Get clicked coordinates
                                    const latLng = mapsMouseEvent.latLng;
                                    const lat = latLng.lat().toFixed(6);
                                    const lng = latLng.lng().toFixed(6);
                                    
                                    // Show info window with search button
                                    searchInfoWindow.setContent(`
                                        <div class="info-window">
                                            <h5>Cari di lokasi ini</h5>
                                            <p>Koordinat: ${lat}, ${lng}</p>
                                            <div class="input-group input-group-sm mb-2">
                                                <input type="text" id="map-search-query" class="form-control" placeholder="Kata kunci pencarian...">
                                            </div>
                                            <button onclick="searchAtLocation('${lat},${lng}')" class="btn btn-primary btn-sm">Cari di Sini</button>
                                        </div>
                                    `);
                                    
                                    searchInfoWindow.setPosition(latLng);
                                    searchInfoWindow.open(map);
                                });
                                
                                // Add marker for Jakarta (as a reference point)
                                const jakarta = { lat: -6.2088, lng: 106.8456 };
                                const marker = new google.maps.Marker({
                                    position: jakarta,
                                    map: map,
                                    title: "Jakarta"
                                });
                                
                                // Add info window for Jakarta marker
                                const infowindow = new google.maps.InfoWindow({
                                    content: `<div class="info-window">
                                        <h5>Jakarta</h5>
                                        <p>Ibu Kota Indonesia</p>
                                    </div>`
                                });
                                
                                // Open info window when marker is clicked
                                marker.addListener("click", () => {
                                    infowindow.open({
                                        anchor: marker,
                                        map,
                                    });
                                });
                                
                                // Add more major cities
                                const majorCities = [
                                    { name: "Surabaya", position: { lat: -7.2575, lng: 112.7521 }, info: "Kota terbesar kedua di Indonesia" },
                                    { name: "Bandung", position: { lat: -6.9175, lng: 107.6191 }, info: "Kota di Jawa Barat" },
                                    { name: "Medan", position: { lat: 3.5952, lng: 98.6722 }, info: "Kota terbesar di Sumatera" },
                                    { name: "Makassar", position: { lat: -5.1477, lng: 119.4327 }, info: "Kota terbesar di Sulawesi" },
                                    { name: "Denpasar", position: { lat: -8.6705, lng: 115.2126 }, info: "Ibukota Provinsi Bali" }
                                ];
                                
                                // Add markers for each major city
                                majorCities.forEach(city => {
                                    const cityMarker = new google.maps.Marker({
                                        position: city.position,
                                        map: map,
                                        title: city.name
                                    });
                                    
                                    const cityInfoWindow = new google.maps.InfoWindow({
                                        content: `<div class="info-window">
                                            <h5>${city.name}</h5>
                                            <p>${city.info}</p>
                                        </div>`
                                    });
                                    
                                    cityMarker.addListener("click", () => {
                                        cityInfoWindow.open({
                                            anchor: cityMarker,
                                            map,
                                        });
                                    });
                                });
                            }
                        </script>
                        <script async defer
                            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhz1N_G8_EYoozDDvCnmZwm-u_zs3jsqs&callback=initDefaultMap">
                        </script>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Anda dapat mencari lokasi spesifik menggunakan form pencarian di atas.
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Saran Pencarian:</small>
                            <div class="mt-1">
                                <a href="<?= site_url('serp/maps/search?query=hotel') ?>" class="badge badge-secondary mr-1">Hotel</a>
                                <a href="<?= site_url('serp/maps/search?query=restoran') ?>" class="badge badge-secondary mr-1">Restoran</a>
                                <a href="<?= site_url('serp/maps/search?query=mall') ?>" class="badge badge-secondary mr-1">Mall</a>
                                <a href="<?= site_url('serp/maps/search?query=rumah sakit') ?>" class="badge badge-secondary mr-1">Rumah Sakit</a>
                                <a href="<?= site_url('serp/maps/search?query=universitas') ?>" class="badge badge-secondary mr-1">Universitas</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Recent Searches Card -->
                <div class="card rounded-0 mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Pencarian Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentSearches)): ?>
                            <ul class="list-group">
                                <?php foreach ($recentSearches as $search): ?>
                                    <li class="list-group-item rounded-0 d-flex justify-content-between align-items-center">
                                        <?= esc($search['keyword']) ?>
                                        <span
                                            class="badge badge-info badge-pill"><?= date('d M Y', strtotime($search['last_searched'])) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Tidak ada pencarian terbaru.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Popular Keywords Card -->
                <div class="card rounded-0">
                    <div class="card-header">
                        <h3 class="card-title">Kata Kunci Populer</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($popularKeywords)): ?>
                            <ul class="list-group">
                                <?php foreach ($popularKeywords as $keyword): ?>
                                    <li class="list-group-item rounded-0 d-flex justify-content-between align-items-center">
                                        <?= esc($keyword['keyword']) ?>
                                        <span class="badge badge-success badge-pill"><?= $keyword['search_count'] ?? 0 ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Tidak ada kata kunci populer.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Trending Searches Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Trending di Google</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($trendingSearches)): ?>
                            <?php if (ENVIRONMENT === 'development'): ?>
                            <div class="small text-muted mb-2">
                                Found <?= count($trendingSearches) ?> trending items
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex flex-wrap">
                                <?php foreach ($trendingSearches as $trend): ?>
                                    <div class="dropdown mr-2 mb-2">
                                        <a href="<?= site_url('serp/maps/search?query=' . urlencode(is_array($trend) ? $trend['title'] : $trend)) ?>" 
                                           class="badge badge-info p-2 dropdown-toggle" id="trend-<?= md5(is_array($trend) ? $trend['title'] : $trend) ?>"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?= esc(is_array($trend) ? $trend['title'] : $trend) ?>
                                        </a>
                                        <?php 
                                        $relatedQueries = [];
                                        if (is_array($trend) && isset($trend['related_queries']) && !empty($trend['related_queries'])): 
                                            $relatedQueries = $trend['related_queries'];
                                        endif;
                                        
                                        if (!empty($relatedQueries)): 
                                        ?>
                                        <div class="dropdown-menu p-2" aria-labelledby="trend-<?= md5(is_array($trend) ? $trend['title'] : $trend) ?>">
                                            <h6 class="dropdown-header bg-light">Trend Breakdown</h6>
                                            <?php foreach ($relatedQueries as $related): ?>
                                            <a class="dropdown-item py-1" href="<?= site_url('serp/maps/search?query=' . urlencode($related)) ?>">
                                                <?= esc($related) ?>
                                            </a>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>Tidak ada trending search tersedia saat ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Search Tips Card -->
                <div class="card rounded-0 mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Tips Pencarian</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-info"></i> Gunakan nama bisnis spesifik untuk hasil yang tepat</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Coba tambahkan nama kota untuk hasil yang lebih baik</li>
                            <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Cari berdasarkan kategori seperti "restoran" atau "hotel"</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 