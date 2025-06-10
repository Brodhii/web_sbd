<?php
require_once('.includes/init_session.php');
require_once('config.php');
include('.includes/header.php');
$title = "Laporan Pesanan";

// ambil data berdasarkan filteer tanggal dan menu
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$menu_id = isset($_GET['menu_id']) ? $_GET['menu_id'] : 'all';

// query untuk mengambil daftar menu sebagai opsi filter
$menu_query = "SELECT menu_id, nama FROM menu";
$menu_result = $conn->query($menu_query);
$menu_options = $menu_result->fetch_all(MYSQLI_ASSOC);

// query utama untuk aporan pesanan
$query = "
    SELECT p.tanggal_pemesanan AS tanggal, 
           u.nama AS pelanggan, 
           m.nama AS menu, 
           p.jumlah AS jumlah, 
           p.status 
    FROM pesanan p
    JOIN users u ON p.user_id = u.user_id
    JOIN menu m ON p.menu_id = m.menu_id
    WHERE p.tanggal_pemesanan = ?
";
$params = [$tanggal];

if ($menu_id !== 'all') {
    $query .= " AND p.menu_id = ?";
    $params[] = $menu_id;
}

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data_tabel = [];
$data_chart_labels = [];
$data_chart_values = [];

while ($row = $result->fetch_assoc()) {
    $data_tabel[] = $row;

    // Simpan data untuk grafik
    if (!isset($data_chart_labels[$row['menu']])) {
        $data_chart_labels[$row['menu']] = 0;
    }
    $data_chart_labels[$row['menu']] += $row['jumlah'];
}

$data_chart_keys = array_keys($data_chart_labels);
$data_chart_values = array_values($data_chart_labels);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h1 class="my-4">Laporan Pesanan</h1>

    <!-- Filter Data -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Pilih Tanggal</label>
                <input type="date" class="form-control" name="tanggal" value="<?= $tanggal; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Pilih Menu</label>
                <select class="form-control" name="menu_id">
                    <option value="all" <?= $menu_id === 'all' ? 'selected' : ''; ?>>Semua Menu</option>
                    <?php foreach ($menu_options as $menu): ?>
                        <option value="<?= $menu['menu_id']; ?>" <?= $menu_id == $menu['menu_id'] ? 'selected' : ''; ?>>
                            <?= $menu['nama']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Filter Laporan</button>
    </form>

    <!-- Tabel Pesanan -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Tabel Pesanan</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Menu</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_tabel as $row): ?>
                        <tr>
                            <td><?= $row['tanggal']; ?></td>
                            <td><?= $row['pelanggan']; ?></td>
                            <td><?= $row['menu']; ?></td>
                            <td><?= $row['jumlah']; ?></td>
                            <td><?= ucfirst($row['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grafik Pesanan -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Grafik Pesanan Berdasarkan Menu</h5>
            <div style="width: 80%; max-width: 600px; margin: auto;">
                <canvas id="barChart"></canvas>
            </div>
            <div style="width: 80%; max-width: 600px; margin: auto; margin-top: 30px;">
                <canvas id="lineChart"></canvas>
            </div>
            <div style="width: 80%; max-width: 400px; margin: auto; margin-top: 30px;">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var barCtx = document.getElementById('barChart').getContext('2d');
        var lineCtx = document.getElementById('lineChart').getContext('2d');
        var doughnutCtx = document.getElementById('doughnutChart').getContext('2d');

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($data_chart_keys); ?>,
                datasets: [{
                    label: 'Total Pesanan',
                    data: <?= json_encode($data_chart_values); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            }
        });

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($data_chart_keys); ?>,
                datasets: [{
                    label: 'Total Pesanan',
                    data: <?= json_encode($data_chart_values); ?>,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2
                }]
            }
        });

        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($data_chart_keys); ?>,
                datasets: [{
                    label: 'Total Pesanan',
                    data: <?= json_encode($data_chart_values); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    borderWidth: 2
                }]
            }
        });
    });
</script>

<?php include('.includes/footer.php'); ?>