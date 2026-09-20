<?php

namespace Tests\Feature\Security;

use App\Models\Avaliation;
use App\Models\AvaliationPdfCache;
use App\Models\Client;
use App\Models\Goal;
use App\Models\CheckinConfig;
use App\Models\UserPlans;
use App\Models\User;
use App\Contracts\TenantVisible;
use App\Services\AvaliationPdfCacheService;
use App\Support\TenantResourceResolver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class TenantReadIsolationTest extends TestCase
{
    private User $owner;
    private User $other;
    private Client $ownedClient;
    private Client $otherClient;
    private Avaliation $ownedAvaliation;
    private Avaliation $otherAvaliation;
    private Goal $otherGoal;
    private CheckinConfig $otherCheckinConfig;
    private UserPlans $otherPlan;

    protected function setUp(): void
    {
        parent::setUp();

        // The test never opens the database configured in the local .env.
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('password');
            $table->string('role');
            $table->boolean('active');
            $table->boolean('confirmation');
        });
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('first_name');
            $table->string('last_name');
        });
        Schema::create('avaliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('photo_front_url')->nullable();
            $table->string('photo_right_url')->nullable();
            $table->string('photo_left_url')->nullable();
            $table->string('photo_rear_url')->nullable();
        });
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
        });
        Schema::create('checkin_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
        });
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
        });

        $ownerId = DB::table('users')->insertGetId($this->userRow('owner@example.test'));
        $otherId = DB::table('users')->insertGetId($this->userRow('other@example.test'));
        $this->owner = User::findOrFail($ownerId);
        $this->other = User::findOrFail($otherId);
        Cache::put($this->owner->getPlanTypeCacheKey(), 'premium', 600);

        $ownedClientId = DB::table('clients')->insertGetId($this->clientRow($ownerId, 'Owned'));
        $otherClientId = DB::table('clients')->insertGetId($this->clientRow($otherId, 'Foreign'));
        $this->ownedClient = Client::findOrFail($ownedClientId);
        $this->otherClient = Client::findOrFail($otherClientId);

        $ownedAvaliationId = DB::table('avaliations')->insertGetId([
            'client_id' => $ownedClientId,
            'photo_front_url' => 'storage/avaliations/photos/owned.jpg',
        ]);
        $otherAvaliationId = DB::table('avaliations')->insertGetId([
            'client_id' => $otherClientId,
            'photo_front_url' => 'storage/avaliations/photos/foreign.jpg',
        ]);
        $this->ownedAvaliation = Avaliation::findOrFail($ownedAvaliationId);
        $this->otherAvaliation = Avaliation::findOrFail($otherAvaliationId);
        $this->otherGoal = Goal::findOrFail(DB::table('goals')->insertGetId(['client_id' => $otherClientId]));
        $this->otherCheckinConfig = CheckinConfig::findOrFail(DB::table('checkin_configs')->insertGetId(['client_id' => $otherClientId]));
        $this->otherPlan = UserPlans::findOrFail(DB::table('user_plans')->insertGetId(['user_id' => $otherId]));
    }

    public function testTenantQueriesOnlyReturnOwnRecords(): void
    {
        $resolver = app(TenantResourceResolver::class);

        $this->assertSame($this->ownedClient->id, $resolver->resolve('client', $this->ownedClient->codedId, $this->owner)->id);
        $this->assertSame($this->ownedAvaliation->id, $resolver->resolve('avaliation', $this->ownedAvaliation->codedId, $this->owner)->id);
        $this->assertSame($this->ownedAvaliation->id, $resolver->resolvePhoto('owned.jpg', $this->owner)->id);
        $this->assertSame([$this->ownedClient->id], Client::visibleTo($this->owner)->pluck('id')->all());
        $this->assertSame([$this->ownedAvaliation->id], Avaliation::visibleTo($this->owner)->pluck('id')->all());

        $this->assertTrue(Gate::forUser($this->owner)->allows('view', $this->ownedClient));
        $this->assertFalse(Gate::forUser($this->owner)->allows('view', $this->otherClient));
        $this->assertTrue(Gate::forUser($this->owner)->allows('share', $this->ownedAvaliation));
        $this->assertFalse(Gate::forUser($this->owner)->allows('share', $this->otherAvaliation));
        $this->assertFalse(Gate::forUser($this->owner)->allows('delete', $this->otherGoal));
        $this->assertFalse(Gate::forUser($this->owner)->allows('update', $this->otherCheckinConfig));
        $this->assertFalse(Gate::forUser($this->owner)->allows('view', $this->otherPlan));
        $this->assertSame([], Goal::visibleTo($this->owner)->pluck('id')->all());
        $this->assertSame([], CheckinConfig::visibleTo($this->owner)->pluck('id')->all());
        $this->assertSame([], UserPlans::visibleTo($this->owner)->pluck('id')->all());
    }

    public function testEveryRegisteredTenantResourceImplementsTheRequiredScopeContract(): void
    {
        foreach (TenantResourceResolver::supportedResources() as $resource => $modelClass) {
            $this->assertTrue(
                is_a($modelClass, TenantVisible::class, true),
                sprintf('%s must implement TenantVisible.', $resource)
            );
            $this->assertTrue(
                method_exists($modelClass, 'scopeVisibleTo'),
                sprintf('%s must define scopeVisibleTo.', $resource)
            );
        }
    }

    public function testOtherTenantCannotOpenClientOrEvaluationReadRoutes(): void
    {
        $this->actingAs($this->owner, 'web');

        $this->get(route('app.client.view', $this->otherClient->codedId))->assertNotFound();
        $this->get(route('app.client.edit', $this->otherClient->codedId))->assertNotFound();
        $this->get(route('app.avaliation.htmlModalView', ['codedId' => $this->otherAvaliation->codedId, 'json' => 1]))->assertNotFound();
        $this->get(route('app.avaliation.htmlModalEdit', ['codedId' => $this->otherAvaliation->codedId]))->assertNotFound();
        $this->get(route('app.avaliation.viewReport', $this->otherAvaliation->codedId))->assertNotFound();
        $this->get(route('app.avaliation.viewReportPDF', $this->otherAvaliation->codedId))->assertNotFound();
        $this->get(route('app.avaliation.showPhoto', 'foreign.jpg'))->assertNotFound();
        $this->get(route('app.avaliation.htmlModalSendWhats', ['cid' => $this->otherAvaliation->codedId]))->assertNotFound();
        $this->get(route('app.avaliation.htmlModalSendMail', ['cid' => $this->otherAvaliation->codedId]))->assertNotFound();
        $this->get(route('app.avaliation.htmlModalAdd', ['cuid' => $this->otherClient->codedId]))->assertNotFound();
        $this->get(route('app.goal.htmlModalAdd', ['cuid' => $this->otherClient->codedId]))->assertNotFound();
        $this->get(route('app.goal.htmlModalPastGoals', ['cuid' => $this->otherClient->codedId, 'json' => 1]))->assertNotFound();
        $this->get(route('app.checkin.config', $this->otherClient->codedId))->assertNotFound();
        $this->get(route('app.subscription.details', ['codedId' => $this->otherPlan->codedId]))->assertNotFound();
        $this->post(route('app.avaliation.doModalAdd'), ['f-cid' => $this->otherClient->codedId])->assertNotFound();
        $this->post(route('app.goal.doModalAdd'), ['f-cid' => $this->otherClient->codedId])->assertNotFound();
        $this->post(route('app.goal.doModalRemove'), ['f-gcid' => $this->otherGoal->codedId])->assertNotFound();
    }

    public function testRootCanResolveResourcesAcrossAccounts(): void
    {
        $rootId = DB::table('users')->insertGetId(array_merge(
            $this->userRow('root@example.test'),
            ['role' => User::ROLE_ROOT]
        ));
        $root = User::findOrFail($rootId);
        $resolver = app(TenantResourceResolver::class);

        $this->assertSame($this->otherClient->id, $resolver->resolve('client', $this->otherClient->codedId, $root)->id);
        $this->assertSame($this->otherAvaliation->id, $resolver->resolve('avaliation', $this->otherAvaliation->codedId, $root, 'update')->id);
        $this->assertSame($this->otherGoal->id, $resolver->resolve('goal', $this->otherGoal->codedId, $root, 'delete')->id);
        $this->assertSame($this->otherCheckinConfig->id, $resolver->resolve('checkin-config', $this->otherCheckinConfig->codedId, $root)->id);
        $this->assertSame($this->otherPlan->id, $resolver->resolve('user-plan', $this->otherPlan->codedId, $root)->id);
    }

    public function testSignedReportRequiresValidSignatureAndDoesNotRequireProfessionalSession(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'pg-report-');
        file_put_contents($path, "%PDF-1.4\n%%EOF\n");

        try {
            $cache = new AvaliationPdfCache(['storage_path' => 'unused.pdf']);
            $service = $this->mock(AvaliationPdfCacheService::class);
            $service->shouldReceive('getCurrentSnapshotCache')->once()->andReturn($cache);
            $service->shouldReceive('isReadyCache')->once()->with($cache)->andReturn(true);
            $service->shouldReceive('absolutePath')->once()->andReturn($path);

            $signedUrl = URL::temporarySignedRoute(
                'app.avaliation.showMyAvaliation',
                now()->addMinutes(10),
                ['codedId' => $this->otherAvaliation->codedId]
            );

            $this->get(route('app.avaliation.showMyAvaliation', $this->otherAvaliation->codedId))->assertStatus(419);
            $this->get($signedUrl)->assertOk()->assertHeader('Content-Type', 'application/pdf');
            $this->get($signedUrl . 'x')->assertStatus(419);
            $expiredUrl = URL::temporarySignedRoute(
                'app.avaliation.showMyAvaliation',
                now()->subMinute(),
                ['codedId' => $this->otherAvaliation->codedId]
            );
            $this->get($expiredUrl)->assertStatus(419);
        } finally {
            unlink($path);
        }
    }

    public function testOwnerCanOpenReadyPdfCache(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'pg-own-report-');
        file_put_contents($path, "%PDF-1.4\n%%EOF\n");

        try {
            $cache = new AvaliationPdfCache(['storage_path' => 'unused.pdf']);
            $service = $this->mock(AvaliationPdfCacheService::class);
            $service->shouldReceive('getCurrentSnapshotCache')->once()->andReturn($cache);
            $service->shouldReceive('isReadyCache')->once()->with($cache)->andReturn(true);
            $service->shouldReceive('absolutePath')->once()->andReturn($path);

            $this->actingAs($this->owner, 'web')
                ->get(route('app.avaliation.viewReportPDF', $this->ownedAvaliation->codedId))
                ->assertOk()
                ->assertHeader('Content-Type', 'application/pdf');
        } finally {
            unlink($path);
        }
    }

    private function userRow(string $email): array
    {
        return [
            'first_name' => 'Test',
            'last_name' => 'Professional',
            'email' => $email,
            'password' => 'unused',
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ];
    }

    private function clientRow(int $ownerId, string $name): array
    {
        return [
            'user_id' => $ownerId,
            'first_name' => $name,
            'last_name' => 'Client',
        ];
    }
}
