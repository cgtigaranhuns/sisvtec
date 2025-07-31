<?php

namespace App\Console\Commands;

use App\Models\Discente;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncIfpeStudents extends Command
{
    protected $signature = 'ifpe:sync-students 
                            {--size=50 : Quantidade por página}
                            {--test : Testar conexão com a API}';

    protected $description = 'Sincroniza discentes com a API do IFPE (Spring Data format)';
    protected $apiBaseUrl = 'https://api.ifpe.edu.br/qacademico/';
    protected $apiToken = 'faBj4kkwVoJLsAnZOfAbwFvflyL5omG5';

    protected $totalCreated = 0;
    protected $totalUpdated = 0;
    protected $totalSkipped = 0;
    protected $totalErrors = 0;

    public function handle()
    {
        if ($this->option('test')) {
            return $this->testConnection();
        }

        $size = (int) $this->option('size');

        $this->info("📆 Início: " . Carbon::now()->format('d-m-Y H:i:s'));
        $this->syncAllPages($size);

        $this->info("\n✅ Concluído!");
        $this->line("🆕 Criados: {$this->totalCreated}");
        $this->line("♻️ Atualizados: {$this->totalUpdated}");
        $this->line("⏭️ Ignorados: {$this->totalSkipped}");
        $this->line("❌ Erros: {$this->totalErrors}");

        return $this->totalErrors > 0 ? 1 : 0;
    }

    protected function syncAllPages($size)
    {
        $page = 0;
        do {
            $hasNext = $this->syncSinglePage($page, $size);
            $page++;
        } while ($hasNext);
    }

    protected function syncSinglePage($page, $size)
    {
        $this->info("📄 Página {$page}");

        $response = $this->makeApiRequest('students', ['page' => $page, 'size' => $size]);

        if (!$response) {
            $this->error("❌ Falha na página {$page}");
            return false;
        }

        $data = $response->json();
        $students = $data['content'] ?? [];

        if (empty($students)) {
            $this->info("🚫 Nenhum registro");
            return false;
        }

        $bar = $this->output->createProgressBar(count($students));
        $bar->start();

        foreach ($students as $student) {
            try {
                $status = $this->processStudent($student);
                match ($status) {
                    'created' => $this->totalCreated++,
                    'updated' => $this->totalUpdated++,
                    'skipped' => $this->totalSkipped++,
                };
            } catch (\Exception $e) {
                $this->totalErrors++;
                Log::error("Erro ao processar", [
                    'matricula' => $student['enrollment'] ?? null,
                    'mensagem' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return !($data['last'] ?? true); // Retorna true se há mais páginas
    }

    protected function processStudent(array $studentData)
    {
        if (empty($studentData['enrollment'])) {
            throw new \Exception("Matrícula ausente");
        }

        $mapped = [
            'nome' => $studentData['fullName'] ?? null,
            'email' => $studentData['email'] ?? null,
            'contato' => $studentData['cellphone'] ?? null,
            'data_nascimento' => isset($studentData['birthday']) ? $this->parseDate($studentData['birthday']) : null,
            'cpf' => $studentData['brCPF'] ?? null,
            'rg' => $studentData['brRG'] ?? null,
            'status_qa' => $studentData['enrollmentStatus'] ?? null,
        ];

        $discente = Discente::updateOrCreate(
            ['matricula' => $studentData['enrollment']],
            $mapped
        );

        if ($discente->wasRecentlyCreated) return 'created';
        if ($discente->wasChanged()) return 'updated';

        return 'skipped';
    }

    protected function makeApiRequest($endpoint, $params = [])
    {
        try {
            $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                            ->withHeaders([
                                'Authorization' => $this->apiToken,
                                'Accept' => 'application/json',
                            ])
                            ->get($this->apiBaseUrl . $endpoint, $params);

            return $response->successful() ? $response : null;
        } catch (\Exception $e) {
            $this->error("⚠️ Requisição falhou: " . $e->getMessage());
            return null;
        }
    }

    protected function parseDate($dateString)
    {
        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    protected function testConnection()
    {
        $this->info("🔌 Testando conexão...");
        $response = $this->makeApiRequest('students', ['page' => 0, 'size' => 1]);

        if (!$response) return 1;

        $data = $response->json();
        $this->info("✅ Conexão OK!");
        $this->line("📦 Estrutura: " . json_encode(array_keys($data), JSON_PRETTY_PRINT));
        return 0;
    }
}
