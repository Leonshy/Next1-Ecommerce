<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptSensitiveData extends Command
{
    protected $signature   = 'app:encrypt-sensitive-data {--dry-run : Mostrar cuántos registros se afectarían sin modificar nada}';
    protected $description = 'Encripta datos sensibles existentes en texto plano (ejecutar una sola vez)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? '[Simulación] No se modificará nada.' : 'Encriptando datos sensibles...');

        $total = 0;
        $total += $this->processColumn('orders',         'customer_phone',   $dryRun);
        $total += $this->processColumn('orders',         'shipping_address',  $dryRun);
        $total += $this->processColumn('profiles',       'phone',             $dryRun);
        $total += $this->processColumn('user_addresses', 'phone',             $dryRun);
        $total += $this->processColumn('user_addresses', 'street_address',    $dryRun);

        $this->newLine();
        $this->info("Total campos procesados: {$total}");

        if (! $dryRun) {
            $this->warn('No vuelvas a ejecutar este comando o los datos ya encriptados se dañarán.');
        }

        return self::SUCCESS;
    }

    private function processColumn(string $table, string $column, bool $dryRun): int
    {
        $count = 0;

        DB::table($table)->whereNotNull($column)->orderBy('id')->chunk(100, function ($rows) use ($table, $column, &$count, $dryRun) {
            foreach ($rows as $row) {
                $value = $row->$column;

                if ($this->isEncrypted($value)) continue;

                $count++;

                if (! $dryRun) {
                    DB::table($table)->where('id', $row->id)->update([
                        $column => Crypt::encryptString($value),
                    ]);
                }
            }
        });

        $this->line("  {$table}.{$column}: {$count} registros " . ($dryRun ? 'pendientes' : 'encriptados'));

        return $count;
    }

    private function isEncrypted(mixed $value): bool
    {
        if (! is_string($value) || strlen($value) < 20) return false;
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
