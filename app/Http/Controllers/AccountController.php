<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ElementTeamAssignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    private const ELEMENT_OPTIONS = [
        'element1' => 'Element 1 : Kualitas Peran dan Layanan',
        'element2' => 'Element 2 : Profesionalisme Penugasan',
        'element3' => 'Element 3 : Manajemen Pengawasan',
        'element4' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
        'element5' => 'Element 5 : Budaya dan Hubungan Organisasi',
    ];

    public function index(Request $request)
    {
        $user = Session::get('user');
        $accounts = Account::query()
            ->orderByRaw("
                CASE
                    WHEN LOWER(role) IN ('administrator', 'admin', 'superadmin') THEN 0
                    WHEN LOWER(role) = 'koordinator' THEN 1
                    WHEN LOWER(role) = 'qa' THEN 2
                    ELSE 3
                END
            ")
            ->orderByRaw("
                CASE
                    WHEN COALESCE(NULLIF(TRIM(display_name), ''), '') = '' THEN 1
                    ELSE 0
                END
            ")
            ->orderBy('display_name')
            ->orderBy('username')
            ->paginate(15);
        $coordinators = Account::query()
            ->where('active', true)
            ->where('role', 'koordinator')
            ->orderBy('display_name')
            ->orderBy('username')
            ->get();
        $teamMembers = Account::query()
            ->where('active', true)
            ->where('role', 'auditor')
            ->orderBy('display_name')
            ->orderBy('username')
            ->get();
        $hasElementAssignmentTable = Schema::hasTable('element_team_assignments');
        $elementAssignments = $hasElementAssignmentTable
            ? ElementTeamAssignment::query()->get()->keyBy('element_slug')
            : collect();

        return view('accounts.index', [
            'pageTitle' => 'Manajemen Akun',
            'user' => $user,
            'accounts' => $accounts,
            'coordinators' => $coordinators,
            'teamMembers' => $teamMembers,
            'elementOptions' => self::ELEMENT_OPTIONS,
            'elementAssignments' => $elementAssignments,
            'hasElementAssignmentTable' => $hasElementAssignmentTable,
            'notifications' => Notification::orderByDesc('created_at')->limit(50)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $action = $request->input('action');
        switch ($action) {
            case 'create_user':
                $data = $request->validate([
                    'new_username' => 'required|string|min:3|max:100|unique:accounts,username',
                    'new_display_name' => 'nullable|string|max:150',
                    'new_role' => 'required|string|in:administrator,koordinator,qa,auditor',
                    'new_password' => 'required|string|min:6',
                ]);
                Account::create([
                    'username' => $data['new_username'],
                    'display_name' => $data['new_display_name'] ?: $data['new_username'],
                    'role' => $data['new_role'],
                    'password_hash' => Hash::make($data['new_password']),
                    'active' => true,
                ]);
                return back()->with('status', 'Akun baru berhasil dibuat.');

            case 'save_element_assignment':
                if (!Schema::hasTable('element_team_assignments')) {
                    return back()->withErrors(['status' => 'Tabel penunjukan tim element belum tersedia. Jalankan migrasi terlebih dahulu.']);
                }

                $draftAssignments = $this->existingElementAssignmentDrafts();
                $payloadRaw = trim((string) $request->input('assignment_payload', ''));

                if ($payloadRaw !== '') {
                    $decodedPayload = json_decode($payloadRaw, true);
                    if (!is_array($decodedPayload)) {
                        return back()
                            ->withErrors(['status' => 'Format penunjukan tim element tidak valid. Muat ulang halaman lalu coba lagi.'])
                            ->withInput();
                    }

                    $draftAssignments = $this->normalizeElementAssignmentPayload($decodedPayload, $draftAssignments);
                } else {
                    $data = $request->validate([
                        'element_slug' => ['required', 'string', Rule::in(array_keys(self::ELEMENT_OPTIONS))],
                        'coordinator_username' => [
                            'nullable',
                            'string',
                            Rule::exists('accounts', 'username')->where(function ($query) {
                                $query->where('role', 'koordinator')->where('active', true);
                            }),
                        ],
                        'member_usernames' => ['nullable', 'array'],
                        'member_usernames.*' => [
                            'string',
                            Rule::exists('accounts', 'username')->where(function ($query) {
                                $query->where('role', 'auditor')->where('active', true);
                            }),
                        ],
                    ]);

                    $draftAssignments[$data['element_slug']] = [
                        'coordinator_username' => trim((string) ($data['coordinator_username'] ?? '')),
                        'member_usernames' => collect($data['member_usernames'] ?? [])
                            ->map(fn ($username) => trim((string) $username))
                            ->filter(fn ($username) => $username !== '')
                            ->unique()
                            ->values()
                            ->all(),
                    ];
                }

                $assignmentValidationError = $this->validateElementAssignmentDrafts($draftAssignments);
                if ($assignmentValidationError !== null) {
                    return back()
                        ->withErrors(['status' => $assignmentValidationError])
                        ->withInput();
                }

                $this->persistElementAssignmentDrafts($draftAssignments);

                return back()->with('status', 'Penunjukan tim element berhasil disimpan.');

            case 'reset_password':
                $data = $request->validate([
                    'reset_username' => 'required|string|exists:accounts,username',
                    'reset_password' => 'required|string|min:6',
                ]);
                Account::where('username', $data['reset_username'])
                    ->update(['password_hash' => Hash::make($data['reset_password'])]);
                return back()->with('status', 'Password berhasil direset.');

            case 'toggle_status':
                $data = $request->validate([
                    'toggle_username' => 'required|string|exists:accounts,username',
                ]);
                $account = Account::where('username', $data['toggle_username'])->firstOrFail();
                $sessionUser = Session::get('user', []);
                if (($sessionUser['username'] ?? '') === $account->username) {
                    return back()->withErrors(['status' => 'Tidak dapat menonaktifkan akun yang sedang digunakan.']);
                }
                $account->active = !$account->active;
                $account->save();
                return back()->with('status', 'Status akun diperbarui.');

            case 'delete_account':
                $data = $request->validate([
                    'delete_username' => 'required|string|exists:accounts,username',
                ]);
                $account = Account::where('username', $data['delete_username'])->firstOrFail();
                $sessionUser = Session::get('user', []);
                if (($sessionUser['username'] ?? '') === $account->username) {
                    return back()->withErrors(['status' => 'Tidak dapat menghapus akun yang sedang digunakan.']);
                }
                $account->delete();
                return back()->with('status', 'Akun berhasil dihapus.');

            default:
                return back()->withErrors(['status' => 'Aksi tidak dikenali.']);
        }
    }

    public function resetPassword(Request $request, Account $account)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $account->update([
            'password_hash' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password berhasil diperbarui.');
    }

    public function toggle(Account $account)
    {
        $sessionUser = Session::get('user', []);
        $currentUsername = $sessionUser['username'] ?? '';
        if ($account->username === $currentUsername) {
            return back()->withErrors(['status' => 'Tidak dapat menonaktifkan akun yang sedang digunakan.']);
        }

        $account->active = !$account->active;
        $account->save();

        return back()->with('status', 'Status akun diperbarui.');
    }

    private function existingElementAssignmentDrafts(): array
    {
        $existingAssignments = ElementTeamAssignment::query()
            ->get()
            ->keyBy('element_slug');

        $draftAssignments = [];
        foreach (array_keys(self::ELEMENT_OPTIONS) as $elementSlug) {
            $assignment = $existingAssignments->get($elementSlug);

            $draftAssignments[$elementSlug] = [
                'coordinator_username' => trim((string) ($assignment?->coordinator_username ?? '')),
                'member_usernames' => collect((array) ($assignment?->member_usernames ?? []))
                    ->map(fn ($username) => trim((string) $username))
                    ->filter(fn ($username) => $username !== '')
                    ->unique()
                    ->values()
                    ->all(),
            ];
        }

        return $draftAssignments;
    }

    private function normalizeElementAssignmentPayload(array $payload, array $baseDraftAssignments): array
    {
        $draftAssignments = $baseDraftAssignments;

        foreach (array_keys(self::ELEMENT_OPTIONS) as $elementSlug) {
            $assignment = $payload[$elementSlug] ?? null;
            if (!is_array($assignment)) {
                continue;
            }

            $draftAssignments[$elementSlug] = [
                'coordinator_username' => trim((string) ($assignment['coordinator_username'] ?? '')),
                'member_usernames' => collect($assignment['member_usernames'] ?? [])
                    ->map(fn ($username) => trim((string) $username))
                    ->filter(fn ($username) => $username !== '')
                    ->unique()
                    ->values()
                    ->all(),
            ];
        }

        return $draftAssignments;
    }

    private function validateElementAssignmentDrafts(array $draftAssignments): ?string
    {
        $usernames = collect($draftAssignments)
            ->flatMap(function (array $assignment) {
                $values = [];
                $coordinatorUsername = trim((string) ($assignment['coordinator_username'] ?? ''));
                if ($coordinatorUsername !== '') {
                    $values[] = $coordinatorUsername;
                }

                return array_merge($values, (array) ($assignment['member_usernames'] ?? []));
            })
            ->filter(fn ($username) => trim((string) $username) !== '')
            ->unique()
            ->values();

        $accountsByUsername = $usernames->isEmpty()
            ? collect()
            : Account::query()
                ->whereIn('username', $usernames->all())
                ->get()
                ->keyBy('username');

        $usedCoordinators = [];
        $usedMembers = [];

        foreach ($draftAssignments as $elementSlug => $assignment) {
            $elementLabel = $this->compactElementLabel((string) $elementSlug);
            $coordinatorUsername = trim((string) ($assignment['coordinator_username'] ?? ''));

            if ($coordinatorUsername !== '') {
                $coordinatorAccount = $accountsByUsername->get($coordinatorUsername);
                if (!$coordinatorAccount || !$coordinatorAccount->active || strtolower((string) $coordinatorAccount->role) !== 'koordinator') {
                    return 'Koordinator untuk '.$elementLabel.' harus menggunakan akun Koordinator yang aktif.';
                }

                if (isset($usedCoordinators[$coordinatorUsername]) && $usedCoordinators[$coordinatorUsername] !== $elementSlug) {
                    return 'Koordinator @'.$coordinatorUsername.' sudah dipakai pada '.$this->compactElementLabel($usedCoordinators[$coordinatorUsername]).'.';
                }

                $usedCoordinators[$coordinatorUsername] = $elementSlug;
            }

            foreach ((array) ($assignment['member_usernames'] ?? []) as $memberUsername) {
                $memberUsername = trim((string) $memberUsername);
                if ($memberUsername === '') {
                    continue;
                }

                $memberAccount = $accountsByUsername->get($memberUsername);
                if (!$memberAccount || !$memberAccount->active || strtolower((string) $memberAccount->role) !== 'auditor') {
                    return 'Anggota Tim untuk '.$elementLabel.' harus menggunakan akun Anggota Tim yang aktif.';
                }

                if (isset($usedMembers[$memberUsername]) && $usedMembers[$memberUsername] !== $elementSlug) {
                    return 'Anggota Tim @'.$memberUsername.' sudah dipakai pada '.$this->compactElementLabel($usedMembers[$memberUsername]).'.';
                }

                $usedMembers[$memberUsername] = $elementSlug;
            }
        }

        return null;
    }

    private function persistElementAssignmentDrafts(array $draftAssignments): void
    {
        DB::transaction(function () use ($draftAssignments) {
            foreach (array_keys(self::ELEMENT_OPTIONS) as $elementSlug) {
                $assignment = $draftAssignments[$elementSlug] ?? [
                    'coordinator_username' => '',
                    'member_usernames' => [],
                ];

                $coordinatorUsername = trim((string) ($assignment['coordinator_username'] ?? ''));
                $memberUsernames = collect((array) ($assignment['member_usernames'] ?? []))
                    ->map(fn ($username) => trim((string) $username))
                    ->filter(fn ($username) => $username !== '')
                    ->unique()
                    ->values()
                    ->all();

                if ($coordinatorUsername === '' && count($memberUsernames) === 0) {
                    ElementTeamAssignment::query()
                        ->where('element_slug', $elementSlug)
                        ->delete();

                    continue;
                }

                ElementTeamAssignment::updateOrCreate(
                    ['element_slug' => $elementSlug],
                    [
                        'coordinator_username' => $coordinatorUsername !== '' ? $coordinatorUsername : null,
                        'member_usernames' => $memberUsernames,
                    ]
                );
            }
        });
    }

    private function compactElementLabel(string $elementSlug): string
    {
        if (preg_match('/^element(\d+)$/', $elementSlug, $matches)) {
            return 'Element '.($matches[1] ?? '');
        }

        return self::ELEMENT_OPTIONS[$elementSlug] ?? $elementSlug;
    }

}
