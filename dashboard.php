<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan Hasil Laut - Rembang</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>Manajemen Penjualan Hasil Laut</h1>
        <p>Login: <?= e($_SESSION['username']) ?> | <a href="logout.php">Keluar</a></p>
    </div>

    <div class="container">

        <?php if ($pesan): ?>
            <div class="pesan"><?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>

        <div class="ringkasan">
            <div class="ringkasan-item">
                <span class="ringkasan-angka"><?= $st['jml'] ?></span>
                <span class="ringkasan-label">Transaksi</span>
            </div>
            <div class="ringkasan-item">
                <span class="ringkasan-angka"><?= rp($st['pendapatan']) ?></span>
                <span class="ringkasan-label">Total Pendapatan</span>
            </div>
        </div>

        <div class="section">
            <h2><?= $edit ? 'Edit Penjualan' : 'Tambah Penjualan Baru' ?></h2>
            <form method="POST" action="?aksi=<?= $edit ? 'edit' : 'tambah' ?>">
                <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                <?php if ($edit): ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="produk_id">Produk</label>
                        <select name="produk_id" id="produk_id" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $kg = '';
                            foreach ($produk_list as $p):
                                if ($p['kategori'] !== $kg):
                                    if ($kg !== '')
                                        echo '</optgroup>';
                                    $kg = $p['kategori'];
                                    echo '<optgroup label="' . htmlspecialchars($kg) . '">';
                                endif;
                                $sel = ($edit && $edit['produk_id'] == $p['id']) ? 'selected' : '';
                                ?>
                                <option value="<?= $p['id'] ?>" <?= $sel ?>><?= htmlspecialchars($p['nama']) ?>
                                    (<?= $p['satuan'] ?>)</option>
                            <?php
                            endforeach;
                            if ($kg !== '')
                                echo '</optgroup>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" step="0.01" min="0.01" required
                            value="<?= $edit ? $edit['jumlah'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="harga_satuan">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" id="harga_satuan" min="1" required
                            value="<?= $edit ? $edit['harga_satuan'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="pembeli">Pembeli</label>
                        <input type="text" name="pembeli" id="pembeli"
                            value="<?= htmlspecialchars($edit ? $edit['pembeli'] : '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" required
                            value="<?= $edit ? $edit['tanggal'] : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <input type="text" name="catatan" id="catatan"
                            value="<?= htmlspecialchars($edit ? ($edit['catatan'] ?? '') : '') ?>">
                    </div>
                </div>
                <div id="preview-total" class="preview-total"></div>
                <div class="form-aksi">
                    <button type="submit" class="btn">Simpan</button>
                    <?php if ($edit): ?>
                        <a href="index.php" class="btn btn-batal">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="section">
            <h2>Data Penjualan</h2>
            <form method="GET" class="form-cari">
                <input type="text" name="cari" placeholder="Cari produk atau pembeli..."
                    value="<?= htmlspecialchars($cari) ?>">
                <button type="submit" class="btn">Cari</button>
                <?php if ($cari): ?>
                    <a href="index.php" class="btn btn-batal">Reset</a>
                <?php endif; ?>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                            <th>Pembeli</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (empty($rows)):
                            ?>
                            <tr>
                                <td colspan="10" class="kosong">Belum ada data penjualan.</td>
                            </tr>
                        <?php
                        else:
                            foreach ($rows as $row):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['produk_nama']) ?></td>
                                    <td><?= htmlspecialchars($row['kategori_nama']) ?></td>
                                    <td><?= $row['jumlah'] ?>         <?= $row['satuan'] ?></td>
                                    <td><?= rp($row['harga_satuan']) ?></td>
                                    <td><strong><?= rp($row['total_harga']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['pembeli']) ?></td>
                                    <td><?= htmlspecialchars($row['catatan'] ?: '-') ?></td>
                                    <td class="aksi-cell">
                                        <a href="?aksi=edit&id=<?= $row['id'] ?>" class="btn btn-kecil">Edit</a>
                                        <form method="POST" action="?aksi=hapus" style="display:inline"
                                            onsubmit="return confirm('Hapus data ini?')"><input type="hidden" name="csrf"
                                                value="<?= e(csrf()) ?>"><input type="hidden" name="id"
                                                value="<?= (int) $row['id'] ?>"><button
                                                class="btn btn-kecil btn-hapus">Hapus</button></form>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">Manajemen Penjualan Hasil Laut &mdash; Kota Rembang</div>

    <script>
        var jml = document.getElementById('jumlah');
        var hrg = document.getElementById('harga_satuan');
        function hitungTotal() {
            var j = parseFloat(jml.value) || 0;
            var h = parseFloat(hrg.value) || 0;
            var t = j * h;
            document.getElementById('preview-total').textContent = t > 0 ? 'Total: Rp ' + t.toLocaleString('id-ID') : '';
        }
        jml.addEventListener('input', hitungTotal);
        hrg.addEventListener('input', hitungTotal);
        hitungTotal();
    </script>
</body>

</html>