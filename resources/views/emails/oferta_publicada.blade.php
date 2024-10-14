<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Oferta Publicada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0; /* Fondo gris claro para el cuerpo */
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            line-height: 1.6;
            color: #555;
        }
        .details {
            margin-top: 20px;
            background-color: #ffffff; /* Fondo blanco para la tarjeta de detalles */
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #ccc; /* Borde alrededor de la tarjeta */
        }
        .details li {
            margin-bottom: 10px;
        }
        strong {
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
        .button {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: white; /* Fondo blanco para el botón */
            color: #00bcd4; /* Color azul cian para las letras del botón */
            text-decoration: none;
            border-radius: 5px;
            border: 2px solid #00bcd4; /* Borde azul cian */
            font-weight: bold;
        }
        .button:hover {
            background-color: #00bcd4; /* Cambia a azul cian al pasar el mouse */
            color: white; /* Cambia el color de las letras a blanco al pasar el mouse */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Nueva Oferta Publicada!</h1>
        <p>Se ha publicado una nueva oferta para el cargo: <strong>{{ $oferta->cargo }}</strong></p>

        <div class="details">
            <p><strong>Detalles:</strong></p>
            <ul>
                <li><strong>Empresa que ofrece la vacante:</strong> {{ $oferta->empresa ? $oferta->empresa->nombre_comercial : 'No disponible' }}</li>
                <li><strong>Ubicación de la empresa:</strong> {{ $oferta->ubicacion ? $oferta->ubicacion->provincia . ', ' . $oferta->ubicacion->canton : 'No disponible' }}</li>
                <li><strong>Modalidad:</strong> {{ $oferta->modalidad }}</li>
                <li><strong>Carga horaria:</strong> {{ $oferta->carga_horaria }}</li>
                <li><strong>Experiencia Requerida:</strong>
                    @if ($oferta->experiencia === 0)
                        No requerida
                    @else
                        {{ $oferta->experiencia }} {{ $oferta->exp_m ? ($oferta->experiencia > 1 ? 'meses' : 'mes') : ($oferta->experiencia > 1 ? 'años' : 'año') }}
                    @endif
                </li>
                <li><strong>Área de la oferta:</strong> {{ $oferta->areas ? $oferta->areas->nombre_area : 'No disponible' }}</li>
                <li><strong>Fecha de publicación:</strong> {{ $oferta->fecha_publi ? \Carbon\Carbon::parse($oferta->fecha_publi)->format('d/m/Y') : 'No disponible' }}</li>
                <li><strong>Fecha límite para postular:</strong> {{ $oferta->fecha_max_pos ? \Carbon\Carbon::parse($oferta->fecha_max_pos)->format('d/m/Y') : 'No disponible' }}</li>
                <li><strong>Descripción:</strong> {{ $oferta->detalles_adicionales }}</li>
            </ul>
            <p class="footer">Para todos los detalles visita la página</p>
        </div>

       <center><a href="http://www.postula.net" class="button">Visitar la página</a></center> 

        <p class="footer">¡No pierdas la oportunidad de postularte!</p>
    </div>
</body>
</html>
