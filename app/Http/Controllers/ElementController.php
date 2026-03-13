<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Element1KegiatanAsurans;
use App\Models\Element1KegiatanAsuransEditLog;
use App\Models\ElementAssessment;
use App\Models\DmsFile;
use App\Models\ElementTeamAssignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ElementController extends Controller
{
    private array $pages = [
        'element1' => 'Element 1 : Kualitas Peran dan Layanan',
        'element2' => 'Element 2 : Profesionalisme Penugasan',
        'element3' => 'Element 3 : Manajemen Pengawasan',
        'element4' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
        'element5' => 'Element 5 : Budaya dan Hubungan Organisasi',
        'element1_kegiatan_asurans' => 'Sub Topik 1 - Kegiatan Asurans',
        'element1_jasa_konsultansi' => 'Sub Topik 2 - Kegiatan Konsultansi',
        'element2_komunikasi_hasil' => 'Element 2 - Komunikasi Hasil Penugasan',
        'element2_pelaksanaan_penugasan' => 'Element 2 - Pelaksanaan Penugasan',
        'element2_pemantauan_tindak_lanjut' => 'Element 2 - Pemantauan Tindak Lanjut',
        'element2_pengembangan_informasi' => 'Element 2 - Pengembangan Informasi Awal',
        'element2_pengendalian_kualitas' => 'Element 2 - Pengendalian Kualitas Penugasan',
        'element2_perencanaan_penugasan' => 'Element 2 - Perencanaan Penugasan',
        'element3_pelaporan_manajemen_kld' => 'Element 3 - Pelaporan kepada Manajemen K/L/D',
        'element3_perencanaan_pengawasan' => 'Element 3 - Perencanaan Pengawasan',
        'element4_dukungan_tik' => 'Element 4 - Dukungan terhadap Teknologi Informasi',
        'element4_manajemen_kinerja' => 'Element 4 - Manajemen Kinerja',
        'element4_mekanisme_pendanaan' => 'Element 4 - Manajemen Sumber Daya Keuangan',
        'element4_pengembangan_sdm_profesional_apip' => 'Element 4 - Pengembangan SDM Profesional APIP',
        'element4_perencanaan_sdm_apip' => 'Element 4 - Perencanaan Kebutuhan dan Pengadaan SDM Pengawasan',
        'element5_akses_informasi_sumberdaya' => 'Element 5 - Akses Informasi Sumberdaya',
        'element5_hubungan_apip_manajemen' => 'Element 5 - Hubungan APIP dan Manajemen',
        'element5_koordinasi_pengawasan' => 'Element 5 - Koordinasi Pengawasan',
        'element5_pembangunan_budaya_integritas' => 'Element 5 - Pembangunan Budaya Integritas',
        'element5_pengelolaan_komunikasi_internal' => 'Element 5 - Pengelolaan Komunikasi Internal',
    ];

    private array $questionPresets = [
        'element1_kegiatan_asurans' => [
            1 => 'Ruang Lingkup dan Fokus',
            2 => 'Analisis dan Atribut Temuan',
            3 => 'Kualitas Opini/Simpulan',
            4 => 'Kualitas Rekomendasi',
        ],
        'element1_jasa_konsultansi' => [
            1 => 'Tujuan dan Ruang Lingkup',
            2 => 'Peran, tanggung jawab dan ekspektasi',
            3 => 'Proses Konsultansi',
            4 => 'Kualitas Hasil Konsultansi',
        ],
        'element2' => [
            1 => 'Perencanaan Penugasan',
            2 => 'Pelaksanaan',
            3 => 'Pelaporan',
            4 => 'Tindak Lanjut',
        ],
        'element3' => [
            1 => 'Perencanaan Pengawasan',
            2 => 'Pelaporan kepada Manajemen K/L/D',
        ],
        'element4' => [
            1 => 'Manajemen Kinerja',
            2 => 'Manajemen Sumber Daya Keuangan',
            3 => 'Perencanaan Kebutuhan dan Pengadaan SDM Pengawasan',
            4 => 'Pengembangan SDM Profesional APIP',
            5 => 'Dukungan terhadap Teknologi Informasi',
        ],
        'element5' => [
            1 => 'Budaya Integritas',
            2 => 'Hubungan Manajemen',
            3 => 'Koordinasi Pengawasan',
            4 => 'Akses Informasi',
        ],
        'element2_komunikasi_hasil' => [
            1 => 'Kualitas Komunikasi',
            2 => 'Kejelasan Hasil',
            3 => 'Tindak Lanjut',
            4 => 'Dokumentasi',
        ],
        'element2_pelaksanaan_penugasan' => [
            1 => 'Kepatuhan Prosedur',
            2 => 'Kecukupan Bukti',
            3 => 'Timeline',
            4 => 'Kualitas Pelaporan',
        ],
        'element2_pemantauan_tindak_lanjut' => [
            1 => 'Rencana TL',
            2 => 'Eksekusi TL',
            3 => 'Monitoring',
            4 => 'Pelaporan TL',
        ],
        'element2_pengembangan_informasi' => [
            1 => 'Perolehan Data',
            2 => 'Validasi',
            3 => 'Analitik',
            4 => 'Diseminasi',
        ],
        'element2_pengendalian_kualitas' => [
            1 => 'QC Perencanaan',
            2 => 'QC Pelaksanaan',
            3 => 'QC Pelaporan',
            4 => 'Perbaikan',
        ],
        'element2_perencanaan_penugasan' => [
            1 => 'Identifikasi Risiko',
            2 => 'Program Kerja',
            3 => 'Sumber Daya',
            4 => 'Persetujuan',
        ],
        'element3_pelaporan_manajemen_kld' => [
            1 => 'Kualitas Penyajian Laporan',
            2 => 'Kualitas Rekomendasi dan Nilai Tambah Strategis',
            3 => 'Pemanfaatan oleh Manajemen',
        ],
        'element3_perencanaan_pengawasan' => [
            1 => 'Struktur Perencanaan',
            2 => 'Fokus dan Sasaran Pengawasan',
            3 => 'Adaptif',
            4 => 'Keterlibatan Manajemen',
        ],
        'element4_dukungan_tik' => [
            1 => 'Integrasi TI untuk Pengawasan Intern',
            2 => 'Pelatihan Pengguna',
            3 => 'Pengembangan dan Pengadaan',
            4 => 'Pemanfaatan TI untuk Fungsi Manajerial Pengawasan',
        ],
        'element4_manajemen_kinerja' => [
            1 => 'Perencanaan Kinerja',
            2 => 'Pengorganisasian Kinerja',
            3 => 'Pengendalian Kinerja',
        ],
        'element4_mekanisme_pendanaan' => [
            1 => 'Perencanaan dan Kecukupan Anggaran',
            2 => 'Penggunaan dan Fleksibilitas Anggaran',
        ],
        'element4_pengembangan_sdm_profesional_apip' => [
            1 => 'Rencana Pengembangan Kompetensi',
            2 => 'Pelaksanaan Pengembangan Kompetensi',
        ],
        'element4_perencanaan_sdm_apip' => [
            1 => 'Perencanaan Kebutuhan SDM',
            2 => 'Rekrutmen dan Distribusi SDM',
        ],
        'element5_akses_informasi_sumberdaya' => [
            1 => 'Ketersediaan Info',
            2 => 'Aksesibilitas',
            3 => 'Keamanan Data',
            4 => 'Pemanfaatan',
        ],
        'element5_hubungan_apip_manajemen' => [
            1 => 'Koordinasi',
            2 => 'Dukungan Manajemen',
            3 => 'Respon Rekomendasi',
            4 => 'Komunikasi Formal',
        ],
        'element5_koordinasi_pengawasan' => [
            1 => 'Rencana Bersama',
            2 => 'Pelaksanaan Kolaboratif',
            3 => 'Berbagi Data',
            4 => 'Evaluasi Bersama',
        ],
        'element5_pembangunan_budaya_integritas' => [
            1 => 'Kebijakan Integritas',
            2 => 'Sosialisasi',
            3 => 'Pengawasan',
            4 => 'Penegakan',
        ],
        'element5_pengelolaan_komunikasi_internal' => [
            1 => 'Saluran Komunikasi',
            2 => 'Frekuensi',
            3 => 'Kejelasan',
            4 => 'Umpan Balik',
        ],
    ];

    // Bisa diisi nanti, untuk sementara bobot default akan dirata.
    private array $weightPresets = [];
    private array $kegiatanWeights = [
        1 => 0.20,
        2 => 0.25,
        3 => 0.25,
        4 => 0.30,
    ];

    public function index()
    {
        if (!Session::has('user')) {
            return redirect(route('login.form', [], false));
        }

        $user = Session::get('user', []);
        $pages = $this->visiblePagesForUser($user);

        return view('elements.index', [
            'pages' => $pages,
            'user' => $user,
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
        ]);
    }

    public function show(string $slug)
    {
        if (!Session::has('user')) {
            return redirect(route('login.form', [], false));
        }

        abort_unless(isset($this->pages[$slug]), 404);

        $guard = $this->guardElementAccess($slug);
        if ($guard !== null) {
            return $guard;
        }

        $summaryModuleConfig = $this->getElementSummaryModuleConfig($slug);
        if ($summaryModuleConfig !== null) {
            return $this->renderElementSummary($slug, $summaryModuleConfig);
        }

        $moduleConfig = $this->getSubtopicModuleConfig($slug);
        if ($moduleConfig !== null) {
            return $this->renderKegiatanAsurans($slug, $moduleConfig);
        }

        return $this->renderForm($slug);
    }

    public function store(Request $request, string $slug)
    {
        if (!Session::has('user')) {
            return redirect(route('login.form', [], false));
        }

        abort_unless(isset($this->pages[$slug]), 404);

        $guard = $this->guardElementAccess($slug);
        if ($guard !== null) {
            return $guard;
        }

        if ($this->getElementSummaryModuleConfig($slug) !== null) {
            return redirect(route('elements.show', $slug, false));
        }

        $moduleConfig = $this->getSubtopicModuleConfig($slug);
        if ($moduleConfig !== null) {
            return $this->handleKegiatanAsurans($request, $slug, $moduleConfig);
        }

        $title = $this->pages[$slug];
        $user = Session::get('user', []);

        $questions = $this->questionPresets[$slug] ?? [1 => 'Kriteria 1', 2 => 'Kriteria 2', 3 => 'Kriteria 3', 4 => 'Kriteria 4'];
        $weights = $this->weightPresets[$slug] ?? $this->evenWeights(count($questions));

        $rules = [
            'scores' => 'required|array',
            'notes' => 'nullable|string|max:500',
            'verify' => 'nullable|boolean',
        ];
        foreach ($questions as $id => $label) {
            $rules["scores.$id"] = 'required|numeric|min:1|max:5';
        }

        $data = $request->validate($rules);

        $scores = [];
        $weighted = 0.0;
        foreach ($questions as $qid => $label) {
            $score = (float)($data['scores'][$qid] ?? 0);
            $scores[$qid] = $score;
            $weighted += $score * ($weights[$qid] ?? 0);
        }
        $weighted = round($weighted, 2);
        $levelData = $this->mapLevel($weighted);

        ElementAssessment::create([
            'subtopic_slug' => $slug,
            'subtopic_title' => $title,
            'scores' => $scores,
            'weighted_total' => $weighted,
            'level' => $levelData['level'],
            'predikat' => $levelData['predikat'],
            'notes' => $data['notes'] ?? null,
            'submitted_by' => $user['username'] ?? null,
            'verified_by' => ($request->boolean('verify') ? ($user['username'] ?? null) : null),
            'verified_at' => $request->boolean('verify') ? now() : null,
        ]);

        return back()->with('status', 'Penilaian tersimpan. Total: ' . $weighted . ' (' . $levelData['predikat'] . ')');
    }

    private function renderForm(string $slug)
    {
        $title = $this->pages[$slug];
        $assessments = ElementAssessment::where('subtopic_slug', $slug)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $questions = $this->questionPresets[$slug] ?? [1 => 'Kriteria 1', 2 => 'Kriteria 2', 3 => 'Kriteria 3', 4 => 'Kriteria 4'];
        $weights = $this->weightPresets[$slug] ?? $this->evenWeights(count($questions));

        return view('elements.form', [
            'title' => $title,
            'slug' => $slug,
            'weights' => $weights,
            'questions' => $questions,
            'assessments' => $assessments,
            'user' => Session::get('user'),
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
        ]);
    }

    private function renderElementSummary(string $slug, ?array $summaryConfig = null)
    {
        $summaryConfig ??= $this->getElementSummaryModuleConfig($slug) ?? [];
        $elementTitle = (string) ($summaryConfig['title'] ?? ($this->pages[$slug] ?? Str::headline($slug)));
        $summaryView = (string) ($summaryConfig['view'] ?? 'elements.element1-summary');
        $summaryStyles = array_values(array_filter((array) ($summaryConfig['styles'] ?? [
            'css/element1-kegiatan-asurans.css',
            'css/element1-summary.css',
        ])));
        $summaryHeaderCode = (string) ($summaryConfig['header_code'] ?? strtoupper((string) Str::of($slug)->replace('element', 'E')));
        $summaryHeaderSubtitle = (string) ($summaryConfig['header_subtitle'] ?? ('Rekap skor dan level dari sub topik '.Str::title(str_replace('_', ' ', $slug))));
        $summaryLevelLabel = (string) ($summaryConfig['level_label'] ?? ('Level '.Str::headline($slug)));
        $summaryInfoModalTitle = (string) ($summaryConfig['info_modal_title'] ?? ('Informasi Level '.Str::headline($slug)));
        $summaryInfoLevels = collect((array) ($summaryConfig['info_levels'] ?? []))
            ->map(fn ($item) => is_array($item) ? $item : [])
            ->values();

        $elementWeight = (float) ($summaryConfig['element_weight'] ?? 0.40);

        $allSubtopicConfigs = collect((array) config('element_subtopic_modules.modules', []))
            ->filter(function ($config, $moduleSlug) use ($slug) {
                return is_array($config) && Str::startsWith((string) $moduleSlug, $slug.'_');
            });

        $configuredSubtopicSlugs = collect((array) ($summaryConfig['subtopic_slugs'] ?? []))
            ->map(fn ($item) => (string) $item)
            ->filter(fn ($item) => $item !== '')
            ->values();

        $subtopicSummaries = ($configuredSubtopicSlugs->isNotEmpty() ? $configuredSubtopicSlugs : $allSubtopicConfigs->keys())
            ->map(function ($moduleSlug) use ($allSubtopicConfigs) {
                $resolvedSlug = (string) $moduleSlug;
                $moduleConfig = $allSubtopicConfigs->get($resolvedSlug);
                if (!is_array($moduleConfig)) {
                    return $this->placeholderSubtopicSummary($resolvedSlug);
                }
                return $this->buildSubtopicSummary($resolvedSlug, $moduleConfig);
            })
            ->filter(fn ($item) => is_array($item))
            ->values();

        $configuredWeightsRaw = collect((array) ($summaryConfig['subtopic_weights'] ?? []))
            ->mapWithKeys(fn ($weight, $moduleSlug) => [(string) $moduleSlug => (float) $weight]);
        $configuredWeightTotal = (float) $configuredWeightsRaw->sum();
        $weightDivider = $configuredWeightTotal > 1.5 ? 100 : 1;
        $configuredWeights = $configuredWeightsRaw->map(fn ($weight) => (float) $weight / $weightDivider);

        $defaultWeight = 0.0;
        if ($subtopicSummaries->count() > 0) {
            if ($configuredWeights->isEmpty()) {
                $defaultWeight = 1 / $subtopicSummaries->count();
            } else {
                $missingCount = (int) $subtopicSummaries
                    ->filter(fn ($item) => !$configuredWeights->has((string) ($item['slug'] ?? '')))
                    ->count();
                $configuredTotal = (float) $configuredWeights->sum();
                if ($missingCount > 0 && $configuredTotal < 1) {
                    $defaultWeight = (1 - $configuredTotal) / $missingCount;
                }
            }
        }

        $scoreComponents = $subtopicSummaries
            ->map(function ($item) use ($configuredWeights, $defaultWeight) {
                $subtopicSlug = (string) ($item['slug'] ?? '');
                $weight = (float) ($configuredWeights->get($subtopicSlug, $defaultWeight));
                $score = (float) ($item['score'] ?? 0);
                return [
                    'slug' => $subtopicSlug,
                    'title' => (string) ($item['title'] ?? $subtopicSlug),
                    'score' => $score,
                    'weight' => $weight,
                    'weighted_score' => $score * $weight,
                ];
            })
            ->values();

        $subtopicCount = $subtopicSummaries->count();
        $elementScore = (float) number_format((float) $scoreComponents->sum('weighted_score'), 2, '.', '');
        $weightedTotal = (float) number_format($elementScore * $elementWeight, 2, '.', '');
        $levelData = $this->mapLevel($elementScore);

        $totalRows = (int) $subtopicSummaries->sum('rows_total');
        $totalVerifiedRows = (int) $subtopicSummaries->sum('rows_verified');
        $completion = $totalRows > 0
            ? (int) round(($totalVerifiedRows / $totalRows) * 100)
            : 0;

        return view($summaryView, [
            'title' => $elementTitle,
            'slug' => $slug,
            'subtopicSummaries' => $subtopicSummaries,
            'scoreComponents' => $scoreComponents,
            'elementScore' => $elementScore,
            'elementWeight' => $elementWeight,
            'weightedTotal' => $weightedTotal,
            'levelData' => $levelData,
            'subtopicCount' => $subtopicCount,
            'totalRows' => $totalRows,
            'totalVerifiedRows' => $totalVerifiedRows,
            'completion' => $completion,
            'summaryStyles' => $summaryStyles,
            'summaryHeaderCode' => $summaryHeaderCode,
            'summaryHeaderSubtitle' => $summaryHeaderSubtitle,
            'summaryLevelLabel' => $summaryLevelLabel,
            'summaryInfoModalTitle' => $summaryInfoModalTitle,
            'summaryInfoLevels' => $summaryInfoLevels,
            'user' => Session::get('user'),
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
        ]);
    }

    private function buildSubtopicSummary(string $slug, array $moduleConfig): array
    {
        $moduleModelClass = (string) ($moduleConfig['model'] ?? Element1KegiatanAsurans::class);
        $moduleWeights = (array) ($moduleConfig['weights'] ?? $this->kegiatanWeights);
        $subtopicTitle = (string) ($moduleConfig['subtopic_title'] ?? ($this->pages[$slug] ?? $slug));
        $subtopicCode = (string) ($moduleConfig['subtopic_code'] ?? '');
        $moduleInfoLevels = collect((array) ($moduleConfig['info_levels'] ?? []))
            ->map(fn ($item) => is_array($item) ? $item : [])
            ->keyBy(fn ($item) => (int) ($item['level'] ?? 0));

        if ($moduleModelClass === '' || !class_exists($moduleModelClass)) {
            return array_merge($this->placeholderSubtopicSummary($slug), [
                'code' => $subtopicCode,
                'title' => $subtopicTitle,
            ]);
        }

        $table = (new $moduleModelClass())->getTable();
        if (!Schema::hasTable($table)) {
            return array_merge($this->placeholderSubtopicSummary($slug), [
                'code' => $subtopicCode,
                'title' => $subtopicTitle,
            ]);
        }

        $this->ensureKegiatanRows($slug, $moduleConfig);
        $rows = $moduleModelClass::orderBy('id')->get(['id', 'level', 'skor', 'verified']);

        $summaryScore = 0.0;
        foreach ($rows as $row) {
            if (is_numeric($row->skor)) {
                $summaryScore += (float) $row->skor;
            } elseif (is_numeric($row->level)) {
                $summaryScore += $this->scoreForKegiatanLevel((float) $row->level, (int) $row->id, $moduleWeights);
            }
        }
        $summaryScore = (float) number_format($summaryScore, 2, '.', '');
        $levelData = $this->mapLevel($summaryScore);
        $levelInt = (int) ($levelData['level'] ?? 1);
        $levelInfo = (array) ($moduleInfoLevels->get($levelInt, []));

        $rowsTotal = $rows->count();
        $rowsVerified = (int) $rows->filter(fn ($row) => (int) ($row->verified ?? 0) === 1)->count();
        $progress = $rowsTotal > 0
            ? (int) round(($rowsVerified / $rowsTotal) * 100)
            : 0;

        return [
            'slug' => $slug,
            'code' => $subtopicCode,
            'title' => $subtopicTitle,
            'score' => $summaryScore,
            'level' => $levelInt,
            'predikat' => (string) $levelData['predikat'],
            'level_description' => (string) ($levelInfo['description'] ?? ''),
            'level_score_range' => (string) ($levelInfo['score_range'] ?? ''),
            'rows_total' => $rowsTotal,
            'rows_verified' => $rowsVerified,
            'progress' => $progress,
        ];
    }

    private function placeholderSubtopicSummary(string $slug): array
    {
        return [
            'slug' => $slug,
            'code' => '',
            'title' => (string) ($this->pages[$slug] ?? Str::headline(str_replace('_', ' ', $slug))),
            'score' => null,
            'level' => null,
            'predikat' => 'Belum Dinilai',
            'level_description' => '',
            'level_score_range' => '',
            'rows_total' => 0,
            'rows_verified' => 0,
            'progress' => 0,
        ];
    }

    private function renderKegiatanAsurans(string $slug = 'element1_kegiatan_asurans', ?array $moduleConfig = null)
    {
        $moduleConfig ??= $this->getSubtopicModuleConfig($slug) ?? [];
        $moduleModelClass = (string) ($moduleConfig['model'] ?? Element1KegiatanAsurans::class);
        $moduleEditLogModelClass = (string) ($moduleConfig['edit_log_model'] ?? Element1KegiatanAsuransEditLog::class);
        $moduleWeights = (array) ($moduleConfig['weights'] ?? $this->kegiatanWeights);
        $modulePageTitle = (string) ($moduleConfig['page_title'] ?? ($this->pages[$slug] ?? $this->pages['element1_kegiatan_asurans']));
        $moduleSubtopicCode = (string) ($moduleConfig['subtopic_code'] ?? 'S1');
        $moduleSubtopicTitle = (string) ($moduleConfig['subtopic_title'] ?? ($this->pages[$slug] ?? 'Sub Topik'));
        $moduleInfoModalTitle = (string) ($moduleConfig['info_modal_title'] ?? ('Informasi Level '.$moduleSubtopicTitle));
        $moduleInfoLevels = collect($moduleConfig['info_levels'] ?? [])->values();
        $moduleView = (string) ($moduleConfig['view'] ?? 'elements.element1-kegiatan-asurans');
        $statementLevelHintMap = collect((array) ($moduleConfig['statement_level_hints'] ?? []))
            ->mapWithKeys(function ($hints, $statement) {
                return [$this->normalizeStatementKey((string) $statement) => (array) $hints];
            })
            ->all();

        $user = Session::get('user', []);
        $canVerify = $this->canUserVerifySlug($user, $slug);
        $dmsTypeOptions = $this->dmsTypeOptions();

        if ($moduleModelClass === '' || !class_exists($moduleModelClass)) {
            return redirect()
                ->route('elements.show', Str::before($slug, '_'))
                ->with('error', 'Konfigurasi modul belum lengkap.');
        }

        $moduleTable = (new $moduleModelClass())->getTable();
        if (!Schema::hasTable($moduleTable)) {
            return redirect()
                ->route('elements.show', Str::before($slug, '_'))
                ->with('error', 'Tabel modul belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $this->ensureKegiatanRows($slug, $moduleConfig);

        $rows = $moduleModelClass::orderBy('id')->get();

        $summaryScore = 0.0;
        foreach ($rows as $row) {
            if (is_numeric($row->skor)) {
                $summaryScore += (float)$row->skor;
            } elseif (is_numeric($row->level)) {
                $summaryScore += $this->scoreForKegiatanLevel((float)$row->level, (int)$row->id, $moduleWeights);
            }
        }
        $summaryScore = (float)number_format($summaryScore, 2, '.', '');
        $levelData = $this->mapLevel($summaryScore);

        $verifyNotes = $rows->filter(function ($r) {
            return trim((string)($r->verify_note ?? '')) !== '';
        })->map(function ($r) {
            return [
                'id' => $r->id,
                'pernyataan' => $r->pernyataan,
                'note' => $r->verify_note,
            ];
        })->values();

        $dmsFiles = DmsFile::with('document')
            ->whereHas('document', function ($q) {
                $q->where('status', 'Aktif');
            })
            ->orderByDesc('uploaded_at')
            ->limit(120)
            ->get()
            ->map(function ($file) use ($dmsTypeOptions) {
                $doc = $file->document;
                $typeAndTag = $this->normalizeDmsTypeTag(
                    (string) ($doc?->type ?? ''),
                    (string) ($doc?->tag ?? ''),
                    $dmsTypeOptions
                );
                $labelParts = array_filter([
                    $doc?->doc_no,
                    $doc?->title,
                    $file->doc_name,
                ]);
                $label = implode(' - ', $labelParts);
                $path = ltrim((string)($file->file_path ?? ''), '/');
                return [
                    'id' => $file->id,
                    'label' => $label ?: 'Berkas DMS',
                    'url' => $path ? '/uploads/'.$path : '',
                    'type' => $typeAndTag['type'],
                    'tag' => $typeAndTag['tag'],
                ];
            });

        $editLogs = collect();
        $editLogTable = (new $moduleEditLogModelClass())->getTable();
        if (Schema::hasTable($editLogTable)) {
            $editLogs = $moduleEditLogModelClass::query()
                ->orderByDesc('created_at')
                ->limit(120)
                ->get(['row_id', 'pernyataan', 'username', 'display_name', 'action', 'created_at']);
        }

        return view($moduleView, [
            'title' => $moduleSubtopicTitle,
            'slug' => $slug,
            'rows' => $rows,
            'summaryScore' => $summaryScore,
            'summaryLevel' => $levelData['level'],
            'summaryPredikat' => $levelData['predikat'],
            'canVerify' => $canVerify,
            'weights' => $moduleWeights,
            'verifyNotes' => $verifyNotes,
            'dmsFiles' => $dmsFiles,
            'editLogs' => $editLogs,
            'user' => $user,
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
            'modulePageTitle' => $modulePageTitle,
            'moduleSubtopicCode' => $moduleSubtopicCode,
            'moduleSubtopicTitle' => $moduleSubtopicTitle,
            'moduleInfoModalTitle' => $moduleInfoModalTitle,
            'moduleInfoLevels' => $moduleInfoLevels,
            'statementLevelHintMap' => $statementLevelHintMap,
        ]);
    }

    private function handleKegiatanAsurans(Request $request, string $slug = 'element1_kegiatan_asurans', ?array $moduleConfig = null)
    {
        $moduleConfig ??= $this->getSubtopicModuleConfig($slug) ?? [];
        $moduleModelClass = (string) ($moduleConfig['model'] ?? Element1KegiatanAsurans::class);
        $moduleEditLogModelClass = (string) ($moduleConfig['edit_log_model'] ?? Element1KegiatanAsuransEditLog::class);
        $moduleWeights = (array) ($moduleConfig['weights'] ?? $this->kegiatanWeights);
        $moduleNotificationTitle = (string) ($moduleConfig['notification_title'] ?? 'Element 1 - Kegiatan Asurans');
        $moduleEditLogTable = (new $moduleEditLogModelClass())->getTable();

        $action = $request->input('action', 'save');
        $user = Session::get('user', []);
        $username = trim((string)($user['username'] ?? ''));

        if ($moduleModelClass === '' || !class_exists($moduleModelClass)) {
            return back()->withErrors('Konfigurasi modul belum lengkap.');
        }

        $moduleTable = (new $moduleModelClass())->getTable();
        if (!Schema::hasTable($moduleTable)) {
            return back()->withErrors('Tabel modul belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $id = (int)$request->input('row_id', 0);
        abort_unless($id > 0, 404);

        $this->ensureKegiatanRows($slug, $moduleConfig);
        $row = $moduleModelClass::findOrFail($id);
        $canVerify = $this->canUserVerifySlug($user, $slug);

        if ($action === 'save') {
            $isVerifiedRow = (int) $row->verified === 1;
            $savedLevelValidationState = collect($row->level_validation_state ?? [])
                ->mapWithKeys(function ($value, $key) {
                    $level = (int) $key;
                    if ($level < 1 || $level > 5) {
                        return [];
                    }
                    return [(string) $level => ((int) $value === 1 ? 1 : 0)];
                })
                ->all();

            $rules = [
                'pernyataan' => 'required|string|max:255',
                'analisis_bukti' => 'nullable|string',
                'analisis_nilai' => 'nullable|string',
                'evidence' => 'nullable|string',
                'dokumen_path' => 'nullable|string|max:255',
                'grad_l1_catatan' => 'nullable|string',
                'grad_l2_catatan' => 'nullable|string',
                'grad_l3_catatan' => 'nullable|string',
                'grad_l4_catatan' => 'nullable|string',
                'grad_l5_catatan' => 'nullable|string',
            ];

            if ($isVerifiedRow) {
                $rules['doc_file_ids'] = 'nullable|array';
            } else {
                $rules['doc_file_ids'] = 'required|array|min:1';
            }
            $rules['doc_file_ids.*'] = 'integer|exists:dms_files,id';

            $data = $request->validate($rules, [
                'doc_file_ids.required' => 'Pilih minimal 1 dokumen sebelum menyimpan.',
                'doc_file_ids.array' => 'Format pilihan dokumen tidak valid.',
                'doc_file_ids.min' => 'Pilih minimal 1 dokumen sebelum menyimpan.',
            ]);

            $activeDocIds = collect();
            $docPath = null;

            if ($isVerifiedRow) {
                $activeDocIds = collect($row->doc_file_ids ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->unique()
                    ->values();
                $docPath = $row->dokumen_path;
            } else {
                $requestedDocIds = collect($data['doc_file_ids'] ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->filter(fn ($id) => $id > 0)
                    ->unique()
                    ->values();

                $resolvedFiles = collect();
                if ($requestedDocIds->isNotEmpty()) {
                    $resolvedFiles = DmsFile::query()
                        ->whereHas('document', function ($q) {
                            $q->where('status', 'Aktif');
                        })
                        ->whereIn('id', $requestedDocIds->all())
                        ->get(['id', 'file_path']);
                }

                $pathByFileId = $resolvedFiles
                    ->mapWithKeys(function ($file) {
                        $path = ltrim((string)($file->file_path ?? ''), '/');
                        return [(int) $file->id => ($path ? '/uploads/'.$path : null)];
                    })
                    ->filter(fn ($path) => !empty($path));

                $activeDocIds = $requestedDocIds
                    ->filter(fn ($id) => $pathByFileId->has($id))
                    ->values();

                if ($activeDocIds->isEmpty()) {
                    return back()->withErrors('Pilih minimal 1 dokumen aktif sebelum menyimpan.');
                }

                $docPath = $activeDocIds->isNotEmpty()
                    ? $pathByFileId->get((int) $activeDocIds->first())
                    : null;
            }

            $payload = [
                'pernyataan' => $data['pernyataan'],
                'analisis_bukti' => $isVerifiedRow ? $row->analisis_bukti : ($data['analisis_bukti'] ?? null),
                'analisis_nilai' => $isVerifiedRow ? $row->analisis_nilai : ($data['analisis_nilai'] ?? null),
                'evidence' => $isVerifiedRow ? $row->evidence : ($data['evidence'] ?? null),
                'dokumen_path' => $docPath,
                'doc_file_ids' => $activeDocIds->values()->all(),
            ];

            $hasEditableUnvalidatedLevel = false;
            foreach (range(1, 5) as $level) {
                $field = 'grad_l'.$level.'_catatan';
                $isLevelLocked = $isVerifiedRow && ((int) data_get($savedLevelValidationState, (string) $level, 0) === 1);
                if ($isLevelLocked) {
                    $payload[$field] = $row->{$field};
                } else {
                    $payload[$field] = $data[$field] ?? null;
                    if ($isVerifiedRow) {
                        $hasEditableUnvalidatedLevel = true;
                    }
                }
            }

            if ($isVerifiedRow && !$hasEditableUnvalidatedLevel) {
                return back()->withErrors('Semua level sudah terverifikasi dan tidak dapat diubah.');
            }

            if (!$isVerifiedRow) {
                $payload['level_validation_state'] = null;
            }

            $row->fill($payload);
            if (!$isVerifiedRow) {
                $row->verified = 0;
            }

            if (!$row->isDirty()) {
                return back()->with('status', 'Tidak ada perubahan data. Riwayat tidak diperbarui.');
            }

            $row->save();

            if (Schema::hasTable($moduleEditLogTable)) {
                $moduleEditLogModelClass::query()->create([
                    'row_id' => (int) $row->id,
                    'pernyataan' => (string) ($row->pernyataan ?? ''),
                    'username' => $username !== '' ? $username : null,
                    'display_name' => trim((string) ($user['display_name'] ?? '')) ?: null,
                    'action' => 'save',
                ]);
            }

            return back()->with('status', 'Data disimpan.');
        }

        if ($action === 'clear') {
            if ((int)$row->verified === 1) {
                return back()->withErrors('Data terverifikasi tidak dapat dihapus.');
            }
            $row->fill([
                'analisis_bukti' => null,
                'analisis_nilai' => null,
                'evidence' => null,
                'dokumen_path' => null,
                'doc_file_ids' => null,
                'grad_l1_catatan' => null,
                'grad_l2_catatan' => null,
                'grad_l3_catatan' => null,
                'grad_l4_catatan' => null,
                'grad_l5_catatan' => null,
                'level_validation_state' => null,
                'verify_note' => null,
            ]);
            $row->verified = 0;
            $row->level = '-';
            $row->skor = null;
            $row->save();

            if (Schema::hasTable($moduleEditLogTable)) {
                $moduleEditLogModelClass::query()->create([
                    'row_id' => (int) $row->id,
                    'pernyataan' => (string) ($row->pernyataan ?? ''),
                    'username' => $username !== '' ? $username : null,
                    'display_name' => trim((string) ($user['display_name'] ?? '')) ?: null,
                    'action' => 'clear',
                ]);
            }

            return back()->with('status', 'Data dibersihkan.');
        }

        if ($action === 'verify') {
            if (!$canVerify) {
                return back()->withErrors('Akses verifikasi hanya untuk Koordinator/Admin.');
            }
            $validated = $request->validate([
                'verified' => 'required|boolean',
                'verify_note' => 'nullable|string|max:500',
                'level_validation' => 'nullable|array',
                'level_validation.*' => 'nullable|boolean',
            ]);
            $isVerified = (int)$validated['verified'] === 1;

            $levelValidationState = collect(range(1, 5))
                ->mapWithKeys(function ($level) use ($validated) {
                    $isValid = (int) data_get($validated, 'level_validation.'.$level, 0) === 1;
                    return [(string) $level => $isValid ? 1 : 0];
                })
                ->all();

            $validatedLevels = collect($levelValidationState)
                ->filter(fn ($isValid) => (int) $isValid === 1)
                ->keys()
                ->map(fn ($level) => (int) $level)
                ->filter(fn ($level) => $level >= 1 && $level <= 5)
                ->values();

            if ($isVerified && $validatedLevels->isEmpty()) {
                return back()->withErrors('Klik tombol Verifikasi pada minimal 1 level sebelum menyimpan verifikasi.');
            }

            if ($isVerified) {
                $maxValidatedLevel = (int) ($validatedLevels->max() ?? 0);
                $missingChainLevels = collect(range(1, $maxValidatedLevel))
                    ->reject(fn ($level) => $validatedLevels->contains($level))
                    ->values();

                if ($missingChainLevels->isNotEmpty()) {
                    return back()->withErrors('Verifikasi level harus berurutan. Verifikasi Level 1 terlebih dahulu, lalu Level 2, dan seterusnya.');
                }
            }

            $wasVerified = (int)$row->verified === 1;
            $levelVal = $isVerified ? (float)$validatedLevels->max() : null;
            $row->verified = $isVerified ? 1 : 0;
            $row->level = $isVerified ? (string)$levelVal : '-';
            $row->skor = $isVerified ? $this->scoreForKegiatanLevel($levelVal, (int)$row->id, $moduleWeights) : null;
            $row->level_validation_state = $isVerified ? $levelValidationState : null;
            $row->verify_note = $isVerified ? ($validated['verify_note'] ?? null) : null;

            if (!$row->isDirty()) {
                return back()->with('status', 'Tidak ada perubahan verifikasi. Riwayat tidak diperbarui.');
            }

            $row->save();

            if (Schema::hasTable($moduleEditLogTable)) {
                $moduleEditLogModelClass::query()->create([
                    'row_id' => (int) $row->id,
                    'pernyataan' => (string) ($row->pernyataan ?? ''),
                    'username' => $username !== '' ? $username : null,
                    'display_name' => trim((string) ($user['display_name'] ?? '')) ?: null,
                    'action' => $isVerified ? 'verify' : 'verify_reset',
                ]);
            }

            if ($isVerified && !$wasVerified) {
                Notification::create([
                    'subtopic_title' => $moduleNotificationTitle,
                    'statement' => $row->pernyataan,
                    'row_id' => $row->id,
                    'coordinator_name' => $user['display_name'] ?? ($user['username'] ?? 'Koordinator'),
                    'coordinator_username' => $user['username'] ?? 'koordinator',
                    'created_at' => now(),
                ]);
            }

            return back()->with('status', $isVerified ? 'Data diverifikasi.' : 'Status verifikasi direset.');
        }

        abort(400, 'Aksi tidak dikenal.');
    }

    private function ensureKegiatanRows(string $slug = 'element1_kegiatan_asurans', ?array $moduleConfig = null): void
    {
        $moduleConfig ??= $this->getSubtopicModuleConfig($slug) ?? [];
        $moduleModelClass = (string) ($moduleConfig['model'] ?? Element1KegiatanAsurans::class);
        $defaults = (array) ($moduleConfig['rows'] ?? ($this->questionPresets[$slug] ?? []));

        if (empty($defaults)) {
            return;
        }

        if ($moduleModelClass === '' || !class_exists($moduleModelClass)) {
            return;
        }

        $table = (new $moduleModelClass())->getTable();
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($defaults as $id => $pernyataan) {
            DB::table($table)->updateOrInsert(
                ['id' => (int) $id],
                ['pernyataan' => (string) $pernyataan]
            );
        }

        DB::table($table)->whereNotIn('id', array_map('intval', array_keys($defaults)))->delete();
    }

    private function scoreForKegiatanLevel(float $level, int $rowId, ?array $weights = null): float
    {
        $activeWeights = $weights ?? $this->kegiatanWeights;
        $bobot = $activeWeights[$rowId] ?? 0.25;
        return round($level * $bobot, 2);
    }

    private function renderBlankElement(string $slug)
    {
        return view('elements.blank', [
            'title' => $this->pages[$slug] ?? 'Element',
            'user' => Session::get('user', []),
        ]);
    }

    private function evenWeights(int $count): array
    {
        if ($count <= 0) {
            return [];
        }
        $w = round(1 / $count, 4);
        $weights = [];
        for ($i = 1; $i <= $count; $i++) {
            $weights[$i] = $w;
        }
        return $weights;
    }

    private function mapLevel(float $score): array
    {
        return match (true) {
            $score >= 5 => ['level' => 5, 'predikat' => 'Optimal'],
            $score >= 3.99 => ['level' => 4, 'predikat' => 'Terintegrasi'],
            $score >= 2.99 => ['level' => 3, 'predikat' => 'Memadai'],
            $score >= 1.99 => ['level' => 2, 'predikat' => 'Terstruktur'],
            default => ['level' => 1, 'predikat' => 'Rintisan'],
        };
    }

    private function getSubtopicModuleConfig(string $slug): ?array
    {
        $modules = (array) config('element_subtopic_modules.modules', []);
        $moduleConfig = $modules[$slug] ?? null;
        return is_array($moduleConfig) ? $moduleConfig : null;
    }

    private function getElementSummaryModuleConfig(string $slug): ?array
    {
        $modules = (array) config('element_summary_modules.modules', []);
        $moduleConfig = $modules[$slug] ?? null;
        return is_array($moduleConfig) ? $moduleConfig : null;
    }

    private function visiblePagesForUser(array $user): array
    {
        $assignedElementSlugs = ElementTeamAssignment::assignedElementSlugsForUser($user);
        if ($assignedElementSlugs === null) {
            return $this->pages;
        }

        return collect($this->pages)
            ->filter(function ($title, $slug) use ($assignedElementSlugs) {
                return in_array(ElementTeamAssignment::topLevelElementSlug((string) $slug), $assignedElementSlugs, true);
            })
            ->all();
    }

    private function guardElementAccess(string $slug): ?\Illuminate\Http\RedirectResponse
    {
        $user = Session::get('user', []);
        if (ElementTeamAssignment::canUserAccessSlug($user, $slug)) {
            return null;
        }

        return redirect()
            ->to(route('dashboard', [], false))
            ->with('error', 'Anda tidak memiliki akses ke element yang dipilih.');
    }

    private function canUserVerifySlug(array $user, string $slug): bool
    {
        $role = strtolower(trim((string) ($user['role'] ?? '')));
        if (in_array($role, ['administrator', 'admin', 'superadmin'], true)) {
            return true;
        }

        if ($role !== 'koordinator') {
            return false;
        }

        if (!Schema::hasTable('element_team_assignments')) {
            return true;
        }

        $username = strtolower(trim((string) ($user['username'] ?? '')));
        if ($username === '') {
            return false;
        }

        $assignedCoordinator = strtolower((string) ElementTeamAssignment::coordinatorUsernameForSlug($slug));

        return $assignedCoordinator !== '' && $assignedCoordinator === $username;
    }

    private function normalizeStatementKey(string $statement): string
    {
        return Str::lower(trim($statement));
    }

    private function dmsTypeOptions(): array
    {
        return [
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
        ];
    }

    private function normalizeDmsTypeTag(string $rawType, string $rawTag, array $typeOptions): array
    {
        $type = trim($rawType);
        $tag = trim($rawTag);

        $typeAliases = [
            'SDM' => 'Sumber Daya Manusia',
        ];
        if (isset($typeAliases[$type])) {
            $type = $typeAliases[$type];
        }

        if (
            stripos($type, 'surat tugas') !== false
            && !array_key_exists($type, $typeOptions)
        ) {
            $type = 'Manajemen Pengawasan';
            if ($tag === '' || strcasecmp($tag, 'Dokumen') === 0) {
                $tag = 'Surat Tugas';
            }
        }

        $hasKnownType = array_key_exists($type, $typeOptions);
        if (!$hasKnownType && ($type === '' || strcasecmp($type, 'dokumen') === 0)) {
            $type = 'Lainnya';
            $hasKnownType = true;
        }

        $allowedTags = $hasKnownType ? ($typeOptions[$type] ?? []) : [];
        if (($tag === '' || strcasecmp($tag, 'Dokumen') === 0) && count($allowedTags) === 1) {
            $tag = $allowedTags[0];
        }

        if ($tag === '') {
            $tag = 'Tanpa Sub Jenis';
        }
        if ($type === '') {
            $type = 'Tanpa Jenis';
        }

        return [
            'type' => $type,
            'tag' => $tag,
        ];
    }

    private function renderLegacyElement(string $slug, string $file)
    {
        $user = Session::get('user', []);
        $legacyPath = realpath(
            base_path('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'backup latsar'.DIRECTORY_SEPARATOR.$file)
        );
        if (!$legacyPath || !is_file($legacyPath)) {
            abort(500, "Legacy file {$file} tidak ditemukan.");
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = $user;

        ob_start();
        include $legacyPath;
        $html = ob_get_clean();

        $styles = '';
        if (preg_match_all('/<style[^>]*>(.*?)<\\/style>/is', $html, $matches)) {
            $styles = implode("\n", $matches[1]);
        }
        $body = $html;
        if (preg_match('/<body[^>]*>(.*)<\\/body>/is', $html, $m)) {
            $body = $m[1];
        }
        // hilangkan header bawaan (page-header) jika ada
        $body = preg_replace('/<div class="page-header">.*?<\\/div>/s', '', $body, 1);
        // hapus seluruh iframe pada konten legacy agar tidak terpisah/layout blur
        $body = preg_replace('/<iframe[^>]*>.*?<\\/iframe>/is', '<div class="iframe-removed" style="display:none;"></div>', $body);
        // hilangkan styling iframe terkait
        $body = preg_replace('/\\.(verify-doc-preview\\s*iframe|iframe)[^\\{]*\\{[^}]*\\}/i', '', $body);
        // sisipkan CSRF ke semua form
        $token = csrf_token();
        $body = preg_replace(
            '/<form\b([^>]*)>/i',
            '<form$1>'."\n".'<input type="hidden" name="_token" value="'.$token.'">',
            $body
        );

        return view('legacy-wrapper', [
            'pageTitle' => $this->pages[$slug] ?? 'Element',
            'showPageTitle' => true,
            'legacyBody' => $body,
            'legacyStyles' => $styles,
            'user' => $user,
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
        ]);
    }
}

