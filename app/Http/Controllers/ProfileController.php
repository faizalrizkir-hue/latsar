<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ElementTeamAssignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private const ELEMENT_OPTIONS = [
        'element1' => 'Element 1 : Kualitas Peran dan Layanan',
        'element2' => 'Element 2 : Profesionalisme Penugasan',
        'element3' => 'Element 3 : Manajemen Pengawasan',
        'element4' => 'Element 4 : Pengelolaan Kinerja dan Sumber Daya Pengawasan',
        'element5' => 'Element 5 : Budaya dan Hubungan Organisasi',
    ];

    public function edit()
    {
        $user = Session::get('user');
        $account = Account::where('username', $user['username'] ?? '')->first();
        $teamAssignmentSummary = [];

        if ($account && Schema::hasTable('element_team_assignments')) {
            $username = trim((string) $account->username);

            if ($username !== '') {
                $assignments = ElementTeamAssignment::query()
                    ->where(function ($query) use ($username) {
                        $query
                            ->where('coordinator_username', $username)
                            ->orWhereJsonContains('member_usernames', $username);
                    })
                    ->get()
                    ->sortBy(fn (ElementTeamAssignment $assignment) => ElementTeamAssignment::topLevelElementSlug((string) $assignment->element_slug))
                    ->values();

                if ($assignments->isNotEmpty()) {
                    $relatedUsernames = $assignments
                        ->flatMap(function (ElementTeamAssignment $assignment) {
                            return array_merge(
                                [trim((string) $assignment->coordinator_username)],
                                collect((array) ($assignment->member_usernames ?? []))
                                    ->map(fn ($memberUsername) => trim((string) $memberUsername))
                                    ->all()
                            );
                        })
                        ->filter(fn ($value) => $value !== '')
                        ->unique()
                        ->values();

                    $displayNameMap = Account::query()
                        ->whereIn('username', $relatedUsernames)
                        ->get()
                        ->mapWithKeys(fn (Account $relatedAccount) => [
                            $relatedAccount->username => trim((string) ($relatedAccount->display_name ?: $relatedAccount->username)),
                        ]);

                    $teamAssignmentSummary = $assignments
                        ->map(function (ElementTeamAssignment $assignment) use ($username, $displayNameMap) {
                            $elementSlug = ElementTeamAssignment::topLevelElementSlug((string) $assignment->element_slug);
                            $coordinatorUsername = trim((string) $assignment->coordinator_username);
                            $memberUsernames = collect((array) ($assignment->member_usernames ?? []))
                                ->map(fn ($memberUsername) => trim((string) $memberUsername))
                                ->filter(fn ($memberUsername) => $memberUsername !== '')
                                ->unique()
                                ->values();

                            $isCoordinator = $coordinatorUsername !== '' && $coordinatorUsername === $username;
                            $memberNames = $memberUsernames
                                ->map(fn ($memberUsername) => $displayNameMap[$memberUsername] ?? $memberUsername)
                                ->values();
                            $otherMemberNames = $memberUsernames
                                ->reject(fn ($memberUsername) => $memberUsername === $username)
                                ->map(fn ($memberUsername) => $displayNameMap[$memberUsername] ?? $memberUsername)
                                ->values();
                            $coordinatorName = $coordinatorUsername !== ''
                                ? ($displayNameMap[$coordinatorUsername] ?? $coordinatorUsername)
                                : null;

                            return [
                                'element_label' => self::ELEMENT_OPTIONS[$elementSlug] ?? ucfirst($elementSlug),
                                'position_label' => $isCoordinator ? 'Koordinator' : 'Anggota Tim',
                                'position_class' => $isCoordinator ? 'is-coordinator' : 'is-member',
                                'summary_label' => $isCoordinator
                                    ? 'Penanggung jawab verifikasi'
                                    : ($coordinatorName ? 'Koordinator: '.$coordinatorName : 'Koordinator belum ditetapkan'),
                                'member_count_text' => $memberUsernames->count().' anggota tim',
                                'people_label' => $isCoordinator ? 'Anggota Tim' : 'Rekan Tim',
                                'people' => ($isCoordinator ? $memberNames : $otherMemberNames)->all(),
                            ];
                        })
                        ->all();
                }
            }
        }

        return view('profile.edit', [
            'pageTitle' => 'Edit Profil',
            'user' => $user,
            'account' => $account,
            'teamAssignmentSummary' => $teamAssignmentSummary,
            'notifications' => Notification::feedForUser((array) $user, null, 50),
        ]);
    }

    public function update(Request $request)
    {
        $user = Session::get('user');
        $account = Account::where('username', $user['username'] ?? '')->firstOrFail();

        // Adapt to legacy field names from backup (new_password, confirm_password, profile_photo, remove_photo)
        $data = $request->validate([
            'display_name' => 'required|string|max:150',
            'new_password' => 'nullable|string|min:6',
            'confirm_password' => 'nullable|string|min:6',
            'profile_photo' => 'nullable|image|max:2048',
            'remove_photo' => 'sometimes|in:0,1',
        ]);

        $updates = [
            'display_name' => $data['display_name'],
        ];

        if (!empty($data['new_password'])) {
            if (($data['new_password'] ?? '') !== ($data['confirm_password'] ?? '')) {
                return back()->withErrors(['password' => 'Konfirmasi password tidak sama.'])->withInput();
            }
            $updates['password_hash'] = Hash::make($data['new_password']);
        }

        $removedPhoto = false;
        $uploadedPhoto = false;

        if ($request->input('remove_photo') === '1' && $account->profile_photo) {
            Storage::disk('public')->delete($account->profile_photo);
            $updates['profile_photo'] = null;
            $removedPhoto = true;
        }

        if ($request->hasFile('profile_photo')) {
            if ($account->profile_photo) {
                Storage::disk('public')->delete($account->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile', 'public');
            $updates['profile_photo'] = $path;
            $uploadedPhoto = true;
        }

        $account->update($updates);

        $sessionUser = Session::get('user', []);
        $sessionUser['display_name'] = $account->display_name;
        $sessionUser['profile_photo'] = $account->profile_photo;
        Session::put('user', $sessionUser);

        $statusMessage = null;
        if ($uploadedPhoto) {
            $statusMessage = 'Profil & foto diperbarui.';
        } elseif (!empty($data['new_password'])) {
            $statusMessage = 'Profil & password diperbarui.';
        } elseif (!$removedPhoto) {
            $statusMessage = 'Profil berhasil diperbarui.';
        }

        return $statusMessage
            ? back()->with('status', $statusMessage)
            : back();
    }
}
