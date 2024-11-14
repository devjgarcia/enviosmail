@extends('layouts.emails')

@section('content')
    
@include('emails.logo_correos')

<div style="width: 100%">
    <h5 style="font-size: 18px; text-align: center">{{ $titulo_correo }}</h5>
    <br>
</div>
<div style="width: 100%">
    <p class="p-body">
        Estimado, {{ $falta->nb_empleado }}, usted presenta una falta de tipo: {{ $falta->grupo }} en la fecha: {{ $fecha }}
    </p>
    <p class="p-body">
        Le invitamos a realizar la justificacion de la misma desde la plataforma SacaMóvil
    </p>
    <div class="text-center">
        <a href="{{ $link_justificacion }}" class="btn-enlace">
            Justificar
        </a>
    </div>
</div>

<div style="width: 100%">
    <h4>Estas han sido sus faltas del mes actual: </h4>
    <table class="table-lice">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Tipo de Falta</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($faltas_mes as $f)
            <tr>
                <td>{{ \Carbon\Carbon::parse($f->fecha_justuficada)->format('Y-m-d') }}</td>
                <td>
                    @if( $falta->grupo != 'INASISTENCIAS' )
                        {{ \Carbon\Carbon::parse($f->fecha_justuficada)->format('H:i') }}
                    @else
                        N/A
                    @endif  
                </td>

                <td>{{ $f->grupo }}</td>
                <td>
                    <a href="{{ env('URL_SACA') }}llic/{{ md5($lice->codigo_lice) }}/?rd=justificaemple/{{ $fecha_sf }}">
                        Justificar
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection