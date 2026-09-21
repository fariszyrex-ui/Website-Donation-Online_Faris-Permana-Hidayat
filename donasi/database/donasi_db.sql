-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Sep 2026 pada 00.46
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `donasi_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `username`, `password`) VALUES
(12345, 'Admin', 'Admin@gmail.com', '$2y$10$gLELcjPwGMUz248Qh3nnXO7PwWEQmvd8RHAzrPZVxdhz7858Pg.Du');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berita`
--

CREATE TABLE `berita` (
  `id_berita` int(10) UNSIGNED NOT NULL,
  `id_campaign` int(10) UNSIGNED NOT NULL,
  `id_admin` int(10) UNSIGNED NOT NULL,
  `judul` varchar(150) NOT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `berita`
--

INSERT INTO `berita` (`id_berita`, `id_campaign`, `id_admin`, `judul`, `isi`, `gambar`, `tanggal`) VALUES
(5, 2, 12345, 'Penyaluran Tahap 1: Masker Respirator dan Oksigen Portabel Tiba di Lokasi', 'Halo #OrangBaik,   Terima kasih atas kepedulianmu! Berkat donasi yang terkumpul hingga tahap pertama ini sebesar Rp60.000.000, tim relawan di lapangan telah berhasil menyalurkan:2.000 masker filtrasi tinggi (N95) untuk warga Desa Terdampak.50 unit tabung oksigen portabel dan vitamin untuk posko kesehatan.Bantuan bahan bakar (BBM) serta kacamata pelindung untuk relawan pemadam garis depan.Kondisi pemadaman di lahan gambut masih memakan waktu karena api di bawah tanah kerap menyala kembali. Mari terus dukung perjuangan mereka dengan membagikan halaman galang dana ini!', '6aafff1751c8b.jpg', '2026-09-20'),
(6, 9, 12345, 'Tim relawan/medis hewan sedang merawat orangutan atau satwa yang berhasil dievakuasi dari area hutan yang terbakar', 'Halo #OrangBaik,\r\n\r\nTerima kasih banyak atas dukungan luar biasa yang sudah kamu berikan! Berkat donasi terkumpul sebesar Rp85.000.000 pada tahap ini, bantuan telah disalurkan langsung ke lokasi:\r\n\r\nDapur Umum Darurat: Berhasil didirikan dan membagikan 300 porsi makanan bergizi serta suplemen harian untuk tim pemadam di garis depan.\r\n\r\nAlat Pemadam Tambahan: 2 unit mesin pompa air portabel dan selang pemadam sepanjang 200 meter telah diserahterimakan kepada relawan lokal untuk menembus titik api gambut yang sulit dijangkau.\r\n\r\nPertolongan Satwa: Tim medis hewan di lapangan telah menerima paket obat luka bakar dan vitamin untuk penanganan pertama satwa yang terisolasi.\r\n\r\nPerjuangan masih panjang. Mari terus bagikan halaman galang dana ini agar makin banyak bantuan yang tersalurkan!', '6ab0004522adf.jpg', '2026-09-20'),
(7, 10, 12345, 'Penyaluran Bantuan Sembako dan Air Bersih di Posko Pengungsian Desa Terisolasi  Isi Berita:', 'Terima kasih atas solidaritas dan kebaikan hati yang telah kamu bagikan. Berkat kepedulianmu, donasi tahap pertama sebesar Rp65.000.000 telah berhasil diwujudkan menjadi bantuan logistik darurat yang disalurkan langsung ke lokasi pengungsian.\r\n\r\nPerjalanan menuju lokasi tidaklah mudah. Akses jalan utama yang tertutup material lumpur dan tanah longsor membuat tim relawan harus menempuh jalur alternatif menggunakan sepeda motor dan berjalan kaki sambil memikul barang bantuan. Namun, lelah tim terbayar tuntas saat melihat antusiasme dan rasa syukur warga ketika bantuan tiba.\r\n\r\nPada tahap ini, bantuan yang telah disalurkan meliputi:\r\n\r\n1.150 Paket Sembako: Berisi beras, minyak goreng, mi instan, sarden, dan biskuit.\r\n\r\n2.Truk Tangki Air Bersih: Menyuplai 10.000 liter air bersih untuk kebutuhan minum dan sanitasi di posko utama.\r\n\r\n3.Perlengkapan Bayi & Balita: 50 paket MPASI, susu formula, dan popok sekali pakai.\r\n\r\n4.Kit Hygiene: Sabun, pembalut, sarung, dan selimut hangat untuk warga pengungsi.\r\n\r\nIbu Maryam (42 tahun), salah satu warga pengungsi, menyampaikan rasa terima kasihnya. Rumah dan seluruh barang-barangnya terendam lumpur, dan bantuan pakaian kering serta makanan siap saji ini sangat berarti bagi keluarga dan anak-anaknya.\r\n\r\nSaat ini, kebutuhan akan air bersih dan obat-obatan masih sangat tinggi karena sumber air warga tercemar total oleh lumpur banjir. Mari rapatkan barisan dan terus bagikan campaign ini agar lebih banyak warga terdampak yang bisa kita bantu!', '6ab001a1633d2.jpeg', '2026-09-20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `campaign`
--

CREATE TABLE `campaign` (
  `id_campaign` int(10) UNSIGNED NOT NULL,
  `id_admin` int(10) UNSIGNED NOT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `target_donasi` decimal(15,2) NOT NULL,
  `dana_terkumpul` decimal(15,2) NOT NULL DEFAULT 0.00,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_batas` date NOT NULL,
  `status` enum('Aktif','Selesai','Ditutup') NOT NULL DEFAULT 'Aktif'
) ;

--
-- Dumping data untuk tabel `campaign`
--

INSERT INTO `campaign` (`id_campaign`, `id_admin`, `judul`, `deskripsi`, `target_donasi`, `dana_terkumpul`, `gambar`, `tanggal_mulai`, `tanggal_batas`, `status`) VALUES
(2, 12345, 'Kebakaran hutan Kalimantan', 'Kebakaran hutan dan lahan (karhutla) di Kalimantan kembali meluas secara masif pada Agustus hingga September 2026 akibat kombinasi cuaca ekstrem dan aktivitas manusia.', 100000000.00, 21000000.00, '6aa5e2c355bdf.jpg', '2026-09-12', '2026-09-13', 'Ditutup'),
(3, 12345, 'Topup ml buat beli wdp', 'Al kisa ada seorang anak yang hobi judol dan sering bolos sekolah ingin topup ml buat beli wdp namun sayangnya uangnya habis dan diapun menangis nyaris depresi berat,oleh sebab itu ayok banty berdonasi agar anak itu tidak masuk rsj', 30000.00, 35000.00, '6aa5f038d0e9f.jpg', '2026-09-12', '2026-10-02', 'Ditutup'),
(4, 12345, 'tes', 'tes', 1000.00, 1000.00, '6aab1384b55db.jpg', '2026-09-16', '2026-09-24', 'Ditutup'),
(8, 12345, 'Sambung Harapan: Jembatan Mimpi Anak-Anak Pelosok Desa Sukamaju', 'Setiap pagi, anak-anak di Desa Sukamaju harus mempertaruhkan nyawa menyeberangi derasnya sungai menggunakan rakit bambu yang sudah lapuk demi bisa sampai ke sekolah. Ketika debit air naik di musim hujan, mereka terpaksa meliburkan diri karena akses jalan terputus total.   Melalui gerakan #SambungHarapan, kami mengajak kamu untuk bergotong royong membangun Jembatan Gantung Layak sepanjang 25 meter. Jembatan ini tidak hanya menghubungkan tempat tinggal mereka dengan sekolah, tetapi juga menjadi jalur utama warga untuk membawa hasil tani dan menjangkau akses kesehatan.', 150000000.00, 50000000.00, '6aaffe73e57d5.jpg', '2026-09-20', '2026-10-28', 'Aktif'),
(9, 12345, 'Darurat Karhutla: Selamatkan Satwa Kalimantan dan Bantu Tim Pemadam', 'Kebakaran hutan dan lahan (karhutla) di Kalimantan kini tidak hanya mengancam kesehatan masyarakat, tetapi juga merusak habitat asli puluhan satwa dilindungi seperti orangutan, bekantan, dan fauna lokal lainnya. Dalam kondisi panik, banyak satwa melarikan diri ke area pemukiman dengan kondisi luka bakar atau dehidrasi parah.\r\n\r\nDi saat yang sama, ratusan relawan dan tim pemadam bekerja 24 jam menembus bara api lahan gambut. Keterbatasan selang air, mesin pompa, hingga makanan bergizi membuat kondisi fisik para pejuang garis depan ini makin drop.\r\n\r\nMelalui gerakan #SelamatkanKalimantan, donasi yang kamu berikan akan disalurkan untuk:\r\n\r\nEvakuasi dan perawatan medis darurat bagi satwa liar terdampak.\r\n\r\nPengadaan mesin pompa air portabel dan selang pemadam jarak jauh.\r\n\r\nOperational Dapur Umum Darurat untuk memasok makanan dan vitamin bagi para relawan.\r\n\r\nPenggunaan Dana: Obat-obatan medis hewan & kandang evakuasi, 2 unit mesin pompa air + selang 200m, serta operasional dapur umum selama 14 hari.', 250000000.00, 150100000.00, '6aaffffeaf9a3.jpeg', '2026-09-20', '2026-09-30', 'Aktif'),
(10, 12345, 'Duka Luwu: Bantu Pangan dan Pakaian Bersih untuk Korban Banjir Bandang', 'Hujan deras dengan intensitas tinggi memicu banjir bandang dan tanah longsor yang menerjang permukiman warga. Air bercampur lumpur setinggi dua meter meluap secara tiba-tiba, merendam puluhan rumah, merusak fasilitas umum, dan memutuskan jalur akses antar-desa.\r\n\r\nRatusan keluarga kini terpaksa mengungsi ke tempat yang lebih tinggi dengan pakaian seadanya. Mereka kehilangan harta benda, pasokan makanan, serta akses air bersih. Banyak balita dan lansia yang mulai terserang penyakit kulit, demam, dan diare di pengungsian.\r\n\r\nMelalui gerakan #PulihkanLuwu, kami mengundang kamu untuk menyalurkan bantuan darurat guna memenuhi kebutuhan dasar para pengungsi selama masa tanggap darurat.\r\n\r\nPenggunaan Dana: Paket sembako darurat, air bersih, pakaian bersih, selimut, MPASI untuk balita, serta perlengkapan sanitasi.', 150000000.00, 130025000.00, '6ab0014a13ef6.jpeg', '2026-09-20', '2026-09-22', 'Aktif'),
(11, 12345, 'Berbagi Kebahagiaan untuk Anak Yatim', 'Mari bersama memberikan dukungan dan kebahagiaan bagi anak-anak yatim yang membutuhkan. Donasi yang terkumpul akan digunakan untuk membantu memenuhi kebutuhan pendidikan, perlengkapan sekolah, makanan, serta kebutuhan sehari-hari mereka. Setiap bantuan, sekecil apa pun, dapat menjadi bentuk kepedulian dan dukungan bagi masa depan mereka.', 10000000.00, 0.00, '6ab07c809dbde.jpg', '2026-09-21', '2026-10-28', 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `donasi`
--

CREATE TABLE `donasi` (
  `id_donasi` int(10) UNSIGNED NOT NULL,
  `id_donatur` int(10) UNSIGNED NOT NULL,
  `id_campaign` int(10) UNSIGNED NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `metode_pembayaran` enum('QRIS','BCA','DANA') NOT NULL,
  `status` enum('Menunggu','Berhasil','Kadaluarsa') NOT NULL DEFAULT 'Menunggu',
  `tanggal_donasi` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data untuk tabel `donasi`
--

INSERT INTO `donasi` (`id_donasi`, `id_donatur`, `id_campaign`, `nominal`, `metode_pembayaran`, `status`, `tanggal_donasi`) VALUES
(3, 2, 2, 1000000.00, 'QRIS', 'Berhasil', '2026-09-12 17:04:14'),
(4, 1, 2, 20000000.00, 'BCA', 'Berhasil', '2026-09-12 17:32:12'),
(5, 2, 3, 35000.00, 'QRIS', 'Berhasil', '2026-09-12 17:37:47'),
(6, 2, 4, 1000.00, 'QRIS', 'Berhasil', '2026-09-16 15:10:41'),
(13, 2, 10, 25000.00, 'QRIS', 'Berhasil', '2026-09-20 09:15:46'),
(14, 2, 10, 5000000.00, 'BCA', 'Berhasil', '2026-09-20 09:18:04'),
(15, 1, 9, 150000000.00, 'BCA', 'Berhasil', '2026-09-20 16:56:51'),
(16, 5, 10, 125000000.00, 'BCA', 'Berhasil', '2026-09-20 17:11:42'),
(17, 6, 8, 50000000.00, 'QRIS', 'Berhasil', '2026-09-20 17:29:28'),
(18, 2, 9, 100000.00, 'QRIS', 'Berhasil', '2026-09-20 17:32:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `donatur`
--

CREATE TABLE `donatur` (
  `id_donatur` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `donatur`
--

INSERT INTO `donatur` (`id_donatur`, `nama`, `email`, `password`, `no_hp`, `alamat`) VALUES
(1, 'Permana', 'Permana@gmail.com', '$2y$10$7YVrgETJ.JbY4dH3kf0Pp.HaoNWeLJhdaFUxPrJi9VOWIAgbIwLIS', '081287556659', 'Bandung'),
(2, 'Zuzu', 'Zuzu@gmail.com', '$2y$10$EcUsT4j0vlplsi.ufRQQi.v68PWoZeSBWc4.yscmN8uJE62k60NWe', '081287556776', 'Ngawi selatan'),
(3, 'jajang', 'jaja@gmail.com', '$2y$10$mCydGOjNatM1Xg75tBARuepNcnzj4IWHdWrWzKS6DNBAkQ6yz08kq', '', ''),
(4, 'jajang', 'jajang@gmail.com', '$2y$10$s3.lcY13Hl8hZrAIvCuHE.p/AC.T0MhhLqUt5mS3Y2Wmn43poABxq', '', ''),
(5, 'Asa', 'asa@gmail.com', '$2y$10$w7vwg8Dc7QwUt7mBDpWPw.aCnO7H5eAN49fjvUxnytJypAosQH6XC', '', ''),
(6, 'AMbaleon', 'Amba@gmail.com', '$2y$10$KCRkiELV9B9J6FD05.oIy.1UMAzsPZHdFHFeWN.g1.2.QtDTD9Znq', '098766789999', 'Ngawi selatan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id_berita`),
  ADD KEY `fk_berita_admin` (`id_admin`),
  ADD KEY `fk_berita_campaign` (`id_campaign`);

--
-- Indeks untuk tabel `campaign`
--
ALTER TABLE `campaign`
  ADD PRIMARY KEY (`id_campaign`),
  ADD KEY `fk_campaign_admin` (`id_admin`);

--
-- Indeks untuk tabel `donasi`
--
ALTER TABLE `donasi`
  ADD PRIMARY KEY (`id_donasi`),
  ADD KEY `fk_donasi_donatur` (`id_donatur`),
  ADD KEY `fk_donasi_campaign` (`id_campaign`);

--
-- Indeks untuk tabel `donatur`
--
ALTER TABLE `donatur`
  ADD PRIMARY KEY (`id_donatur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12347;

--
-- AUTO_INCREMENT untuk tabel `berita`
--
ALTER TABLE `berita`
  MODIFY `id_berita` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `campaign`
--
ALTER TABLE `campaign`
  MODIFY `id_campaign` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `donasi`
--
ALTER TABLE `donasi`
  MODIFY `id_donasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `donatur`
--
ALTER TABLE `donatur`
  MODIFY `id_donatur` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD CONSTRAINT `fk_berita_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_berita_campaign` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id_campaign`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `campaign`
--
ALTER TABLE `campaign`
  ADD CONSTRAINT `fk_campaign_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `donasi`
--
ALTER TABLE `donasi`
  ADD CONSTRAINT `fk_donasi_campaign` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id_campaign`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donasi_donatur` FOREIGN KEY (`id_donatur`) REFERENCES `donatur` (`id_donatur`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
