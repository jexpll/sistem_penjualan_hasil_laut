<?php
require_once __DIR__ . '/auth.php';
wajib_login();
$aksi = $_GET['aksi'] ?? '';
$pesan = $_GET['pesan'] ?? '';
function data_penjualan()
{
    $jumlah = (float) ($_POST['jumlah'] ?? 0);
    $harga = (int) ($_POST['harga_satuan'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    if ((int) ($_POST['produk_id'] ?? 0) < 1 || $jumlah <= 0 || $harga < 1 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal))
        exit('Input tidak valid.');
    return [(int) $_POST['produk_id'], $jumlah, $harga, trim($_POST['pembeli'] ?? '') ?: 'Umum', $tanggal, trim($_POST['catatan'] ?? ''), (int) ($jumlah * $harga)];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    if ($aksi === 'hapus')
        $ok = $pdo->prepare('DELETE FROM penjualan WHERE id=?')->execute([$id]);
    else {
        $d = data_penjualan();
        if ($aksi === 'tambah')
            $ok = $pdo->prepare('INSERT INTO penjualan (produk_id,jumlah,harga_satuan,total_harga,pembeli,tanggal,catatan) VALUES (?,?,?,?,?,?,?)')->execute([$d[0], $d[1], $d[2], $d[6], $d[3], $d[4], $d[5]]);
        elseif ($aksi === 'edit')
            $ok = $pdo->prepare('UPDATE penjualan SET produk_id=?,jumlah=?,harga_satuan=?,total_harga=?,pembeli=?,tanggal=?,catatan=? WHERE id=?')->execute([$d[0], $d[1], $d[2], $d[6], $d[3], $d[4], $d[5], $id]);
        else
            $ok = false;
    }
    redirect_msg($ok ? 'Data berhasil disimpan.' : 'Operasi gagal.');
}
$edit = null;
if ($aksi === 'edit' && isset($_GET['id'])) {
    $s = $pdo->prepare('SELECT * FROM penjualan WHERE id=?');
    $s->execute([(int) $_GET['id']]);
    $edit = $s->fetch();
}
$produk_list = $pdo->query('SELECT p.id,p.nama,p.satuan,k.nama kategori FROM produk p JOIN kategori k ON p.kategori_id=k.id ORDER BY k.nama,p.nama')->fetchAll();
$cari = trim($_GET['cari'] ?? '');
$sql = 'SELECT pj.*,p.nama produk_nama,p.satuan,k.nama kategori_nama FROM penjualan pj JOIN produk p ON pj.produk_id=p.id JOIN kategori k ON p.kategori_id=k.id';
if ($cari) {
    $s = $pdo->prepare($sql . ' WHERE p.nama LIKE ? OR pj.pembeli LIKE ? ORDER BY pj.tanggal DESC,pj.id DESC');
    $s->execute(["%$cari%", "%$cari%"]);
    $rows = $s->fetchAll();
} else
    $rows = $pdo->query($sql . ' ORDER BY pj.tanggal DESC,pj.id DESC')->fetchAll();
$st = $pdo->query('SELECT COUNT(*) jml,COALESCE(SUM(total_harga),0) pendapatan FROM penjualan')->fetch();
function rp($n)
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}
include __DIR__ . '/dashboard.php';
?>