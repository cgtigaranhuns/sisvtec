<?php

namespace App\Console\Commands;

use App\Models\Discente;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncIfpeStudents extends Command
{
    protected $signature = 'ifpe:sync-students 
                            {--page=0 : Página inicial} 
                            {--size=50 : Quantidade por página} 
                            {--all : Sincronizar todas as páginas}
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

        $size = (int)$this->option('size');

        $this->info("🕒 Iniciando sincronização: " . Carbon::now()->format('d-m-Y H:i:s'));

        if ($this->option('all')) {
            $this->syncAllPages($size);
        } else {
            $page = (int)$this->option('page');
            $this->syncSinglePage($page, $size);
        }

        $this->info("\n🎉 Sincronização concluída!");
        $this->line("👉 Novos registros: {$this->totalCreated}");
        $this->line("🔄 Atualizados: {$this->totalUpdated}");
        $this->line("⏭️ Ignorados: {$this->totalSkipped}");
        $this->line("❌ Erros: {$this->totalErrors}");

        return $this->totalErrors > 0 ? 1 : 0;
    }

    protected function syncAllPages($size)
    {
        $page = 0;
        do {
            $hasMore = $this->syncSinglePage($page, $size);
            $page++;
        } while ($hasMore);
    }

    protected function syncSinglePage($page, $size)
    {
        $this->info("📄 Página {$page} - Buscando {$size} registros...");

        $response = $this->makeApiRequest('students', ['page' => $page, 'size' => $size]);
        if (!$response) {
            $this->error("❌ Falha ao obter dados da página {$page}");
            return false;
        }

        $data = $response->json();
        $students = $data['content'] ?? [];

        if (empty($students)) {
            $this->info("ℹ️ Nenhum dado encontrado na página {$page}");
            return false;
        }

        $this->info("🔄 Processando " . count($students) . " registros...");
        $bar = $this->output->createProgressBar(count($students));
        $bar->start();

        foreach ($students as $student) {
            try {
                $result = $this->processStudent($student);

                if ($result === 'created') {
                    $this->totalCreated++;
                } elseif ($result === 'updated') {
                    $this->totalUpdated++;
                } elseif ($result === 'skipped') {
                    $this->totalSkipped++;
                }
            } catch (\Exception $e) {
                Log::error("Erro ao processar estudante: " . $e->getMessage(), [
                    'student_data' => $student,
                    'error' => $e
                ]);
                $this->error("⚠️ Erro: " . $e->getMessage());
                $this->totalErrors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return !($data['last'] ?? true);
    }

    protected function processStudent(array $studentData)
    {
        if (empty($studentData['enrollment'])) {
            throw new \Exception("Matrícula não informada");
        }

        $apiData = [
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
            $apiData
        );

        if ($discente->wasRecentlyCreated) {
            return 'created';
        }

        if ($discente->wasChanged()) {
            $changes = $discente->getChanges();
            $this->info("🔧 Atualizado: {$studentData['enrollment']} (Campos: " . implode(', ', array_keys($changes)) . ")");
            return 'updated';
        }

        return 'skipped';
    }

    protected function testConnection()
    {
        $this->info('🔌 Testando conexão com a API...');

        $response = $this->makeApiRequest('students', ['page' => 0, 'size' => 1]);
        if (!$response) return 1;

        $data = $response->json();

        $this->info('✅ Conexão bem-sucedida!');
        $this->line("🔁 Status: {$response->status()}");
        $this->line("📦 Estrutura: " . json_encode(array_keys($data), JSON_PRETTY_PRINT));

        if (isset($data['content'])) {
            $this->line("- Página atual: " . ($data['number'] ?? 'N/A'));
            $this->line("- Tamanho: " . ($data['size'] ?? 'N/A'));
            $this->line("- Total de elementos: " . ($data['totalElements'] ?? 'N/A'));
            $this->line("- Total de páginas: " . ($data['totalPages'] ?? 'N/A'));
        }

        return 0;
    }

    protected function makeApiRequest($endpoint, $params = [])
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
            ])->withHeaders([
                'Authorization' => $this->apiToken,
                'Accept' => 'application/json',
            ])->get($this->apiBaseUrl . $endpoint, $params);

            if (!$response->successful()) {
                $this->handleApiError($response);
                return null;
            }

            return $response;
        } catch (\Exception $e) {
            $this->error("❌ Erro na requisição: " . $e->getMessage());
            return null;
        }
    }

    protected function handleApiError($response)
    {
        $this->error("Erro na API: " . $response->status());
        $body = $response->body();
        $this->line("📨 Resposta: " . (strlen($body) > 200 ? substr($body, 0, 200) . '...' : $body));
    }

    protected function parseDate($dateString)
    {
        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("⚠️ Erro ao analisar data: {$dateString}");
            return null;
        }
    }
}
