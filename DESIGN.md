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

**ENERGY 1 / RHYTHM 1 / MOTION 1**

- ENERGY 1: Layout tenang. Tidak ada hero besar, tidak ada CTA raksasa. Energi datang
  dari hierarki tipografi dan baris data, bukan dari ukuran.
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
| Tinta (teks, ikon) | `#1a1a19` | `#f5f5f4` | Teks hampir hitam, bukan abu-abu, supaya isi tabel terbaca |
| Permukaan | `#ffffff` | `#191918` | Panel putih polos tanpa gradien |
| Latar halaman | `#f4f4f2` | `#0f0f0e` | Memisahkan panel dari latar lewat warna, bukan bayangan |
| Garis | `#d7d7d3` | `#33332f` | Garis rambut 1px sebagai pemisah, bukan drop shadow |
| Inti (hijau) | `#0d5c48` | `#4ade9f` | Hijau kartu katalog. Aksi utama, status tersedia, link |
| Aksen (amber) | `#a15c00` | `#f0b429` | Hanya untuk perhatian: denda, jatuh tempo dekat. Amber terang hanya untuk garis dan titik; teks amber memakai versi gelap agar kontras AA |
| Merah status | `#b3261e` | `#f87171` | Hanya untuk destruktif dan keterlambatan, bukan untuk navigasi |

Maksimal dua warna inti (hijau, merah) plus satu aksen (amber) plus netral. Tidak ada
warna keempat, tidak ada gradien di mana pun. Pada sistem data, gradien tidak
menyampaikan informasi apa pun. Tidak ada glow, dan tidak ada blur di lebih dari satu
elemen.

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

2px untuk tombol, input, dan tag. 0 untuk panel dan tabel. Tidak ada elemen pill.
Radius bukan alat hierarki di produk ini; jarak dan garis yang melakukan pekerjaan itu.

## Pola Identitas

Empat pola yang diulang di seluruh produk:

1. **Garis rambut horizontal, bukan kartu berbayang.** Baris dipisahkan 1px
   `#d7d7d3`. Kepadatan itu sendiri yang tampilannya.
2. **Angka tabular rata kanan.** Semua kolom numerik memakai
   `font-variant-numeric: tabular-nums` supaya angka sejajar vertikal.
3. **Tag status persegi kecil** dengan titik solid di kiri. Hanya untuk status nyata:
   Tersedia, Dipinjam, Terlambat, Menunggu, Selesai. Tidak pernah untuk hiasan.
4. **Bilah layanan di atas**, bukan navbar marketing. Wordmark berupa teks
   "PERPUSTAKAAN" dengan tracking normal, bukan gambar buatan.

## Alasan keputusan lain

| Keputusan | Alasan |
|---|---|
| Tabel, bukan kartu | Data yang dibandingkan berulang lebih cepat dipindai sebagai tabel |
| Header tabel lengket | Petugas membaca 40 baris tanpa kehilangan konteks kolom |
| Tanpa hero di halaman dalam | Setiap halaman adalah alat kerja, bukan halaman pemasar |
| Angka tabular | Pembacaan angka demanding presisi, bukan estetika |
| Filter menempel di atas tabel | Pencarian katalog terjadi lewat filter, bukan dengan menggulir |
| Struk peminjaman bisa dicetak | Petugas meminta tanda tangan secara fisik di kertas |
| Tanpa statistik di halaman publik | Angka harus berasal dari database, bukan angka pemanis |
| Bento grid dan tiga langkah dihapus | Komposisi marketing tidak sesuai dengan alat operasional |
