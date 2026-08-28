<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

$query = mysqli_query($koneksi, "
    SELECT l.*, k.nama_komoditas, k.warna_polygon 
    FROM lahan l 
    LEFT JOIN komoditas k ON l.id_komoditas = k.id
");

$features = [];

while($row = mysqli_fetch_assoc($query)) {
    // geojson field from DB should contain valid GeoJSON geometry like:
    // {"type": "Polygon", "coordinates": [...]}
    $geometry = json_decode($row['geojson']);
    
    if ($geometry) {
        $feature = [
            'type' => 'Feature',
            'properties' => [
                'id' => $row['id'],
                'kode_lahan' => $row['kode_lahan'],
                'nama_pemilik' => $row['nama_pemilik'],
                'kecamatan' => $row['kecamatan'],
                'luas' => $row['luas'],
                'komoditas' => $row['nama_komoditas'],
                'warna' => $row['warna_polygon'],
                'keterangan' => $row['keterangan']
            ],
            'geometry' => $geometry
        ];
        $features[] = $feature;
    }
}

echo json_encode([
    'type' => 'FeatureCollection',
    'features' => $features
]);
?>
