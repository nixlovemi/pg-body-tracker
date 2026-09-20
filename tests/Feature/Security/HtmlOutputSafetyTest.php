<?php

namespace Tests\Feature\Security;

use App\Helpers\ModelValidation;
use App\Helpers\AvaliationGraph\AvaliationGraphAbstract;
use App\Helpers\Report\ReportColumns;
use App\Models\Avaliation;
use App\Models\Client;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use ReflectionMethod;
use Tests\TestCase;

class HtmlOutputSafetyTest extends TestCase
{
    public function testClientNotesAreEscapedAndKeepLineBreaksInSharedReportPartial(): void
    {
        $avaliation = new Avaliation([
            'client_notes' => "Olá <script>alert(1)</script>\n<img src=x onerror=alert(1)>",
        ]);

        $html = view('components.avaliationReport.partials.client-notes', [
            'Avaliation' => $avaliation,
            'HAS_PAGE_BREAK' => false,
            'DIV_ROW_CLASSES' => 'row',
        ])->render();

        $this->assertStringContainsString('Olá &lt;script&gt;alert(1)&lt;/script&gt;<br />', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
    }

    public function testFlashAndValidationErrorsRenderAsText(): void
    {
        session()->flash('success', '<img src=x onerror=alert(1)>');
        $errors = new ViewErrorBag();
        $errors->put('default', new MessageBag(['msg' => 'Campo <script>alert(1)</script>' . "\n" . 'Outra linha']));

        $html = view('layout.partials.alert-return-messages', [
            'errors' => $errors,
        ])->render();

        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
        $this->assertStringContainsString('Campo &lt;script&gt;alert(1)&lt;/script&gt;<br />', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('aria-label="Close"', $html);
    }

    public function testValidationMessageContainsPlainTextAndStructuredErrors(): void
    {
        $validation = new ModelValidation(['name' => '']);
        $validation->addField('name', ['required'], 'Nome');

        $result = $validation->validate();

        $this->assertTrue($result->isError());
        $this->assertStringNotContainsString('<br', $result->getValueFromResponse('messages'));
        $this->assertCount(1, $result->getValueFromResponse('errors'));
    }

    public function testClientNameInGeneratedReportIsEscaped(): void
    {
        $client = new Client([
            'first_name' => '<img src=x onerror=alert(1)>',
            'last_name' => 'Silva',
        ]);

        $value = ReportColumns::clientFullName()->getValue($client, []);

        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', e($value));
        $this->assertStringNotContainsString('<img', e($value));
    }

    public function testGraphTableKeepsGeneratedMarkersAndEscapesOtherValues(): void
    {
        $graph = new class(0) extends AvaliationGraphAbstract {
            protected function getAvaliation(): Avaliation
            {
                return new Avaliation();
            }

            protected function getConfig(): array
            {
                return [];
            }

            protected function getClassName(): string
            {
                return 'test';
            }
        };
        $label = $this->invokeGraphMethod($graph, 'getTableRowLabel', ['<script>alert(1)</script>', '#ff0000']);
        $this->invokeGraphMethod($graph, 'addHeadItem', [$label]);
        $this->invokeGraphMethod($graph, 'addBodyItem', [$label, '<img src=x onerror=alert(1)>']);

        $html = $this->invokeGraphMethod($graph, 'getDataTableHtml');

        $this->assertStringContainsString('<a href="javascript:;"', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
    }

    private function invokeGraphMethod(AvaliationGraphAbstract $graph, string $methodName, array $args = [])
    {
        $method = new ReflectionMethod(AvaliationGraphAbstract::class, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($graph, $args);
    }
}
