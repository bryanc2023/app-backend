<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Oferta;

class ActualizarEstadoOfertas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ofertas:actualizar-estado';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar el estado de las ofertas según la fecha máxima de postulación';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fechaActual = now();
        Oferta::where('fecha_max_pos', '<', $fechaActual)
            ->where('estado', '!=', 'Culminado')
            ->update(['estado' => 'Inactiva']);

        $this->info('Estado de ofertas actualizadas');
        return 0;
    }
}
