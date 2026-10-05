# Pertemuan 3 - Formulir HTML dan CSS Dasar

## Baseline
- Menggunakan struktur dari hasil P2 yang sudah kita pelajari sebagai dasar pengembangan P3.
- Menyalin `index.html` dan `img/foto-profil.jpg` ke `pertemuan-03/`.

## Implementasi Formulir
- Elemen form yang digunakan: [form, div, label, input, select dan option, textarea, button, br]
- Tipe input yang digunakan: [saya menggunakan text = mahasiswa, email, number = semester, date = tanggal kunjungan, radio = jenis pesan: pertanyaan atau saran, checkbox = topik yang diminati yaitu HTML/CSS].
- Atribut validasi yang digunakan: [Atribut validasi yang digunakan: required, minlength, maxlength, min, dan max. Tipe email, number, dan date juga membantu memeriksa format isian]

## Pengujian GET dan POST
- Hasil pengujian GET: Form mengirim data melalui query string pada URL dan memuat ulang index.html; hanya field yang memiliki atribut name ikut dikirim.
- Contoh URL encoding yang ditemukan: Spasi pada nama atau pesan dikirim sebagai +, sedangkan karakter khusus seperti `@` pada email dikirim sebagai %40 (contoh: `nama=Vilcent+Pitersen&email=Vilcent%40email.com`)
- Hasil pengujian POST: Belum dapat diuji melalui formulir ini karena atribut form saat ini adalah `method="get"`. Untuk menguji POST, ubah menjadi `method="post"` dan gunakan endpoint yang menerima POST.

## CSS Dasar
- Selector elemen: [h2, h3, p, ol]
- Selector class: [form group dan input form]
- Selector ID: [about dan contact]
- Properti CSS dasar yang digunakan: [background-color, color, font-family, font-size, border, padding, dan margin]
## Pengujian dan Perbaikan
- Galat yang ditemukan: [ukuran foto yang besar]
- Penyebab galat: [Ukuran asli foto 1254 × 1254 piksel, jadi terlihat terlalu besar sebelum ukurannya diatur di HTML]
- Perbaikan yang dilakukan: [melakukan perbaikan ukuran foto dari 1254 x 1254 menjadi width="175" height="175"]
- Hasil pengujian ulang: [foto terlihat rapi dan pas untuk di lihat]

## GitHub Pages
URL: [https://vilcentp.github.io/2611500043-PWD-TI1J-2627G/]
