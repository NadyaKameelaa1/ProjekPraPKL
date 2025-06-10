<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email_user'];
$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';

// Ambil parameter pencarian
$lokasi = isset($_GET['lokasi']) ? mysqli_real_escape_string($koneksi, $_GET['lokasi']) : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
$dewasa = isset($_GET['dewasa']) ? intval($_GET['dewasa']) : 1;
$anak = isset($_GET['anak']) ? intval($_GET['anak']) : 0;
$kamar = isset($_GET['kamar']) ? intval($_GET['kamar']) : 1;

$id_hotel = isset($_GET['id_hotel']) ? intval($_GET['id_hotel']) : 0;
$id_kamar = isset($_GET['id_kamar']) ? intval($_GET['id_kamar']) : 0;

// Query untuk hotel
$query = mysqli_query($koneksi, "SELECT hotels.*, MIN(kamar.harga_kamar) AS harga_terendah 
    FROM hotels
    LEFT JOIN kamar ON hotels.id_hotel = kamar.id_hotel
    WHERE hotels.id_hotel = $id_hotel
    GROUP BY hotels.id_hotel");

$hotels = mysqli_fetch_assoc($query);

$query_fasilitas_hotel = mysqli_query($koneksi, "SELECT fasilitas_hotel FROM hotels WHERE id_hotel = '$id_hotel'");

// Query untuk kamar dengan gambar
$query_kamar = mysqli_query($koneksi, "SELECT
    k.*,
    kg.gambarA, kg.gambarB, kg.gambarC, kg.gambarD, kg.gambarE
    FROM kamar k
    LEFT JOIN kamar_gambar kg ON k.id_kamar = kg.id_kamar
    WHERE k.id_hotel = $id_hotel");



$hotel_descriptions = [
    2042 => "Berlokasi strategis tepat di tepi Pantai Bandengan yang populer, D’SEASON Premiere menjadi hotel bintang 3 terbaik untuk Anda yang ingin berlibur dengan menikmati pemandangan  laut yang begitu memesona berbalut suasana Jawa yang kental. Akomodasi ini menjadi opsi paling tepat bagi penikmat pantai maupun penyuka alam yang ingin sejenak melepaskan diri dari penatnya kesibukan. D'SEASON Premiere menawarkan kemudahan akses ke berbagai tempat wisata menarik di Jepara, seperti Pantai Bandengan, Pantai Kartini, Benteng Portugis, dan Museum Kartini. Akomodasi ini juga sangat mudah dijangkau baik dengan kendaraan pribadi maupun moda transportasi umum. Tak hanya itu, fasilitas yang ditawarkan juga memadai, memenuhi kebutuhan tamu yang menginap untuk kebutuhan bisnis maupun berlibur. Tipe kamarnya juga cukup variatif, memberikan pilihan yang lebih beragam untuk tamu sehingga bisa mendapatkan kamar yang sesuai dengan kebutuhan. D'SEASON Premiere Jepara memanjakan tamu yang menginap dengan menyediakan fasilitas yang lengkap, baik di dalam maupun di luar kamar. Beberapa fasilitas di kamar antara lain AC, televisi layar datar dengan saluran premium, air mineral gratis, kulkas, meja, serta mesin pembuat kopi dan teh. Beberapa tipe kamar juga dilengkapi dengan kamar mandi pribadi dan bathtub serta balkon yang menawarkan pemandangan pantai yang luas dan sangat menawan. Sementar itu, di luar kamar, D’SEASON Premiere menawarkan fasilitas penunjang berupa kolam renang, area bermain anak, pusat kebugaran, spa, restoran, dan bar. Bagi tamu yang memiliki kepentingan bisnis atau acara khusus, tersedia pula area fungsional dengan alat penunjang yang lengkap dan modern.Tak ketinggalan, guna memenuhi kebutuhan para tamu selama menginap, seperti penatu dan penitipan bagasi, Hotel D’SEASON Premiere juga didukung dengan layanan resepsionis 24 jam. Hotel D'SEASON Premiere beralamat di Jalan Pariwisata No.9, Bandengan, Jepara, Jawa Tengah. Aksesnya cukup mudah untuk dijangkau para tamu yang menggunakan kendaraan pribadi maupun moda transportasi umum. Tamu yang menggunakan kendaraan pribadi dapat mengakses Jalan Jepara-Bangsri, lalu berbelok ke Jalan Raya Tirta Samudra hingga sampai ke akomodasi. Tamu bisa memarkir kendaraan di area yang tersedia dengan jaminan keamanan 24 jam. Sementara bagi tamu yang menggunakan bus, Terminal Jepara akan menjadi destinasi pemberhentian terakhir. Lalu, tamu bisa melanjutkan perjalanan dengan moda transportasi umum lainnya.",
    
    2027 => "Berlokasi di Semarang, 2 km dari Stasiun Semarang Tawang, Gumaya Tower Hotel menawarkan spa & pusat kebugaran dan pemandangan kota. Fasilitas yang tersedia di akomodasi ini adalah restoran, layanan kamar, resepsionis 24 jam, dan WiFi gratis di seluruh area akomodasi.Brown Canyon lokasinya sejauh 16 km, dan Tugu Muda berjarak 2 km dari hotel. Hotel menyediakan kamar ber-AC dengan meja kerja, mesin kopi, kulkas, brankas, TV layar datar, dan kamar mandi pribadi dengan bidet. Di Gumaya Tower Hotel, setiap kamar memiliki sprei dan handuk. Sarapan hariannya menawarkan pilihan prasmanan, ala Amerika, atau Asia. Gumaya Tower Hotel menawarkan akomodasi bintang 5 dengan sauna dan kolam renang outdoor sepanjang tahun.Gumaya Tower Hotel Semarang terletak di Jalan Gajahmada Nomor 59-61, Kembangsari, Kecamatan Semarang Tengah, Kota Semarang, Jawa Tengah. Berada di pusat kota, hotel ini mudah diakses dengan transportasi umum maupun kendaraan pribadi. Selain itu, para tamu dapat dengan mudah mengakses berbagai destinasi utama di Semarang. Hotel ini menyediakan layanan sewa mobil dan pusat layanan taksi untuk memudahkan Anda menjelajahi kota. Berbagai tipe kamar mewah juga tersedia di Gumaya Tower Hotel Semarang.",
    
    2032 => "Ibis Styles Semarang adalah pilihan akomodasi yang tepat bagi Anda yang ingin menginap di Kota Semarang. Lokasinya yang berada di pusat kota, membuat hotel ini mudah dijangkau dan memberikan kemudahan akses ke berbagai tempat penting di Kota Semarang. Cocok bagi Anda yang ingin berlibur maupun mengadakan perjalanan bisnis di Kota Semarang. Dengan lokasi yang strategis di pusat kota Semarang, Ibis Styles Semarang sangat cocok untuk akomodasi berlibur maupun perjalanan bisnis. Ada banyak tempat-tempat menarik dan penting di Semarang yang lokasinya tidak jauh dari hotel. Sehingga Anda dapat dengan mudah mengakses berbagai tempat wisata, pusat perbelanjaan, restoran, dan fasilitas umum lainnya. Selain itu hotel ini juga memberikan kenyamanan maksimal karena setiap kamarnya sudah dilengkapi dengan fasilitas modern yang akan memenuhi kebutuhan Anda selama menginap.",

    2045 => "Laras Asri Resort & Spa terletak di JL.Jendral Sudirman 335, Salatiga, Central Java, tepatnya di kawasan Argomulyo, Salatiga, Jawa Tengah, Indonesia. Lokasi ini berjarak sekitar 3 km dari pusat kota Salatiga, menjadikannya tempat yang ideal bagi Anda yang ingin menikmati ketenangan namun tetap dekat dengan berbagai fasilitas kota. Beberapa landmark populer yang dapat Anda kunjungi di sekitar area ini termasuk Taman Kota Salatiga dan Alun-Alun Pancasila, yang menawarkan pemandangan indah dan suasana yang menyenangkan. Laras Asri Resort & Spa adalah pilihan yang tepat untuk Anda yang mencari tempat menginap dengan suasana tenang dan nyaman. Resort ini menawarkan lingkungan yang asri dan jauh dari kebisingan kota, cocok untuk Anda yang ingin beristirahat dari rutinitas sehari-hari. Selain itu, pelayanan yang ramah dan profesional menjamin pengalaman menginap yang menyenangkan. Dengan berbagai pilihan aktivitas rekreasi yang dapat dinikmati di sekitar resort, seperti berjalan-jalan di taman yang indah atau menikmati pemandangan alam sekitar, Anda akan merasa segar kembali setelah menginap di sini. Laras Asri Resort & Spa menawarkan berbagai fasilitas yang dapat memenuhi kebutuhan Anda selama menginap. Fasilitas yang tersedia termasuk AC di setiap kamar, restoran dengan menu yang beragam, serta kolam renang yang dapat Anda gunakan untuk bersantai dan menikmati waktu luang. Selain itu, resort ini juga menyediakan layanan resepsionis 24 jam yang siap membantu Anda kapan saja. Semua fasilitas ini dirancang untuk memberikan kenyamanan dan kemudahan bagi Anda selama menginap di Laras Asri Resort & Spa. Terletak di kawasan strategis, Laras Asri Resort & Spa menawarkan akses mudah ke berbagai tempat menarik di Salatiga. Lokasinya yang berada di JL.Jendral Sudirman memudahkan Anda untuk menjelajahi kota dan sekitarnya. Untuk mencapai resort ini, Anda dapat menggunakan kendaraan pribadi atau transportasi umum yang tersedia. Selain itu, Anda juga dapat memanfaatkan layanan transportasi online yang banyak tersedia di kawasan ini untuk memudahkan perjalanan Anda selama menginap.",
  
    2046 => "Front One Gosyen Hotel Salatiga adalah salah satu hotel terbaik di Salatiga. Lokasinya berada di pusat kota, dekat dengan berbagai fasilitas kota dan berbagai destinasi kuliner. Selain itu, harga menginap di hotel ini cukup terjangkau. Fasilitas dan layanan hospitality yang profesional di sini menjamin kenyamanan setiap tamu yang menginap. Hotel ini menyediakan berbagai fasilitas untuk menunjang kenyamanan istirahat Anda. Di dalam kamar, Anda akan menjumpai double bed atau twin bed, AC, meja, pembuat kopi/teh, TV, dan brankas dalam kamar. Shower, hairdryer, dan toiletries lengkap tersedia dan bisa digunakan secara cuma-cuma. Tersedia connecting room untuk Anda yang memerlukannya. Fasilitas umum yang tersedia meliputi akses Wi-Fi, ruangan fungsional, restoran, dan kolam renang. Hotel ini berlokasi di kawasan strategis di kota Salatiga, tepatnya di daerah Tingkir. Dari pusat kota, Anda hanya perlu menempuh jarak 300 m menuju hotel ini. Dari Terminal Bus Tingkir, Anda perlu menempuh jarak 4,5 km yang dapat ditempuh dengan kendaraan pribadi, kendaraan umum, atau transportasi online.",

    2047 => "Berlokasi di Salatiga, 43 km dari Brown Canyon, Wahid Prime Hotel Salatiga menyediakan akomodasi dengan lounge bersama, parkir pribadi gratis, dan bar. Akomodasi ini menawarkan layanan kamar dan resepsionis 24 jam untuk Anda. Di hotel, setiap kamar memiliki meja kerja, TV layar datar, kamar mandi pribadi, seprai, dan handuk. Semua unit di Wahid Prime Hotel Salatiga memiliki AC dan lemari pakaian. Anda dapat menikmati sarapan prasmanan di Wahid Prime Hotel Salatiga. Anda akan menemukan restoran yang menyajikan masakan Indonesia di hotel. Pilihan hidangan vegetarian juga dapat dipesan. Museum Kereta Api berjarak 19 km dari Wahid Prime Hotel Salatiga. Bandara Bandara Internasional Adisumarmo berjarak sejauh 45 km.",

    2048 => "Lafayette Boutique Hotel adalah hotel bintang lima dengan pelayanan istimewa dan fasilitas mewah di Catur Tunggal, Depok, Yogyakarta. Tak hanya dekat tempat rekreasi seperti Monumen Yogya Kembali atau Plaza Ambarrukmo, hotel ini pun bisa dijangkau dengan mudah dari arah mana saja. Salah satunya dari Terminal Condong Catur yang hanya berjarak 931 meter ke akomodasi. Berbeda dari hotel modern pada umumnya, hotel ini menawarkan suasana yang nyaman dan menyenangkan. Didukung desain arsitektur unik, pelayanan ramah serta ragam fasilitas eksklusif pun dapat Anda peroleh saat menginap di Lafayette Boutique Hotel. Selain itu, hotel tersebut juga berada di lokasi yang bagus. Tidak heran apabila akomodasi dapat diakses dengan cepat serta mudah menggunakan bermacam jenis moda transportasi umum maupun kendaraan pribadi. Bagi Anda yang ingin menikmati kebersamaan yang manis dan romantis bersama pasangan, Lafayette Boutique Hotel ialah opsi terbaik. Anda akan mendapatkan pengalaman menginap tidak terlupakan bersama orang terkasih. Demikian pula bila Anda hendak menghabiskan waktu liburan dengan keluarga, Lafayette Boutique Hotel dapat menjadi pilihan tepat. Hal ini tak lain karena fasilitas serta pelayanan super yang ditawarkan, sehingga menjadikan setiap tamu puas juga kerasan berada di akomodasi. Bahkan, ingin lebih lama untuk menginap.",

    2049 => "sofia Boutique Residence merupakan hotel bintang 4 yang layak dipertimbangkan untuk kebutuhan perjalanan Anda. Pasalnya, akomodasi ini sangat cocok untuk liburan, staycation, bulan madu, ataupun perjalanan bisnis. Letaknya strategis, di Jalan Karya Utama, Sedan, Ngaglik, Sleman sehingga memberi Anda kemudahan akses ke berbagai tempat menarik di Yogyakarta. Dari luar, hotel ini tampak megah dan menjulang tinggi. Desainnya begitu cantik dengan dominasi ornamen hitam dan emas, membuat Sofia Boutique Residence tampak elegan dan mewah. Dengan harga mulai Rp1 jutaan per malam, Anda sudah bisa menikmati pengalaman menginap yang berkesan di Sofia Boutique Residence. Memiliki bangunan dan interior yang memukau menjadikan Sofia Boutique Residence sebagai ikon kemewahan di tengah Kota Yogyakarta. Dengan memadukan keindahan dan keanggunan, Anda akan merasakan pengalaman menginap bak di istana. Setiap aspek yang ditawarkan Sofia Boutique Residence tak lepas dari keindahan. Secara otomatis hal ini efektif meningkatkan kenyamanan dan kepuasan selama menginap. Hampir sebagian besar tamu yang telah menikmati pengalaman menyenangkan dan berkesan tak segan memberikan beragam ulasan positif. Lokasi yang strategis memudahkan Anda mengakses berbagai atraksi menarik, seperti pusat belanja, fasilitas umum, tempat makan, dan tempat wisata. Dengan segala hal yang ditawarkan, menginap di Sofia Boutique Residence merupakan pilihan tepat untuk mendapatkan pengalaman berkesan tak terlupakan. Terletak di lokasi strategis membuat Sofia Boutique Residence mudah dijangkau. Dari Stasiun Tugu, Anda hanya perlu menempuh perjalanan selama 17 menit, sedangkan dari Stasiun Lempuyangan hanya 16 menit berkendara. Mall Malioboro dan Ambarrukmo Plaza dapat dijangkau dengan berkendara selama 16 menit. Monumen Yogya Kembali terletak sekitar 2,1 km dari penginapan, sedangkan Exotarium Mini Zoo berjarak sekitar 4,3 km. Letak yang strategis memudahkan Anda mengeksplorasi area di sekitar akomodasi dengan berjalan kaki, menggunakan kendaraan pribadi, maupun menggunakan moda transportasi umum yang tersedia. ",

    2050 => "Yogyakarta Marriott Hotel adalah pilihan yang tepat bagi Anda yang mencari suguhan mewah untuk liburan Anda. Manjakan diri Anda dengan layanan terbaik dan buat liburan Anda berkesan dengan menginap di sini. Apakah Anda seorang shopaholic? Menginap di Yogyakarta Marriott Hotel pasti akan memanjakan Anda dengan banyaknya pusat perbelanjaan di dekatnya.Perawatan spa adalah salah satu fitur utama hotel. Manjakan diri Anda dengan perawatan relaksasi yang meremajakan Anda. Yogyakarta Marriott Hotel adalah hotel dekat Bandara, akomodasi yang ideal sambil menunggu penerbangan Anda berikutnya. Nikmati tempat istirahat yang memuaskan selama transit Anda. Dari acara bisnis hingga pertemuan perusahaan, Yogyakarta Marriott Hotel menyediakan layanan dan fasilitas lengkap yang Anda dan kolega Anda butuhkan. Bersenang-senanglah dengan berbagai fasilitas hiburan untuk Anda dan seluruh keluarga di Yogyakarta Marriott Hotel, akomodasi yang indah untuk liburan keluarga Anda. Jika Anda berencana untuk menginap jangka panjang, menginap di Yogyakarta Marriott Hotel adalah pilihan yang tepat untuk Anda. Menyediakan berbagai fasilitas dan kualitas layanan yang luar biasa, akomodasi ini pasti membuat Anda merasa seperti di rumah. Layanan berkualitas tertinggi yang menyertai fasilitasnya yang luas akan membuat Anda mendapatkan pengalaman liburan terbaik. Pusat kebugaran hotel wajib dicoba selama Anda menginap di sini. Nikmati hari yang menyenangkan dan santai di kolam renang, baik Anda bepergian sendiri atau bersama orang yang Anda cintai. Dapatkan penawaran terbaik untuk perawatan spa berkualitas terbaik untuk bersantai dan meremajakan diri Anda.Resepsionis 24 jam tersedia untuk melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. Nikmati hidangan favorit Anda dengan masakan khusus dari Yogyakarta Marriott Hotel khusus untuk Anda. WiFi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Yogyakarta Marriott Hotel adalah hotel dengan kenyamanan dan layanan yang sangat baik menurut sebagian besar tamu hotel. Dapatkan momen berharga dan tak terlupakan selama Anda menginap di Yogyakarta Marriott Hotel.",

    2051 => "Hotel ini adalah tempat terbaik bagi Anda yang menginginkan liburan yang tenang dan damai, jauh dari keramaian. Apakah Anda seorang shopaholic? Menginap di Royal Ambarrukmo Yogyakarta pasti akan memanjakan Anda dengan banyaknya pusat perbelanjaan di dekatnya. Perawatan spa adalah salah satu fitur utama hotel. Manjakan diri Anda dengan perawatan relaksasi yang meremajakan Anda. Layanan berkualitas tertinggi yang menyertai fasilitasnya yang luas akan membuat Anda mendapatkan pengalaman liburan terbaik. Pusat kebugaran hotel wajib dicoba selama Anda menginap di sini. Nikmati hari yang menyenangkan dan santai di kolam renang, baik Anda bepergian sendiri atau bersama orang yang Anda cintai. Dapatkan penawaran terbaik untuk perawatan spa berkualitas terbaik untuk bersantai dan meremajakan diri Anda.Resepsionis 24 jam tersedia untuk melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. Nikmati hidangan favorit Anda dengan masakan khusus dari Royal Ambarrukmo Yogyakarta khusus untuk Anda. WiFi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Royal Ambarrukmo Yogyakarta adalah hotel dengan kenyamanan dan layanan yang sangat baik menurut sebagian besar tamu hotel. Nikmati suguhan mewah dan pengalaman tak tertandingi dengan menginap di Royal Ambarrukmo Yogyakarta.",

    2052 => "Menginap di Grand Mercure Yogyakarta Adi Sucipto adalah pilihan yang baik ketika Anda mengunjungi  Depok. Resepsionis 24 jam siap melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. Wi-Fi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Grand Mercure Yogyakarta Adi Sucipto adalah hotel dengan kenyamanan dan pelayanan yang sangat baik menurut sebagian besar tamu hotel. Dapatkan momen berharga dan tak terlupakan selama Anda menginap di Grand Mercure Yogyakarta Adi Sucipto.",

    2028 => "Menginap di Grand Mercure Yogyakarta Adi Sucipto adalah pilihan yang baik ketika Anda mengunjungi Depok. Pelayanan dengan kualitas terbaik yang menyertai fasilitasnya yang lengkap akan membuat Anda mendapatkan pengalaman liburan terbaik. Nikmati hari yang menyenangkan dan santai di kolam renang, baik saat Anda bepergian sendiri atau bersama orang yang Anda cintai. Resepsionis 24 jam siap melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. Wi-Fi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Grand Mercure Yogyakarta Adi Sucipto adalah hotel dengan kenyamanan dan pelayanan yang sangat baik menurut sebagian besar tamu hotel. Dapatkan momen berharga dan tak terlupakan selama Anda menginap di Grand Mercure Yogyakarta Adi Sucipto",

    2029 => "The Royal Surakarta Heritage - Handwritten Collection adalah hotel di lokasi yang baik, tepatnya berada di Kampung Baru.Kualitas pelayanan super, ditambah berbagai fasilitas unggulan akan membuat saat-saat menginap di sini menjadi sebuah pengalaman berkesan yang luar biasa. Tersedia kolam renang untuk Anda bersantai sendiri maupun bersama teman dan keluarga. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. The Royal Surakarta Heritage - Handwritten Collection adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu. Pengalaman berkesan dan tak terlupakan akan Anda dapatkan selama menginap di The Royal Surakarta Heritage - Handwritten Collection. ",

    2030 => "Hotel Santika Premiere Semarang adalah hotel di lokasi yang baik, tepatnya berada di Semarang Tengah. Hotel Santika Premiere Semarang adalah pilihan tepat bagi Anda yang ingin menghabiskan waktu dengan berbagai fasilitas mewah. Nikmati kualitas layanan terbaik dan pengalaman mengesankan selama menginap di hotel ini.Layanan spa adalah fasilitas nomor wahid di hotel ini. Habiskan waktu Anda dengan melakukan relaksasi dan memanjakan diri. Hotel Santika Premiere Semarang memiliki segala fasilitas penunjang bisnis untuk Anda dan kolega. Hotel Santika Premiere Semarang adalah tempat bermalam yang tepat bagi Anda yang berlibur bersama keluarga. Nikmati segala fasilitas hiburan untuk Anda dan keluarga. Hotel Santika Premiere Semarang memberikan pengalaman menginap yang unik di dalam bangunan bersejarah yang sulit Anda temukan di tempat lain. Jika Anda berniat menginap dalam jangka waktu yang lama, Hotel Santika Premiere Semarang adalah pilihan tepat. Berbagai fasilitas yang tersedia dan kualitas pelayanan yang baik akan membuat Anda merasa sedang berada di rumah sendiri. Pengalaman menginap Anda tak akan terlupakan berkat pelayanan istimewa yang disertai oleh berbagai fasilitas pendukung untuk kenyamanan Anda. Pusat kebugaran menjadi salah satu fasilitas yang wajib Anda coba saat menginap di tempat ini. Tersedia kolam renang untuk Anda bersantai sendiri maupun bersama teman dan keluarga. Manjakan diri Anda dengan menikmati fasilitas spa yang memberikan harga dan kualitas pelayanan terbaik. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. Terdapat restoran yang menyajikan menu lezat ala Hotel Santika Premiere Semarang khusus untuk Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Hotel Santika Premiere Semarang adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu.",

    2031 => "Menginap di Novotel Semarang adalah pilihan yang baik ketika Anda mengunjungi Sekayu. Resepsionis 24 jam tersedia untuk melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. Wi-Fi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Novotel Semarang adalah hotel dengan kenyamanan luar biasa dan pelayanan prima menurut sebagian besar tamu hotel.Dapatkan momen berharga dan tak terlupakan selama Anda menginap di Novotel Semarang.",

    2033 => "Terletak di jantung kota Solo, Alila Solo berdiri sebagai mercusuar kemewahan dan kenyamanan. Hotel ini menawarkan berbagai fasilitas kelas dunia, termasuk akomodasi elegan dengan kamar dan suite luas yang dirancang dengan estetika modern dan dilengkapi dengan fasilitas terbaru untuk memastikan masa inap yang nyaman. Para tamu dapat bersantai dan bersantai di Agra Rooftop, yang menawarkan keindahan Gunung Merbabu dan siluet Gunung Merapi yang tak tertandingi yang dikombinasikan dengan pemandangan cakrawala kota yang indah. Mereka juga dapat memilih untuk menikmati perawatan yang meremajakan di Spa Alila, yang menampilkan perawatan tradisional Jawa terbaik dengan sentuhan modern. Bagi mereka yang ingin tetap aktif, pusat kebugaran canggih tersedia 24/7, memungkinkan para tamu untuk menikmati jacuzzi yang meremajakan setelah berolahraga yang melelahkan. Alila Solo juga dikenal dengan ruang pertemuan dan ruang serbaguna dan lengkap, cocok untuk pernikahan, konferensi, dan pertemuan sosial. Ruang serbaguna yang megah, yang membentang lebih dari 2.000 meter persegi, dapat menampung lebih dari 3.000 tamu dalam satu acara.",

    2034 => "Pengalaman menginap Anda tak akan terlupakan berkat pelayanan istimewa yang disertai oleh berbagai fasilitas pendukung untuk kenyamanan Anda. Tersedia kolam renang untuk Anda bersantai sendiri maupun bersama teman dan keluarga. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Novotel Solo adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu. Nikmati pelayanan mewah dan pengalaman tak terlupakan ala Novotel Solo selama Anda menginap di sini.",

    2036 => "Aston Purwokerto Hotel & Convention Center adalah pilihan tepat bagi Anda yang ingin menghabiskan waktu dengan berbagai fasilitas mewah. Nikmati kualitas layanan terbaik dan pengalaman mengesankan selama menginap di hotel ini. Aston Purwokerto Hotel & Convention Center memiliki segala fasilitas penunjang bisnis untuk Anda dan kolega. Aston Purwokerto Hotel & Convention Center adalah tempat bermalam yang tepat bagi Anda yang berlibur bersama keluarga. Nikmati segala fasilitas hiburan untuk Anda dan keluarga. Jika Anda berniat menginap dalam jangka waktu yang lama, Aston Purwokerto Hotel & Convention Center adalah pilihan tepat. Berbagai fasilitas yang tersedia dan kualitas pelayanan yang baik akan membuat Anda merasa sedang berada di rumah sendiri. Pengalaman menginap Anda tak akan terlupakan berkat pelayanan istimewa yang disertai oleh berbagai fasilitas pendukung untuk kenyamanan Anda. Pusat kebugaran menjadi salah satu fasilitas yang wajib Anda coba saat menginap di tempat ini. Tersedia kolam renang untuk Anda bersantai sendiri maupun bersama teman dan keluarga.Manjakan diri Anda dengan menikmati fasilitas spa yang memberikan harga dan kualitas pelayanan terbaik. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. Terdapat restoran yang menyajikan menu lezat ala Aston Purwokerto Hotel & Convention Center khusus untuk Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Aston Purwokerto Hotel & Convention Center adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu. Pengalaman berkesan dan tak terlupakan akan Anda dapatkan selama menginap di Aston Purwokerto Hotel & Convention Center",

    2037 => "Menginap di Luminor Hotel Purwokerto By WH adalah pilihan yang baik ketika Anda mengunjungi Purwokerto Timur. Resepsionis 24 jam tersedia untuk melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. Nikmati hidangan favorit Anda dengan masakan khusus dari Luminor Hotel Purwokerto By WH khusus untuk Anda. Wi-Fi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Luminor Hotel Purwokerto By WH adalah hotel dengan kenyamanan luar biasa dan pelayanan prima menurut sebagian besar tamu hotel. Dapatkan momen berharga dan tak terlupakan selama Anda menginap di Luminor Hotel Purwokerto By WH.",

    2038 => "Elsotel Purwokerto by Daphna Management adalah tempat bermalam yang tepat bagi Anda yang berlibur bersama keluarga. Nikmati segala fasilitas hiburan untuk Anda dan keluarga. Jika Anda berniat menginap dalam jangka waktu yang lama, Elsotel Purwokerto by Daphna Management adalah pilihan tepat. Berbagai fasilitas yang tersedia dan kualitas pelayanan yang baik akan membuat Anda merasa sedang berada di rumah sendiri. Pelayanan memuaskan serta fasilitas hotel yang memadai akan membuat Anda nyaman berada di Elsotel Purwokerto by Daphna Management. Pusat kebugaran menjadi salah satu fasilitas yang wajib Anda coba saat menginap di tempat ini. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. Terdapat restoran yang menyajikan menu lezat ala Elsotel Purwokerto by Daphna Management khusus untuk Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Elsotel Purwokerto by Daphna Management adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu. Dengan fasilitas yang memadai, Elsotel Purwokerto by Daphna Management menjadi pilihan yang tepat untuk menginap.",

    2039 => "Hotel Surya Yudha Purwokerto memiliki segala fasilitas penunjang bisnis untuk Anda dan kolega. Hotel Surya Yudha Purwokerto adalah tempat bermalam yang tepat bagi Anda yang berlibur bersama keluarga. Nikmati segala fasilitas hiburan untuk Anda dan keluarga. Jika Anda berniat menginap dalam jangka waktu yang lama, Hotel Surya Yudha Purwokerto adalah pilihan tepat. Berbagai fasilitas yang tersedia dan kualitas pelayanan yang baik akan membuat Anda merasa sedang berada di rumah sendiri. Pelayanan memuaskan serta fasilitas hotel yang memadai akan membuat Anda nyaman berada di Hotel Surya Yudha Purwokerto. Tersedia kolam renang untuk Anda bersantai sendiri maupun bersama teman dan keluarga. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. Terdapat restoran yang menyajikan menu lezat ala Hotel Surya Yudha Purwokerto khusus untuk Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Hotel Surya Yudha Purwokerto adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu. Dengan fasilitas yang memadai, Hotel Surya Yudha Purwokerto menjadi pilihan yang tepat untuk menginap.",

    2040 => "Java Heritage Hotel Purwokerto adalah pilihan tepat bagi Anda yang ingin menghabiskan waktu dengan berbagai fasilitas mewah. Nikmati kualitas layanan terbaik dan pengalaman mengesankan selama menginap di hotel ini. Java Heritage Hotel Purwokerto memiliki segala fasilitas penunjang bisnis untuk Anda dan kolega. Java Heritage Hotel Purwokerto adalah tempat bermalam yang tepat bagi Anda yang berlibur bersama keluarga. Nikmati segala fasilitas hiburan untuk Anda dan keluarga. Jika Anda berniat menginap dalam jangka waktu yang lama, Java Heritage Hotel Purwokerto adalah pilihan tepat. Berbagai fasilitas yang tersedia dan kualitas pelayanan yang baik akan membuat Anda merasa sedang berada di rumah sendiri. hotel ini adalah pilihan yang pas jika Anda mencari liburan yang tenang dan jauh dari keramaian. Pengalaman menginap Anda tak akan terlupakan berkat pelayanan istimewa yang disertai oleh berbagai fasilitas pendukung untuk kenyamanan Anda. Pusat kebugaran menjadi salah satu fasilitas yang wajib Anda coba saat menginap di tempat ini. Tersedia kolam renang untuk Anda bersantai sendiri maupun bersama teman dan keluarga. Manjakan diri Anda dengan menikmati fasilitas spa yang memberikan harga dan kualitas pelayanan terbaik. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. Terdapat restoran yang menyajikan menu lezat ala Java Heritage Hotel Purwokerto khusus untuk Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Java Heritage Hotel Purwokerto adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu.",

    2043 => "Menginap di Ocean View Residence - Hotel adalah pilihan yang baik ketika Anda mengunjungi Tahunan. Resepsionis 24 jam tersedia untuk melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. Nikmati hidangan favorit Anda dengan masakan khusus dari Ocean View Residence - Hotel khusus untuk Anda. WiFi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Ocean View Residence - Hotel adalah hotel dengan kenyamanan luar biasa dan layanan yang sangat baik menurut sebagian besar tamu hotel. Dengan semua fasilitas yang ditawarkan, Ocean View Residence - Hotel adalah tempat yang tepat untuk menginap.",

    2044 => "Tilem Beach Hotel & Resort adalah tempat bermalam yang tepat bagi Anda yang berlibur bersama keluarga. Nikmati segala fasilitas hiburan untuk Anda dan keluarga. hotel ini adalah pilihan tepat bagi Anda dan pasangan yang ingin menikmati liburan romantis. Dapatkan pengalaman yang penuh kesan bersama pasangan dengan menginap di Tilem Beach Hotel & Resort. hotel ini adalah pilihan yang pas jika Anda mencari liburan yang tenang dan jauh dari keramaian. Pelayanan memuaskan serta fasilitas hotel yang memadai akan membuat Anda nyaman berada di Tilem Beach Hotel & Resort. Tersedia kolam renang untuk Anda bersantai sendiri maupun bersama teman dan keluarga. Resepsionis siap 24 jam untuk melayani proses check-in, check-out dan kebutuhan Anda yang lain. Jangan ragu untuk menghubungi resepsionis, kami siap melayani Anda. Terdapat restoran yang menyajikan menu lezat ala Tilem Beach Hotel & Resort khusus untuk Anda. WiFi tersedia di seluruh area publik properti untuk membantu Anda tetap terhubung dengan keluarga dan teman. Tilem Beach Hotel & Resort adalah akomodasi dengan fasilitas baik dan kualitas pelayanan memuaskan menurut sebagian besar tamu.",

  ];

    function getHotelDescription($hotel_id, $hotel_name = '') {
    global $hotel_descriptions;
    
    if (isset($hotel_descriptions[$hotel_id])) {
        return $hotel_descriptions[$hotel_id];
    }
    
    // Fallback if no description exists
    return "Selamat datang di " . htmlspecialchars($hotel_name) . ". Hotel ini menawarkan akomodasi yang nyaman dengan fasilitas dan layanan yang sangat baik untuk masa menginap Anda. Nikmati keramahtamahan terbaik dan fasilitas modern selama kunjungan Anda.";
}

$current_hotel_description = getHotelDescription($id_hotel, $hotels['nama_hotel']);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $hotels['nama_hotel'];?> | Javast</title>
    <link rel="stylesheet" href="hotel2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
     
  <div class="navbar">
    <div class="logo">
        <img src="logo/Logo_Javast.png" alt="Logo_Javast">

    </div>
    <div class="menu">
        <a href="home.php">Beranda</a>
        <a href="tentang.php">Tentang</a>
        <a href="kontak_kami.php">Kontak Kami</a>

    </div>

    <div class="dropdown">
        <button class="dropdown-btn"> 
            <i class="fas fa-user"></i> <?= htmlspecialchars($username) ?> ▼
        </button>
        <div class="dropdown-menu">
            <a href="profil.php">Profil</a>
            <a href="booking.php">Booking</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
</div>

    <!-- <div class="container-header"> 
</div> -->
      <div class="hotel-container">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="card">
          <h5>Check-in</h5>
          <input type="text" value="<?= $check_in ?>" readonly>
        </div>
        <div class="card">
          <h5>Check-out</h5>
          <input type="text" value="<?= $check_out ?>" readonly>
        </div>
        <div class="card">
          <h5>Fasilitas Hotel</h5>
          <ul>
            
              <?php while ($f_hotel = mysqli_fetch_assoc($query_fasilitas_hotel)): ?>
              <?php
              $fasilitas = explode(',', $f_hotel['fasilitas_hotel']);
              foreach ($fasilitas as $item) {
                  $item = trim($item);
                  if (!empty($item)) {
                      echo '<li>'.htmlspecialchars($item).'</li> ';
                  }
              }
              ?>
              <?php endwhile ?>
          </ul>
        </div>
        <div class="card">
          <h5>Dewasa</h5>
          <input type="number" value="<?= $_GET['dewasa'] ?? '1' ?>" readonly>
        </div>
        <div class="card">
          <h5>Anak - anak</h5>
          <input type="number" value="<?= $_GET['anak'] ?? '0' ?>" readonly>
        </div>
      </div>


<div class="main-content" style="flex-direction: column; gap:20px; width:100%;">
      
    <div class="hotel-card">

        <div class="hotel-info">
            <div class="description">
              <div class="header-container">
                <div class="rating-facilities-hotel">
                    <div class="rating">
                            <?php echo str_repeat("★", $hotels['bintang_hotel']); ?>
                            <i class="fa-solid fa-thumbs-up"></i>
                        </div>
                    <div class="facilities">
                            <?php 
                            // Contoh: Ambil 3 fasilitas pertama
                            $fasilitas = explode(", ", $hotels['fasilitas_hotel']);
                            for ($i = 0; $i < min(4, count($fasilitas)); $i++) {
                                echo "<span>" . trim($fasilitas[$i]) . "</span> ";
                            }
                            ?>
                            <span>Dan masih banyak lagi</span>
                        </div>  
                </div>
                <h2 class="title"><?php echo strtoupper($hotels['nama_hotel']); ?></h2>
                    <span class="hotel-type">
                        <i class="fa-solid fa-location-dot"></i> <?php echo  $hotels['lokasi_hotel']; ?>
                    </span>

                    <span class="hotel-alamat">
                        <?php echo  $hotels['alamat_hotel']; ?>
                    </span>
                
                     <div class="price-wrapper">
                        <span class="price">Rp. <?php echo number_format($hotels['harga_terendah'], 0, ',', '.'); ?></span><br>
                        <span class="per-night">1 Malam</span>
                    </div>
            </div>
        </div>

        <div class="hotel-image-container">
                <img src="/JAVAST/Admin/Gambar/Hotel/<?php echo $hotels['gambar_hotel']; ?>" alt="<?php echo $hotels['nama_hotel']; ?>" class="hotel-image">
        </div>
    </div>
    </div>

    
    <!-- deskripsi singkat -->

    <div class="description-card">
      <div class="card-content">
        <h4>Deskripsi Hotel</h4>
        <p class="hotel-description">
            <?php echo $current_hotel_description; ?>
        </p>
      </div>
    </div>

    <br>

    


    
    
      <!-- Konten kamar -->
  <div class="kamar-content">
    <?php while ($data_kamar = mysqli_fetch_assoc($query_kamar)): ?>
    <?php
    // Cari gambar pertama yang tersedia
    $thumbnail = "";
    $gambar_fields = ['gambarA', 'gambarB', 'gambarC', 'gambarD', 'gambarE'];
    
    foreach ($gambar_fields as $field) {
        if (!empty($data_kamar[$field])) {
            $thumbnail_path = '/JAVAST/Admin/Gambar/Kamar/'.$data_kamar[$field];
            $full_path = $_SERVER['DOCUMENT_ROOT'].$thumbnail_path;

            if (file_exists($full_path)) {
                $thumbnail = $thumbnail_path;
                break;
            }
        }
    }
    ?>
        <div class="kamar-card">
          <?php if ($thumbnail): ?>
            <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($data_kamar['nama_kamar']) ?>">
        <?php else: ?>
            <img src="gambar/default-room.jpg" alt="Kamar Default">
        <?php endif; ?>
          <div class="kamar-info">
             <h4><?= htmlspecialchars($data_kamar['nama_kamar']) ?></h4>
             
            
            <div class="facilities">
              <span><?= htmlspecialchars($data_kamar['tipe_kasur']) ?></span>
              <br>
              <br>
            
             <b>Fasilitas</b>

             <br>

              <?php
              $fasilitas = explode(',', $data_kamar['fasilitas_kamar']);
              $counter = 0; // Penghitung untuk menentukan <br>

              echo '<div class="facilities-container">'; // Container utama

              foreach ($fasilitas as $item) {
                  $item = trim($item);
                  if (!empty($item)) {
                      echo '<span class="facility-item">' . htmlspecialchars($item) . '</span>';
                      
                      $counter++;
                      // Tambah <br> setelah setiap 3 fasilitas
                      if ($counter % 3 == 0) {
                          echo '<br class="facility-break">';
                      }
                  }
              }

              echo '</div>';
              ?>

              <b>Kapasitas</b>
              <br>

              <span><?= $data_kamar['jumlah_dewasa'] + $data_kamar['jumlah_anak'] ?> Tamu</span>

              <br> 
              <br>
              
              <b>Ketersediaan kamar</b>
              <br>
              <span><?= $data_kamar['jumlah_kamar'] ?> Kamar </span>
          </div>

          <div class="box-button">
              <div class="price">Rp. <?= number_format($data_kamar['harga_kamar'], 0, ',', '.') ?></div><br>
              <div class="btn-pilih-kamar">
                <a href="#" onclick="showNotification()">Pilih Kamar</a>
              </div>
              
              <br>

              
              
          </div>
         </div>
        </div>
         <?php endwhile; ?>
 
      
      </div>
    </div>
  </div>
  </div>


    
    <br>
    <br>
    

</div>       
            

    <footer>
        <div class="footer-container">
            <div class="footer-logo">
                
                <img src="logo/Logo_Javast.png" alt="Javast Logo">
                <p>Selamat datang di platform Javast, tempat terbaik untuk memesan hotel
                    hampir di seluruh kota di Jawa Tengah. Website ini
                    dirancang khusus untuk memudahkan Anda dalam menemukan
                    dan memesan akomodasi sesuai dengan kebutuhan, baik untuk keperluan bisnis maupun liburan.</p>
            </div>
    
            <div class="footer-links">
                <h3>Link</h3>
                <ul>
                    <li><a href="home.php">Beranda</a></li>
                    <li><a href="tentang.php">Tentang</a></li>
                    <li><a href="kontak_kami.php">Kontak Kami Us</a></li>
                </ul>
            </div>
    
            <div class="footer-social">
                <h3>Ikuti Kami</h3>
                <ul>
                    <li><a href="#"><i class="fab fa-facebook"></i> Javast</a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i> @javast.hotel</a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i> @javast.hotel</a></li>
                </ul>
            </div>
        
    </footer>

    <script>
    function showNotification() {
    alert("Isi search bar terlebih dahulu!");
    // Optional: If you still want to redirect after the alert
    window.location.href = "home.php";
}
</script>

</body>
</html>