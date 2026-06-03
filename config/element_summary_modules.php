<?php

return [
    'modules' => [
        'element1' => [
            'view' => 'elements.element1-summary',
            'title' => 'Elemen 1 : Kualitas Peran dan Layanan',
            'header_code' => 'E1',
            'header_subtitle' => 'Ringkasan skor tertimbang, level elemen, dan capaian topik Elemen 1',
            'level_label' => 'Level Elemen 1',
            'info_modal_title' => 'Informasi Level Elemen',
            'styles' => [
                'css/element1-kegiatan-asurans.css',
                'css/element1-summary.css',
            ],
            'element_weight' => 0.40,
            'subtopic_slugs' => [
                'element1_kegiatan_asurans',
                'element1_jasa_konsultansi',
            ],
            'subtopic_weights' => [
                'element1_kegiatan_asurans' => 0.80,
                'element1_jasa_konsultansi' => 0.20,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'description' => 'Kualitas peran dan layanan terbatas dengan pengawasan administratif atau transaksi yang bersifat ad-hoc, ruang lingkup yang sempit, serta tanpa metodologi baku.',
                ],
                [
                    'level' => 2,
                    'description' => 'Kualitas peran dan layanan mencakup pengawasan kepatuhan dan konsultansi sederhana, dengan pendekatan administratif dan prosedur dasar, yang belum melembagakan praktik pengawasan intern berbasis risiko.',
                ],
                [
                    'level' => 3,
                    'description' => 'Kualitas peran dan layanan mencakup pengawasan kepatuhan, kinerja, dan konsultansi strategis atas manajemen risiko organisasi, dengan pengawasan intern berbasis risiko mendukung perbaikan operasional, TKMRPI, serta pengendalian kecurangan pada organisasi kementerian/lembaga/pemerintah daerah dan prioritas pembangunan nasional.',
                ],
                [
                    'level' => 4,
                    'description' => 'Kualitas peran dan layanan mencerminkan pengawasan yang terintegrasi dengan manajemen risiko organisasi, menghasilkan asurans menyeluruh atas efektivitas TKMRPI dan pengendalian kecurangan, serta menghasilkan perbaikan yang terkonvergensi lintas unit kerja dan mendukung prioritas pembangunan nasional.',
                ],
                [
                    'level' => 5,
                    'description' => 'Kualitas peran dan layanan Pengawasan Intern menghasilkan insight dan foresight atas keseluruhan manajemen risiko serta peluang perbaikan masa depan melalui inovasi, teknologi, dan praktik terbaik pengawasan intern.',
                ],
            ],        ],
        'element2' => [
            'view' => 'elements.element1-summary',
            'title' => 'Elemen 2 : Profesionalisme Penugasan',
            'header_code' => 'E2',
            'header_subtitle' => 'Ringkasan skor tertimbang, level elemen, dan capaian 6 topik Elemen 2',
            'level_label' => 'Level Elemen 2',
            'info_modal_title' => 'Informasi Level Elemen 2',
            'styles' => [
                'css/element1-kegiatan-asurans.css',
                'css/element1-summary.css',
            ],
            'element_weight' => 0.20,
            'subtopic_slugs' => [
                'element2_pengembangan_informasi',
                'element2_perencanaan_penugasan',
                'element2_pelaksanaan_penugasan',
                'element2_komunikasi_hasil',
                'element2_pemantauan_tindak_lanjut',
                'element2_pengendalian_kualitas',
            ],
            'subtopic_weights' => [
                'element2_pengembangan_informasi' => 0.20,
                'element2_perencanaan_penugasan' => 0.15,
                'element2_pelaksanaan_penugasan' => 0.30,
                'element2_komunikasi_hasil' => 0.15,
                'element2_pemantauan_tindak_lanjut' => 0.10,
                'element2_pengendalian_kualitas' => 0.10,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'description' => 'Penugasan dilaksanakan belum mengikuti standar dan prosedur. Aktivitas pengawasan bersifat reaktif terhadap perintah pimpinan. Hasil penugasan belum mampu menghasilkan rekomendasi yang bernilai tambah.',
                ],
                [
                    'level' => 2,
                    'description' => 'Penugasan mulai mengikuti prosedur dan format dasar namun berorientasi pada kepatuhan administratif. Analisis dan reviu terbatas, serta hasil belum memberikan nilai tambah signifikan.',
                ],
                [
                    'level' => 3,
                    'description' => 'Penugasan dilaksanakan dengan metodologi dan terdokumentasi, berbasis bukti dan risiko, serta melalui reviu berjenjang. Hasil pengawasan menghasilkan rekomendasi yang valid dan dibahas dengan mitra.',
                ],
                [
                    'level' => 4,
                    'description' => 'Penugasan dilaksanakan sesuai dengan standar dengan mekanisme mutu dan pembelajaran organisasi. Evaluasi temuan dilakukan kolaboratif dan mendorong perbaikan sistemik serta peningkatan TKMRPI.',
                ],
                [
                    'level' => 5,
                    'description' => 'Profesionalisme mencerminkan peran strategis APIP sebagai mitra perubahan. Penugasan berbasis data dan proyeksi ke depan, menghasilkan rekomendasi antisipatif, dan menjadi dasar perbaikan kebijakan.',
                ],
            ],        ],
        'element3' => [
            'view' => 'elements.element1-summary',
            'title' => 'Elemen 3 : Manajemen Pengawasan',
            'header_code' => 'E3',
            'header_subtitle' => 'Ringkasan skor tertimbang, level elemen, dan capaian 2 topik Elemen 3',
            'level_label' => 'Level Elemen 3',
            'info_modal_title' => 'Informasi Level Elemen 3',
            'styles' => [
                'css/element1-kegiatan-asurans.css',
                'css/element1-summary.css',
            ],
            'element_weight' => 0.20,
            'subtopic_slugs' => [
                'element3_perencanaan_pengawasan',
                'element3_pelaporan_manajemen_kld',
            ],
            'subtopic_weights' => [
                'element3_perencanaan_pengawasan' => 0.60,
                'element3_pelaporan_manajemen_kld' => 0.40,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'description' => 'Perencanaan pengawasan belum memiliki fokus dan sasaran. Laporan masih berupa narasi deskriptif dengan rekomendasi yang masih normatif.',
                ],
                [
                    'level' => 2,
                    'description' => 'Perencanaan pengawasan sudah memiliki fokus dan sasaran, meski belum sesuai dengan prioritas kementerian/lembaga/pemerintah daerah. Laporan hanya berisi deskripsi kepatuhan administratif dengan rekomendasi yang bersifat teknis/operasional.',
                ],
                [
                    'level' => 3,
                    'description' => 'Perencanaan pengawasan fokus dan menyasar prioritas kementerian/lembaga/pemerintah daerah. Laporan telah mengungkap permasalahan hingga ke akar penyebab dengan rekomendasi yang menyasar pada aspek krusial untuk perbaikan pelaksanaan program prioritas kementerian/lembaga/pemerintah daerah.',
                ],
                [
                    'level' => 4,
                    'description' => 'Perencanaan pengawasan fokus dan menyasar prioritas jangka menengah/panjang kementerian/lembaga/pemerintah daerah, terintegrasi dengan manajemen risiko. Laporan telah mengungkap permasalahan hingga ke akar penyebab dengan rumusan rekomendasi yang menyasar pada aspek krusial (overall opinion) dan memberikan nilai tambah bagi kementerian/lembaga/pemerintah daerah dan lintas instansi.',
                ],
                [
                    'level' => 5,
                    'description' => 'Perencanaan pengawasan fokus dan menyasar prioritas kementerian/lembaga/pemerintah daerah serta mempertimbangkan target lintas sektor/organisasi yang mendukung prioritas nasional. Laporan telah mengungkap permasalahan hingga ke akar penyebab dengan rumusan rekomendasi yang menyasar pada aspek krusial (termasuk proyeksi atas TKMRPI) dan memberikan nilai tambah strategis bagi kementerian/lembaga/pemerintah daerah, lintas instansi, dan nasional.',
                ],
            ],        ],
        'element4' => [
            'view' => 'elements.element1-summary',
            'title' => 'Elemen 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'header_code' => 'E4',
            'header_subtitle' => 'Ringkasan skor tertimbang, level elemen, dan capaian 5 topik Elemen 4',
            'level_label' => 'Level Elemen 4',
            'info_modal_title' => 'Informasi Level Elemen 4 - Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'styles' => [
                'css/element1-kegiatan-asurans.css',
                'css/element1-summary.css',
            ],
            'element_weight' => 0.10,
            'subtopic_slugs' => [
                'element4_manajemen_kinerja',
                'element4_mekanisme_pendanaan',
                'element4_perencanaan_sdm_apip',
                'element4_pengembangan_sdm_profesional_apip',
                'element4_dukungan_tik',
            ],
            'subtopic_weights' => [
                'element4_manajemen_kinerja' => 0.20,
                'element4_mekanisme_pendanaan' => 0.20,
                'element4_perencanaan_sdm_apip' => 0.20,
                'element4_pengembangan_sdm_profesional_apip' => 0.20,
                'element4_dukungan_tik' => 0.20,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'description' => 'Pengelolaan kinerja belum terdefinisi dan tidak terkelola. Pengelolaan sumber daya bersifat ad-hoc, belum terstruktur, dan tanpa dukungan teknologi.',
                ],
                [
                    'level' => 2,
                    'description' => 'Pengelolaan kinerja berorientasi keluaran (output). Pengelolaan sumber daya mulai teratur namun masih terbatas dalam memenuhi kebutuhan strategis pengawasan.',
                ],
                [
                    'level' => 3,
                    'description' => 'Pengelolaan kinerja telah sistematis dan berorientasi hasil (outcome). Pengelolaan sumber daya efektif, sesuai standar, mendukung profesionalisme SDM serta pelaksanaan rencana dan strategi pengawasan.',
                ],
                [
                    'level' => 4,
                    'description' => 'Pengelolaan kinerja adaptif dan terintegrasi dengan mekanisme perbaikan berkelanjutan. Pengelolaan sumber daya terpadu, efisien, profesional, berorientasi perbaikan berkelanjutan, dan didukung teknologi informasi terintegrasi.',
                ],
                [
                    'level' => 5,
                    'description' => 'Pengelolaan kinerja strategis, responsif, dan prediktif. Pengelolaan sumber daya responsif dengan kebutuhan masa depan, berorientasi kompetensi global, inovatif, efisien, dan dukungan teknologi terotomasi bersifat prediktif.',
                ],
            ],        ],
        'element5' => [
            'view' => 'elements.element1-summary',
            'title' => 'Elemen 5 : Budaya dan Hubungan Organisasi',
            'header_code' => 'E5',
            'header_subtitle' => 'Ringkasan skor tertimbang, level elemen, dan capaian 4 topik Elemen 5',
            'level_label' => 'Level Elemen 5',
            'info_modal_title' => 'Informasi Level Elemen 5 - Budaya dan Hubungan Organisasi',
            'styles' => [
                'css/element1-kegiatan-asurans.css',
                'css/element1-summary.css',
            ],
            'element_weight' => 0.10,
            'subtopic_slugs' => [
                'element5_pembangunan_budaya_integritas',
                'element5_hubungan_apip_manajemen',
                'element5_koordinasi_pengawasan',
                'element5_akses_informasi_sumberdaya',
            ],
            'subtopic_weights' => [
                'element5_pembangunan_budaya_integritas' => 0.20,
                'element5_hubungan_apip_manajemen' => 0.40,
                'element5_koordinasi_pengawasan' => 0.10,
                'element5_akses_informasi_sumberdaya' => 0.30,
            ],
            'info_levels' => [
                [
                    'level' => 1,
                    'description' => 'APIP belum menegakkan nilai-nilai integritas dan etika organisasi. APIP belum difungsikan dalam organisasi. Akses informasi pengawasan tertutup.',
                ],
                [
                    'level' => 2,
                    'description' => 'APIP telah memahami nilai-nilai integritas dan etika organisasi, serta terdapat saluran pengaduan. Hubungan organisasi bersifat formal dan fungsional terbatas. Akses informasi pengawasan terbuka secara terbatas.',
                ],
                [
                    'level' => 3,
                    'description' => 'APIP sudah mengimplementasikan nilai integritas dan etika organisasi dalam pelaksanaan tugas. Hubungan organisasi strategis sesuai mandat pimpinan kementerian/lembaga/pemerintah daerah. Koordinasi internal dan eksternal memberikan manfaat terhadap peningkatan kualitas pengawasan untuk perbaikan organisasi. Akses informasi pengawasan terbuka sepenuhnya.',
                ],
                [
                    'level' => 4,
                    'description' => 'Integritas APIP dikelola proaktif dan telah mendapat pengakuan organisasi. Hubungan organisasi adaptif terhadap perubahan mandat pimpinan kementerian/lembaga/pemerintah daerah. Akses informasi pengawasan terbuka berkelanjutan untuk kolaborasi.',
                ],
                [
                    'level' => 5,
                    'description' => 'Budaya integritas APIP menjadi role model nasional. Hubungan organisasi terintegrasi lintas sektor pembangunan. APIP selalu dilibatkan dalam pengambilan keputusan kebijakan strategis organisasi. Akses informasi pengawasan dikelola secara strategis dan terintegrasi yang berpengaruh terhadap kebijakan publik.',
                ],
            ],        ],
    ],
];


