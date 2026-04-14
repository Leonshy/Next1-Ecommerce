<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExportToMysql extends Command
{
    protected $signature = 'db:export-mysql {--output=mysql_data.sql : Archivo de salida}';

    protected $description = 'Exporta todos los datos de SQLite a un archivo SQL compatible con MySQL';

    // Tablas que NO se exportan (son transitorias o las maneja Laravel)
    protected array $skip = [
        'sqlite_sequence',
        'migrations',
        'cache',
        'cache_locks',
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
    ];

    public function handle(): int
    {
        $output = $this->option('output');
        $outputPath = base_path($output);

        $this->info("Conectando a SQLite y exportando datos...");

        // Obtener todas las tablas de SQLite
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
        $tableNames = collect($tables)->pluck('name')->filter(fn($t) => !in_array($t, $this->skip))->values();

        $this->info("Tablas encontradas: " . $tableNames->count());

        $sql = [];
        $sql[] = "-- Exportación de datos Next1 E-Commerce";
        $sql[] = "-- Generado: " . now()->format('Y-m-d H:i:s');
        $sql[] = "-- Compatible con MySQL 5.7+";
        $sql[] = "";
        $sql[] = "SET FOREIGN_KEY_CHECKS=0;";
        $sql[] = "SET NAMES utf8mb4;";
        $sql[] = "";

        $totalRows = 0;

        foreach ($tableNames as $table) {
            $rows = DB::table($table)->get();

            if ($rows->isEmpty()) {
                $sql[] = "-- Tabla '$table': vacía";
                $sql[] = "";
                continue;
            }

            $sql[] = "-- Tabla: $table ({$rows->count()} filas)";
            $sql[] = "DELETE FROM `$table`;";

            $columns = array_keys((array) $rows->first());
            $columnList = implode(', ', array_map(fn($c) => "`$c`", $columns));

            foreach ($rows->chunk(50) as $chunk) {
                $valueGroups = [];

                foreach ($chunk as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        if (is_int($value) || is_float($value)) {
                            return $value;
                        }
                        // Escapar strings para MySQL
                        $escaped = str_replace(
                            ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
                            ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
                            (string) $value
                        );
                        return "'" . $escaped . "'";
                    }, (array) $row);

                    $valueGroups[] = "(" . implode(', ', $values) . ")";
                }

                $sql[] = "INSERT INTO `$table` ($columnList) VALUES";
                $lastIdx = count($valueGroups) - 1;
                foreach ($valueGroups as $i => $vg) {
                    $sql[] = "  " . $vg . ($i < $lastIdx ? "," : ";");
                }
            }

            $sql[] = "";
            $totalRows += $rows->count();
        }

        $sql[] = "";
        $sql[] = "SET FOREIGN_KEY_CHECKS=1;";
        $sql[] = "";
        $sql[] = "-- Exportación completada: $totalRows filas totales";

        file_put_contents($outputPath, implode("\n", $sql));

        $this->info("✓ Exportación completada: $totalRows filas en {$tableNames->count()} tablas");
        $this->info("✓ Archivo guardado en: $outputPath");
        $this->line("");
        $this->line("Próximos pasos:");
        $this->line("  1. Sube mysql_data.sql al servidor: scp mysql_data.sql usuario@servidor:/tmp/");
        $this->line("  2. En el servidor ejecuta las migraciones: php artisan migrate");
        $this->line("  3. Importa los datos: mysql -h localhost -u USER -p DB < /tmp/mysql_data.sql");

        return Command::SUCCESS;
    }
}
