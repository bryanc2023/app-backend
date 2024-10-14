<?php

namespace App\Jobs;

use App\Mail\OfertaPublicadaMail;
use App\Models\Postulante;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOfertaPublicadaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $oferta;

    public function __construct($oferta)
    {
        $this->oferta = $oferta;
    }

    public function handle()
    {
        // Obtener los postulantes interesados en el área de la oferta
        $postulantes = Postulante::whereHas('areas', function($query) {
            $query->where('id_area', $this->oferta->id_area);
        })->get();

        // Enviar correos a cada postulante
        foreach ($postulantes as $postulante) {
            $user = $postulante->usuario; // Obtener el usuario asociado
        
            if ($user && $user->email) { // Verificar si el usuario y su correo existen
                Mail::to($user->email)->send(new OfertaPublicadaMail($this->oferta));
            }
        }
    }
}
