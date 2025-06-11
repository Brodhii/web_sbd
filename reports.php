<?php
// Ganti include init_session.php dengan session_start() langsung
session_start();
require_once('config.php');

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

// Memanggil header template Anda
include('.includes/header.php');
$title = "Laporan Pesanan";

// Ambil data berdasarkan filter tanggal dan menu
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$menu_id = isset($_GET['menu_id']) && $_GET['menu_id'] !== 'all' ? (int)$_GET['menu_id'] : 'all';

// Query untuk mengambil daftar menu sebagai opsi filter
$menu_query = "SELECT menu_id, nama FROM menu ORDER BY nama ASC";
$menu_result = $conn->query($menu_query);
$menu_options = $menu_result->fetch_all(MYSQLI_ASSOC);


// ===================================================================
// QUERY UTAMA DIPERBAIKI TOTAL
// ===================================================================
$query = "
    SELECT 
        DATE(p.tanggal_pesanan) AS tanggal,
        u.username AS pelanggan,
        m.nama AS menu,
        dp.jumlah AS jumlah,
        dp.harga_saat_pesan,
        p.status_pesanan AS status
    FROM 
        detail_pesanan dp
    JOIN 
        pesanan p ON dp.id_pesanan = p.id_pesanan
    JOIN 
        users u ON p.user_id = u.user_id
    JOIN 
        menu m ON dp.id_menu = m.menu_id
    WHERE 
        DATE(p.tanggal_pesanan) = ?
";
$params = [$tanggal];
$types = "s";

if ($menu_id !== 'all') {
    $query .= " AND dp.id_menu = ?";
    $params[] = $menu_id;
    $types .= "i";
}

$query .= " ORDER BY p.tanggal_pesanan DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query Gagal: " . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data_tabel = [];
$data_chart_labels = [];

while ($row = $result->fetch_assoc()) {
    $data_tabel[] = $row;

    // Siapkan data untuk grafik
    $menu_nama = $row['menu'];
    if (!isset($data_chart_labels[$menu_nama])) {
        $data_chart_labels[$menu_nama] = 0;
    }
    $data_chart_labels[$menu_nama] += $row['jumlah'];
}

$data_chart_keys = array_keys($data_chart_labels);
$data_chart_values = array_values($data_chart_labels);
// ===================================================================
// AKHIR PERBAIKAN QUERY
// ===================================================================
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Laporan Pesanan</h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Pilih Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= htmlspecialchars($tanggal); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Pilih Menu</label>
                        <select class="form-select" name="menu_id">
                            <option value="all" <?= $menu_id === 'all' ? 'selected' : ''; ?>>Semua Menu</option>
                            <?php foreach ($menu_options as $menu): ?>
                                <option value="<?= $menu['menu_id']; ?>" <?= $menu_id == $menu['menu_id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($menu['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Detail Laporan Tanggal: <?= htmlspecialchars($tanggal); ?></h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Menu</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php if (!empty($data_tabel)): ?>
                        <?php foreach ($data_tabel as $row): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                                <td><?= htmlspecialchars($row['pelanggan']); ?></td>
                                <td><?= htmlspecialchars($row['menu']); ?></td>
                                <td><?= $row['jumlah']; ?></td>
                                <td><span class="badge bg-label-info"><?= htmlspecialchars($row['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Tidak ada data untuk filter yang dipilih.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($data_tabel)): ?>
    <div class="card mt-4">
        <h5 class="card-header">Grafik Penjualan Menu</h5>
        <div class="card-body">
            <canvas id="barChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Hanya jalankan script jika ada data untuk grafik
        <?php if (!empty($data_chart_keys)): ?>
        var barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($data_chart_keys); ?>,
                datasets: [{
                    label: 'Total Kuantitas Terjual',
                    data: <?= json_encode($data_chart_values); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        <?php endif; ?>
    });
</script>

<?php include('.includes/footer.php'); ?>