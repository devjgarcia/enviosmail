@extends('layouts.emails')

@section('content')
    
@include('emails.logo_correos')

<div style="width: 100%">
    <h5 style="font-size: 18px; text-align: center">{{ $titulo_correo }}</h5>
    <br>
</div>
<div style="width: 100%">
    <p>
        Listado de empleados que están Inasistentes en fecha: {{ $fecha }}
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
                <th>Fecha</th>
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
                <td>{{ $lista->fechaina_format }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection