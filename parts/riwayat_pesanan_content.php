<?php
// File ini hanya berisi konten (tabel riwayat).
// Variabel $conn dan $user_id diwariskan dari file pemanggil.

$index = 1;

// Menggunakan query yang paling aman untuk mencegah error karena spasi/huruf besar-kecil
$query = "
    SELECT 
        p.id_pesanan,
        p.nomor_meja,
        p.tanggal_pesanan,
        p.status_pesanan,
        SUM(dp.jumlah * dp.harga_saat_pesan) AS total_harga,
        GROUP_CONCAT(CONCAT(m.nama, ' (', dp.jumlah, 'x)') SEPARATOR '<br>') AS detail_menu
    FROM 
        pesanan p
    JOIN 
        detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    JOIN 
        menu m ON dp.id_menu = m.menu_id
    WHERE 
        p.user_id = ? AND TRIM(LOWER(p.status_pesanan)) = 'selesai'
    GROUP BY 
        p.id_pesanan 
    ORDER BY 
        p.tanggal_pesanan DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query Gagal: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pesanan_history = $stmt->get_result();
?>

<div class="card shadow-sm">
    <div class="card-header bg-light text-dark">
        <h5 class="mb-0 fw-bold">Pesanan Selesai</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomor Meja</th>
                        <th>Detail Pesanan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pesanan_history->num_rows > 0): ?>
                        <?php while ($row = $pesanan_history->fetch_assoc()): ?>
                        <tr>
                            <td><?= $index++;?></td>
                            <td><strong><?= htmlspecialchars($row['nomor_meja']); ?></strong></td>
                            <td><?= $row['detail_menu']; ?></td>
                            <td>Rp <?= number_format($row['total_harga']); ?></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($row['status_pesanan']); ?></span></td>
                            <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Anda belum memiliki riwayat pesanan yang selesai.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>