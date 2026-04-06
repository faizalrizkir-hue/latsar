<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureDatabaseServerLock;
use App\Models\Account;
use App\Models\ElementTeamAssignment;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BootstrapsCoreTables;
use Tests\TestCase;

class RoleAccessMatrixTest extends TestCase
{
    use BootstrapsCoreTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootstrapCoreTables();
        $this->resetCoreTables();
        $this->withoutMiddleware(EnsureDatabaseServerLock::class);

        foreach ([
            ['username' => 'admin', 'display_name' => 'Administrator', 'role' => 'administrator'],
            ['username' => 'qa1', 'display_name' => 'QA BPKP', 'role' => 'qa'],
            ['username' => 'koor1', 'display_name' => 'Koordinator 1', 'role' => 'koordinator'],
            ['username' => 'koor2', 'display_name' => 'Koordinator 2', 'role' => 'koordinator'],
            ['username' => 'auditor1', 'display_name' => 'Auditor 1', 'role' => 'auditor'],
        ] as $account) {
            Account::query()->create([
                'username' => $account['username'],
                'password_hash' => Hash::make('rahasia123'),
                'display_name' => $account['display_name'],
                'role' => $account['role'],
                'active' => true,
            ]);
        }
    }

    public function test_admin_routes_only_accessible_for_administrator_role(): void
    {
        $cases = [
            ['username' => 'admin', 'role' => 'administrator', 'expected' => 200],
            ['username' => 'qa1', 'role' => 'qa', 'expected' => 403],
            ['username' => 'koor1', 'role' => 'koordinator', 'expected' => 403],
            ['username' => 'auditor1', 'role' => 'auditor', 'expected' => 403],
        ];

        foreach ($cases as $case) {
            $sessionUser = $this->sessionUser($case['username'], $case['role']);

            $this->withSession(['user' => $sessionUser, 'last_activity_at' => time()])
                ->get('/accounts')
                ->assertStatus($case['expected']);

            $this->withSession(['user' => $sessionUser, 'last_activity_at' => time()])
                ->get('/element-preferences')
                ->assertStatus($case['expected']);
        }
    }

    public function test_restricted_roles_cannot_access_unassigned_element_pages(): void
    {
        ElementTeamAssignment::query()->create([
            'element_slug' => 'element1',
            'coordinator_username' => 'koor2',
            'member_usernames' => ['auditor1'],
        ]);

        $this->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->get('/elements/element1')
            ->assertRedirect(route('dashboard'));

        $this->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->get('/elements/element1_kegiatan_asurans')
            ->assertRedirect(route('dashboard'));
    }

    public function test_assigned_roles_can_access_their_element_pages(): void
    {
        ElementTeamAssignment::query()->create([
            'element_slug' => 'element1',
            'coordinator_username' => 'koor1',
            'member_usernames' => ['auditor1'],
        ]);

        $this->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->get('/elements/element1')
            ->assertOk();

        $this->withSession(['user' => $this->sessionUser('auditor1', 'auditor'), 'last_activity_at' => time()])
            ->get('/elements/element1_kegiatan_asurans')
            ->assertOk();
    }

    public function test_notification_channel_authorization_matrix_per_role(): void
    {
        config([
            'broadcasting.connections.reverb.key' => 'test-key',
            'broadcasting.connections.reverb.secret' => 'test-secret',
        ]);

        ElementTeamAssignment::query()->create([
            'element_slug' => 'element1',
            'coordinator_username' => 'koor1',
            'member_usernames' => ['auditor1'],
        ]);

        $payloadAll = [
            'socket_id' => '1234.5678',
            'channel_name' => 'private-notifications.all',
        ];
        $payloadElement1 = [
            'socket_id' => '1234.5678',
            'channel_name' => 'private-notifications.element.element1',
        ];

        $this->withSession(['user' => $this->sessionUser('admin', 'administrator'), 'last_activity_at' => time()])
            ->postJson('/notifications/auth', $payloadAll)
            ->assertOk()
            ->assertJsonStructure(['auth']);

        $this->withSession(['user' => $this->sessionUser('qa1', 'qa'), 'last_activity_at' => time()])
            ->postJson('/notifications/auth', $payloadAll)
            ->assertOk()
            ->assertJsonStructure(['auth']);

        $this->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->postJson('/notifications/auth', $payloadAll)
            ->assertStatus(403);

        $this->withSession(['user' => $this->sessionUser('koor1', 'koordinator'), 'last_activity_at' => time()])
            ->postJson('/notifications/auth', $payloadElement1)
            ->assertOk()
            ->assertJsonStructure(['auth']);

        $this->withSession(['user' => $this->sessionUser('auditor1', 'auditor'), 'last_activity_at' => time()])
            ->postJson('/notifications/auth', $payloadElement1)
            ->assertOk()
            ->assertJsonStructure(['auth']);

        $this->withSession(['user' => $this->sessionUser('koor2', 'koordinator'), 'last_activity_at' => time()])
            ->postJson('/notifications/auth', $payloadElement1)
            ->assertStatus(403);
    }

    private function sessionUser(string $username, string $role): array
    {
        return [
            'id' => 1,
            'username' => $username,
            'display_name' => $username,
            'role' => $role,
            'role_label' => Account::roleLabel($role),
            'profile_photo' => null,
        ];
    }
}
