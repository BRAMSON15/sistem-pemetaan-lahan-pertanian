 
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
        var existingGeojson = window.existingLahanGeojson || null;
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
                    // Prediksi: tambahkan process noise (pergerakan pejalan kaki ~1-2m per update)
                    var walkSpeedDeg = 2.0 / 111320; // 2 meter dalam derajat
                    var processNoise = walkSpeedDeg * walkSpeedDeg;
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
                    map.setView(newPoint, 19, { animate: true, duration: 1.0 });
                } else {
                    map.panTo(newPoint, {
                        animate: true,
                        duration: 0.5,
                        easeLinearity: 0.5
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

                    var shouldAddPoint = false;

                    if (lastGpsPoint !== null) {
                        var dist = getDistanceFromLatLonInMeters(lastGpsPoint[0], lastGpsPoint[1], smoothLat, smoothLng);
                        
                        // Dynamic threshold: semakin buruk akurasi, semakin jauh user harus bergerak agar titik dicatat
                        // Ini mencegah GPS drift (titik bergerak sendiri saat user diam) di kondisi sinyal buruk.
                        var dynamicThreshold = Math.max(minDistanceThreshold, accuracy * 0.15); 
                        
                        if (dist < dynamicThreshold) {
                            // Anggap user masih diam (hanya noise GPS)
                            return;
                        }
                        
                        // Outlier: lompatan terlalu besar dalam waktu singkat (noise ekstrem)
                        if (dist > accuracy * 2 && dist > 50) {
                            gpsStatus.innerHTML = '<span class="text-warning"><i class="fa fa-exclamation-triangle"></i> GPS loncat terdeteksi — di-filter. (Akurasi: ' + Math.round(accuracy) + 'm)</span>';
                            return;
                        }

                        shouldAddPoint = true;
                    } else {
                        shouldAddPoint = true;
                    }

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
    