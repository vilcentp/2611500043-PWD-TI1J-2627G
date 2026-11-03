<?php
declare(strict_types=1);
session_start();

/*
 * Demo mandiri fitur Peminjaman Fasilitas Kampus.
 * Saat digabung ke proyek lama, pertahankan section dan handler Kontak
 * yang sudah ada; tambahkan menu/section peminjaman ke template lama.
 */

$daftarFasilitas = [
    'Ruang Kelas',
    'Aula',
    'Laboratorium Komputer',
    'Proyektor',
];

function bersihkanInput(string $nilai): string
{
    // Normalisasi whitespace dan buang spasi di awal/akhir.
    $nilai = trim($nilai);
    $nilai = preg_replace('/\s+/u', ' ', $nilai) ?? $nilai;
    return $nilai;
}

function h(?string $nilai): string
{
    return htmlspecialchars($nilai ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Post-Redirect-Get: proses POST, simpan hasil/pesan ke session, lalu redirect. */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = bersihkanInput((string)($_POST['nama'] ?? ''));
    $fasilitas = bersihkanInput((string)($_POST['fasilitas'] ?? ''));
    $tanggal = bersihkanInput((string)($_POST['tanggal'] ?? ''));
    $durasiRaw = bersihkanInput((string)($_POST['durasi'] ?? ''));
    $keperluan = trim((string)($_POST['keperluan'] ?? ''));
    $keperluan = preg_replace("/\r\n?/", "\n", $keperluan) ?? $keperluan;
    $keperluan = preg_replace('/[ \t]+/u', ' ', $keperluan) ?? $keperluan;

    $errors = [];

    if ($nama === '') {
        $errors[] = 'Nama penanggung jawab wajib diisi.';
    } elseif (mb_strlen($nama) > 100) {
        $errors[] = 'Nama maksimal 100 karakter.';
    }

    if (!in_array($fasilitas, $daftarFasilitas, true)) {
        $errors[] = 'Pilih jenis fasilitas yang tersedia.';
    }

    $tanggalObj = DateTimeImmutable::createFromFormat('!Y-m-d', $tanggal);
    $tanggalValid = $tanggalObj && $tanggalObj->format('Y-m-d') === $tanggal;
    if (!$tanggalValid) {
        $errors[] = 'Tanggal peminjaman tidak valid.';
    } elseif ($tanggal < date('Y-m-d')) {
        $errors[] = 'Tanggal peminjaman tidak boleh sebelum hari ini.';
    }

    $durasi = filter_var($durasiRaw, FILTER_VALIDATE_INT);
    if ($durasi === false || $durasi < 1 || $durasi > 24) {
        $errors[] = 'Durasi peminjaman harus berupa angka 1 sampai 24 jam.';
    }

    if ($keperluan === '') {
        $errors[] = 'Keperluan peminjaman wajib diisi.';
    } elseif (mb_strlen($keperluan) > 1000) {
        $errors[] = 'Keperluan maksimal 1000 karakter.';
    }

    if ($errors) {
        $_SESSION['flash_errors'] = $errors;
        // Simpan nilai yang sudah dinormalisasi untuk mengisi ulang form.
        $_SESSION['old_input'] = [
            'nama' => $nama,
            'fasilitas' => $fasilitas,
            'tanggal' => $tanggal,
            'durasi' => $durasiRaw,
            'keperluan' => $keperluan,
        ];
    } else {
        $_SESSION['flash_success'] = 'Permohonan berhasil divalidasi dan diproses.';
        $_SESSION['hasil_peminjaman'] = [
            'nama' => $nama,
            'fasilitas' => $fasilitas,
            'tanggal' => $tanggal,
            'durasi' => (string)$durasi,
            'keperluan' => $keperluan,
        ];
        unset($_SESSION['old_input']);
    }

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '#peminjaman-fasilitas');
    exit;
}

/* Ambil flash data setelah redirect, lalu hapus agar hanya tampil sekali. */
$errors = $_SESSION['flash_errors'] ?? [];
$success = $_SESSION['flash_success'] ?? '';
$hasil = $_SESSION['hasil_peminjaman'] ?? null;
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['flash_errors'], $_SESSION['flash_success'], $_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Fasilitas Kampus</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
<header class="site-header">
    <nav class="navbar" aria-label="Navigasi utama">
        <a class="logo" href="#home">Kampus</a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="nav-menu">
            Menu
        </button>
        <ul class="nav-menu" id="nav-menu">
            <li><a href="#home">Beranda</a></li>
            <li><a href="#about">Tentang</a></li>
            <li><a href="#peminjaman-fasilitas">Peminjaman Fasilitas</a></li>
            <li><a href="#contact">Kontak</a></li>
        </ul>
    </nav>
</header>

<main>
    <section id="home" class="hero">
        <div class="hero-content">
            <p class="eyebrow">Layanan Kampus</p>
            <h1>Sistem Peminjaman Fasilitas Kampus</h1>
            <p>Ajukan peminjaman fasilitas kampus melalui formulir yang sederhana.</p>
            <a class="button button-primary" href="#peminjaman-fasilitas">Ajukan Peminjaman</a>
        </div>
    </section>

    <section id="about" class="section intro">
        <h2>Tentang Layanan</h2>
        <p>Gunakan formulir ini untuk mengajukan penggunaan ruang kelas, aula,
            laboratorium komputer, atau proyektor. Pengiriman formulir belum
            berarti permohonan telah disetujui.</p>
    </section>

    <!-- Section baru ditempatkan sebelum section#contact. -->
    <section id="peminjaman-fasilitas" class="section section-tinted">
        <div class="form-card">
            <p class="eyebrow">Formulir</p>
            <h2>Permohonan Peminjaman Fasilitas Kampus</h2>
            <p class="muted">Lengkapi semua kolom bertanda wajib.</p>

            <?php if ($success !== ''): ?>
                <div class="notice notice-success" role="status"><?= h($success) ?></div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="notice notice-error" role="alert">
                    <strong>Periksa kembali data berikut:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= h($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="form-peminjaman" method="POST" action="#peminjaman-fasilitas" novalidate>
                <div class="form-group">
                    <label for="nama">Nama Penanggung Jawab <span aria-hidden="true">*</span></label>
                    <input type="text" id="nama" name="nama" maxlength="100"
                           autocomplete="name" required
                           value="<?= h($old['nama'] ?? '') ?>"
                           placeholder="Masukkan nama lengkap">
                    <small class="field-error" id="nama-error"></small>
                </div>

                <div class="form-group">
                    <label for="fasilitas">Jenis Fasilitas <span aria-hidden="true">*</span></label>
                    <select id="fasilitas" name="fasilitas" required>
                        <option value="">-- Pilih fasilitas --</option>
                        <?php foreach ($daftarFasilitas as $item): ?>
                            <option value="<?= h($item) ?>"
                                <?= (($old['fasilitas'] ?? '') === $item) ? 'selected' : '' ?>>
                                <?= h($item) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="field-error" id="fasilitas-error"></small>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Peminjaman <span aria-hidden="true">*</span></label>
                    <input type="date" id="tanggal" name="tanggal"
                           min="<?= h(date('Y-m-d')) ?>" required
                           value="<?= h($old['tanggal'] ?? '') ?>">
                    <small class="field-error" id="tanggal-error"></small>
                </div>

                <div class="form-group">
                    <label for="durasi">Durasi Peminjaman (jam) <span aria-hidden="true">*</span></label>
                    <input type="number" id="durasi" name="durasi" min="1" max="24"
                           step="1" required value="<?= h($old['durasi'] ?? '') ?>"
                           placeholder="Contoh: 3">
                    <small class="field-error" id="durasi-error"></small>
                </div>

                <div class="form-group">
                    <label for="keperluan">Keperluan Peminjaman <span aria-hidden="true">*</span></label>
                    <textarea id="keperluan" name="keperluan" rows="4" maxlength="1000"
                              required placeholder="Jelaskan keperluan peminjaman"><?= h($old['keperluan'] ?? '') ?></textarea>
                    <small class="field-error" id="keperluan-error"></small>
                </div>

                <div class="button-row">
                    <button class="button button-primary" type="submit">Kirim</button>
                    <button class="button button-secondary" type="reset">Batal</button>
                </div>
                <p class="form-note">* Wajib diisi. Data diproses menggunakan PHP dan metode POST.</p>
            </form>
        </div>

        <?php if (is_array($hasil)): ?>
            <div class="result-card" aria-live="polite">
                <h2>Data Permohonan yang Diproses</h2>
                <p class="muted">Data lolos validasi. Persetujuan fasilitas tetap mengikuti prosedur kampus.</p>
                <dl class="result-list">
                    <div><dt>Nama Penanggung Jawab</dt><dd><?= h($hasil['nama']) ?></dd></div>
                    <div><dt>Jenis Fasilitas</dt><dd><?= h($hasil['fasilitas']) ?></dd></div>
                    <div><dt>Tanggal Peminjaman</dt><dd><?= h($hasil['tanggal']) ?></dd></div>
                    <div><dt>Durasi</dt><dd><?= h($hasil['durasi']) ?> jam</dd></div>
                    <div><dt>Keperluan</dt><dd><?= nl2br(h($hasil['keperluan'])) ?></dd></div>
                </dl>
            </div>
        <?php endif; ?>
    </section>

    <!-- Section Kontak tetap tersedia dan tidak digunakan untuk peminjaman. -->
    <section id="contact" class="section contact-section">
        <h2>Kontak</h2>
        <p>Untuk pertanyaan lebih lanjut, silakan hubungi bagian administrasi kampus.</p>
        <!-- Jika proyek lama memiliki formulir/handler Kontak, pertahankan kode aslinya di sini. -->
    </section>
</main>

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> Sistem Informasi Kampus</p>
</footer>
</body>
</html>