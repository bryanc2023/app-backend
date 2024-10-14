<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Oferta; 

class OfertaPublicadaMail extends Mailable
{
    use Queueable, SerializesModels;
    public $oferta;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Oferta $oferta)
    {
        $this->oferta = $oferta; // Asigna la oferta a la propiedad
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.oferta_publicada') // Vista del correo
                    ->subject('Nueva oferta publicada: ' . $this->oferta->cargo); // Asunto
                  
    }
}
