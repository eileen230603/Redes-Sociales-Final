<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evento;
use Carbon\Carbon;

class FinalizarEventosVencidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventos:finalizar-vencidos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca automáticamente como finalizados los eventos que han pasado su fecha y hora de finalización';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de finalización automática de eventos...');

        // Obtener eventos que:
        // 1. Tienen fecha_fin en el pasado
        // 2. No están ya finalizados (estado != 'finalizado' y estado != 'cancelado')
        // 3. No tienen fecha_finalizacion registrada
        $ahora = Carbon::now();
        
        $eventosVencidos = Evento::where('fecha_fin', '<=', $ahora)
            ->where('estado', '!=', 'cancelado')
            ->where('estado', '!=', 'finalizado')
            ->whereNull('fecha_finalizacion')
            ->get();

        $contador = 0;

        foreach ($eventosVencidos as $evento) {
            // Marcar como finalizado
            $evento->estado = 'finalizado';
            $evento->fecha_finalizacion = $ahora;
            $evento->save();

            $contador++;
            $this->line("✓ Evento '{$evento->titulo}' marcado como finalizado (ID: {$evento->id})");
        }

        if ($contador > 0) {
            $this->info("✅ Se finalizaron {$contador} evento(s) automáticamente.");
        } else {
            $this->info("ℹ️ No hay eventos pendientes de finalizar.");
        }

        return Command::SUCCESS;
    }
}
