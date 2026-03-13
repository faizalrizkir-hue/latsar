<?php

use App\Models\Element1KegiatanAsurans;
use App\Models\Element1KegiatanAsuransEditLog;
use App\Models\Element1JasaKonsultansi;
use App\Models\Element1JasaKonsultansiEditLog;
use App\Models\Element2KomunikasiHasil;
use App\Models\Element2KomunikasiHasilEditLog;
use App\Models\Element2PengendalianKualitas;
use App\Models\Element2PengendalianKualitasEditLog;
use App\Models\Element2PemantauanTindakLanjut;
use App\Models\Element2PemantauanTindakLanjutEditLog;
use App\Models\Element2PengembanganInformasi;
use App\Models\Element2PengembanganInformasiEditLog;
use App\Models\Element2PelaksanaanPenugasan;
use App\Models\Element2PelaksanaanPenugasanEditLog;
use App\Models\Element2PerencanaanPenugasan;
use App\Models\Element2PerencanaanPenugasanEditLog;
use App\Models\Element3PelaporanManajemenKld;
use App\Models\Element3PelaporanManajemenKldEditLog;
use App\Models\Element3PerencanaanPengawasan;
use App\Models\Element3PerencanaanPengawasanEditLog;
use App\Models\Element4ManajemenKinerja;
use App\Models\Element4ManajemenKinerjaEditLog;
use App\Models\Element4MekanismePendanaan;
use App\Models\Element4MekanismePendanaanEditLog;
use App\Models\Element4DukunganTik;
use App\Models\Element4DukunganTikEditLog;
use App\Models\Element4PengembanganSdmProfesionalApip;
use App\Models\Element4PengembanganSdmProfesionalApipEditLog;
use App\Models\Element4PerencanaanSdmApip;
use App\Models\Element4PerencanaanSdmApipEditLog;

return [
    'modules' => [
        'element1_kegiatan_asurans' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element1KegiatanAsurans::class,
            'edit_log_model' => Element1KegiatanAsuransEditLog::class,
            'page_title' => 'Element 1 - Kualitas Peran dan Layanan',
            'subtopic_code' => 'S1',
            'subtopic_title' => 'Sub Topik 1 - Kegiatan Asurans',
            'info_modal_title' => 'Informasi Level Sub Topik 1 - Kegiatan Asurans',
            'notification_title' => 'Element 1 - Kegiatan Asurans',
            'rows' => [
                1 => 'Ruang Lingkup dan Fokus',
                2 => 'Analisis dan Atribut Temuan',
                3 => 'Kualitas Opini/Simpulan',
                4 => 'Kualitas Rekomendasi',
            ],
            'weights' => [
                1 => 0.20,
                2 => 0.25,
                3 => 0.25,
                4 => 0.30,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Kegiatan asurans bersifat reaktif, dengan pendekatan sederhana dan cakupan yang terbatas pada kepatuhan administratif sehingga belum memberikan keyakinan memadai yang dapat digunakan manajemen.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Kegiatan asurans terstruktur dan berulang dengan fokus pada kepatuhan prosedur dasar, kualitas praktik dan hasil pengawasan masih terbatas, belum sesuai standar, serta belum berkontribusi pada perbaikan akar masalah dan perbaikan TKMRPI.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Kegiatan asurans berbasis risiko memberikan keyakinan memadai atas kepatuhan regulasi, pencapaian tujuan, dan aspek efisiensi, efektivitas, dan ekonomis (3E), serta mulai menghasilkan simpulan dan rekomendasi yang berkontribusi pada perbaikan TKMRPI dan pengendalian kecurangan pada organisasi dan prioritas pembangunan nasional.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Kegiatan asurans telah terintegrasi dan berbasis manajemen risiko, memberikan keyakinan memadai melalui asurans menyeluruh atas efektivitas TKMRPI pada tingkat organisasi dan prioritas pembangunan nasional. Informasi pengawasan disajikan secara konvergen dan digunakan oleh pimpinan organisasi dalam pengambilan keputusan strategis.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Kegiatan asurans bersifat antisipatif dan inovatif, memanfaatkan praktik terbaik untuk menghasilkan insight dan foresight atas manajemen risiko. Kegiatan asurans menyesuaikan dinamika kebijakan dan strategi K/L/D, memberikan deteksi dini atas risiko dan peluang masa depan, serta menyajikan rekomendasi proyektif yang mendorong peningkatan TKMRPI secara dini, holistik, dan selaras dengan pembangunan nasional.',
                ],
            ],
            'statement_level_hints' => [
                'Ruang Lingkup dan Fokus' => [
                    1 => 'Kegiatan pengawasan belum memiliki ruang lingkup yang jelas.',
                    2 => 'Ruang lingkup mengarah kepada aspek ketaatan, tetapi tidak menyebutkan secara lengkap objek pengawasan (unit/kegiatan), periode yang diawasi, wilayah/lokasi, dan proses bisnis utama.',
                    3 => 'Ruang lingkup berbasis risiko dan mengarah pada pengawasan ketaatan, kinerja, maupun pengawasan atas TKMRPI, termasuk deteksi kecurangan. Ruang lingkup telah mempertimbangkan risiko signifikan area strategis pada lintas unit.',
                    4 => 'Ruang lingkup berbasis risiko telah mencakup proses bisnis utama, yang di dalamnya juga menyertakan sasaran perbaikan pada area strategis dan mengarah pada konvergensi untuk memberikan informasi menyeluruh atas kualitas TKMRPI K/L/D.',
                    5 => 'Ruang lingkup terhubung langsung ke penilaian risiko penugasan dan materialitas, menunjukkan mengapa area tertentu diprioritaskan; menyertakan inklusi/eksklusi eksplisit, asumsi, dependensi data, dan batasan tanggung jawab',
                ],
                'Analisis dan Atribut Temuan' => [
                    1 => 'Analisis dan atribut temuan tidak lengkap.',
                    2 => 'Analisis dilakukan dengan membandingkan dokumen/proses dengan kriteria, namun masih berfokus pada kepatuhan administratif (ceklist tanpa melihat substansi); atribut temuan mulai terbentuk tetapi sebab belum mencapai akar masalah (root cause) dan akibat belum dapat dinilai materialitasnya.',
                    3 => 'Atribut temuan lengkap berdasarkan analisis: 1) kondisi dibandingkan dengan kriteria secara jelas dan terperinci; 2) sebab dianalisis menggunakan root cause analysis; 3) akibat dikuantifikasi dampak finansial maupun operasional; 4) temuan mampu mendeteksi risiko kecurangan; 5) pemecahan hambatan proses (debottlenecking).',
                    4 => 'Analisis dilakukan memanfaatkan data analytics untuk mengidentifikasi anomali dan menggunakan teknik forensik pada area berisiko tinggi.',
                    5 => 'Analisis menghubungkan setiap temuan dengan tujuan strategis dan TKMRPI sehingga menghasilkan informasi yang menggambarkan perubahan lingkungan di masa depan beserta dampaknya.',
                ],
                'Kualitas Opini/Simpulan' => [
                    1 => 'Simpulan tidak menjawab sasaran dan tujuan pengawasan yang ditetapkan.',
                    2 => 'Simpulan menegaskan kepatuhan dokumen/prosedur, tetapi belum menjelaskan akar masalah.',
                    3 => 'Simpulan menjawab seluruh tujuan dan sasaran pengawasan yang meliputi: kesesuaian terhadap peraturan perundang-undangan, standar kinerja, pencapaian tujuan, keseluruhan aspek 3E, dan kualitas TKMRPI atas objek pengawasan, namun belum dapat menyimpulkan untuk keseluruhan organisasi (K/L/D).',
                    4 => 'Simpulan mendukung untuk menghasilkan informasi menyeluruh atas kualitas tata kelola manajemen risiko dan pengendalian intern (termasuk atas risiko dan pengendalian kecurangan) terhadap area strategis K/L/D.',
                    5 => 'Simpulan memberi pola dan indikator risiko kunci untuk memproyeksikan tren, risiko, peluang, dan perubahan yang mungkin terjadi beserta dampaknya, lalu mengaitkannya dengan opsi kebijakan/strategi yang selaras dengan tujuan TKMRPI pada level makro.',
                ],
                'Kualitas Rekomendasi' => [
                    1 => 'Rekomendasi tidak menjawab tujuan dan sasaran pengawasan, tidak selaras dengan temuan yang diangkat.',
                    2 => 'Rekomendasi sudah sesuai dengan temuan yang diangkat namun sebatas pada perbaikan yang bersifat administratif dan tidak memperbaiki akar permasalahan.',
                    3 => 'Rekomendasi selaras dengan temuan dan menjawab tujuan pengawasan, serta memberikan perbaikan substantif terhadap TKMRPI unit kerja organisasi, termasuk penguatan pengendalian dan deteksi dini kecurangan. Rekomendasi disusun berdasarkan tingkat urgensi yang perlu segera ditangani dan dilengkapi dengan rencana tindak lanjut yang dapat diimplementasikan.',
                    4 => 'Rekomendasi mencakup perbaikan TKMRPI, termasuk penguatan pengendalian kecurangan pada proses bisnis utama serta area strategis di K/L/D.',
                    5 => 'Rekomendasi mencakup pemberian inovasi, saran perbaikan, serta rencana tindak terhadap risiko dan peluang proyektif di masa depan, termasuk untuk penguatan strategi dan kebijakan yang meningkatkan nilai organisasi.',
                ],
            ],
        ],
        'element1_jasa_konsultansi' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element1JasaKonsultansi::class,
            'edit_log_model' => Element1JasaKonsultansiEditLog::class,
            'page_title' => 'Element 1 - Kualitas Peran dan Layanan',
            'subtopic_code' => 'S2',
            'subtopic_title' => 'Sub Topik 2 - Kegiatan Konsultansi',
            'info_modal_title' => 'Informasi Level Sub Topik 2 - Kegiatan Konsultansi',
            'notification_title' => 'Element 1 - Kegiatan Konsultansi',
            'rows' => [
                1 => 'Tujuan dan Ruang Lingkup',
                2 => 'Peran, tanggung jawab dan ekspektasi',
                3 => 'Proses Konsultansi',
                4 => 'Kualitas Hasil Konsultansi',
            ],
            'weights' => [
                1 => 0.20,
                2 => 0.20,
                3 => 0.30,
                4 => 0.30,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Kegiatan konsultansi bersifat insidental, berupa jawaban singkat atau klarifikasi administratif, belum didasarkan pada metodologi konsultansi, tidak terdokumentasi, dan belum mengarah pada perbaikan proses atau risiko organisasi.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Kegiatan konsultansi telah terstruktur dan dapat diulang, menggunakan prosedur dasar, namun lingkup yang disepakati masih fokus pada isu administratif dan prosedural. Kualitas masukan terbatas pada regulasi yang ada dan belum mendukung penguatan TKMRPI organisasi.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Kegiatan konsultansi memfasilitasi proses manajemen risiko organisasi dan mendorong internalisasi MR ke dalam manajemen kinerja, termasuk fasilitasi penerapan MR tematik risiko kecurangan dan risiko kemitraan.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Kegiatan konsultansi memberikan pandangan strategis atas manajemen risiko organisasi secara menyeluruh (entity-wide) serta manajemen risiko pembangunan nasional yang menjadi mandat organisasi (goverment-wide), termasuk aspek lingkungan strategis seperti infrastruktur, dan budaya risiko, serta hambatan kelancaran pelaksanaan program pembangunan (debottlenecking).',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Kegiatan konsultansi memberikan pandangan strategis yang bersifat antisipatif dan jangka panjang untuk memperkuat resiliensi organisasi serta kemampuan merespons perubahan lingkungan strategis secara responsif (agile), selaras dengan prinsip manajemen risiko dan pendekatan analisis risiko terkini.',
                ],
            ],
            'statement_level_hints' => [
                'Tujuan dan Ruang Lingkup' => [
                    1 => 'Tujuan konsultansi belum jelas dan tidak terkait dengan tujuan organisasi, sehingga ruang lingkupnya tidak tepat. Belum ada rencana kerja yang sistematis maupun indikator keberhasilan yang disepakati bersama.',
                    2 => 'Tujuan konsultansi telah dirumuskan dan didukung oleh rencana kerja yang sesuai. Ruang lingkup mengarah pada TKMRPI meskipun parsial. Indikator keberhasilan belum disepakati bersama yang berpotensi menimbulkan ketidakjelasan ukuran atau parameter yang digunakan untuk menilai kualitas hasil kegiatan konsultansi.',
                    3 => 'Tujuan konsultansi telah selaras dengan prioritas dan strategi organisasi (unit kerja) dalam perbaikan TKMRPI, ruang lingkup ditetapkan berbasis risiko, indikator keberhasilan dan kriteria mutu hasil disepakati bersama.',
                    4 => 'Tujuan konsultansi telah terintegrasi dengan siklus perencanaan kinerja dan peta risiko organisasi K/L/D, dengan ruang lingkup yang mencakup berbagai proses serta unit kerja di dalam K/L/D.',
                    5 => 'Tujuan dan ruang lingkup konsultansi terhubung dengan penilaian risiko penugasan dan materialitas serta memberikan penegasan mengapa area strategis tertentu diprioritaskan, dengan menyertakan inklusi/eksklusi, asumsi, dan dependensi maupun keterhubungan data.',
                ],
                'Peran, tanggung jawab dan ekspektasi' => [
                    1 => 'Batasan peran dan tanggung jawab belum jelas dan ekspektasi terhadap hasil penugasan belum dituangkan secara tertulis.',
                    2 => 'Batasan peran dan tanggung jawab telah dinyatakan, namun ekspektasi manajemen organisasi (unit kerja) belum dirumuskan dengan indikator yang jelas dan terukur.',
                    3 => 'Batasan peran dan tanggung jawab telah dinyatakan. Ekspektasi organisasi (unit kerja) telah dirumuskan dengan indikator yang jelas dan terukur. APIP tidak mengambil alih keputusan manajemen (unit kerja).',
                    4 => 'Batasan peran dan tanggung jawab telah dinyatakan untuk tidak mengambil alih keputusan manajemen K/L/D. Ekspektasi mencakup strategi perbaikan TKMRPI lintas organisasi (unit kerja) atau organisasi (K/L/D).',
                    5 => 'Ekspektasi telah mencakup strategi perbaikan TKMRPI organisasi (K/L/D) berkelanjutan.',
                ],
                'Proses Konsultansi' => [
                    1 => 'Proses pemberian jasa konsultansi masih menggunakan pendekatan sederhana (seperti diskusi informal atau pemberian tanggapan atas informasi dasar), dan belum terdokumentasi dengan baik.',
                    2 => 'Proses konsultansi menggunakan pendekatan yang mulai terstruktur dan relevan dengan konteks permasalahan yang dihadapi, tetapi belum diperkuat analisis mendalam dengan triangulasi sumber/teknik dan belum mengurai akar penyebab, sehingga dasar untuk menyusun saran perbaikan masih bersifat umum dan lemah.',
                    3 => 'Proses konsultansi relevan dengan konteks permasalahan dan dilakukan melalui pemetaan dan pemahaman proses bisnis beserta risiko dan pengendalian yang ada, termasuk deteksi risiko kecurangan serta penyusunan rencana respons yang tepat.',
                    4 => 'Proses konsultansi dilaksanakan melalui pendampingan dan kajian menyeluruh atas manajemen risiko organisasi, termasuk fasilitasi dialog risiko lintas-unit, analisis penyebab utama permasalahan sistemik, serta koordinasi pemangku kepentingan. Proses konsultansi juga mencakup dukungan teknis seperti layanan forensik (forensic services dan fraud risk assessment) dan fasilitasi penyelesaian hambatan kelancaran pembangunan (debottlenecking), yang dilengkapi indikator kinerja dan mekanisme pemantauan terpadu lintas organisasi (unit kerja) atau organisasi (K/L/D).',
                    5 => 'Proses konsultansi dilaksanakan melalui pendekatan prospektif yang memanfaatkan analisis risiko jangka panjang, pemodelan risiko, serta penilaian kesiapan organisasi dalam menghadapi dinamika lingkungan strategis K/L/D untuk menentukan berbagai opsi saran kebijakan prioritas yang terukur serta memastikan saran strategis kepada organisasi (K/L/D) bersifat antisipatif terhadap perubahan dan risiko di masa depan (emerging risk).',
                ],
                'Kualitas Hasil Konsultansi' => [
                    1 => 'Hasil konsultansi tidak menjawab tujuan dan ruang lingkup serta belum menyasar ke akar masalah serta dampaknya terhadap kinerja organisasi.',
                    2 => 'Hasil konsultansi lebih menekankan penertiban dokumentasi, namun belum mengatasi akar masalah serta belum mempertimbangkan manfaat, biaya, dan risiko dari tindakan perbaikan.',
                    3 => 'Hasil konsultansi bersifat strategis pada organisasi (unit kerja) dan telah menyasar akar masalah yang menghambat proses kinerja beserta pengendalian yang diperlukan. Saran dilengkapi penanggung jawab, tenggat waktu, dan prasyarat data/sumber daya, serta disertai indikator keberhasilan dan skema pemantauan yang terukur.',
                    4 => 'Hasil konsultansi bersifat strategis dan mengurai hambatan lintas organisasi (unit kerja), mencakup penguatan proses bisnis serta pengendalian, dengan rencana implementasi yang dilengkapi analisis manfaat-biaya dan risiko, serta telah diintegrasikan ke dokumen perencanaan strategis organisasi (K/L/D).',
                    5 => 'Hasil konsultansi bersifat strategis dan telah memberikan berbagai opsi kebijakan yang bersifat prediktif serta dilengkapi dengan indikator dampak perbaikan tata kelola berkelanjutan.',
                ],
            ],
        ],
        'element2_pengembangan_informasi' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element2PengembanganInformasi::class,
            'edit_log_model' => Element2PengembanganInformasiEditLog::class,
            'page_title' => 'Element 2 : Profesionalisme Penugasan',
            'subtopic_code' => 'S1',
            'subtopic_title' => 'Sub Topik 1 - Pengembangan Informasi Awal',
            'info_modal_title' => 'Informasi Level Sub Topik 1 - Pengembangan Informasi Awal',
            'notification_title' => 'Element 2 - Pengembangan Informasi Awal',
            'rows' => [
                1 => 'Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)',
                2 => 'Desain Penugasan Pengawasan (DPP)',
            ],
            'weights' => [
                1 => 0.40,
                2 => 0.60,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Tidak terdapat kegiatan Telaah atau Penelitian Awal. Pengawasan dilaksanakan tanpa identifikasi isu strategis, pemahaman entitas, atau analisis awal terhadap kebijakan dan risiko.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Telaah/Penelitian Awal dilakukan terbatas dan administratif. Analisis masih parsial dan belum menyeluruh terhadap strategi, tujuan, proses bisnis, dan risiko. DPP disusun namun belum lengkap dan belum melalui reviu berjenjang.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Telaah/Penelitian Awal dilaksanakan dengan dokumentasi dan reviu berjenjang. Analisis substantif atas strategi, tujuan, proses bisnis, risiko, dan isu strategis digunakan sebagai dasar penyusunan DPP yang lengkap, serta telah memenuhi prinsip logika pengawasan dan prinsip pengawasan intern berbasis risiko.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Telaah/Penelitian Awal dilakukan konsisten dengan metodologi baku dan dikelola sistematis sebagai basis perencanaan pengawasan tahunan. DPP disusun dengan pendekatan berbasis risiko dan mekanisme konsisten antar penugasan, memungkinkan analisis lintas penugasan dan pembelajaran organisasi.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Telaah/Penelitian Awal dan DPP terintegrasi secara digital dalam sistem pengawasan. Hasilnya berfungsi sebagai knowledge base strategis yang digunakan untuk analitik lintas waktu, proyeksi risiko, dan perumusan arah pengawasan jangka menengah/panjang.',
                ],
            ],
            'statement_level_hints' => [
                'Pelaksanaan Pengembangan Informasi Awal (Pra-Perencanaan)' => [
                    1 => 'Telaah dan/atau Penelitian Awal tidak dilakukan.',
                    2 => 'Telaah dan/atau Penelitian Awal dilakukan terbatas.',
                    3 => 'Telaah dan/atau penelitian awal telah dilakukan, terdapat dokumentasi dan reviu berjenjang, serta terdapat analisis sebagai dasar untuk memutuskan akan dilakukannya pengawasan.',
                    4 => 'Telaah dan/atau Penelitian Awal dilakukan secara konsisten dan dikelola sistematis sebagai basis perencanaan pengawasan tahunan/periode.',
                    5 => 'Telaah dan/atau Penelitian Awal terintegrasi secara digital dan berfungsi sebagai knowledge pengawasan strategis organisasi.',
                ],
                'Desain Penugasan Pengawasan (DPP)' => [
                    1 => 'Tidak ada DPP.',
                    2 => 'DPP disusun, namun belum memuat seluruh komponen.',
                    3 => 'DPP memuat seluruh komponen, telah melalui reviu berjenjang, dan telah sesuai dengan alur logika pengawasan.',
                    4 => 'DPP disusun dengan metodologi dan mekanisme konsisten antar penugasan.',
                    5 => 'DPP terintegrasi dalam sistem pengawasan organisasi.',
                ],
            ],
        ],
        'element2_perencanaan_penugasan' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element2PerencanaanPenugasan::class,
            'edit_log_model' => Element2PerencanaanPenugasanEditLog::class,
            'page_title' => 'Element 2 : Profesionalisme Penugasan',
            'subtopic_code' => 'S2',
            'subtopic_title' => 'Sub Topik 2 - Perencanaan Penugasan',
            'info_modal_title' => 'Informasi Level Sub Topik 2 - Perencanaan Penugasan',
            'notification_title' => 'Element 2 - Perencanaan Penugasan',
            'rows' => [
                1 => 'Penyusunan Dokumen Perencanaan',
                2 => 'Penyusunan Program Kerja',
            ],
            'weights' => [
                1 => 0.30,
                2 => 0.70,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Proses perencanaan penugasan pengawasan belum memiliki struktur dan metodologi yang baku.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'APIP mulai memiliki dokumen perencanaan penugasan, namun sifatnya masih administratif dan belum memenuhi seluruh parameter substantif.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Perencanaan telah dilaksanakan secara sistematis untuk setiap penugasan.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Perencanaan telah dilaksanakan secara sistematis untuk setiap penugasan dengan mengacu pada desain penugasan pengawasan (DPP), sudah terintegrasi dalam organisasi. Dokumen perencanaan dan program kerja disusun dengan metodologi yang sistematis, direviu berjenjang, dan ditetapkan.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'APIP telah mengembangkan sistem perencanaan pengawasan yang bersifat dinamis, digital, dan strategis. Dokumen perencanaan dan program kerja tidak hanya berfungsi untuk mengatur pelaksanaan penugasan, tetapi juga digunakan sebagai alat perencanaan strategis kelembagaan dan evaluasi.',
                ],
            ],
            'statement_level_hints' => [
                'Penyusunan Dokumen Perencanaan' => [
                    1 => 'Tidak ada dokumen perencanaan.',
                    2 => 'Dokumen perencanaan bersifat administratif dan belum lengkap.',
                    3 => 'Dokumen perencanaan lengkap, direviu, disahkan, dan selaras dengan Desain Penugasan Pengawasan (DPP).',
                    4 => 'Dokumen perencanaan disusun dengan mekanisme dan format yang konsisten, serta dimonitor pelaksanaannya.',
                    5 => 'Dokumen perencanaan adaptif, terintegrasi, dan menjadi bagian dari sistem perencanaan pengawasan kelembagaan.',
                ],
                'Penyusunan Program Kerja' => [
                    1 => 'Tidak ada program kerja.',
                    2 => 'Program kerja tersedia namun belum direviu dan belum disahkan.',
                    3 => 'Program kerja tersedia, direviu, disahkan, dan selaras dengan DPP.',
                    4 => 'Program kerja adaptif, fokus pada area kunci, dan peningkatan kualitas TKMRPI.',
                    5 => 'Program kerja digital, terintegrasi, dan menjadi bagian dari sistem manajemen kinerja dan sumber daya organisasi.',
                ],
            ],
        ],
        'element2_pelaksanaan_penugasan' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element2PelaksanaanPenugasan::class,
            'edit_log_model' => Element2PelaksanaanPenugasanEditLog::class,
            'page_title' => 'Element 2 : Profesionalisme Penugasan',
            'subtopic_code' => 'S3',
            'subtopic_title' => 'Sub Topik 3 - Pelaksanaan Penugasan',
            'info_modal_title' => 'Informasi Level Sub Topik 3 - Pelaksanaan Penugasan',
            'notification_title' => 'Element 2 - Pelaksanaan Penugasan',
            'rows' => [
                1 => 'Identifikasi dan Pengumpulan data/informasi',
                2 => 'Pelaksanaan Pedoman/Program Kerja',
                3 => 'Penyusunan Opini, Simpulan, dan Rekomendasi',
            ],
            'weights' => [
                1 => 0.30,
                2 => 0.40,
                3 => 0.30,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Proses pelaksanaan penugasan pengawasan belum sistematis dan tidak terdokumentasi.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Proses pelaksanaan penugasan sudah berdasarkan metodologi yang ditetapkan namun belum konsisten.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Pelaksanaan pengawasan mengikuti metodologi dan langkah kerja sebagaimana tercantum dalam desain dan program kerja pengawasan.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Pelaksanaan pengawasan dilaksanakan konsisten di seluruh bidang dengan standar metodologi seragam. Penggunaan teknologi mulai diterapkan untuk dokumentasi, analisis, dan reviu hasil.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Pelaksanaan pengawasan sudah berbasis data dan teknologi secara menyeluruh. Pengujian dan analisis dilakukan secara continuous, menggunakan data real-time dari berbagai sumber.',
                ],
            ],
            'statement_level_hints' => [
                'Identifikasi dan Pengumpulan data/informasi' => [
                    1 => 'Identifikasi dan Pengumpulan data/informasi dilakukan secara sporadis dan tidak sistematis.',
                    2 => 'Identifikasi dan Pengumpulan data/informasi sudah mulai dilakukan secara sistematis namun belum dilakukan pengujian secara memadai.',
                    3 => 'Identifikasi dan Pengumpulan data/informasi dilakukan secara sistematis dan telah dilakukan pengujian secara memadai.',
                    4 => 'Identifikasi dan Pengumpulan data/informasi dilakukan secara sistematis dan telah dilakukan pengujian secara memadai, serta telah mulai memanfaatkan teknologi.',
                    5 => 'Identifikasi dan Pengumpulan data/informasi terintegrasi dengan teknologi dan berkelanjutan, memanfaatkan big data, data analytics, dan AI, dari lintas sumber dan periode waktu. Terdapat Sistem Informasi yang mampu menarik, memverifikasi, dan memperbarui data secara otomatis dari berbagai entitas, sehingga hasilnya mendukung analisis real-time dan pengawasan continuous bagi pengambilan keputusan strategis.',
                ],
                'Pelaksanaan Pedoman/Program Kerja' => [
                    1 => 'Pelaksanaan pengawasan tidak mengacu pada program kerja.',
                    2 => 'Pelaksanaan tidak sepenuhnya mengacu pada program kerja; sudah ada dokumentasi tetapi belum memadai.',
                    3 => 'Pelaksanaan sesuai dengan Program kerja beserta langkah kerja alternatif (jika ada), sesuai dengan peran dalam tim, didokumentasikan secara memadai, telah melalui reviu/supervisi.',
                    4 => 'Pelaksanaan konsisten, berbasis data dan mulai memanfaatkan teknologi, serta dilengkapi pengendalian mutu yang lebih terukur.',
                    5 => 'Pelaksanaan program kerja dilakukan secara terintegrasi dan terdigitalisasi, dengan pembagian peran, supervisi, dan dokumentasi berbasis sistem. Progres dan hasil langkah kerja dimonitor secara real-time melalui platform pengawasan terintegrasi.',
                ],
                'Penyusunan Opini, Simpulan, dan Rekomendasi' => [
                    1 => 'Tidak ada Opini, simpulan, rekomendasi.',
                    2 => 'Opini, simpulan, rekomendasi sudah ada namun belum didukung kertas kerja yang memadai dan belum sepenuhnya menjawab tujuan penugasan.',
                    3 => 'Opini, simpulan, rekomendasi didukung dengan kertas kerja yang memadai dan menjawab tujuan pengawasan, serta telah dibahas bersama klien dan/atau entitas mitra untuk memperoleh tanggapan dan rencana tindak perbaikan.',
                    4 => 'Opini, simpulan, dan rekomendasi disusun konsisten dan terdokumentasi dengan sistematis, dengan pengendalian mutu dan dukungan teknologi.',
                    5 => 'Opini, Simpulan, Rekomendasi disusun secara memadai, berbasis analitik data, pembelajaran lintas penugasan, dan pandangan ke depan, yaitu kemampuan APIP untuk mengidentifikasi potensi risiko dan tantangan tata kelola di masa mendatang, serta memberikan rekomendasi yang bersifat antisipatif, dan strategis.',
                ],
            ],
        ],
        'element2_komunikasi_hasil' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element2KomunikasiHasil::class,
            'edit_log_model' => Element2KomunikasiHasilEditLog::class,
            'page_title' => 'Element 2 : Profesionalisme Penugasan',
            'subtopic_code' => 'S4',
            'subtopic_title' => 'Sub Topik 4 - Komunikasi Hasil Penugasan',
            'info_modal_title' => 'Informasi Level Sub Topik 4 - Komunikasi Hasil Penugasan',
            'notification_title' => 'Element 2 - Komunikasi Hasil Penugasan',
            'rows' => [
                1 => 'Kelengkapan Atribut Laporan dan Ketepatan Waktu Penyampaian',
            ],
            'weights' => [
                1 => 1.00,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Komunikasi hasil pengawasan bersifat sporadis, tidak terstruktur.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Komunikasi hasil pengawasan sudah dilakukan secara formal dalam bentuk laporan, tetapi fokusnya masih administratif dan berorientasi pada penyampaian kewajiban, bukan nilai tambah.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Komunikasi hasil pengawasan dilakukan secara sistematis. Hasil pengawasan disampaikan tepat waktu kepada pihak yang relevan, disertai pembahasan untuk memastikan pemahaman dan kesepakatan tindak lanjut.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Laporan hasil pengawasan konsisten dalam format, mutu, dan kedalaman substansi, serta dilengkapi dengan analisis akar penyebab, dampak, dan signifikansi risiko. Komunikasi hasil pengawasan mendorong perbaikan sistemik dan peningkatan kualitas TKMRPI pada entitas yang diawasi, serta menghasilkan komitmen dan rencana tindak perbaikan.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Komunikasi hasil pengawasan telah bertransformasi menjadi alat strategis kelembagaan dan kebijakan publik. Laporan dan hasil pengawasan disajikan dalam format yang interaktif, analitik, dan foresight-oriented menyajikan pola risiko lintas sektor dan rekomendasi antisipatif terhadap tantangan masa depan. APIP memanfaatkan teknologi dan sistem manajemen pengetahuan untuk memastikan hasil pengawasan berdampak luas, kredibel, dan berkelanjutan.',
                ],
            ],
            'statement_level_hints' => [
                'Kelengkapan Atribut Laporan dan Ketepatan Waktu Penyampaian' => [
                    1 => 'Tidak ada laporan atau laporan masih disusun secara sporadis.',
                    2 => 'Laporan disusun namun belum memenuhi seluruh atribut; belum ada kejelasan batas waktu penyampaian laporan.',
                    3 => 'Laporan memenuhi seluruh atribut sesuai dengan sifat dan jenis penugasan, melalui reviu berjenjang, terdapat rencana tindak perbaikan; laporan disampaikan secara formal kepada pihak yang tepat dan terdapat standar batas waktu penyampaian laporan.',
                    4 => 'Penyusunan laporan memanfaatkan pendekatan visualisasi data, analitik interaktif, dan insight foresight; disampaikan tepat waktu sesuai standar batas waktu.',
                    5 => 'Laporan disusun menggunakan platform digital dan analitik data lintas penugasan, menyajikan visualisasi interaktif.',
                ],
            ],
        ],
        'element2_pemantauan_tindak_lanjut' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element2PemantauanTindakLanjut::class,
            'edit_log_model' => Element2PemantauanTindakLanjutEditLog::class,
            'page_title' => 'Element 2 : Profesionalisme Penugasan',
            'subtopic_code' => 'S5',
            'subtopic_title' => 'Sub Topik 5 - Pemantauan Tindak Lanjut',
            'info_modal_title' => 'Informasi Level Sub Topik 5 - Pemantauan Tindak Lanjut',
            'notification_title' => 'Element 2 - Pemantauan Tindak Lanjut',
            'rows' => [
                1 => 'Pelaksanaan Pemantauan Tindak Lanjut Rekomendasi',
                2 => 'Evaluasi Efektivitas Tindak Lanjut Rekomendasi',
                3 => 'Konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra',
            ],
            'weights' => [
                1 => 0.40,
                2 => 0.30,
                3 => 0.30,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Tidak ada mekanisme pemantauan tindak lanjut formal.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Pemantauan tindak lanjut belum dilakukan secara sistematis; hasilnya tidak terdokumentasi.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Pemantauan tindak lanjut dilakukan rutin sesuai pedoman dan berbasis bukti.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Pemantauan tindak lanjut terintegrasi dengan sistem informasi pengawasan.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Pemantauan tindak lanjut secara real time dan berkelanjutan.',
                ],
            ],
            'statement_level_hints' => [
                'Pelaksanaan Pemantauan Tindak Lanjut Rekomendasi' => [
                    1 => 'APIP tidak memiliki mekanisme, kebijakan SOP, maupun Pedoman pemantauan tindak lanjut hasil pengawasan.',
                    2 => 'APIP berupaya memantau tindak lanjut hasil pengawasan namun belum terjadwal atau tidak konsisten secara berkala.',
                    3 => 'APIP telah melakukan pemantauan tindak lanjut hasil pengawasan secara berkala menggunakan sistem atau format baku.',
                    4 => 'APIP memiliki sistem informasi yang mengintegrasikan pemantauan tindak lanjut hasil pengawasan dengan seluruh proses bisnis pengawasan.',
                    5 => 'APIP melakukan pemantauan secara berkelanjutan dan terotomatisasi, serta terintegrasi dengan manajemen risiko dan manajemen kinerja.',
                ],
                'Evaluasi Efektivitas Tindak Lanjut Rekomendasi' => [
                    1 => 'APIP tidak melakukan pengujian terhadap dampak dan efektivitas rekomendasi yang diberikan kepada klien dan/atau entitas mitra.',
                    2 => 'APIP melakukan pengujian, namun terbatas pada administrasi.',
                    3 => 'APIP melakukan pengujian dengan pendekatan Manajemen Risiko dan digunakan untuk perbaikan kebijakan dan pengendalian intern.',
                    4 => 'APIP secara berkelanjutan, melakukan pengujian efektivitas dan meningkatkan serta memperbaiki tata kelola, manajemen risiko, dan pengendalian intern.',
                    5 => 'APIP secara berkelanjutan melakukan pengujian efektivitas tindak lanjut yang terintegrasi dalam sistem manajemen risiko dan kinerja organisasi, serta memanfaatkannya untuk pembelajaran dan inovasi tata kelola.',
                ],
                'Konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra' => [
                    1 => 'APIP tidak melakukan konfirmasi kepada Manajemen Klien dan/atau Entitas Mitra.',
                    2 => 'APIP melakukan konfirmasi, namun belum terstruktur.',
                    3 => 'APIP melakukan konfirmasi sesuai prosedur, dengan pendekatan berbasis risiko, serta menilai efektivitas tindak lanjut.',
                    4 => 'APIP melakukan konfirmasi secara real time dan digunakan untuk analisis strategis.',
                    5 => 'APIP mengelola tindak lanjut rekomendasi sebagai sumber insight strategis untuk perbaikan sistemik dan pencegahan risiko berulang di seluruh ekosistem pengawasan.',
                ],
            ],
        ],
        'element2_pengendalian_kualitas' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element2PengendalianKualitas::class,
            'edit_log_model' => Element2PengendalianKualitasEditLog::class,
            'page_title' => 'Element 2 : Profesionalisme Penugasan',
            'subtopic_code' => 'S6',
            'subtopic_title' => 'Sub Topik 6 - Pengendalian Kualitas Penugasan',
            'info_modal_title' => 'Informasi Level Sub Topik 6 - Pengendalian Kualitas Penugasan',
            'notification_title' => 'Element 2 - Pengendalian Kualitas Penugasan',
            'rows' => [
                1 => 'Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan',
                2 => 'Melaksanakan Penjaminan Kualitas Internal',
                3 => 'Melaksanakan Telaah Sejawat Ekstern',
            ],
            'weights' => [
                1 => 0.40,
                2 => 0.30,
                3 => 0.30,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Tidak ada QA; mutu tergantung individu.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Ada supervisi, review dilakukan, tapi secara sporadik dan tanpa pedoman.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'QAIP intern dan ekstern berjalan; hasil penugasan direview sesuai standar; terdapat umpan balik dan digunakan untuk pengembangan perbaikan secara berkelanjutan.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'QAIP intern dan ekstern lengkap; hasil QA digunakan untuk perbaikan berkelanjutan.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'QAIP terintegrasi dengan manajemen kinerja dan mendukung pengambilan keputusan strategis.',
                ],
            ],
            'statement_level_hints' => [
                'Melaksanakan Reviu Berjenjang pada Setiap Tahapan Penugasan Pengawasan' => [
                    1 => 'APIP belum melaksanakan reviu berjenjang.',
                    2 => 'APIP telah melaksanakan reviu berjenjang, namun belum rutin dan belum terdapat pedoman baku.',
                    3 => 'APIP telah melaksanakan reviu berjenjang sesuai pedoman dan dilakukan pada setiap tahapan penugasan.',
                    4 => 'APIP telah melaksanakan reviu berjenjang secara sistematis pada semua tahapan penugasan dan menjadi bagian dari mekanisme kendali mutu organisasi.',
                    5 => 'APIP telah melaksanakan reviu berjenjang pada setiap tahapan dan menjadi bagian dari continuous quality improvement yang terotomasi, serta terintegrasi dengan manajemen kinerja.',
                ],
                'Melaksanakan Penjaminan Kualitas Internal' => [
                    1 => 'APIP belum memiliki mekanisme Penilaian Intern Periodik.',
                    2 => 'Penilaian Intern Periodik dilakukan secara sporadik dan belum sistematis.',
                    3 => 'Penilaian Intern Periodik dilaksanakan secara formal, terstruktur, dan menghasilkan perbaikan mutu.',
                    4 => 'Penilaian Intern Periodik berkelanjutan, terintegrasi, dan berdampak pada peningkatan kapabilitas APIP.',
                    5 => 'Penilaian Intern Periodik menjadi bagian dari siklus continuous improvement dan mendorong peningkatan kapabilitas kelembagaan.',
                ],
                'Melaksanakan Telaah Sejawat Ekstern' => [
                    1 => 'APIP belum memiliki mekanisme Telaah Sejawat Ekstern.',
                    2 => 'Telaah Sejawat dilakukan insidental dan belum sistematis.',
                    3 => 'Telaah Sejawat dilaksanakan sesuai pedoman dan ditindaklanjuti secara formal.',
                    4 => 'Telaah Sejawat dilaksanakan secara berkala dan hasilnya diintegrasikan dalam peningkatan kapabilitas APIP.',
                    5 => 'Telaah Sejawat menjadi siklus pembelajaran strategis lintas APIP dan katalis peningkatan tata kelola pengawasan nasional.',
                ],
            ],
        ],
        'element3_perencanaan_pengawasan' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element3PerencanaanPengawasan::class,
            'edit_log_model' => Element3PerencanaanPengawasanEditLog::class,
            'page_title' => 'Element 3 : Manajemen Pengawasan',
            'subtopic_code' => 'S1',
            'subtopic_title' => 'Sub Topik 1 - Perencanaan Pengawasan',
            'info_modal_title' => 'Informasi Level Sub Topik 1 - Perencanaan Pengawasan',
            'notification_title' => 'Element 3 - Perencanaan Pengawasan',
            'rows' => [
                1 => 'Struktur Perencanaan',
                2 => 'Fokus dan Sasaran Pengawasan',
                3 => 'Adaptif',
                4 => 'Keterlibatan Manajemen',
            ],
            'weights' => [
                1 => 0.15,
                2 => 0.40,
                3 => 0.20,
                4 => 0.25,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Perencanaan pengawasan belum terstruktur, belum memiliki fokus, dan belum mengakomodasi adanya perubahan, serta tidak ada partisipasi aktif manajemen dalam perencanaan.',
                ],
                [
                    'level' => 2,
                    'score_range' => '1,99 - 2,98',
                    'description' => 'Perencanaan pengawasan telah dibuat namun belum terstruktur secara komprehensif, fokus dan sasaran pengawasannya belum tepat menyasar prioritas K/L/D, telah mengakomodasi perubahan namun dilakukan tanpa didukung basis analisis yang jelas, dan hanya melibatkan partisipasi aktif APIP dalam penyusunannya.',
                ],
                [
                    'level' => 3,
                    'score_range' => '2,99 - 3,98',
                    'description' => 'Perencanaan pengawasan telah terstruktur secara komprehensif dengan berbasis risiko, telah berfokus pada program/kegiatan prioritas K/L/D, dan mengakomodasi adanya perubahan, serta didukung oleh partisipasi aktif pimpinan APIP.',
                ],
                [
                    'level' => 4,
                    'score_range' => '3,99 - 4,99',
                    'description' => 'Perencanaan pengawasan telah terstruktur secara komprehensif dengan dukungan produk perencanaan turunan, terintegrasi dengan manajemen risiko K/L/D, telah berfokus pada program/kegiatan prioritas K/L/D saat ini maupun jangka panjang, mengakomodasi adanya perubahan, dan terdapat partisipasi aktif pimpinan ataupun manajemen K/L/D dan APIP.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Perencanaan pengawasan memiliki struktur komprehensif dan disempurnakan untuk kebutuhan di masa depan, dukungan inovasi dan praktik terbaik, fokus dan menyasar pada program/kegiatan prioritas bagi K/L/D maupun nasional serta bersifat proyeksi atas TKMRPI, mengakomodasi perubahan, serta terdapat partisipasi aktif pimpinan K/L/D dan APIP.',
                ],
            ],
            'statement_level_hints' => [
                'Struktur Perencanaan' => [
                    1 => 'Tidak terdapat rencana pengawasan yang terstruktur.',
                    2 => 'Rencana pengawasan telah dibuat, namun belum memiliki struktur rencana pengawasan yang komprehensif sehingga alokasi sumber daya yang ditetapkan belum mempertimbangkan tingkat risiko area pengawasan.',
                    3 => 'Rencana pengawasan telah dibuat dengan struktur yang komprehensif, sesuai dengan standar, dan berbasis risiko. Sehingga alokasi sumber daya yang ditetapkan sudah mempertimbangkan tingkat risiko serta dampak adanya pembatasan alokasi sumber daya.',
                    4 => 'Rencana pengawasan telah komprehensif, fleksibel, memanfaatkan inovasi teknologi, dan mempertimbangkan integrasi manajemen risiko.',
                    5 => 'Rencana pengawasan telah komprehensif, berbasis risiko, didukung produk turunan, serta telah menunjukkan berbagai proses penyempurnaan dalam perencanaan untuk mempersiapkan kebutuhan di masa depan maupun dinamika K/L/D dari tahun ke tahun.',
                ],
                'Fokus dan Sasaran Pengawasan' => [
                    1 => 'Fokus dan sasaran/lingkup pengawasan belum ada, sehingga strategi pengawasannya bersifat reaktif.',
                    2 => 'Fokus dan sasaran/lingkup pengawasan yang ditetapkan berbasis pertimbangan manajemen, sehingga strategi pengawasan yang ditetapkan bersifat compliance based.',
                    3 => 'Fokus dan sasaran/lingkup pengawasan telah ditetapkan berbasis risiko sehingga area pengawasannya menyasar pada program prioritas K/L/D serta strategi pengawasannya berbasis risiko (pengawasan intern berbasis risiko).',
                    4 => 'Fokus dan sasaran/lingkup pengawasan disusun melalui proses penilaian kematangan manajemen risiko, mempertimbangkan prioritas jangka menengah K/L/D serta dalam rangka membangun opini makro/overall opinion.',
                    5 => 'Fokus dan sasaran/lingkup pengawasan telah menghasilkan strategi pengawasan yang bersifat insight dan foresight serta mempertimbangkan ketercapaian visi dan target K/L/D, lintas sektor/instansi, dan/atau prioritas nasional.',
                ],
                'Adaptif' => [
                    1 => 'Perencanaan pengawasan belum selaras dengan sumber informasi kinerja, profil/register risiko, dan pengendalian.',
                    2 => 'Perencanaan pengawasan sudah menyelaraskan dengan sumber informasi kinerja, profil/register risiko, dan pengendalian namun belum digunakan untuk merancang/memperbaiki area pengawasan.',
                    3 => 'Perencanaan pengawasan sudah menyelaraskan dengan sumber informasi kinerja, risiko, dan pengendalian dan digunakan untuk merancang/memperbaiki area pengawasan.',
                    4 => 'Perencanaan pengawasan sudah menyelaraskan secara terintegrasi dengan manajemen risiko dan manajemen kinerja organisasi dan prioritas pembangunan nasional, dengan mempertimbangkan perubahan lingkungan strategis organisasi, serta digunakan untuk melakukan konvergensi area pengawasan.',
                    5 => 'Perencanaan pengawasan didasarkan atas keseluruhan manajemen risiko serta peluang perbaikan masa depan melalui inovasi, teknologi, dan praktik terbaik pengawasan intern.',
                ],
                'Keterlibatan Manajemen' => [
                    1 => 'Partisipasi aktif pimpinan ataupun manajemen K/L/D dan APIP belum ada.',
                    2 => 'Partisipasi aktif hanya dilakukan oleh pimpinan APIP dalam perencanaan pengawasan.',
                    3 => 'Partisipasi aktif dilakukan oleh pimpinan ataupun manajemen K/L/D dan APIP dalam perencanaan pengawasan.',
                    4 => 'Partisipasi aktif pimpinan ataupun manajemen K/L/D dan APIP dalam perencanaan pengawasan, serta mengintegrasikannya dengan proses manajemen risiko di lingkungan organisasi/instansi pemerintah K/L/D.',
                    5 => 'Partisipasi aktif pimpinan ataupun manajemen K/L/D dan APIP dalam perencanaan pengawasan yang terintegrasi dengan perencanaan strategis K/L/D serta berperan aktif memastikan perencanaan pengawasan telah mendukung bagian pengawasan program nasional/lintas sektor.',
                ],
            ],
        ],
        'element3_pelaporan_manajemen_kld' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element3PelaporanManajemenKld::class,
            'edit_log_model' => Element3PelaporanManajemenKldEditLog::class,
            'page_title' => 'Element 3 : Manajemen Pengawasan',
            'subtopic_code' => 'S2',
            'subtopic_title' => 'Sub Topik 2 - Pelaporan kepada Manajemen K/L/D',
            'info_modal_title' => 'Informasi Level Sub Topik 2 - Pelaporan kepada Manajemen K/L/D',
            'notification_title' => 'Element 3 - Pelaporan kepada Manajemen K/L/D',
            'rows' => [
                1 => 'Kualitas Penyajian Laporan',
                2 => 'Kualitas Rekomendasi dan Nilai Tambah Strategis',
                3 => 'Pemanfaatan oleh Manajemen',
            ],
            'weights' => [
                1 => 0.15,
                2 => 0.60,
                3 => 0.25,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'APIP belum memiliki pola komunikasi yang terstruktur kepada pimpinan K/L/D atau terdapat pelaporan formal tertulis berupa narasi namun tidak disampaikan secara berkala. Laporan hanya berisi deskripsi permasalahan kepatuhan administratif dengan rekomendasi yang normatif sehingga tidak bermanfaat.',
                ],
                [
                    'level' => 2,
                    'score_range' => '1,99 - 2,98',
                    'description' => 'APIP telah mengkomunikasikan secara formal dan berkala kepada pimpinan K/L/D namun hanya berorientasi pada pemenuhan mandat/regulasi. Laporan hanya berisi deskripsi permasalahan kepatuhan administratif dengan rekomendasi yang bersifat teknis/operasional.',
                ],
                [
                    'level' => 3,
                    'score_range' => '2,99 - 3,98',
                    'description' => 'APIP telah mengkomunikasikan secara independen, berkala, dan formal (tertulis dan tidak tertulis) langsung kepada pimpinan organisasi K/L/D sesuai standar yang berlaku. Laporan telah mengungkap permasalahan hingga ke akar penyebab dengan rumusan rekomendasi yang bersifat strategis maupun operasional.',
                ],
                [
                    'level' => 4,
                    'score_range' => '3,99 - 4,99',
                    'description' => 'APIP telah mengkomunikasikan kepada pimpinan K/L/D dalam beragam bentuk (tidak terbatas pada laporan formal) dan disampaikan sesuai kebutuhan pengguna. Laporan telah mengungkap hasil integrasi manajemen risiko, permasalahan hingga ke akar penyebab dengan rumusan rekomendasi strategis yang memberikan nilai tambah bagi organisasi. Seluruh rekomendasi dimanfaatkan untuk perbaikan dan digunakan dalam pengambilan keputusan strategis organisasi.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'APIP telah mengkomunikasikan kepada pimpinan K/L/D dengan didukung penggunaan teknologi informasi serta disampaikan sesuai kebutuhan pengguna. Laporan telah mengungkap hasil internalisasi manajemen risiko secara holistik (serta peluang perbaikan masa depan), permasalahan hingga ke akar penyebab dengan rumusan rekomendasi strategis yang memberikan nilai tambah bagi organisasi maupun secara nasional. Seluruh rekomendasi dimanfaatkan untuk perbaikan dan digunakan dalam pengambilan keputusan strategis organisasi maupun pada level nasional.',
                ],
            ],
            'statement_level_hints' => [
                'Kualitas Penyajian Laporan' => [
                    1 => 'APIP belum memiliki pola atau mekanisme komunikasi yang terstruktur atas konvergensi hasil pengawasan kepada pimpinan K/L/D.',
                    2 => 'APIP mengkomunikasikan konvergensi hasil pengawasan kepada pimpinan K/L/D hanya berorientasi pada pemenuhan kepatuhan regulasi. Penyajiannya sudah didukung dengan angka/tabel/matriks yang relevan.',
                    3 => 'APIP mengomunikasikan secara formal, independen, dan berkala (baik tertulis maupun tidak tertulis) konvergensi hasil pengawasan secara langsung kepada pimpinan K/L/D. Substansi/laporan mencakup angka, tabel, atau matriks yang relevan dan penyajiannya sudah didukung dengan infografis yang substantif.',
                    4 => 'APIP telah mengomunikasikan konvergensi hasil pengawasan secara fleksibel dan terintegrasi dengan manajemen risiko yang mencakup beragam data/informasi secara tertulis maupun tidak tertulis sesuai kebutuhan pimpinan K/L/D (secara berkala maupun waktu tertentu/insidental).',
                    5 => 'APIP telah mengkomunikasikan konvergensi hasil pengawasan kepada pimpinan K/L/D secara real-time dan dengan memanfaatkan inovasi teknologi informasi.',
                ],
                'Kualitas Rekomendasi dan Nilai Tambah Strategis' => [
                    1 => 'Laporan hanya berisi deskripsi permasalahan yang berupa kepatuhan administratif. Rekomendasi cenderung normatif.',
                    2 => 'Laporan konvergensi hasil pengawasan hanya berisi deskripsi permasalahan yang berupa kepatuhan administratif. Rekomendasi hanya menyasar penyelesaian permasalahan yang bersifat teknis/operasional dan belum ke permasalahan strategis.',
                    3 => 'Laporan konvergensi hasil pengawasan telah mengungkapkan permasalahan strategis yang disintesiskan dari berbagai hasil pengawasan individu dan disertai dengan akar penyebabnya. Rekomendasi telah menyasar pada aspek-aspek krusial dan memberikan nilai tambah strategis terhadap kebutuhan pimpinan K/L/D.',
                    4 => 'Laporan konvergensi hasil pengawasan telah mengungkapkan permasalahan strategis yang disintesiskan dari berbagai hasil pengawasan individu disertai dengan akar penyebabnya. Rekomendasi telah menyasar pada aspek yang krusial dan memberikan nilai tambah strategis terhadap kinerja organisasi atau instansi pemerintah K/L/D dan lintas instansi.',
                    5 => 'Laporan konvergensi hasil pengawasan telah mengungkapkan permasalahan strategis yang disintesiskan dari berbagai hasil pengawasan individu disertai dengan akar penyebabnya. Rekomendasi telah menyasar pada aspek yang krusial dan memberikan nilai tambah strategis terhadap organisasi atau instansi pemerintah K/L/D, lintas instansi, dan prioritas nasional.',
                ],
                'Pemanfaatan oleh Manajemen' => [
                    1 => 'Rekomendasi dan informasi yang tersaji pada konvergensi hasil pengawasan tidak dimanfaatkan oleh pimpinan K/L/D.',
                    2 => 'Rekomendasi dan informasi yang tersaji pada konvergensi hasil pengawasan digunakan dan ditindaklanjuti oleh pimpinan K/L/D, namun pemanfaatan hanya untuk perbaikan administratif.',
                    3 => 'Rekomendasi dan informasi yang tersaji pada konvergensi hasil pengawasan ditindaklanjuti, serta terdapat rekomendasi/informasi pengawasan kunci yang digunakan sebagai pengambilan keputusan strategis atas perbaikan pelaksanaan program utama K/L/D.',
                    4 => 'Rekomendasi dan informasi yang tersaji pada konvergensi hasil pengawasan ditindaklanjuti, serta terdapat rekomendasi/informasi pengawasan kunci yang digunakan sebagai pengambilan keputusan strategis atas perbaikan prioritas K/L/D dan lintas instansi.',
                    5 => 'Rekomendasi dan informasi yang tersaji pada konvergensi hasil pengawasan ditindaklanjuti, serta terdapat rekomendasi/informasi pengawasan kunci yang digunakan sebagai pengambilan keputusan strategis atas perbaikan program prioritas nasional.',
                ],
            ],
        ],
        'element4_manajemen_kinerja' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element4ManajemenKinerja::class,
            'edit_log_model' => Element4ManajemenKinerjaEditLog::class,
            'page_title' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'subtopic_code' => 'S1',
            'subtopic_title' => 'Sub Topik 1 - Manajemen Kinerja',
            'info_modal_title' => 'Informasi Level Sub Topik 1 - Manajemen Kinerja',
            'notification_title' => 'Element 4 - Manajemen Kinerja',
            'rows' => [
                1 => 'Perencanaan Kinerja',
                2 => 'Pengorganisasian Kinerja',
                3 => 'Pengendalian Kinerja',
            ],
            'weights' => [
                1 => 0.50,
                2 => 0.25,
                3 => 0.25,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Kinerja belum terdefinisikan dan terkelola.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Kinerja telah didefinisikan dengan cukup jelas, namun masih berfokus pada output/administratif dan dikelola secara sederhana.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Kinerja telah dirumuskan dan didefinisikan dengan sistematis dan jelas, berorientasi hasil sesuai dengan ekspektasi stakeholders utama, serta telah terorganisir dan terkendali dengan baik.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Kinerja direncanakan dan diorganisir secara adaptif dengan konteks strategis, dengan pemantauan dan perbaikan kinerja yang terintegrasi.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Kinerja direncanakan dan diorganisir secara responsif dengan kebutuhan masa depan, dengan pengendalian kinerja kematangan tinggi.',
                ],
            ],
            'statement_level_hints' => [
                'Perencanaan Kinerja' => [
                    1 => 'Belum memiliki ukuran dan target kinerja, atau telah memiliki ukuran dan target kinerja, namun belum jelas didefinisikan.',
                    2 => 'Target dan indikator kinerja telah didefinisikan dengan jelas, namun masih berfokus pada output/administratif.',
                    3 => 'Target dan indikator kinerja telah didefinisikan secara jelas dan berorientasi hasil.',
                    4 => 'Perencanaan kinerja adaptif.',
                    5 => 'Perencanaan kinerja responsif.',
                ],
                'Pengorganisasian Kinerja' => [
                    1 => 'Struktur dan pembagian kinerja belum didefinisikan dengan jelas.',
                    2 => 'Struktur dan pembagian kinerja telah didefinisikan dengan jelas, namun masih sederhana.',
                    3 => 'Struktur dan pembagian kinerja telah didefinisikan dengan jelas dan memadai.',
                    4 => 'Pengorganisasian kinerja adaptif.',
                    5 => 'Pengorganisasian kinerja responsif.',
                ],
                'Pengendalian Kinerja' => [
                    1 => 'Belum memiliki mekanisme pengendalian kinerja/mekanisme pengendalian kinerja yang ada tidak jelas.',
                    2 => 'Telah memiliki mekanisme pengendalian kinerja, namun masih sederhana.',
                    3 => 'Telah memiliki mekanisme pengendalian kinerja yang jelas dan memadai.',
                    4 => 'Pengendalian kinerja yang terintegrasi.',
                    5 => 'Pengendalian kinerja kematangan tinggi, strategis, dan dinamis.',
                ],
            ],
        ],
        'element4_mekanisme_pendanaan' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element4MekanismePendanaan::class,
            'edit_log_model' => Element4MekanismePendanaanEditLog::class,
            'page_title' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'subtopic_code' => 'S2',
            'subtopic_title' => 'Sub Topik 2 - Manajemen Sumber Daya Keuangan',
            'info_modal_title' => 'Informasi Level Sub Topik 2 - Manajemen Sumber Daya Keuangan',
            'notification_title' => 'Element 4 - Manajemen Sumber Daya Keuangan',
            'rows' => [
                1 => 'Perencanaan dan Kecukupan Anggaran',
                2 => 'Penggunaan dan Fleksibilitas Anggaran',
            ],
            'weights' => [
                1 => 0.50,
                2 => 0.50,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Pendanaan bersifat ad-hoc dan perencanaan kebutuhan belum terbangun, dengan fokus penggunaan pada aspek operasional.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Anggaran sudah dialokasikan khusus dengan perhitungan sederhana dan fleksibilitas yang kaku. Anggaran banyak dialokasikan/digunakan untuk pengawasan yang kurang urgen, dengan penggunaan yang inefisien.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Anggaran memenuhi kebutuhan pelaksanaan fungsi pengawasan intern dan diprioritaskan untuk aktivitas/pengawasan yang urgen, sesuai dengan ekspektasi stakeholders utama. Memiliki mekanisme penyesuaian anggaran, dengan penggunaan anggaran proporsional/wajar.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Anggaran memenuhi kebutuhan pelaksanaan fungsi pengawasan intern dan diprioritaskan untuk aktivitas/pengawasan yang urgen, termasuk kebutuhan jangka menengah/panjang dan mendukung inovasi APIP. Memiliki fleksibilitas dan efisiensi penggunaan anggaran.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Anggaran memenuhi kebutuhan pelaksanaan fungsi pengawasan intern dan diprioritaskan untuk aktivitas/pengawasan yang urgen termasuk target-target lintas sektor/K/L/D dan mendukung inovasi untuk stakeholders utama APIP/masyarakat. Penggunaan anggaran fleksibel, antisipatif, dan bernilai tambah.',
                ],
            ],
            'statement_level_hints' => [
                'Perencanaan dan Kecukupan Anggaran' => [
                    1 => 'Pendanaan ad-hoc dan belum memiliki mekanisme perhitungan kebutuhan anggaran.',
                    2 => 'Pendanaan sudah dialokasikan khusus untuk fungsi pengawasan intern, dengan perhitungan anggaran secara sederhana.',
                    3 => 'Anggaran dihitung dan disediakan sesuai kebutuhan fungsi pengawasan intern, sesuai dengan program prioritas K/L/D.',
                    4 => 'Anggaran dihitung dan disediakan sesuai kebutuhan fungsi pengawasan intern, sesuai dengan program prioritas K/L/D tahun bersangkutan dan jangka menengah/panjang K/L/D, serta mendukung penciptaan inovasi/penelitian dan pengembangan yang bermanfaat untuk organisasi APIP.',
                    5 => 'Anggaran dihitung dan disediakan sesuai kebutuhan fungsi pengawasan intern, sesuai dengan program prioritas K/L/D tahun bersangkutan dengan target-target lintas sektor/K/L/D untuk mendukung prioritas nasional, serta mendukung penciptaan inovasi/penelitian dan pengembangan yang bermanfaat untuk stakeholders utama APIP/masyarakat.',
                ],
                'Penggunaan dan Fleksibilitas Anggaran' => [
                    1 => 'Anggaran lebih banyak dialokasikan/digunakan untuk operasional, belum memiliki mekanisme perubahan anggaran.',
                    2 => 'Anggaran telah banyak dialokasikan/digunakan untuk pengawasan, namun kurang urgen. Memiliki mekanisme perubahan anggaran namun dilakukan tanpa didukung basis analisis yang jelas. Terdapat inefisiensi/pemborosan penggunaan anggaran.',
                    3 => 'Anggaran telah diprioritaskan dan digunakan untuk aktivitas/pengawasan yang urgen, sesuai dengan ekspektasi stakeholders utama. Memiliki kemampuan meninjau penggunaan dan penyesuaian anggaran melalui proses tertentu, dengan penggunaan anggaran proporsional/wajar.',
                    4 => 'Anggaran telah diprioritaskan/digunakan untuk aktivitas/pengawasan yang urgen, fleksibel terhadap perubahan, serta digunakan secara efisien.',
                    5 => 'Anggaran telah diprioritaskan/digunakan untuk aktivitas/pengawasan yang urgen, fleksibel dan antisipatif terhadap perubahan, dengan penggunaan anggaran yang bernilai tambah.',
                ],
            ],
        ],
        'element4_perencanaan_sdm_apip' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element4PerencanaanSdmApip::class,
            'edit_log_model' => Element4PerencanaanSdmApipEditLog::class,
            'page_title' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'subtopic_code' => 'S3',
            'subtopic_title' => 'Sub Topik 3 - Perencanaan Kebutuhan dan Pengadaan SDM Pengawasan',
            'info_modal_title' => 'Informasi Level Sub Topik 3 - Perencanaan Kebutuhan dan Pengadaan SDM Pengawasan',
            'notification_title' => 'Element 4 - Perencanaan Kebutuhan dan Pengadaan SDM Pengawasan',
            'rows' => [
                1 => 'Perencanaan Kebutuhan SDM',
                2 => 'Rekrutmen dan Distribusi SDM',
            ],
            'weights' => [
                1 => 0.50,
                2 => 0.50,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Perencanaan SDM masih bersifat ad-hoc, tanpa dasar analisis jabatan dan analisis beban kerja, maupun dokumen kebutuhan, sehingga rekrutmen dan penempatan SDM belum berbasis pada informasi, peta jabatan dan kebutuhan pengawasan.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Perencanaan SDM mulai dibangun melalui analisis jabatan dan analisis beban kerja namun belum sesuai ketentuan dan belum menjadi dasar rencana pemenuhan kebutuhan SDM, sehingga rekrutmen masih bersifat administratif, belum berbasis informasi dan peta jabatan, dan distribusi pegawai belum proporsional.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Perencanaan kebutuhan SDM sudah tersusun melalui analisis jabatan dan analisis beban kerja yang lengkap dan berbasis risiko operasional, sehingga seleksi dan penempatan pegawai dapat dilakukan terbuka dan objektif dengan acuan informasi dan peta jabatan, menghasilkan SDM yang sesuai kualifikasi dan terdistribusi proporsional pada fungsi pengawasan.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Perencanaan SDM telah berbasis analisis jabatan dan analisis beban kerja yang menyasar area paling signifikan dan berisiko strategis sesuai PPBR, sehingga rekrutmen dan penempatan berlangsung kompetitif dan akuntabel dengan perhatian pada risiko strategis, sistem manajemen talenta yang mulai terbentuk belum sepenuhnya dimanfaatkan untuk seleksi dan suksesi manajer dan pimpinan APIP.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Perencanaan SDM telah berbasis analisis jabatan dan analisis beban kerja untuk seluruh jabatan dengan pertimbangan proyeksi kebutuhan nilai tambah pengawasan APIP di masa depan dan diperbarui real-time melalui integrasi sistem kepegawaian, sehingga rekrutmen dan penempatan berlangsung adaptif dan selaras dengan strategi organisasi serta manajemen talenta yang digunakan sebagai dasar seleksi dan suksesi manajer dan pimpinan APIP.',
                ],
            ],
            'statement_level_hints' => [
                'Perencanaan Kebutuhan SDM' => [
                    1 => 'APIP belum melaksanakan proses analisis jabatan dan analisis beban kerja. Tidak terdapat dokumen resmi maupun data pendukung terkait informasi dan peta jabatan (mencakup antara lain: kualifikasi jabatan, tugas pokok, dan syarat jabatan) atau pengusulan kebutuhan SDM.',
                    2 => 'APIP telah memulai pelaksanaan analisis jabatan atau analisis beban kerja, namun pelaksanaannya masih belum sesuai ketentuan, dan outputnya seperti Informasi Jabatan dan Peta Jabatan tidak lengkap sesuai ketentuan. Hasilnya belum digunakan sebagai dasar pengusulan kebutuhan SDM.',
                    3 => 'APIP telah melaksanakan analisis jabatan dan analisis beban kerja sesuai ketentuan dengan mempertimbangkan risiko operasional, dengan output hasil analisis yang lengkap mencakup seluruh jabatan APIP. Pada tahap ini, sudah terdapat dokumen penetapan kebutuhan SDM dan rencana pemenuhan kebutuhan SDM.',
                    4 => 'APIP telah melaksanakan analisis jabatan dan analisis beban kerja seluruh jabatan dengan memperhatikan kebutuhan penanganan pada area paling signifikan dan berisiko strategis bagi organisasi (sesuai Perencanaan Pengawasan Berbasis Risiko/PPBR). Pada tahap ini, sudah terdapat dokumen penetapan kebutuhan dan rencana pemenuhan kebutuhan SDM didasarkan pada analisis tersebut.',
                    5 => 'APIP telah melaksanakan analisis jabatan dan analisis beban kerja seluruh jabatan yang digunakan sebagai dasar pengusulan kebutuhan SDM, dengan memperhatikan proyeksi kebutuhan pemberian nilai tambah pengawasan/layanan APIP di masa mendatang. Pada tahap ini, sudah terdapat dokumen penetapan kebutuhan dan rencana pemenuhan kebutuhan SDM didasarkan pada analisis tersebut. Melalui integrasi ke dalam sistem informasi kepegawaian intern APIP, analisis dilakukan secara real-time terhadap dinamika internal dan eksternal yang berpotensi mengubah kebutuhan SDM (contohnya: perubahan kebijakan, organisasi, atau prioritas pengawasan).',
                ],
                'Rekrutmen dan Distribusi SDM' => [
                    1 => 'Rekrutmen dan penempatan pegawai pengawasan belum memiliki prosedur formal, tidak berbasis kebutuhan sesuai informasi dan peta jabatan, dan belum menjamin kesesuaian jumlah maupun distribusi pegawai terhadap kebutuhan pengawasan.',
                    2 => 'Rekrutmen dilaksanakan untuk memenuhi formasi secara administratif melalui jalur yang sah, namun belum kompetitif, belum merekrut SDM dengan profil yang tepat, dan distribusi pegawai masih belum proporsional.',
                    3 => 'Seleksi dan penempatan pegawai pengawasan dilaksanakan terbuka, objektif, memperhatikan profil risiko, mengacu pada Informasi dan Peta Jabatan sesuai penetapan kebutuhan SDM, sudah menghasilkan SDM yang sesuai kualifikasi dan ditempatkan secara proporsional pada fungsi pengawasan intern.',
                    4 => 'Proses rekrutmen dan penempatan berjalan kompetitif dan akuntabel, memperhatikan risiko strategis, disertai evaluasi berkala atas efektivitas dan pemerataan hasil rekrutmen dan retensi SDM. Sistem manajemen talenta sudah terbentuk namun belum difungsikan sebagai dasar seleksi dan suksesi manajer dan pimpinan APIP.',
                    5 => 'Sistem rekrutmen dan penempatan terintegrasi dengan strategi pengembangan organisasi dan manajemen talenta ASN nasional, dengan memperhatikan proyeksi kebutuhan pemberian nilai tambah pengawasan/layanan APIP di masa mendatang, dilaksanakan adaptif melalui berbagai jalur rekrutmen serta kerja sama profesional, menjamin keberlanjutan dan optimalisasi distribusi SDM pengawasan. Manajemen talenta difungsikan sebagai dasar seleksi dan suksesi manajer dan pimpinan APIP.',
                ],
            ],
        ],
        'element4_pengembangan_sdm_profesional_apip' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element4PengembanganSdmProfesionalApip::class,
            'edit_log_model' => Element4PengembanganSdmProfesionalApipEditLog::class,
            'page_title' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'subtopic_code' => 'S4',
            'subtopic_title' => 'Sub Topik 4 - Pengembangan SDM Profesional APIP',
            'info_modal_title' => 'Informasi Level Sub Topik 4 - Pengembangan SDM Profesional APIP',
            'notification_title' => 'Element 4 - Pengembangan SDM Profesional APIP',
            'rows' => [
                1 => 'Rencana Pengembangan Kompetensi',
                2 => 'Pelaksanaan Pengembangan Kompetensi',
            ],
            'weights' => [
                1 => 0.50,
                2 => 0.50,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'APIP belum memiliki rencana pengembangan kompetensi formal, dan pelatihan masih dilakukan secara insidentil berdasarkan arahan pimpinan tanpa pemenuhan jam pelatihan minimum.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'APIP telah memiliki dan melaksanakan rencana pelatihan tahunan yang masih bersifat sederhana dan administratif, belum berbasis standar kompetensi jabatan, serta belum mendata pemenuhan jam pelatihan minimum secara sistematis.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'APIP melaksanakan pengembangan kompetensi tahunan dan lima tahunan sesuai ketentuan, berfokus pada kompetensi inti, lintas, dan sertifikasi profesional, dengan pimpinan aktif di organisasi profesi, pemenuhan jam pelatihan, dan penguatan kerja sama tim.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'APIP melaksanakan pengembangan kompetensi secara sistematis berbasis risiko dan kompleksitas penugasan, dengan pemeliharaan sertifikasi, partisipasi pimpinan aktif di organisasi profesi, pemenuhan jam pelatihan, penguatan kerja sama tim, dan evaluasi sederhana atas kegiatan pembelajaran.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'APIP melaksanakan pengembangan kompetensi secara komprehensif dan terintegrasi, dengan evaluasi menyeluruh atas kegiatan pembelajaran, pembaruan berkelanjutan mempertimbangkan praktik global, partisipasi aktif pimpinan, pemenuhan jam pelatihan penuh, serta pembentukan tim berbasis penilaian kompetensi sistematis.',
                ],
            ],
            'statement_level_hints' => [
                'Rencana Pengembangan Kompetensi' => [
                    1 => 'APIP belum memiliki rencana pengembangan kompetensi formal, dan pelatihan masih bersifat ad-hoc berdasarkan arahan pimpinan.',
                    2 => 'APIP telah memiliki rencana pelatihan tahunan sederhana, belum mendasarkan pada standar kompetensi jabatan.',
                    3 => 'APIP telah menyusun rencana pengembangan kompetensi tahunan dan lima tahunan sesuai ketentuan, berfokus pada kompetensi inti, lintas, dan sertifikasi profesional yang relevan.',
                    4 => 'APIP telah menyusun rencana pengembangan kompetensi yang mencakup kompetensi inti, lintas, dan tematik berbasis risiko dan kompleksitas penugasan, serta pemeliharaan sertifikasi profesi secara berkala.',
                    5 => 'APIP memiliki sistem pengembangan kompetensi yang terintegrasi, terukur, dan berkelanjutan, dengan pembaruan rutin berdasarkan hasil penilaian kompetensi dan praktik global.',
                ],
                'Pelaksanaan Pengembangan Kompetensi' => [
                    1 => 'APIP melaksanakan kegiatan pengembangan kompetensi secara insidentil berdasarkan arahan pimpinan, tanpa rencana formal dan belum memenuhi jam pelatihan minimum.',
                    2 => 'APIP melaksanakan pengembangan kompetensi berdasarkan rencana pelatihan tahunan sederhana, bersifat administratif, dan pemenuhan jam pelatihan minimum tidak didata dengan baik.',
                    3 => 'APIP melaksanakan pengembangan kompetensi sesuai rencana tahunan dan lima tahunan yang berfokus pada kompetensi inti, lintas, dan sertifikasi profesional, dengan pimpinan berpartisipasi aktif dalam organisasi profesi serta pemenuhan jam pelatihan minimum bagi pegawai. Pada tahap ini setiap personel dapat berperan secara efektif dan bekerja sama dalam tim (team building).',
                    4 => 'APIP melaksanakan pengembangan kompetensi secara sistematis mencakup kompetensi inti, lintas, dan tematik berbasis risiko dan kompleksitas penugasan, disertai pemeliharaan sertifikasi profesi, evaluasi pembelajaran sederhana, partisipasi aktif pimpinan dalam organisasi profesi, serta pemenuhan jam pelatihan minimum. Pada tahap ini setiap personel dapat berperan secara efektif dan bekerja sama dalam tim (team building).',
                    5 => 'APIP melaksanakan pengembangan kompetensi secara komprehensif dan terintegrasi dengan sistem informasi, disertai evaluasi pembelajaran menyeluruh, pembaruan berkelanjutan mempertimbangkan perubahan praktik global, partisipasi strategis pimpinan dalam organisasi profesi, serta pemenuhan penuh jam pelatihan minimum seluruh pegawai. Pada tahap ini, pembentukan tim didasarkan pada penilaian kompetensi sistematis.',
                ],
            ],
        ],
        'element4_dukungan_tik' => [
            'view' => 'elements.element1-kegiatan-asurans',
            'model' => Element4DukunganTik::class,
            'edit_log_model' => Element4DukunganTikEditLog::class,
            'page_title' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'subtopic_code' => 'S5',
            'subtopic_title' => 'Sub Topik 5 - Dukungan terhadap Teknologi Informasi',
            'info_modal_title' => 'Informasi Level Sub Topik 5 - Dukungan terhadap Teknologi Informasi',
            'notification_title' => 'Element 4 - Dukungan terhadap Teknologi Informasi',
            'rows' => [
                1 => 'Integrasi TI untuk Pengawasan Intern',
                2 => 'Pelatihan Pengguna',
                3 => 'Pengembangan dan Pengadaan',
                4 => 'Pemanfaatan TI untuk Fungsi Manajerial Pengawasan',
            ],
            'weights' => [
                1 => 0.60,
                2 => 0.30,
                3 => 0.05,
                4 => 0.05,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'score_range' => '0 - 1,99',
                    'description' => 'Dukungan teknologi tidak tersedia.',
                ],
                [
                    'level' => 2,
                    'score_range' => '2 - 2,99',
                    'description' => 'Dukungan teknologi sebagai alat administratif dasar.',
                ],
                [
                    'level' => 3,
                    'score_range' => '3 - 3,99',
                    'description' => 'Dukungan teknologi analitika data sederhana yang penerapannya masih selektif pada tugas tertentu.',
                ],
                [
                    'level' => 4,
                    'score_range' => '4 - 4,99',
                    'description' => 'Dukungan teknologi untuk analitika data yang terintegrasi dengan sistem organisasi.',
                ],
                [
                    'level' => 5,
                    'score_range' => '5',
                    'description' => 'Dukungan teknologi menyediakan otomasi deteksi dini dan analisis prediktif berkelanjutan, melalui Data Science/Data Modelling.',
                ],
            ],
            'statement_level_hints' => [
                'Integrasi TI untuk Pengawasan Intern' => [
                    1 => 'Tidak tersedia TI, dokumen disimpan dan diolah secara manual.',
                    2 => 'Dukungan TI hanya sebatas alat bantu dasar untuk melaksanakan penugasan.',
                    3 => 'Dukungan TI mendukung analitika data sederhana, namun masih selektif pada penugasan tertentu.',
                    4 => 'Dukungan TI terintegrasi dengan platform organisasi/pemerintah, sehingga memungkinkan analitika data secara berkala, masih terkendala dengan integrasi dengan pusat data organisasi/pemerintah.',
                    5 => 'Dukungan TI bekerja secara berkelanjutan, memanfaatkan Data Science, integrasi data sudah real-time, dan menghasilkan dashboard hasil pengawasan.',
                ],
                'Pelatihan Pengguna' => [
                    1 => 'Belum ada pelatihan teknologi informasi.',
                    2 => 'Pembelajaran bersifat otodidak, tergantung kemampuan individu.',
                    3 => 'Ada pelatihan dasar penggunaan aplikasi audit dan analitika data, didukung manual dan program pelatihan mandiri.',
                    4 => 'Pengguna kompeten dan tersertifikasi, pembelajaran berkelanjutan mendukung kolaborasi dan komunikasi.',
                    5 => 'Budaya digital melembaga, pembelajaran bersifat adaptif menggunakan manajemen berbagi pengetahuan.',
                ],
                'Pengembangan dan Pengadaan' => [
                    1 => 'Belum ada rencana atau kebijakan pengadaan sistem TI pengawasan.',
                    2 => 'Pengadaan atau penggunaan dukungan TI dilakukan individu, belum terstandar dan tidak terkoordinasi.',
                    3 => 'Pengembangan dukungan TI dilakukan secara terbatas di unit APIP atau menggunakan aplikasi eksternal yang diakui pemerintah.',
                    4 => 'Pengembangan dan pemeliharaan sistem TI dilakukan terkoordinasi dan terintegrasi dengan arsitektur TI organisasi.',
                    5 => 'Pengembangan TI bersifat lincah (agile), kolaboratif lintas instansi, dan sepenuhnya selaras dengan arsitektur SPBE pemerintah.',
                ],
                'Pemanfaatan TI untuk Fungsi Manajerial Pengawasan' => [
                    1 => 'Manajemen pengawasan dilaksanakan secara manual, tanpa dukungan sistem atau aplikasi.',
                    2 => 'Manajemen pengawasan menggunakan aplikasi dasar untuk tujuan dokumentasi dan administrasi.',
                    3 => 'Manajemen pengawasan memanfaatkan sistem atau aplikasi TI untuk sebagian proses pengelolaan, namun penerapannya belum mencakup seluruh fungsi manajerial pengawasan.',
                    4 => 'Manajemen pengawasan dikelola melalui sistem TI terintegrasi yang menghubungkan proses bisnis prioritas untuk pengawasan dan mendukung fleksibilitas pengelolaan kinerja serta keuangan.',
                    5 => 'Manajemen pengawasan didukung oleh dashboard TI yang terintegrasi, memfasilitasi pemantauan kinerja dan penyediaan informasi bagi pimpinan dalam pengambilan keputusan dan penentuan prioritas pengawasan.',
                ],
            ],
        ],
    ],
];
