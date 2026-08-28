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
                            <button type="button" id="btn-start-gps" class="btn btn-sm btn-primary"><i class="fa fa-play"></i> Mulai Jalan</button>
                            <button type="button" id="btn-stop-gps" class="btn btn-sm btn-danger" disabled><i class="fa fa-stop"></i> Berhenti & Buat Area</button>
                            <button type="button" id="btn-reset-gps" class="btn btn-sm btn-warning" disabled><i class="fa fa-refresh"></i> Reset</button>
                            <div id="gps-status" class="small font-weight-bold mt-2 text-secondary">Status: Menunggu instruksi...</div>
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
        // Logika GPS Walking Survey dengan Animasi
        // ==========================
        var watchId = null;
        var gpsTrackPoints = [];
        var gpsPolyline = null;
        var isTracking = false;

        const btnStartGps = document.getElementById('btn-start-gps');
        const btnStopGps = document.getElementById('btn-stop-gps');
        const btnResetGps = document.getElementById('btn-reset-gps');
        const gpsStatus = document.getElementById('gps-status');

        // Variabel untuk tracking
        var lastGpsPoint = null;
        var minDistanceThreshold = 5; // Minimum 5 meter sebelum menambah titik baru

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

        btnStartGps.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }

            // Cek apakah sudah tracking
            if (isTracking) {
                alert('GPS tracking sudah berjalan! Klik "Berhenti & Buat Area" untuk menghentikan.');
                return;
            }

            // Animasi tombol
            btnStartGps.disabled = true;
            btnStartGps.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Inisialisasi...';
            
            gpsStatus.innerHTML = '<span class="text-primary"><i class="fa fa-spinner fa-spin"></i> Mencari sinyal GPS...</span>';
            gpsStatus.style.animation = 'slideIn 0.3s ease';
            
            // Hapus layer lama jika ada
            drawnItems.clearLayers();
            if (gpsPolyline) {
                map.removeLayer(gpsPolyline);
            }
            gpsTrackPoints = [];
            lastGpsPoint = null;
            
            // Inisialisasi Polyline untuk tracking jalan dengan style halus
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
            let positionCount = 0;

            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    if (!isTracking) return; // Stop jika tracking sudah dibatalkan

                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    // Abaikan sinyal GPS yang sangat tidak akurat (di atas 20 meter)
                    // untuk mencegah titik awal loncat-loncat saat user masih diam
                    if (accuracy > 20) {
                        gpsStatus.style.animation = 'none';
                        setTimeout(() => {
                            gpsStatus.innerHTML = `<span class="text-warning"><i class="fa fa-spinner fa-spin"></i> Menunggu sinyal GPS stabil... (Akurasi: ${Math.round(accuracy)}m)</span>`;
                            gpsStatus.style.animation = 'slideIn 0.2s ease';
                        }, 10);
                        return;
                    }

                    positionCount++;

                    // HANYA tambah titik jika jarak dari titik terakhir >= minDistanceThreshold
                    if (lastGpsPoint === null || 
                        getDistanceFromLatLonInMeters(lastGpsPoint[0], lastGpsPoint[1], lat, lng) >= minDistanceThreshold) {
                        
                        const newPoint = [lat, lng];
                        gpsTrackPoints.push(newPoint);
                        lastGpsPoint = newPoint;
                        
                        // Perbarui polyline secara dinamis
                        gpsPolyline.setLatLngs(gpsTrackPoints);
                        
                        // Gunakan panTo dan setZoom alih-alih flyTo agar transisi pergerakan
                        // menjadi sangat halus dan tidak kaku/melompat saat user berjalan
                        if (lastGpsPoint === null || gpsTrackPoints.length === 1) {
                            map.setView(newPoint, 20, { animate: true, duration: 1.5 });
                        } else {
                            map.panTo(newPoint, {
                                animate: true,
                                duration: 1.0,
                                easeLinearity: 0.1
                            });
                        }
                    }

                    // Update status dengan animasi
                    gpsStatus.style.animation = 'none';
                    setTimeout(() => {
                        gpsStatus.innerHTML = `<span class="text-success"><i class="fa fa-circle text-danger blink"></i> Tracking aktif... (${Math.round(accuracy)}m) | Titik tercatat: ${gpsTrackPoints.length}</span>`;
                        gpsStatus.style.animation = 'slideIn 0.2s ease';
                    }, 10);
                    
                    btnStopGps.disabled = false;
                    btnResetGps.disabled = false;
                },
                function(error) {
                    console.error(error);
                    let errorMsg = error.message;
                    if (error.code === 1) errorMsg = 'Akses lokasi ditolak. Izinkan akses GPS di browser.';
                    if (error.code === 2) errorMsg = 'Sinyal GPS tidak tersedia.';
                    if (error.code === 3) errorMsg = 'Request timeout. Coba lagi.';
                    
                    gpsStatus.innerHTML = '<span class="text-danger"><i class="fa fa-warning"></i> Gagal: ' + errorMsg + '</span>';
                    isTracking = false;
                    btnStartGps.disabled = false;
                    btnStartGps.innerHTML = '<i class="fa fa-play"></i> Mulai Jalan';
                    btnStopGps.disabled = true;
                    btnResetGps.disabled = true;
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 2000, 
                    timeout: 10000
                }
            );
        });

        btnStopGps.addEventListener('click', function() {
            if(watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            
            isTracking = false;
            btnStartGps.disabled = false;
            btnStartGps.innerHTML = '<i class="fa fa-play"></i> Mulai Jalan';
            btnStopGps.disabled = true;
            btnResetGps.disabled = false;
            
            if(gpsTrackPoints.length < 3) {
                gpsStatus.innerHTML = '<span class="text-warning"><i class="fa fa-info-circle"></i> Minimal 3 titik diperlukan. Terekam: ' + gpsTrackPoints.length + '</span>';
                setTimeout(() => {
                    if(!isTracking) {
                        gpsStatus.innerHTML = 'Status: Tracking dihentikan. Tekan "Reset" untuk mulai ulang.';
                    }
                }, 3000);
                return;
            }

            // Hapus polyline tracking, ubah jadi polygon tertutup dengan animasi
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
            setTimeout(() => {
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
            setTimeout(() => {
                warning.style.display = 'none';
            }, 300);
            
            // Status update dengan animasi
            gpsStatus.style.animation = 'slideIn 0.3s ease';
            gpsStatus.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> Tracking dihentikan! Area lahan berhasil direkam. Siap disimpan.</span>';
        });

        btnResetGps.addEventListener('click', function() {
            // Stop geolocation watch terlebih dahulu
            if(watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            
            isTracking = false;
            
            // Animasi keluar
            if (gpsPolyline) {
                gpsPolyline.setStyle({opacity: 0});
                setTimeout(() => {
                    if (gpsPolyline && map.hasLayer(gpsPolyline)) {
                        map.removeLayer(gpsPolyline);
                    }
                    gpsPolyline = null;
                }, 300);
            }
            
            drawnItems.eachLayer(layer => {
                layer.setStyle({opacity: 0});
            });
            
            setTimeout(() => {
                drawnItems.clearLayers();
                gpsTrackPoints = [];
                lastGpsPoint = null;
                document.getElementById('geojson').value = '';
                
                btnStartGps.disabled = false;
                btnStartGps.innerHTML = '<i class="fa fa-play"></i> Mulai Jalan';
                btnStopGps.disabled = true;
                btnResetGps.disabled = true;
                
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
