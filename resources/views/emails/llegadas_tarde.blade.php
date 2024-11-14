@extends('layouts.emails')

@section('content')
    
@include('emails.logo_correos')

<div style="width: 100%">
    <h5 style="font-size: 18px; text-align: center">{{ $titulo_correo }}</h5>
    <br>
</div>
<div style="width: 100%">
    <p>
        Listado de empleados que han llegado tarde en fecha: {{ $fecha }}
    </p>
</div>
<div style="width: 100%">
    <table class="table-lice">
        <thead>
            <tr>
                <th>Cód. Empleado</th>
                <th>Nombres</th>
                <th>Empresa</th>
                <th>Cargo</th>
                <th>Departamento</th>
                <th>Hora Entrada</th>
                <th>Últ. 7 días</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listado as $lista)
            <tr>
                <td>{{ $lista->co_empleado }}</td>
                <td>{{ $lista->nb_empleado }}</td>
                <td>{{ $lista->nb_empresas }}</td>
                <td>{{ $lista->nb_cargo }}</td>
                <td>{{ $lista->nb_dpto }}</td>
                <td>{{ $lista->horallega_format }}</td>
                <td>{{ $lista->canti_llegadas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div style="width: 100%">
    <p>
        <b>Últ. 7 días: </b> cantidad de veces que ha llegado tarde en los últimos 7 días.
    </p>
</div>
@endsection