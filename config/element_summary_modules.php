<?php

return [
    'modules' => [
        'element1' => [
            'view' => 'elements.element1-summary',
            'title' => 'Element 1 : Kualitas Peran dan Layanan',
            'header_code' => 'E1',
            'header_subtitle' => 'Rekap Skor Tertimbang dan Level dari Sub Topik Element 1',
            'level_label' => 'Level Element 1',
            'info_modal_title' => 'Informasi Level Element',
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
                    'description' => 'Kualitas peran dan layanan mencakup pengawasan kepatuhan, kinerja, dan konsultansi strategis atas manajemen risiko organisasi, dengan pengawasan intern berbasis risiko mendukung perbaikan operasional, TKMRPI, serta pengendalian kecurangan pada organisasi K/L/D dan prioritas pembangunan nasional.',
                ],
                [
                    'level' => 4,
                    'description' => 'Kualitas peran dan layanan mencerminkan pengawasan yang terintegrasi dengan manajemen risiko organisasi, menghasilkan asurans menyeluruh atas efektivitas TKMRPI dan pengendalian kecurangan, serta menghasilkan perbaikan yang terkonvergensi lintas unit kerja dan mendukung prioritas pembangunan nasional.',
                ],
                [
                    'level' => 5,
                    'description' => 'Kualitas peran dan layanan pengawasan intern menghasilkan insight dan foresight atas keseluruhan manajemen risiko serta peluang perbaikan masa depan melalui inovasi, teknologi, dan praktik terbaik pengawasan intern.',
                ],
            ],
        ],
        'element2' => [
            'view' => 'elements.element1-summary',
            'title' => 'Element 2 : Profesionalisme Penugasan',
            'header_code' => 'E2',
            'header_subtitle' => 'Rekap Skor Tertimbang dan Level dari 6 Sub Topik Element 2',
            'level_label' => 'Level Element 2',
            'info_modal_title' => 'Informasi Level Element 2',
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
                    'description' => 'Profesionalisme penugasan masih berada pada tahap rintisan dan belum konsisten antar sub topik.',
                ],
                [
                    'level' => 2,
                    'description' => 'Profesionalisme penugasan mulai terstruktur, namun penerapan antar sub topik masih belum merata.',
                ],
                [
                    'level' => 3,
                    'description' => 'Profesionalisme penugasan telah memadai, dijalankan sistematis, dan mendukung pengawasan berbasis risiko.',
                ],
                [
                    'level' => 4,
                    'description' => 'Profesionalisme penugasan telah terintegrasi lintas sub topik, dengan mutu dan tindak lanjut yang lebih konsisten.',
                ],
                [
                    'level' => 5,
                    'description' => 'Profesionalisme penugasan telah optimal, terdigitalisasi, adaptif, dan mendukung pengambilan keputusan strategis.',
                ],
            ],
        ],
        'element3' => [
            'view' => 'elements.element1-summary',
            'title' => 'Element 3 : Manajemen Pengawasan',
            'header_code' => 'E3',
            'header_subtitle' => 'Rekap Skor Tertimbang dan Level dari 2 Sub Topik Element 3',
            'level_label' => 'Level Element 3',
            'info_modal_title' => 'Informasi Level Element 3',
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
                    'description' => 'Perencanaan pengawasan sudah memiliki fokus dan sasaran, meski belum sesuai dengan prioritas K/L/D. Laporan hanya berisi deskripsi kepatuhan administratif dengan rekomendasi yang bersifat teknis/operasional.',
                ],
                [
                    'level' => 3,
                    'description' => 'Perencanaan pengawasan fokus dan menyasar prioritas K/L/D. Laporan telah mengungkap permasalahan hingga ke akar penyebab dengan rekomendasi yang menyasar pada aspek krusial untuk perbaikan pelaksanaan program prioritas K/L/D.',
                ],
                [
                    'level' => 4,
                    'description' => 'Perencanaan pengawasan fokus dan menyasar prioritas jangka menengah/panjang K/L/D, terintegrasi dengan manajemen risiko. Laporan telah mengungkap permasalahan hingga ke akar penyebab dengan rumusan rekomendasi yang menyasar pada aspek krusial (overall opinion) dan memberikan nilai tambah bagi K/L/D dan lintas instansi.',
                ],
                [
                    'level' => 5,
                    'description' => 'Perencanaan pengawasan fokus dan menyasar prioritas K/L/D serta mempertimbangkan target lintas sektor/organisasi yang mendukung prioritas nasional. Laporan telah mengungkap permasalahan hingga ke akar penyebab dengan rumusan rekomendasi yang menyasar pada aspek krusial (termasuk proyeksi atas TKMRPI) dan memberikan nilai tambah strategis bagi K/L/D, lintas instansi, dan nasional.',
                ],
            ],
        ],
        'element4' => [
            'view' => 'elements.element1-summary',
            'title' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
            'header_code' => 'E4',
            'header_subtitle' => 'Rekap Skor Tertimbang dan Level dari 5 Sub Topik Element 4',
            'level_label' => 'Level Element 4',
            'info_modal_title' => 'Informasi Level Element 4 - Pengelolaan Kinerja dan Sumber Daya Pengawasan',
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
                    'description' => 'Pengelolaan kinerja dan sumber daya pengawasan masih ad-hoc; proses kinerja, keuangan, SDM, dan dukungan teknologi belum berjalan terstruktur.',
                ],
                [
                    'level' => 2,
                    'description' => 'Pengelolaan mulai terstruktur secara administratif, tetapi pemenuhan dan pemanfaatan sumber daya pada fungsi pengawasan belum merata.',
                ],
                [
                    'level' => 3,
                    'description' => 'Pengelolaan kinerja dan sumber daya sudah memadai, terencana, serta mendukung pelaksanaan pengawasan berbasis kebutuhan prioritas.',
                ],
                [
                    'level' => 4,
                    'description' => 'Pengelolaan kinerja dan sumber daya telah terintegrasi lintas proses, adaptif terhadap risiko strategis, dan mendukung efektivitas pengawasan.',
                ],
                [
                    'level' => 5,
                    'description' => 'Pengelolaan kinerja dan sumber daya telah optimal, antisipatif, berbasis data, dan berorientasi nilai tambah strategis bagi organisasi.',
                ],
            ],
        ],
        'element5' => [
            'view' => 'elements.element1-summary',
            'title' => 'Element 5 : Budaya dan Hubungan Organisasi',
            'header_code' => 'E5',
            'header_subtitle' => 'Rekap Skor Tertimbang dan Level dari 4 Sub Topik Element 5',
            'level_label' => 'Level Element 5',
            'info_modal_title' => 'Informasi Level Element 5 - Budaya dan Hubungan Organisasi',
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
                    'description' => 'Budaya dan hubungan organisasi belum terkelola secara sistematis; internalisasi integritas serta kolaborasi dengan pemangku kepentingan masih terbatas.',
                ],
                [
                    'level' => 2,
                    'description' => 'Budaya dan hubungan organisasi mulai dibangun melalui kebijakan dan sosialisasi awal, namun implementasi dan pemanfaatannya belum konsisten.',
                ],
                [
                    'level' => 3,
                    'description' => 'Budaya integritas, hubungan manajemen, koordinasi pengawasan, dan akses informasi telah berjalan memadai dan mendukung proses pengawasan.',
                ],
                [
                    'level' => 4,
                    'description' => 'Budaya dan hubungan organisasi telah terintegrasi lintas proses, konsisten, terdokumentasi, dan memberi nilai tambah bagi penguatan pengawasan.',
                ],
                [
                    'level' => 5,
                    'description' => 'Budaya dan hubungan organisasi mencapai kematangan tinggi, adaptif terhadap perubahan, didukung teknologi, dan menjadi rujukan praktik baik.',
                ],
            ],
        ],
    ],
];
