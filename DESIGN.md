# DESIGN.md

Arah desain ini berasal dari pilihan pemilik proyek. Setiap keputusan visual di bawah
punya alasan satu kalimat. Ubah isinya hanya bila identitas berubah, bukan karena
eksperimen.

## Identitas

Sistem operasi layanan perpustakaan, bukan brosurzoo. Orang yang memakai sistem ini
adalah petugas yang membaca tabel 40 baris dalam satu layar, dan anggota yang ingin
tahu "buku saya kapan harus kembali". Karena itu prioritas utama adalah **kepadatan
data yang bisa dipindai**, bukan hiburan visual. Produk ini terlihat
seperti alat kerja: formulir dan dokumen, bukan halaman jualan.

## Dial

**ENERGY 2 / RHYTHM 1 / MOTION 1**

- ENERGY 2: Identitas diperkuat dengan palet pastel dan gradien, tapi tidak lewat
  layout. Ukuran, posisi, dan komposisi tetap tenang seperti sebelumnya; energi
  datang dari warna, bukan dari membesarkan elemen. Naiknya satu tingkat dari versi
  1, dan tidak boleh dinaikkan lagi tanpa alasan baru.
- RHYTHM 1: Grid seragam dan dapat diprediksi. Setiap halaman memakai struktur yang
  sama: judul bagian, kontrol filter, tabel. Keseragaman ini pilihan sadar, bukan
  kelalaian. Komposisi yang bervariasi justru memperlambat pembacaan tabel.
- MOTION 1: Hanya state hover, focus, dan transisi pada elemen interaktif (120ms).
  Tidak ada scroll-reveal, parallax, atau animasi masuk. Semua transisi menghormati
  `prefers-reduced-motion`.

## Palet

Netral tidak dihitung sebagai bagian palet.

| Peran | Terang | Gelap | Alasan |
|---|---|---|---|
| Tinta (teks, ikon) | `#1f1e2e` | `#f2f1f8` | Teks hampir hitam dengan hint ungu, supaya isi tabel terbaca dan senada dengan warna utama |
| Permukaan | `#ffffff` | `#1b1a24` | Panel putih polos tanpa gradien |
| Latar halaman | `#f4f3fb` | `#121118` | Memisahkan panel dari latar lewat warna, bukan bayangan |
| Garis | `#e6e4f2` | `#2f2e3d` | Garis rambut 1px sebagai pemisah, bukan drop shadow |
| Inti (periwinkle) | `#4a4b96` | `#a7a7f0` | Aksi utama, status tersedia, link |
| Aksen (apricot) | `#8a5310` | `#e8a94f` | Hanya untuk perhatian: denda, jatuh tempo dekat. Aksen terang hanya untuk garis dan titik; teks aksen memakai versi gelap agar kontras AA |
| Merah status (rose) | `#ad2b3f` | `#f4909b` | Hanya untuk destruktif dan keterlambatan, bukan untuk navigasi |

Maksimal dua warna inti (periwinkle, rose) plus satu aksen (apricot) plus netral.
Tidak ada warna keempat. Hijau katalog diganti periwinkle pada 2026-09-28, jadi
nama token `--inti`, `--aksen`, dan `--bahaya` sengaja dibiarkan agar seluruh view
yang sudah ada tidak perlu disentuh.

### Lapis warna: tinta dan isian

Setiap warna punya dua lapis yang tidak boleh dicampur:

- **Lapis tinta** (`--inti`, `--aksen`, `--bahaya`): gelap di mode terang, terang di
  mode gelap. Dipakai sebagai teks, garis, dan titik.
- **Lapis isian** (`--pastel-*`): selalu lembut, dipakai sebagai latar gradien. Teks
  di atasnya memakai `--inti-kunci`.

Kalau kedua lapis digabung menjadi satu token, salah satunya pasti gagal kontras.
Seluruh 44 pasangan warna teks dan latar sudah diuji kontrasnya di kedua mode.

### Gradien

Gradien diizinkan, tapi hanya di tiga tempat, dan alasannya berbeda-beda:

1. **Permukaan brand**: tombol utama, item menu yang aktif, mark logo, kartu
   anggota, dan kepala kartu auth. Gunanya memisahkan lapisan identitas dari
   lapisan data.
2. **Kepala panel** (`.kepala-kartu`): gradien sangat tipis yang menandai bahwa isi
   panel berikutnya adalah data.
3. **Latar halaman**: tiga genangan pastel ber-alpha rendah. Tujuannya kedalaman
   pada bidang kosong.

Semua gradien berhenti pada sudut 135 derajat agar arah cahaya konsisten, dan
disusun dari token `--pastel-*` sehingga mode gelap hanya perlu menimpa satu lapis.

Yang tetap datar dan tidak boleh diberi gradien: isi tabel, baris tabel, kepala
tabel, isian formulir, dan angka di kartu metrik. Di sinilah angka dibaca, dan
warna bergradasi di belakang angka menurunkan keterbacaan. Ini alasan yang
membedakan versi ini dari versi sebelumnya yang melarang gradien sama sekali.


## Tipografi

**IBM Plex Sans** untuk teks, **IBM Plex Mono** untuk identifier.

Alasan: tugas utama pengguna adalah membandingkan angka dan kode, yaitu stok, tanggal
kembali, nomor anggota, dan ISBN. IBM Plex Sans punya figur tabular yang sejajar serta
karakter adminsitratif yang cocok untuk produk layanan, berbeda dari sans-serif
generik. IBM Plex Mono dipakai hanya untuk ISBN, nomor anggota, dan kode peminjaman
supaya identifier mudah dibandingkan antarbaris. Ini bukan gaya terminal, hanya
kejelasan tabular.

Skala tipografi: 11px untuk label dengan `uppercase`, 12px untuk metadata, 14px untuk
isi tabel, 16px untuk body, dan hanya judul halaman yang naik ke 24px dengan weight
600. Tidak ada judul besar di tengah halaman. Tidak ada uppercase dengan letter
spacing lebar; label kolom memakai `uppercase` 11px karena label-column adalah pengarah
pembacaan tabel, bukan gaya.

## Radius

Token radius di `app.css` yang berlaku: 6px untuk tombol, input, dan tag; 12px untuk
kartu; 16px untuk panel besar dan kartu auth. Sudut membulat dipakai sebagai alat
hierarki, jadi tidak semua elemen memakai nilai yang sama. Tidak ada elemen pill
penuh, kecuali disk kecil penanda metrik dan titik status.

## Pola Identitas

Lima pola yang diulang di seluruh produk:

1. **Garis rambut horizontal, bukan kartu berbayang.** Baris dipisahkan 1px
   `#e6e4f2`. Kepadatan itu sendiri yang tampilannya.
2. **Angka tabular rata kanan.** Semua kolom numerik memakai
   `font-variant-numeric: tabular-nums` supaya angka sejajar vertikal.
3. **Tag status kecil** dengan titik solid di kiri. Hanya untuk status nyata:
   Tersedia, Dipinjam, Terlambat, Menunggu, Selesai. Tidak pernah untuk hiasan.
4. **Bilah layanan di atas**, bukan navbar marketing.
5. **Mark buku terbuka, digambar tangan.** Wordmark tetap teks, tapi mark-nya
   adalah buku terbuka dengan satu garis halaman di tiap halaman, digambar dengan
   bahasa garis yang sama seperti `x-icon` supaya logo dan ikon tidak terlihat
   berasal dari dua sistem gambar berbeda. Mark ini diulang di sidebar, di kepala
   kartu auth, di kartu anggota, dan sebagai tanda air besar di halaman tamu.
   Alasannya: produk ini tentang buku, jadi produknya harus memakai bentuk buku,
   bukan glyph kotak umum.

## Batas yang tidak boleh dilewati

- **Gradien tidak menyentuh isi tabel, baris tabel, kepala tabel, isian formulir,
  atau angka metrik.** Di situlah angka dibaca.
- **Blur maksimal pada dua elemen**: topbar dan lapisan laci di layar sempit.
  Tidak lebih, karena blur di banyak tempat sekaligus membuat tabel terasa berkabut.
- **Tidak ada glow** di mana pun, termasuk di sekitar tombol dan kartu.
- Tidak ada statistik, testimoni, atau klaim yang tidak berasal dari database.

## Alasan keputusan lain

| Keputusan | Alasan |
|---|---|
| Tabel, bukan kartu | Data yang dibandingkan berulang lebih cepat dipindai sebagai tabel |
| Header tabel lengket | Petugas membaca 40 baris tanpa kehilangan konteks kolom |
| Tanpa hero di halaman dalam | Setiap halaman adalah alat kerja, bukan halaman pemasar |
| Angka tabular | Pembacaan angka menuntut presisi, bukan estetika |
| Filter menempel di atas tabel | Pencarian katalog terjadi lewat filter, bukan dengan menggulir |
| Struk peminjaman bisa dicetak | Petugas meminta tanda tangan secara fisik di kertas |
| Tanpa statistik di halaman publik | Angka harus berasal dari database, bukan angka pemanis |
| Bento grid dan tiga langkah dihapus | Komposisi marketing tidak sesuai dengan alat operasional |
| Gradien hanya di permukaan brand | Owner meminta tema pastel pada 2026-09-28. Batas yang ditulis di atas menjaga tabel tetap terbaca, sehingga keputusan owner tidak merusak bagian yang justru alasan produk ini ada |
