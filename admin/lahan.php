<?php
require_once '../koneksi.php';

// Processing data harus dilakukan sebelum output HTML apapun
if(isset($_POST['hapus']) || isset($_GET['hapus'])) {
    $source = isset($_POST['hapus']) ? INPUT_POST : INPUT_GET;
    $id = filter_input($source, 'hapus', FILTER_VALIDATE_INT);
    $status = 'gagal';

    if($id !== false && $id !== null && $id > 0) {
        $delete_query = mysqli_query($koneksi, "DELETE FROM lahan WHERE id=$id");
        if($delete_query && mysqli_affected_rows($koneksi) === 1) {
            $status = 'berhasil';
        }
    }

    header("location:lahan.php?hapus_status=$status");
    exit();
}

if(isset($_POST['simpan'])) {
    $kode_lahan = mysqli_real_escape_string($koneksi, $_POST['kode_lahan']);
    $pemilik = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $luas = mysqli_real_escape_string($koneksi, $_POST['luas']);
    $komoditas = mysqli_real_escape_string($koneksi, $_POST['id_komoditas']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $geojson = mysqli_real_escape_string($koneksi, $_POST['geojson']); 

    mysqli_query($koneksi, "INSERT INTO lahan (kode_lahan, nama_pemilik, kecamatan, luas, id_komoditas, keterangan, geojson) VALUES ('$kode_lahan', '$pemilik', '$kecamatan', '$luas', '$komoditas', '$keterangan', '$geojson')");
    header("location:lahan.php");
    exit();
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $kode_lahan = mysqli_real_escape_string($koneksi, $_POST['kode_lahan']);
    $pemilik = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $luas = mysqli_real_escape_string($koneksi, $_POST['luas']);
    $komoditas = mysqli_real_escape_string($koneksi, $_POST['id_komoditas']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $geojson = mysqli_real_escape_string($koneksi, $_POST['geojson']); 

    mysqli_query($koneksi, "UPDATE lahan SET kode_lahan='$kode_lahan', nama_pemilik='$pemilik', kecamatan='$kecamatan', luas='$luas', id_komoditas='$komoditas', keterangan='$keterangan', geojson='$geojson' WHERE id='$id'");
    header("location:lahan.php");
    exit();
}

// Ambil data untuk edit jika ada parameter edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_result = mysqli_query($koneksi, "SELECT * FROM lahan WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$is_tambah = isset($_GET['tambah_lahan']);
$is_edit = isset($_GET['edit']);

// Setelah semua processing selesai, baru include header
require_once 'layout_header.php';
?>

<?php if($is_tambah || $is_edit): ?>
    <!-- Halaman Form Tambah/Edit Data Lahan -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0" style="font-weight: 600;">
            <?= $is_edit ? 'Edit Data Lahan' : 'Tambah Data Lahan' ?>
        </h4>
        <a href="lahan.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="card p-4">
        <form action="" method="post" id="form-lahan">
            <?php if($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Kode Lahan</label>
                        <input type="text" class="form-control" name="kode_lahan" 
                               value="<?= $edit_data ? htmlspecialchars($edit_data['kode_lahan']) : '' ?>"
                               placeholder="Contoh: LHN-001" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Nama Pemilik / Lahan</label>
                        <input type="text" class="form-control" name="nama_pemilik" 
                               value="<?= $edit_data ? htmlspecialchars($edit_data['nama_pemilik']) : '' ?>"
                               placeholder="Masukkan nama lahan/pemilik" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Luas Lahan (Ha)</label>
                        <input type="number" step="0.01" class="form-control" name="luas" 
                               value="<?= $edit_data ? $edit_data['luas'] : '' ?>"
                               placeholder="Masukkan luas lahan" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Kecamatan</label>
                        <select class="form-control" name="kecamatan" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php 
                            $q_kec = mysqli_query($koneksi, "SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
                            while($kec = mysqli_fetch_assoc($q_kec)): 
                            ?>
                                <option value="<?= $kec['nama_kecamatan'] ?>" 
                                    <?= ($edit_data && $edit_data['kecamatan'] == $kec['nama_kecamatan']) ? 'selected' : '' ?>>
                                    <?= $kec['nama_kecamatan'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Komoditas</label>
                        <select class="form-control" name="id_komoditas" required>
                            <option value="">-- Pilih Komoditas --</option>
                            <?php 
                            $q_kom = mysqli_query($koneksi, "SELECT * FROM komoditas");
                            while($k = mysqli_fetch_assoc($q_kom)): 
                            ?>
                                <option value="<?= $k['id'] ?>" 
                                    <?= ($edit_data && $edit_data['id_komoditas'] == $k['id']) ? 'selected' : '' ?>>
                                    <?= $k['nama_komoditas'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="text-muted small font-weight-bold">Deskripsi</label>
                        <textarea class="form-control" name="keterangan" rows="4" placeholder="Masukkan deskripsi lahan"><?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : '' ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Peta & Kordinat Polygon (Leaflet Draw)</label>
                        <!-- Peta Leaflet untuk menggambar polygon -->
                        <div id="map-admin" style="height: 350px; border-radius: 8px; border: 1px solid #ced4da; z-index:1;"></div>
                        <input type="hidden" name="geojson" id="geojson" 
                               value="<?= $edit_data ? htmlspecialchars($edit_data['geojson']) : '' ?>" required>
                        <div id="warning-geo" class="text-danger small mt-2" style="font-weight: bold;">
                            <i class="fa fa-info-circle"></i> Gunakan alat gambar (kotak/polygon) di atas peta untuk memetakan area lahan.
                        </div>

                        <!-- Fitur GPS Walking Survey -->
                        <div id="gps-controls" class="mt-3 p-3 border rounded bg-light" style="border-left: 4px solid #2e7d32 !important;">
                            <span class="font-weight-bold d-block mb-2" style="color: #2e7d32;"><i class="fa fa-location-arrow"></i> Mode GPS Walking Survey</span>
                            <p class="small text-muted mb-2">Bawa perangkat Anda mengelilingi batas lahan untuk memetakan secara otomatis.</p>
                            
                            <!-- Sensitivitas GPS -->
                            <div class="mb-2">
                                <label class="small text-muted font-weight-bold d-block mb-1">Sensitivitas GPS:</label>
                                <div class="btn-group btn-group-sm" role="group" id="gps-sensitivity-group">
                                    <button type="button" class="btn btn-outline-success gps-sens-btn" data-level="high" title="Akurasi < 20m — sinyal kuat">🟢 Tinggi</button>
                                    <button type="button" class="btn btn-outline-warning gps-sens-btn active" data-level="medium" title="Akurasi < 50m — sinyal normal" style="background-color: #ffc107; color: #212529;">🟡 Sedang</button>
                                    <button type="button" class="btn btn-outline-danger gps-sens-btn" data-level="low" title="Akurasi < 150m — sinyal lemah">🔴 Rendah</button>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap mb-2" style="gap: 5px;">
                                <button type="button" id="btn-start-gps" class="btn btn-sm btn-primary"><i class="fa fa-play"></i> Mulai Jalan</button>
                                <button type="button" id="btn-stop-gps" class="btn btn-sm btn-danger" disabled><i class="fa fa-stop"></i> Berhenti & Buat Area</button>
                                <button type="button" id="btn-reset-gps" class="btn btn-sm btn-warning" disabled><i class="fa fa-refresh"></i> Reset</button>
                                <button type="button" id="btn-add-manual" class="btn btn-sm btn-info" disabled title="Klik di peta untuk tambah titik manual"><i class="fa fa-map-pin"></i> + Titik Manual</button>
                            </div>

                            <!-- Signal Bar Indicator -->
                            <div id="gps-signal-bar" class="mb-2" style="display: none;">
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <span class="small font-weight-bold">Sinyal:</span>
                                    <div style="display: flex; align-items: flex-end; gap: 2px; height: 16px;">
                                        <div id="sig-bar-1" style="width: 4px; height: 4px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-2" style="width: 4px; height: 7px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-3" style="width: 4px; height: 10px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-4" style="width: 4px; height: 13px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-5" style="width: 4px; height: 16px; background: #ccc; border-radius: 1px;"></div>
                                    </div>
                                    <span id="signal-label" class="small font-weight-bold text-muted">--</span>
                                </div>
                            </div>

                            <!-- Progress bar inisialisasi -->
                            <div id="gps-init-progress" class="mb-2" style="display: none;">
                                <div class="progress" style="height: 6px;">
                                    <div id="gps-init-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: 0%;"></div>
                                </div>
                                <small id="gps-init-text" class="text-muted">Mencari sinyal terbaik...</small>
                            </div>

                            <div id="gps-status" class="small font-weight-bold mt-2 text-secondary">Status: Menunggu instruksi...</div>
                            <div id="gps-stats" class="small text-muted mt-1" style="display: none;">
                                📊 Titik: <span id="stat-points">0</span> | Jarak: <span id="stat-distance">0</span>m
                            </div>
                            
                            <!-- Tips sinyal lemah -->
                            <div id="gps-tips" class="small text-muted mt-2 p-2 rounded" style="background: #fff3cd; display: none;">
                                <i class="fa fa-lightbulb-o text-warning"></i> <strong>Tips sinyal lemah:</strong> Bergerak perlahan, jauhi bangunan tinggi, atau gunakan "Titik Manual" dengan klik di peta.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex mt-4">
                        <?php if($is_edit): ?>
                            <button type="submit" name="edit" class="btn btn-warning mr-2 px-4">
                                <i class="fa fa-save"></i> Update
                            </button>
                        <?php else: ?>
                            <button type="submit" name="simpan" class="btn btn-green mr-2 px-4">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        <?php endif; ?>
                        <a href="lahan.php" class="btn btn-light px-4 border">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php 
    ob_start();
    ?>
    <script>
        // Inisialisasi peta admin dengan animasi
        var map = L.map('map-admin', { 
            maxZoom: 22,
            fullscreenControl: true,
            fullscreenControlOptions: { position: 'topleft' },
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true
        }).setView([-2.1465, 133.4357], 10);
        
        // Layer peta dengan smooth transition
        var googleHybrid = L.tileLayer('http://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps', 
            maxZoom: 22,
            maxNativeZoom: 19,
            className: 'tile-layer'
        });

        var esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri',
            maxZoom: 22,
            maxNativeZoom: 18,
            className: 'tile-layer'
        });

        googleHybrid.addTo(map);
        
        // Layer control dengan styling modern
        const layerCtrl = L.control.layers({
            "Google Hybrid": googleHybrid,
            "Esri Satellite": esriSatellite
        }, null, { collapsed: true });
        layerCtrl.addTo(map);

        // Style layer control
        setTimeout(() => {
            const layerControl = document.querySelector('.leaflet-control-layers');
            if(layerControl) {
                layerControl.classList.add('modern-layer-control');
            }
        }, 100);

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: { 
                polyline: false, 
                circle: false, 
                circlemarker: false, 
                marker: false,
                polygon: { 
                    allowIntersection: false, 
                    showArea: true,
                    shapeOptions: {
                        color: '#667eea',
                        fillColor: '#667eea',
                        fillOpacity: 0.65,
                        weight: 2.5
                    }
                }, 
                rectangle: {
                    shapeOptions: {
                        color: '#667eea',
                        fillColor: '#667eea',
                        fillOpacity: 0.65,
                        weight: 2.5
                    }
                }
            },
            edit: { featureGroup: drawnItems, remove: true }
        });
        map.addControl(drawControl);

        map.on(L.Draw.Event.CREATED, function (event) {
            var layer = event.layer;
            drawnItems.clearLayers();
            drawnItems.addLayer(layer);
            
            // Animasi sempurna
            layer.setStyle({
                color: '#667eea',
                fillColor: '#667eea',
                weight: 2.5,
                opacity: 0,
                fillOpacity: 0
            });
            
            setTimeout(() => {
                layer.setStyle({
                    opacity: 0.9,
                    fillOpacity: 0.65,
                    transition: 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)'
                });
            }, 50);
            
            document.getElementById('geojson').value = JSON.stringify(layer.toGeoJSON().geometry);
            
            // Tampilkan/sembunyikan warning dengan animasi
            const warning = document.getElementById('warning-geo');
            warning.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                warning.style.display = 'none';
            }, 300);
        });

        // ==========================
        // Tampilkan Polygon yang Sudah Ada (Untuk Mode Edit)
        // ==========================
        <?php if($edit_data && !empty($edit_data['geojson'])): ?>
        var existingGeojson = <?= $edit_data['geojson'] ?>;
        if(existingGeojson && existingGeojson.coordinates) {
            var existingLayer;
            if(existingGeojson.type === 'Polygon') {
                // Konversi koordinat untuk Leaflet (lat, lng)
                var coords = existingGeojson.coordinates[0].map(function(coord) {
                    return [coord[1], coord[0]]; // swap lng,lat to lat,lng
                });
                existingLayer = L.polygon(coords, {
                    color: '#667eea',
                    fillColor: '#667eea',
                    weight: 2.5,
                    opacity: 0.9,
                    fillOpacity: 0.65
                });
            }
            
            if(existingLayer) {
                drawnItems.addLayer(existingLayer);
                map.flyToBounds(existingLayer.getBounds(), {
                    padding: [50, 50],
                    duration: 1.5
                });
                
                // Sembunyikan warning
                document.getElementById('warning-geo').style.display = 'none';
            }
        }
        <?php endif; ?>

        // ==========================
        // Tampilkan Lahan Eksisting (Peta Referensi) dengan Animasi
        // ==========================
        fetch('../get_lahan.php')
            .then(response => response.json())
            .then(data => {
                var existingLayer = L.geoJSON(data, {
                    style: function(feature) {
                        return {
                            fillColor: '#9e9e9e',
                            color: '#666',
                            weight: 2,
                            opacity: 0.7,
                            fillOpacity: 0.35,
                            dashArray: '4, 3',
                            lineCap: 'round',
                            lineJoin: 'round'
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        const tooltip = L.tooltip({
                            permanent: false,
                            direction: 'top',
                            className: 'custom-tooltip'
                        });
                        tooltip.setContent("📍 " + (feature.properties.kode_lahan || "Lahan Tersimpan"));
                        layer.bindTooltip(tooltip);
                        
                        // Hover effect halus
                        layer.on('mouseover', function() {
                            this.setStyle({
                                weight: 2.5,
                                opacity: 0.9,
                                fillOpacity: 0.5,
                                color: '#667eea'
                            });
                        });
                        
                        layer.on('mouseout', function() {
                            this.setStyle({
                                weight: 2,
                                opacity: 0.7,
                                fillOpacity: 0.35,
                                color: '#666'
                            });
                        });
                    }
                }).addTo(map);
                
                // Toggle dengan animasi
                const overlayControl = L.control.layers(null, {
                    "Tampilkan Lahan Eksisting": existingLayer
                }, {collapsed: false});
                overlayControl.addTo(map);
            })
            .catch(e => {
                console.error('Gagal memuat lahan eksisting:', e);
            });

        // ==========================
        // Logika GPS Walking Survey — Versi Toleran Sinyal Lemah
        // ==========================
        var watchId = null;
        var gpsTrackPoints = [];
        var gpsPolyline = null;
        var isTracking = false;
        var isManualMode = false;
        var manualClickHandler = null;

        const btnStartGps = document.getElementById('btn-start-gps');
        const btnStopGps = document.getElementById('btn-stop-gps');
        const btnResetGps = document.getElementById('btn-reset-gps');
        const btnAddManual = document.getElementById('btn-add-manual');
        const gpsStatus = document.getElementById('gps-status');
        const gpsStats = document.getElementById('gps-stats');
        const gpsSignalBar = document.getElementById('gps-signal-bar');
        const gpsTips = document.getElementById('gps-tips');
        const gpsInitProgress = document.getElementById('gps-init-progress');

        // Variabel untuk tracking
        var lastGpsPoint = null;
        var minDistanceThreshold = 3; // Minimum 3 meter sebelum menambah titik baru
        var gpsSignalSamples = [];
        var lastStableGpsPoint = null;
        var totalDistance = 0;

        // ==========================
        // Sistem Akurasi Adaptif 3 Level
        // ==========================
        var accuracyLevels = {
            high:   { maxAccuracy: 20,  avgAccuracy: 15,  label: 'Tinggi',  color: '#28a745' },
            medium: { maxAccuracy: 50,  avgAccuracy: 40,  label: 'Sedang',  color: '#ffc107' },
            low:    { maxAccuracy: 150, avgAccuracy: 120, label: 'Rendah',  color: '#dc3545' }
        };
        var currentAccuracyLevel = 'medium'; // Default sedang

        // Event listener untuk tombol sensitivitas
        document.querySelectorAll('.gps-sens-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.gps-sens-btn').forEach(function(b) {
                    b.classList.remove('active');
                    b.style.backgroundColor = '';
                    b.style.color = '';
                });
                this.classList.add('active');
                currentAccuracyLevel = this.dataset.level;
                var lvl = accuracyLevels[currentAccuracyLevel];
                
                // Style tombol aktif
                if (currentAccuracyLevel === 'high') {
                    this.style.backgroundColor = '#28a745';
                    this.style.color = '#fff';
                } else if (currentAccuracyLevel === 'medium') {
                    this.style.backgroundColor = '#ffc107';
                    this.style.color = '#212529';
                } else {
                    this.style.backgroundColor = '#dc3545';
                    this.style.color = '#fff';
                }
                
                gpsStatus.innerHTML = '<span class="text-info"><i class="fa fa-info-circle"></i> Sensitivitas diubah ke: ' + lvl.label + ' (maks ' + lvl.maxAccuracy + 'm)</span>';
            });
        });

        // ==========================
        // Kalman Filter Sederhana untuk Smoothing GPS
        // ==========================
        var kalmanFilter = {
            lat: null,
            lng: null,
            variance: null,
            
            reset: function() {
                this.lat = null;
                this.lng = null;
                this.variance = null;
            },
            
            process: function(lat, lng, accuracy) {
                // Konversi akurasi ke variance (accuracy dalam meter, kita bekerja di derajat)
                // ~111320 meter per derajat latitude
                var accuracyDeg = accuracy / 111320;
                var measurement_variance = accuracyDeg * accuracyDeg;
                
                if (this.lat === null) {
                    // Inisialisasi pertama
                    this.lat = lat;
                    this.lng = lng;
                    this.variance = measurement_variance;
                } else {
                    // Prediksi: tambahkan sedikit process noise (untuk pergerakan berjalan)
                    var processNoise = 0.000001; // ~0.11m proses noise
                    this.variance += processNoise;
                    
                    // Update (Kalman gain)
                    var kalmanGain = this.variance / (this.variance + measurement_variance);
                    
                    this.lat = this.lat + kalmanGain * (lat - this.lat);
                    this.lng = this.lng + kalmanGain * (lng - this.lng);
                    this.variance = (1 - kalmanGain) * this.variance;
                }
                
                return { lat: this.lat, lng: this.lng };
            }
        };

        // ==========================
        // Helper Functions
        // ==========================
        function getAverageAccuracy() {
            if (gpsSignalSamples.length === 0) return null;
            const total = gpsSignalSamples.reduce((sum, value) => sum + value, 0);
            return total / gpsSignalSamples.length;
        }

        function pushSignalSample(accuracy) {
            gpsSignalSamples.push(accuracy);
            if (gpsSignalSamples.length > 8) {
                gpsSignalSamples.shift();
            }
        }

        // Fungsi hitung jarak antara dua koordinat (Haversine formula)
        function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius bumi dalam meter
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c;
            return distance;
        }

        // Update signal bar visual indicator
        function updateSignalBar(accuracy) {
            gpsSignalBar.style.display = 'block';
            var bars = [
                document.getElementById('sig-bar-1'),
                document.getElementById('sig-bar-2'),
                document.getElementById('sig-bar-3'),
                document.getElementById('sig-bar-4'),
                document.getElementById('sig-bar-5')
            ];
            var signalLabel = document.getElementById('signal-label');
            
            var activeBars, color, label;
            if (accuracy <= 10) {
                activeBars = 5; color = '#28a745'; label = 'Sangat Kuat (' + Math.round(accuracy) + 'm)';
            } else if (accuracy <= 20) {
                activeBars = 4; color = '#28a745'; label = 'Kuat (' + Math.round(accuracy) + 'm)';
            } else if (accuracy <= 50) {
                activeBars = 3; color = '#ffc107'; label = 'Sedang (' + Math.round(accuracy) + 'm)';
            } else if (accuracy <= 100) {
                activeBars = 2; color = '#fd7e14'; label = 'Lemah (' + Math.round(accuracy) + 'm)';
            } else {
                activeBars = 1; color = '#dc3545'; label = 'Sangat Lemah (' + Math.round(accuracy) + 'm)';
            }
            
            bars.forEach(function(bar, i) {
                bar.style.background = (i < activeBars) ? color : '#ccc';
                bar.style.transition = 'background 0.3s ease';
            });
            signalLabel.textContent = label;
            signalLabel.style.color = color;
        }

        // Update statistik tracking
        function updateStats() {
            if (gpsTrackPoints.length > 0) {
                gpsStats.style.display = 'block';
                document.getElementById('stat-points').textContent = gpsTrackPoints.length;
                document.getElementById('stat-distance').textContent = Math.round(totalDistance);
            }
        }

        // Tambahkan titik ke track
        function addTrackPoint(lat, lng, panMap) {
            var newPoint = [lat, lng];
            
            // Hitung jarak dari titik terakhir
            if (lastGpsPoint !== null) {
                var dist = getDistanceFromLatLonInMeters(lastGpsPoint[0], lastGpsPoint[1], lat, lng);
                totalDistance += dist;
            }
            
            gpsTrackPoints.push(newPoint);
            lastGpsPoint = newPoint;
            lastStableGpsPoint = newPoint;
            
            if (gpsPolyline) {
                gpsPolyline.setLatLngs(gpsTrackPoints);
            }
            
            if (panMap !== false) {
                if (gpsTrackPoints.length === 1) {
                    map.setView(newPoint, 19, { animate: true, duration: 1.2 });
                } else {
                    map.panTo(newPoint, {
                        animate: true,
                        duration: 0.8,
                        easeLinearity: 0.2
                    });
                }
            }
            
            updateStats();
            btnStopGps.disabled = false;
            btnResetGps.disabled = false;
            btnAddManual.disabled = false;
        }

        // ==========================
        // GPS Start dengan Inisialisasi Progresif
        // ==========================
        btnStartGps.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }

            if (isTracking) {
                alert('GPS tracking sudah berjalan! Klik "Berhenti & Buat Area" untuk menghentikan.');
                return;
            }

            // Animasi tombol
            btnStartGps.disabled = true;
            btnStartGps.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Inisialisasi...';
            
            gpsStatus.innerHTML = '<span class="text-primary"><i class="fa fa-spinner fa-spin"></i> Mencari sinyal GPS...</span>';
            gpsStatus.style.animation = 'slideIn 0.3s ease';
            
            // Tampilkan progress bar inisialisasi
            gpsInitProgress.style.display = 'block';
            var initBar = document.getElementById('gps-init-bar');
            var initText = document.getElementById('gps-init-text');
            initBar.style.width = '0%';
            
            // Hapus layer lama jika ada
            drawnItems.clearLayers();
            if (gpsPolyline) {
                map.removeLayer(gpsPolyline);
            }
            gpsTrackPoints = [];
            lastGpsPoint = null;
            lastStableGpsPoint = null;
            gpsSignalSamples = [];
            totalDistance = 0;
            kalmanFilter.reset();

            // Inisialisasi Polyline untuk tracking jalan
            gpsPolyline = L.polyline(gpsTrackPoints, {
                color: '#667eea',
                weight: 4,
                dashArray: '5, 10',
                lineCap: 'round',
                lineJoin: 'round',
                opacity: 0.8,
                smoothFactor: 1.0
            }).addTo(map);

            isTracking = true;
            var positionCount = 0;
            var initPhase = true;
            var initStartTime = Date.now();
            var initDuration = 15000; // 15 detik inisialisasi
            var bestInitAccuracy = Infinity;
            var bestInitPosition = null;
            var initTimer = null;
            var hasAutoDowngraded = false;

            // Timer progress bar inisialisasi
            var initProgressTimer = setInterval(function() {
                if (!initPhase) {
                    clearInterval(initProgressTimer);
                    gpsInitProgress.style.display = 'none';
                    return;
                }
                var elapsed = Date.now() - initStartTime;
                var progress = Math.min((elapsed / initDuration) * 100, 100);
                initBar.style.width = progress + '%';
                
                var remaining = Math.max(0, Math.ceil((initDuration - elapsed) / 1000));
                initText.textContent = 'Mencari sinyal terbaik... (' + remaining + ' detik lagi)';
                
                // Setelah timeout, keluar dari init phase
                if (elapsed >= initDuration && initPhase) {
                    initPhase = false;
                    clearInterval(initProgressTimer);
                    gpsInitProgress.style.display = 'none';
                    
                    // Jika masih belum dapat sinyal yang sesuai level
                    var lvl = accuracyLevels[currentAccuracyLevel];
                    if (bestInitAccuracy > lvl.maxAccuracy) {
                        // Auto-downgrade level
                        if (currentAccuracyLevel === 'high') {
                            // Turunkan ke sedang
                            document.querySelector('[data-level="medium"]').click();
                            hasAutoDowngraded = true;
                            gpsStatus.innerHTML = '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> Sinyal lemah. Otomatis turun ke level Sedang (maks 50m).</span>';
                        } else if (currentAccuracyLevel === 'medium') {
                            // Turunkan ke rendah
                            document.querySelector('[data-level="low"]').click();
                            hasAutoDowngraded = true;
                            gpsStatus.innerHTML = '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> Sinyal lemah. Otomatis turun ke level Rendah (maks 150m).</span>';
                        }
                        
                        // Tampilkan tips sinyal lemah
                        gpsTips.style.display = 'block';
                        
                        // Jika sudah punya posisi apapun, gunakan itu
                        if (bestInitPosition) {
                            var filtered = kalmanFilter.process(bestInitPosition.lat, bestInitPosition.lng, bestInitAccuracy);
                            addTrackPoint(filtered.lat, filtered.lng);
                            
                            btnStartGps.innerHTML = '<i class="fa fa-circle text-danger blink"></i> Merekam...';
                            gpsStatus.innerHTML = '<span class="text-success"><i class="fa fa-circle text-danger blink"></i> Tracking aktif (sinyal lemah, menggunakan filter). Titik: ' + gpsTrackPoints.length + '</span>';
                        }
                    }
                }
            }, 200);

            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    if (!isTracking) return;

                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    var accuracy = position.coords.accuracy;

                    pushSignalSample(accuracy);
                    var avgAccuracy = getAverageAccuracy();
                    
                    // Update signal bar
                    updateSignalBar(accuracy);

                    var lvl = accuracyLevels[currentAccuracyLevel];

                    // Fase Inisialisasi: kumpulkan sinyal terbaik
                    if (initPhase) {
                        if (accuracy < bestInitAccuracy) {
                            bestInitAccuracy = accuracy;
                            bestInitPosition = { lat: lat, lng: lng };
                        }
                        
                        // Jika sinyal sudah cukup bagus sesuai level, langsung mulai
                        if (accuracy <= lvl.maxAccuracy) {
                            initPhase = false;
                            gpsInitProgress.style.display = 'none';
                            
                            var filtered = kalmanFilter.process(lat, lng, accuracy);
                            addTrackPoint(filtered.lat, filtered.lng);
                            
                            btnStartGps.innerHTML = '<i class="fa fa-circle text-danger blink"></i> Merekam...';
                            gpsStatus.innerHTML = '<span class="text-success"><i class="fa fa-circle text-danger blink"></i> Tracking aktif! Mulai berjalan mengelilingi batas lahan.</span>';
                            return;
                        }
                        
                        // Masih dalam init phase, tampilkan status
                        gpsStatus.innerHTML = '<span class="text-primary"><i class="fa fa-spinner fa-spin"></i> Mencari sinyal... (terbaik: ' + Math.round(bestInitAccuracy) + 'm, target: < ' + lvl.maxAccuracy + 'm)</span>';
                        return;
                    }

                    // === Fase Tracking Aktif ===
                    
                    // Filter sinyal berdasarkan level yang dipilih
                    if (accuracy > lvl.maxAccuracy * 2) {
                        // Sinyal sangat buruk (2x lipat dari threshold) — skip total
                        gpsStatus.innerHTML = '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> Sinyal terlalu lemah (' + Math.round(accuracy) + 'm). Menunggu perbaikan...</span>';
                        gpsTips.style.display = 'block';
                        return;
                    }
                    
                    // Terapkan Kalman filter untuk smoothing
                    var filtered = kalmanFilter.process(lat, lng, accuracy);
                    var smoothLat = filtered.lat;
                    var smoothLng = filtered.lng;

                    positionCount++;

                    // Outlier detection: jika titik baru melompat terlalu jauh (> 3x akurasi)
                    if (lastGpsPoint !== null) {
                        var dist = getDistanceFromLatLonInMeters(lastGpsPoint[0], lastGpsPoint[1], smoothLat, smoothLng);
                        
                        // Jarak terlalu kecil — skip (belum bergerak)
                        if (dist < 2) {
                            return;
                        }
                        
                        // Outlier: lompatan terlalu besar — gunakan Kalman saja, jangan panik
                        if (dist > accuracy * 3 && dist > 100) {
                            gpsStatus.innerHTML = '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> GPS loncat terdeteksi — di-filter. (Akurasi: ' + Math.round(accuracy) + 'm)</span>';
                            return;
                        }
                    }

                    // Tambah titik jika jarak cukup
                    var shouldAddPoint = lastGpsPoint === null ||
                        getDistanceFromLatLonInMeters(lastGpsPoint[0], lastGpsPoint[1], smoothLat, smoothLng) >= minDistanceThreshold;

                    if (shouldAddPoint) {
                        addTrackPoint(smoothLat, smoothLng);
                    }

                    // Status info
                    var signalQuality = accuracy <= 20 ? 'Kuat' : (accuracy <= 50 ? 'Sedang' : 'Lemah');
                    gpsStatus.style.animation = 'none';
                    setTimeout(function() {
                        gpsStatus.innerHTML = '<span class="text-success"><i class="fa fa-circle text-danger blink"></i> Tracking aktif — Sinyal: ' + signalQuality + ' (' + Math.round(accuracy) + 'm) | Titik: ' + gpsTrackPoints.length + '</span>';
                        gpsStatus.style.animation = 'slideIn 0.2s ease';
                    }, 10);
                },
                function(error) {
                    console.error(error);
                    var errorMsg = error.message;
                    if (error.code === 1) errorMsg = 'Akses lokasi ditolak. Izinkan akses GPS di browser.';
                    if (error.code === 2) errorMsg = 'Sinyal GPS tidak tersedia. Coba gunakan "Titik Manual".';
                    if (error.code === 3) errorMsg = 'Request timeout. Sinyal GPS sangat lemah. Coba level "Rendah" atau "Titik Manual".';
                    
                    gpsStatus.innerHTML = '<span class="text-danger"><i class="fa fa-warning"></i> ' + errorMsg + '</span>';
                    
                    // Jangan langsung stop — aktifkan manual mode
                    if (error.code === 2 || error.code === 3) {
                        btnAddManual.disabled = false;
                        gpsTips.style.display = 'block';
                        gpsStatus.innerHTML += '<br><span class="text-info small"><i class="fa fa-hand-pointer-o"></i> Gunakan tombol "+ Titik Manual" untuk menandai titik di peta.</span>';
                        // Biarkan tracking tetap aktif agar manual mode bisa dipakai
                        btnStartGps.innerHTML = '<i class="fa fa-map-pin"></i> Mode Manual';
                    } else {
                        isTracking = false;
                        btnStartGps.disabled = false;
                        btnStartGps.innerHTML = '<i class="fa fa-play"></i> Mulai Jalan';
                        btnStopGps.disabled = true;
                        btnResetGps.disabled = true;
                    }
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 5000,    // 5 detik — data cache berguna di sinyal lemah
                    timeout: 30000       // 30 detik — beri waktu lebih untuk mendapatkan posisi
                }
            );
        });

        // ==========================
        // Tombol Tambah Titik Manual
        // ==========================
        btnAddManual.addEventListener('click', function() {
            if (!isManualMode) {
                // Aktifkan mode manual
                isManualMode = true;
                btnAddManual.classList.remove('btn-info');
                btnAddManual.classList.add('btn-success');
                btnAddManual.innerHTML = '<i class="fa fa-hand-pointer-o"></i> Klik di Peta...';
                
                gpsStatus.innerHTML = '<span class="text-info"><i class="fa fa-crosshairs"></i> Mode Manual aktif — Klik di peta untuk menambahkan titik.</span>';
                
                // Ubah cursor peta
                document.getElementById('map-admin').style.cursor = 'crosshair';
                
                // Pastikan polyline ada
                if (!gpsPolyline) {
                    gpsPolyline = L.polyline(gpsTrackPoints, {
                        color: '#667eea',
                        weight: 4,
                        dashArray: '5, 10',
                        lineCap: 'round',
                        lineJoin: 'round',
                        opacity: 0.8,
                        smoothFactor: 1.0
                    }).addTo(map);
                    isTracking = true;
                }
                
                // Handler klik peta
                manualClickHandler = function(e) {
                    if (!isManualMode) return;
                    
                    var lat = e.latlng.lat;
                    var lng = e.latlng.lng;
                    
                    addTrackPoint(lat, lng, false);
                    
                    // Marker sementara di titik yang diklik
                    var marker = L.circleMarker([lat, lng], {
                        radius: 6,
                        color: '#667eea',
                        fillColor: '#fff',
                        fillOpacity: 1,
                        weight: 2
                    }).addTo(map);
                    
                    // Hapus marker setelah 2 detik
                    setTimeout(function() {
                        map.removeLayer(marker);
                    }, 2000);
                    
                    gpsStatus.innerHTML = '<span class="text-success"><i class="fa fa-check"></i> Titik manual ditambahkan! Total: ' + gpsTrackPoints.length + ' titik. Klik lagi atau nonaktifkan.</span>';
                    
                    btnStopGps.disabled = false;
                    btnResetGps.disabled = false;
                };
                
                map.on('click', manualClickHandler);
                
            } else {
                // Nonaktifkan mode manual
                isManualMode = false;
                btnAddManual.classList.remove('btn-success');
                btnAddManual.classList.add('btn-info');
                btnAddManual.innerHTML = '<i class="fa fa-map-pin"></i> + Titik Manual';
                
                document.getElementById('map-admin').style.cursor = '';
                
                if (manualClickHandler) {
                    map.off('click', manualClickHandler);
                    manualClickHandler = null;
                }
                
                if (gpsTrackPoints.length > 0) {
                    gpsStatus.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> Mode manual dinonaktifkan. Total: ' + gpsTrackPoints.length + ' titik.</span>';
                } else {
                    gpsStatus.innerHTML = 'Status: Mode manual dinonaktifkan.';
                }
            }
        });

        // ==========================
        // Stop & Buat Area
        // ==========================
        btnStopGps.addEventListener('click', function() {
            if(watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            
            // Nonaktifkan mode manual
            if (isManualMode) {
                isManualMode = false;
                if (manualClickHandler) {
                    map.off('click', manualClickHandler);
                    manualClickHandler = null;
                }
                document.getElementById('map-admin').style.cursor = '';
                btnAddManual.classList.remove('btn-success');
                btnAddManual.classList.add('btn-info');
                btnAddManual.innerHTML = '<i class="fa fa-map-pin"></i> + Titik Manual';
            }
            
            isTracking = false;
            btnStartGps.disabled = false;
            btnStartGps.innerHTML = '<i class="fa fa-play"></i> Mulai Jalan';
            btnStopGps.disabled = true;
            btnResetGps.disabled = false;
            btnAddManual.disabled = true;
            gpsSignalBar.style.display = 'none';
            gpsInitProgress.style.display = 'none';
            
            if(gpsTrackPoints.length < 3) {
                gpsStatus.innerHTML = '<span class="text-warning"><i class="fa fa-info-circle"></i> Minimal 3 titik diperlukan. Terekam: ' + gpsTrackPoints.length + '. Gunakan "Titik Manual" untuk menambah.</span>';
                btnAddManual.disabled = false;
                setTimeout(function() {
                    if(!isTracking) {
                        gpsStatus.innerHTML = 'Status: Tracking dihentikan. Tekan "Reset" untuk mulai ulang.';
                    }
                }, 5000);
                return;
            }

            // Hapus polyline tracking, ubah jadi polygon tertutup
            if (gpsPolyline) {
                map.removeLayer(gpsPolyline);
                gpsPolyline = null;
            }
            
            var polygon = L.polygon(gpsTrackPoints, {
                color: '#00d084',
                fillColor: '#00d084',
                weight: 2.5,
                opacity: 0,
                fillOpacity: 0,
                lineCap: 'round',
                lineJoin: 'round'
            });
            
            drawnItems.addLayer(polygon);
            
            // Animasi polygon muncul
            setTimeout(function() {
                polygon.setStyle({
                    opacity: 0.9,
                    fillOpacity: 0.65,
                    transition: 'all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)'
                });
            }, 50);
            
            map.flyToBounds(polygon.getBounds(), {
                padding: [80, 80],
                duration: 1.5
            });

            // Masukkan ke input form
            document.getElementById('geojson').value = JSON.stringify(polygon.toGeoJSON().geometry);
            
            // Animasi warning keluar
            const warning = document.getElementById('warning-geo');
            warning.style.animation = 'slideOut 0.3s ease';
            setTimeout(function() {
                warning.style.display = 'none';
            }, 300);
            
            // Hitung luas area polygon (approximate)
            var areaM2 = 0;
            if (gpsTrackPoints.length >= 3) {
                // Shoelace formula sederhana (approximate untuk area kecil)
                for (var i = 0; i < gpsTrackPoints.length; i++) {
                    var j = (i + 1) % gpsTrackPoints.length;
                    var lat1r = gpsTrackPoints[i][0] * Math.PI / 180;
                    var lat2r = gpsTrackPoints[j][0] * Math.PI / 180;
                    var dlng = (gpsTrackPoints[j][1] - gpsTrackPoints[i][1]) * Math.PI / 180;
                    areaM2 += dlng * (2 + Math.sin(lat1r) + Math.sin(lat2r));
                }
                areaM2 = Math.abs(areaM2 * 6371000 * 6371000 / 2);
            }
            var areaHa = (areaM2 / 10000).toFixed(2);
            
            // Status update
            gpsTips.style.display = 'none';
            gpsStats.style.display = 'none';
            gpsStatus.style.animation = 'slideIn 0.3s ease';
            gpsStatus.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> Area berhasil direkam! ' + gpsTrackPoints.length + ' titik | ~' + areaHa + ' Ha | Jarak: ' + Math.round(totalDistance) + 'm. Siap disimpan.</span>';
        });

        // ==========================
        // Reset
        // ==========================
        btnResetGps.addEventListener('click', function() {
            if(watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            
            // Nonaktifkan mode manual
            if (isManualMode) {
                isManualMode = false;
                if (manualClickHandler) {
                    map.off('click', manualClickHandler);
                    manualClickHandler = null;
                }
                document.getElementById('map-admin').style.cursor = '';
            }
            
            isTracking = false;
            kalmanFilter.reset();
            totalDistance = 0;
            
            // Animasi keluar
            if (gpsPolyline) {
                gpsPolyline.setStyle({opacity: 0});
                setTimeout(function() {
                    if (gpsPolyline && map.hasLayer(gpsPolyline)) {
                        map.removeLayer(gpsPolyline);
                    }
                    gpsPolyline = null;
                }, 300);
            }
            
            drawnItems.eachLayer(function(layer) {
                layer.setStyle({opacity: 0});
            });
            
            setTimeout(function() {
                drawnItems.clearLayers();
                gpsTrackPoints = [];
                lastGpsPoint = null;
                document.getElementById('geojson').value = '';
                
                btnStartGps.disabled = false;
                btnStartGps.innerHTML = '<i class="fa fa-play"></i> Mulai Jalan';
                btnStopGps.disabled = true;
                btnResetGps.disabled = true;
                btnAddManual.disabled = true;
                btnAddManual.classList.remove('btn-success');
                btnAddManual.classList.add('btn-info');
                btnAddManual.innerHTML = '<i class="fa fa-map-pin"></i> + Titik Manual';
                
                gpsSignalBar.style.display = 'none';
                gpsStats.style.display = 'none';
                gpsTips.style.display = 'none';
                gpsInitProgress.style.display = 'none';
                
                gpsStatus.style.animation = 'slideIn 0.3s ease';
                gpsStatus.innerHTML = '<span class="text-secondary">Status: Direset. Siap tracking baru...</span>';
                
                const warning = document.getElementById('warning-geo');
                warning.style.display = 'block';
                warning.style.animation = 'slideIn 0.3s ease';
            }, 300);
        });

        document.getElementById('form-lahan').addEventListener('submit', function(e) {
            if(document.getElementById('geojson').value === '') {
                e.preventDefault();
                alert('Silakan gambar area lahan pada peta terlebih dahulu!');
            }
        });
    </script>
    <?php 
    $extra_scripts = ob_get_clean();
    ?>

<?php else: ?>
    <!-- Halaman Data Lahan (Tabel) -->
    <?php if(isset($_GET['hapus_status'])): ?>
        <div class="alert <?= $_GET['hapus_status'] === 'berhasil' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <?= $_GET['hapus_status'] === 'berhasil' ? 'Data lahan berhasil dihapus.' : 'Data lahan gagal dihapus atau tidak ditemukan.' ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0" style="font-weight: 600;">Data Lahan</h4>
    </div>

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px;">
            <a href="?tambah_lahan=1" class="btn btn-green"><i class="fa fa-plus"></i> Tambah Data</a>
            <div class="input-group" style="width: 250px; max-width: 100%;">
                <input type="text" class="form-control" placeholder="Cari data lahan...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-center" style="font-size: 14px;">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama Lahan</th>
                        <th>Luas (Ha)</th>
                        <th>Kecamatan</th>
                        <th>Komoditas</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $data_lahan = mysqli_query($koneksi, "SELECT l.*, k.nama_komoditas FROM lahan l LEFT JOIN komoditas k ON l.id_komoditas = k.id ORDER BY l.id DESC");
                    $no = 1; 
                    while($row = mysqli_fetch_assoc($data_lahan)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['kode_lahan'] ?? '-') ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['nama_pemilik']) ?></td>
                        <td><?= $row['luas'] ?></td>
                        <td><?= htmlspecialchars($row['kecamatan'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['nama_komoditas']) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <form method="post" action="lahan.php" class="d-inline" onsubmit="return confirm('Hapus lahan ini? Tindakan ini tidak dapat dibatalkan.');">
                                <input type="hidden" name="hapus" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap: 10px;">
            <small class="text-muted">Showing data entries</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'layout_footer.php'; ?>
