<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    private const GUIDE_DIRECTORY = 'uploads/panduan';

    private const PROCESS_IMAGE_EXTENSIONS = ['gif', 'jpeg', 'jpg', 'png', 'webp'];

    private const PROCESS_IMAGE_KEYWORDS = ['bisnis', 'business', 'process', 'proses'];

    private const GUIDES = [
        [
            'key' => 'administrator',
            'title' => 'Administrator',
            'audience' => 'Administrator',
            'description' => 'Panduan pengelolaan akun, preferensi elemen, informasi umum, DMS, dan monitoring progres penilaian.',
            'preferred_file' => 'administrator.pdf',
            'keywords' => ['administrator', 'admin', 'pengelola'],
            'accent' => 'blue',
            'embed' => [
                'url' => 'https://online.fliphtml5.com/bpnya/bdaj/',
                'title' => 'Panduan PK APIP (Administrator)',
                'provider' => 'Online FlipHTML5',
                'id' => 'bpnya-bdaj',
                'ratio' => '5:3',
                'width' => '100%',
                'height' => '100%',
                'version' => '2',
            ],
        ],
        [
            'key' => 'user',
            'title' => 'User (Koordinator / Anggota Tim)',
            'audience' => 'Koordinator dan Anggota Tim',
            'description' => 'Panduan pengisian penilaian, unggah bukti dukung, verifikasi, dan koordinasi kerja tim.',
            'preferred_file' => 'user.pdf',
            'keywords' => ['user', 'pengguna', 'koordinator', 'anggota', 'auditor'],
            'accent' => 'teal',
            'embed' => [
                'url' => 'https://online.fliphtml5.com/bpnya/oejy/',
                'title' => 'Panduan PK APIP (User)',
                'provider' => 'Online FlipHTML5',
                'id' => 'bpnya-oejy',
                'ratio' => '5:3',
                'width' => '100%',
                'height' => '100%',
                'version' => '2',
            ],
        ],
    ];

    public function index()
    {
        if (! Session::has('user')) {
            return redirect()->route('login.form');
        }

        $sessionUser = (array) Session::get('user', []);

        return view('guides.index', [
            'pageTitle' => 'Panduan',
            'user' => $sessionUser,
            'guides' => $this->resolveGuides(),
            'processImages' => $this->resolveProcessImages(),
            'guideDirectory' => self::GUIDE_DIRECTORY,
            'notifications' => Notification::feedForUser($sessionUser, null, 50),
        ]);
    }

    private function resolveGuides(): array
    {
        $directory = public_path(self::GUIDE_DIRECTORY);
        $pdfFiles = File::isDirectory($directory)
            ? collect(File::files($directory))
                ->filter(fn ($file) => Str::lower((string) $file->getExtension()) === 'pdf')
                ->sortBy(fn ($file) => Str::lower((string) $file->getFilename()))
                ->values()
            : collect();

        return collect(self::GUIDES)
            ->map(function (array $guide, int $index) use ($directory, $pdfFiles): array {
                $matchedFile = $this->resolveGuideFile($directory, $pdfFiles, $guide, $index);
                $fileName = $matchedFile ? (string) $matchedFile->getFilename() : '';
                $embed = is_array($guide['embed'] ?? null) ? $guide['embed'] : [];
                $embedUrl = trim((string) ($embed['url'] ?? ''));

                return [
                    'key' => $guide['key'],
                    'title' => $guide['title'],
                    'audience' => $guide['audience'],
                    'description' => $guide['description'],
                    'preferred_file' => $guide['preferred_file'],
                    'accent' => $guide['accent'],
                    'available' => $matchedFile !== null || $embedUrl !== '',
                    'file_name' => $fileName,
                    'file_url' => $matchedFile ? asset(self::GUIDE_DIRECTORY.'/'.rawurlencode($fileName)) : '',
                    'file_size' => $matchedFile ? $this->humanFileSize((int) $matchedFile->getSize()) : '',
                    'last_modified' => $matchedFile ? date('d M Y', (int) $matchedFile->getMTime()) : '',
                    'embed_url' => $embedUrl,
                    'embed_title' => trim((string) ($embed['title'] ?? '')),
                    'embed_provider' => trim((string) ($embed['provider'] ?? 'Online Flipbook')),
                    'embed_id' => trim((string) ($embed['id'] ?? '')),
                    'embed_ratio' => trim((string) ($embed['ratio'] ?? '3:2')),
                    'embed_lightbox' => trim((string) ($embed['lightbox'] ?? 'yes')),
                    'embed_width' => trim((string) ($embed['width'] ?? '100%')),
                    'embed_height' => trim((string) ($embed['height'] ?? 'auto')),
                    'embed_version' => trim((string) ($embed['version'] ?? '1')),
                    'embed_script_url' => trim((string) ($embed['script_url'] ?? '')),
                ];
            })
            ->all();
    }

    private function resolveProcessImages(): array
    {
        $directory = public_path(self::GUIDE_DIRECTORY);

        if (! File::isDirectory($directory)) {
            return [];
        }

        $imageFiles = collect(File::files($directory))
            ->filter(fn ($file): bool => in_array(Str::lower((string) $file->getExtension()), self::PROCESS_IMAGE_EXTENSIONS, true))
            ->sortBy(fn ($file): string => Str::lower((string) $file->getFilename()))
            ->values();

        $processImages = $imageFiles
            ->filter(function ($file): bool {
                $fileName = Str::lower((string) $file->getFilename());

                return collect(self::PROCESS_IMAGE_KEYWORDS)
                    ->contains(fn (string $keyword): bool => Str::contains($fileName, $keyword));
            })
            ->values();

        if ($processImages->isEmpty()) {
            $processImages = $imageFiles;
        }

        return $processImages
            ->map(function ($file): array {
                $fileName = (string) $file->getFilename();
                $title = trim((string) preg_replace('/\s+/', ' ', str_replace(['-', '_'], ' ', pathinfo($fileName, PATHINFO_FILENAME))));

                return [
                    'title' => $title !== '' ? $title : 'Gambar Proses Bisnis',
                    'file_name' => $fileName,
                    'file_url' => asset(self::GUIDE_DIRECTORY.'/'.rawurlencode($fileName)),
                    'file_size' => $this->humanFileSize((int) $file->getSize()),
                    'last_modified' => date('d M Y', (int) $file->getMTime()),
                ];
            })
            ->values()
            ->all();
    }

    private function resolveGuideFile(string $directory, $pdfFiles, array $guide, int $index): ?\SplFileInfo
    {
        $preferredFile = trim((string) ($guide['preferred_file'] ?? ''));
        if ($preferredFile !== '') {
            $preferredPath = $directory.DIRECTORY_SEPARATOR.$preferredFile;
            if (is_file($preferredPath)) {
                return new \SplFileInfo($preferredPath);
            }
        }

        $keywords = collect((array) ($guide['keywords'] ?? []))
            ->map(fn ($keyword) => Str::lower(trim((string) $keyword)))
            ->filter()
            ->values();

        $matchedByKeyword = $pdfFiles->first(function ($file) use ($keywords): bool {
            $fileName = Str::lower((string) $file->getFilename());

            return $keywords->contains(fn (string $keyword) => Str::contains($fileName, $keyword));
        });

        if ($matchedByKeyword) {
            return $matchedByKeyword;
        }

        if ($pdfFiles->count() === count(self::GUIDES)) {
            return $pdfFiles->get($index);
        }

        return null;
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $bytes;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        $precision = $unitIndex === 0 ? 0 : 1;

        return number_format($size, $precision, ',', '.').' '.$units[$unitIndex];
    }
}
