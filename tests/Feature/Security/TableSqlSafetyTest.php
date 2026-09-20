<?php

namespace Tests\Feature\Security;

use App\Models\Avaliation;
use App\Models\Client;
use App\Tables\AvaliationsTable;
use App\Tables\ClientsTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Okipa\LaravelTable\Column;
use ReflectionMethod;
use ReflectionProperty;
use Tests\TestCase;

class TableSqlSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::connection()->getPdo()->sqliteCreateFunction('CONCAT', fn (...$parts) => implode('', $parts));

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('first_name');
            $table->string('last_name');
        });
        Schema::create('avaliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
        });

        DB::table('clients')->insert([
            ['id' => 1, 'user_id' => 1, 'first_name' => 'Ana', 'last_name' => "O'Neil"],
            ['id' => 2, 'user_id' => 1, 'first_name' => 'Ana', 'last_name' => '100%_Fit'],
            ['id' => 3, 'user_id' => 1, 'first_name' => 'Bia', 'last_name' => 'Lima'],
            ['id' => 4, 'user_id' => 2, 'first_name' => 'Ana', 'last_name' => "O'Neil"],
        ]);
        DB::table('avaliations')->insert([
            ['id' => 1, 'client_id' => 1],
            ['id' => 2, 'client_id' => 2],
            ['id' => 3, 'client_id' => 4],
        ]);
    }

    public function testClientNameSearchBindsSpecialTextAndRespectsTenant(): void
    {
        $search = $this->column(ClientsTable::class, 'first_name')->getSearchableClosure();
        $query = $search(Client::query()->where('user_id', 1), "O'Neil");

        $this->assertSame([1], $query->pluck('id')->all());
        $this->assertStringContainsString("LIKE ? ESCAPE '!'", $query->toSql());
        $this->assertContains("%O'Neil%", $query->getBindings());

        $literal = $search(Client::query()->where('user_id', 1), '%_');
        $this->assertSame([2], $literal->pluck('id')->all());
        $this->assertContains('%!%!_%', $literal->getBindings());

        $injection = $search(Client::query()->where('user_id', 1), "' OR 1=1 --");
        $this->assertSame([], $injection->pluck('id')->all());
    }

    public function testAvaliationNameSearchBindsTextAndRespectsTenant(): void
    {
        $search = $this->column(AvaliationsTable::class, 'full_name')->getSearchableClosure();
        $query = Avaliation::query()
            ->select('avaliations.id')
            ->join('clients', 'clients.id', '=', 'avaliations.client_id')
            ->where('clients.user_id', 1);

        $search($query, "O'Neil");
        $this->assertSame([1], $query->pluck('avaliations.id')->all());
        $this->assertStringContainsString("LIKE ? ESCAPE '!'", $query->toSql());
        $this->assertContains("%O'Neil%", $query->getBindings());

        $literal = Avaliation::query()
            ->select('avaliations.id')
            ->join('clients', 'clients.id', '=', 'avaliations.client_id')
            ->where('clients.user_id', 1);
        $search($literal, '%_');
        $this->assertSame([2], $literal->pluck('avaliations.id')->all());
    }

    public function testClientNameSortUsesOnlyAllowedDirections(): void
    {
        $sort = $this->column(ClientsTable::class, 'first_name')->getSortableClosure();
        $ascending = $sort(Client::query()->where('user_id', 1), 'asc');
        $this->assertSame([2, 1, 3], $ascending->pluck('id')->all());

        $descending = $sort(Client::query()->where('user_id', 1), 'desc');
        $this->assertSame([3, 1, 2], $descending->pluck('id')->all());

        $invalid = $sort(Client::query()->where('user_id', 1), 'desc; DROP TABLE clients');
        $this->assertSame([2, 1, 3], $invalid->pluck('id')->all());
        $this->assertStringEndsWith(' asc', $invalid->toSql());
    }

    private function column(string $tableClass, string $attribute): Column
    {
        $method = new ReflectionMethod($tableClass, 'columns');
        $method->setAccessible(true);

        $table = new $tableClass();
        if ($table instanceof AvaliationsTable) {
            $client = new ReflectionProperty(AvaliationsTable::class, 'Client');
            $client->setAccessible(true);
            $client->setValue($table, null);
        }

        foreach ($method->invoke($table) as $column) {
            if ($column->getAttribute() === $attribute) {
                return $column;
            }
        }

        $this->fail("Column {$attribute} not found in {$tableClass}");
    }
}
