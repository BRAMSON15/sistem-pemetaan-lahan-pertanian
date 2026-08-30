// ============================================================
// INISIALISASI PETA
// ============================================================
const map = L.map('map', {
    maxZoom: 22,
    zoomAnimation: true,
    fadeAnimation: true,
    markerZoomAnimation: true
}).setView([-2.1465, 133.4357], 10);

// Layer peta
const hybridLayer = L.tileLayer('http://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}', {
    attribution: '&copy; Google Maps', maxZoom: 22, maxNativeZoom: 19
});
const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: '&copy; Esri', maxZoom: 22, maxNativeZoom: 18
});
const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap', maxZoom: 22, maxNativeZoom: 19
});

hybridLayer.addTo(map);

const mapStage = document.querySelector('.map-stage');
const layerMenu = document.createElement('div');
layerMenu.className = 'custom-layer-menu';
layerMenu.innerHTML = `
    <button type="button" class="layer-menu-toggle" aria-label="Pilih jenis peta" title="Pilih jenis peta">
        <i class="fa fa-map" aria-hidden="true"></i>
    </button>
    <div class="layer-menu-panel" role="menu" aria-label="Pilihan jenis peta">
        <button type="button" class="layer-option active" data-layer="google" aria-pressed="true">
            <span class="dot dot-google"></span> Satelit (Google)
        </button>
        <button type="button" class="layer-option" data-layer="esri" aria-pressed="false">
            <span class="dot dot-esri"></span> Satelit (Esri)
        </button>
        <button type="button" class="layer-option" data-layer="osm" aria-pressed="false">
            <span class="dot dot-osm"></span> Peta Standar (OSM)
        </button>
    </div>
`;
mapStage.appendChild(layerMenu);

const layerToggleBtn = layerMenu.querySelector('.layer-menu-toggle');
const layerOptions = layerMenu.querySelectorAll('.layer-option');

function setMapLayer(type) {
    const layerMap = {
        google: hybridLayer,
        esri: esriLayer,
        osm: osmLayer
    };

    Object.values(layerMap).forEach(layer => {
        if (map.hasLayer(layer)) map.removeLayer(layer);
    });

    if (layerMap[type]) {
        layerMap[type].addTo(map);
    }

    layerOptions.forEach(option => {
        const active = option.dataset.layer === type;
        option.classList.toggle('active', active);
        option.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
}

layerToggleBtn.addEventListener('click', () => {
    layerMenu.classList.toggle('open');
});

layerOptions.forEach(option => {
    option.addEventListener('click', () => {
        const type = option.dataset.layer;
        setMapLayer(type);
        layerMenu.classList.remove('open');
    });
});

setMapLayer('google');

// ============================================================
// VARIABEL GLOBAL
// ============================================================
let clickMarker = null;       // pin merah saat klik area kosong
let geocodeTimeout = null;

// ============================================================
// KLIK DI MANA SAJA = LANGSUNG MUNCUL INFO (Google Maps style)
// Klik area kosong → pin merah + koordinat + reverse geocode
// Klik polygon/marker lahan → info lahan
// ============================================================
map.on('click', function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;

    // Hapus pin merah sebelumnya
    if (clickMarker) {
        map.removeLayer(clickMarker);
        clickMarker = null;
    }

    // Buat pin merah
    const pinIcon = L.divIcon({
        className: '',
        html: '<div class="click-pin-marker"><i class="fa fa-map-marker"></i></div>',
        iconSize: [30, 38],
        iconAnchor: [15, 38],
        popupAnchor: [0, -40]
    });

    clickMarker = L.marker([lat, lng], { icon: pinIcon, zIndexOffset: 9999 }).addTo(map);

    // Popup loading
    clickMarker.bindPopup(buildClickLoading(), { maxWidth: 320, minWidth: 270 }).openPopup();

    // Reverse geocode
    clearTimeout(geocodeTimeout);
    geocodeTimeout = setTimeout(() => {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=id`)
            .then(r => r.json())
            .then(geo => {
                const addr = geo.address || {};
                const content = buildClickPopup(lat, lng, {
                    display: geo.display_name || 'Lokasi tidak diketahui',
                    village: addr.village || addr.suburb || addr.hamlet || '-',
                    district: addr.city_district || addr.county || addr.municipality || '-',
                    city: addr.city || addr.town || addr.regency || '-',
                    province: addr.state || '-'
                });
                if (clickMarker) clickMarker.getPopup().setContent(content);
            })
            .catch(() => {
                const content = buildClickPopup(lat, lng, {
                    display: 'Gagal memuat informasi', village: '-', district: '-', city: '-', province: '-'
                });
                if (clickMarker) clickMarker.getPopup().setContent(content);
            });
    }, 200);
});

// Popup saat loading
function buildClickLoading() {
    return `<div class="popup-lokasi">
        <div class="lok-header"><i class="fa fa-map-pin"></i><span>Titik Lokasi</span></div>
        <div class="lok-loading"><i class="fa fa-spinner fa-spin"></i> Memuat informasi lokasi...</div>
    </div>`;
}

// Popup titik lokasi lengkap
function buildClickPopup(lat, lng, info) {
    const latF = lat.toFixed(7);
    const lngF = lng.toFixed(7);
    const coord = `${latF}, ${lngF}`;
    const gUrl = `https://www.google.com/maps?q=${lat},${lng}`;
    const oUrl = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}&zoom=17`;

    return `<div class="popup-lokasi">
        <div class="lok-header"><i class="fa fa-map-pin"></i><span>Titik Lokasi</span></div>
        <div class="lok-coord">
            <div>${latF}<br>${lngF}</div>
            <button class="btn-copy-coord" onclick="copyCoord('${coord}', this)"><i class="fa fa-copy"></i> Salin</button>
        </div>
        <div class="lok-body">
            <div class="lok-address">${info.display}</div>
            <div class="lok-detail">
                <span class="dk">Desa</span><span class="dv">${info.village}</span>
                <span class="dk">Kecamatan</span><span class="dv">${info.district}</span>
                <span class="dk">Kota/Kab</span><span class="dv">${info.city}</span>
                <span class="dk">Provinsi</span><span class="dv">${info.province}</span>
            </div>
        </div>
        <div class="lok-footer">
            <a href="${gUrl}" target="_blank" class="btn-gmaps-link"><i class="fa fa-external-link"></i> Google Maps</a>
            <a href="${oUrl}" target="_blank" class="btn-osm-link"><i class="fa fa-external-link"></i> OpenStreetMap</a>
        </div>
    </div>`;
}

// Salin koordinat
window.copyCoord = function(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        btn.innerHTML = '<i class="fa fa-check"></i> OK!';
        setTimeout(() => { btn.innerHTML = '<i class="fa fa-copy"></i> Salin'; }, 2000);
    }).catch(() => {
        const el = document.createElement('textarea');
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        btn.innerHTML = '<i class="fa fa-check"></i> OK!';
        setTimeout(() => { btn.innerHTML = '<i class="fa fa-copy"></i> Salin'; }, 2000);
    });
};

// ============================================================
// LOAD DATA LAHAN
// ============================================================
fetch('get_lahan.php')
    .then(r => r.json())
    .then(data => {
        const markerGroup = L.layerGroup().addTo(map);
        const polygonGroup = L.layerGroup().addTo(map);

        // ── Fungsi buat popup lahan ──
        function lahanPopup(props, geometry) {
            const center = getCenterOfGeometry(geometry);
            const gUrl = center ? `https://www.google.com/maps?q=${center[0]},${center[1]}` : '#';
            const coord = center ? `${center[0].toFixed(6)}, ${center[1].toFixed(6)}` : '-';

            return `<div class="popup-lahan">
                <div class="popup-header">
                    <h4><i class="fa fa-map-marker" style="color:${props.warna||'#388e3c'};margin-right:6px;"></i>${props.kode_lahan || 'Lahan'}</h4>
                    <span class="badge-komoditas"><span style="display:inline-block;width:8px;height:8px;background:${props.warna||'#999'};border-radius:50%;"></span>${props.komoditas || '-'}</span>
                </div>
                <div class="popup-body">
                    <div class="info-row"><span class="lbl">Pemilik</span><span class="val">${props.nama_pemilik || '-'}</span></div>
                    <div class="info-row"><span class="lbl">Kecamatan</span><span class="val">${props.kecamatan || '-'}</span></div>
                    <div class="info-row"><span class="lbl">Luas</span><span class="val">${props.luas || '-'} Ha</span></div>
                    <div class="info-row"><span class="lbl">Koordinat</span><span class="val" style="font-family:monospace;font-size:12px;">${coord}</span></div>
                    <div class="info-row"><span class="lbl">Keterangan</span><span class="val" style="font-weight:400;color:#666;">${props.keterangan || '-'}</span></div>
                    <div class="popup-preview">
                        <canvas id="pv-${props.id}" width="260" height="100" style="display:block;width:100%;height:100px;"></canvas>
                    </div>
                </div>
                <div class="popup-footer">
                    <a href="${gUrl}" target="_blank" class="btn-gmaps"><i class="fa fa-external-link"></i> Lihat di Google Maps</a>
                    <button class="btn-salin" onclick="copyCoord('${coord}',this)"><i class="fa fa-copy"></i> Salin Koordinat</button>
                </div>
            </div>`;
        }

        function getCenterOfGeometry(geometry) {
            if (!geometry || geometry.type !== 'Polygon' || !geometry.coordinates[0]) return null;
            const ring = geometry.coordinates[0];
            let latSum = 0, lngSum = 0;
            ring.forEach(c => { lngSum += c[0]; latSum += c[1]; });
            return [latSum / ring.length, lngSum / ring.length];
        }

        // ── Fungsi gambar preview polygon di canvas ──
        function drawPreview(canvas, geometry, color) {
            if (!canvas || !geometry || geometry.type !== 'Polygon') return;
            const ctx = canvas.getContext('2d');
            const ring = geometry.coordinates[0];
            const xs = ring.map(c => c[0]), ys = ring.map(c => c[1]);
            const xMin = Math.min(...xs), xMax = Math.max(...xs);
            const yMin = Math.min(...ys), yMax = Math.max(...ys);
            const pad = 12;
            const w = canvas.width - pad * 2, h = canvas.height - pad * 2;
            const xR = xMax - xMin || 1, yR = yMax - yMin || 1;
            const sc = Math.min(w / xR, h / yR);
            const ox = (canvas.width - xR * sc) / 2, oy = (canvas.height - yR * sc) / 2;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#f8fdf4';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.beginPath();
            ring.forEach((c, i) => {
                const px = ox + (c[0] - xMin) * sc;
                const py = canvas.height - (oy + (c[1] - yMin) * sc);
                i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
            });
            ctx.closePath();
            ctx.fillStyle = (color || '#388e3c') + '66';
            ctx.fill();
            ctx.strokeStyle = color || '#388e3c';
            ctx.lineWidth = 2.5;
            ctx.stroke();
        }

        // ── Render data ke peta ──
        const geoJsonLayer = L.geoJSON(data, {
            style: function(feature) {
                const c = feature.properties.warna || '#388e3c';
                return {
                    color: c, weight: 2.5, opacity: 0.9,
                    fillColor: c, fillOpacity: 0.3
                };
            },
            onEachFeature: function(feature, layer) {
                const props = feature.properties;
                const color = props.warna || '#388e3c';
                const popup = lahanPopup(props, feature.geometry);

                // Klik polygon → popup lahan (langsung, tanpa mode)
                layer.bindPopup(popup, { maxWidth: 320, minWidth: 280 });
                layer.on('popupopen', () => {
                    const cv = document.getElementById(`pv-${props.id}`);
                    drawPreview(cv, feature.geometry, color);
                });

                // Hover effect
                layer.on('mouseover', function() {
                    this.setStyle({ weight: 3.5, fillOpacity: 0.5 });
                });
                layer.on('mouseout', function() {
                    this.setStyle({ weight: 2.5, fillOpacity: 0.3 });
                });

                // Klik polygon → stop propagation agar pin merah tidak muncul di atas polygon
                layer.on('click', function(e) {
                    // hapus pin merah jika ada
                    if (clickMarker) { map.removeLayer(clickMarker); clickMarker = null; }
                    L.DomEvent.stopPropagation(e);
                });

                polygonGroup.addLayer(layer);

                // ── MARKER TITIK LOKASI (besar, jelas, kontras) ──
                const center = layer.getBounds().getCenter();
                const marker = L.marker(center, {
                    title: props.kode_lahan || 'Lokasi lahan',
                    zIndexOffset: 500,
                    icon: L.divIcon({
                        className: '',
                        html: `<div class="land-marker-icon" style="--mc:${color};">
                                   <i class="fa fa-map-marker"></i>
                               </div>`,
                        iconSize: [40, 48],
                        iconAnchor: [20, 48],
                        popupAnchor: [0, -48]
                    })
                });

                marker.bindPopup(popup, { maxWidth: 320, minWidth: 280 });
                marker.bindTooltip(`<b>${props.kode_lahan || 'Lahan'}</b>`, {
                    direction: 'top', offset: [0, -44], className: 'lahan-tooltip'
                });
                marker.on('popupopen', () => {
                    const cv = document.getElementById(`pv-${props.id}`);
                    drawPreview(cv, feature.geometry, color);
                });
                marker.on('click', function(e) {
                    if (clickMarker) { map.removeLayer(clickMarker); clickMarker = null; }
                    L.DomEvent.stopPropagation(e);
                });
                marker.feature = feature;
                markerGroup.addLayer(marker);
            }
        });

        // Auto-zoom ke lahan
        if (geoJsonLayer.getLayers().length > 0) {
            map.fitBounds(geoJsonLayer.getBounds(), { padding: [50, 50], maxZoom: 16 });
        }

        // Layer control
        const overlayControl = L.control.layers(null, {
            'Batas Lahan': polygonGroup,
            'Titik Lokasi': markerGroup
        }, {
            collapsed: true,
            position: 'bottomleft'
        });
overlayControl.addTo(map);

        // ── Pencarian ──
        const searchBtn = document.getElementById('btn-cari-lahan');
        const searchInput = document.getElementById('input-kode-lahan');
        const errorMsg = document.getElementById('pesan-error-cari');

        if (searchBtn && searchInput) {
            const doSearch = () => {
                const q = searchInput.value.trim().toLowerCase();
                if (!q) return;
                searchBtn.disabled = true;

                let found = false;
                markerGroup.eachLayer(m => {
                    const p = m.feature.properties;
                    const k = (p.kode_lahan || '').toLowerCase();
                    const n = (p.nama_pemilik || '').toLowerCase();
                    const c = (p.kecamatan || '').toLowerCase();
                    if (k.includes(q) || n.includes(q) || c.includes(q)) {
                        found = true;
                        if (clickMarker) { map.removeLayer(clickMarker); clickMarker = null; }
                        map.flyTo(m.getLatLng(), 18, { duration: 1.2 });
                        setTimeout(() => m.openPopup(), 1300);
                    }
                });

                if (!found) {
                    errorMsg.style.display = 'block';
                    setTimeout(() => { errorMsg.style.display = 'none'; }, 3000);
                }
                searchBtn.disabled = false;
            };

            searchBtn.addEventListener('click', doSearch);
            searchInput.addEventListener('keypress', e => { if (e.key === 'Enter') doSearch(); });
        }
    })
    .catch(err => console.error('Error:', err));
