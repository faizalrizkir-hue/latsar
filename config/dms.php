<?php

return [
    'type_options' => [
        'Manajemen Pengawasan' => [
            'Surat Tugas',
            'Laporan Hasil Pengawasan (LHP)',
            'Program Kerja Pengawasan Tahunan (PKPT)',
            'Tanda Bukti',
            'Telaah Sejawat',
        ],
        'Sumber Daya Manusia' => ['Dokumen SDM'],
        'Keuangan' => ['Dokumen Keuangan'],
        'Pemanfaatan Sistem Informasi (SI)' => ['Dokumen Sistem Informasi (SI)'],
        'Pedoman/Kebijakan' => ['Dokumen Pedoman/Kebijakan'],
        'Lainnya' => ['Dokumen Lainnya'],
    ],
    'upload' => [
        'max_kilobytes' => 5120,
        // Keep false by default so existing behavior is unchanged.
        // Set true in production to enforce strict allowlist.
        'enforce_allowlist' => false,
        'allowed_extensions' => [],
        'allowed_mime_types' => [],
        'blocked_extensions' => [
            'php',
            'php3',
            'php4',
            'php5',
            'phtml',
            'phar',
            'exe',
            'dll',
            'com',
            'cmd',
            'bat',
            'ps1',
            'sh',
            'vbs',
            'js',
            'jar',
            'msi',
            'scr',
        ],
        'blocked_mime_prefixes' => [
            'text/x-php',
            'application/x-httpd-php',
            'application/x-php',
            'application/x-msdownload',
            'application/x-dosexec',
            'application/x-sh',
            'application/x-bat',
            'application/x-msdos-program',
        ],
    ],
];
