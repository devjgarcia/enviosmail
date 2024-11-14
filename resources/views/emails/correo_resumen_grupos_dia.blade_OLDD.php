@extends('layouts.emails')

@section('content')

@include('emails.logo_correos')

<div style="width: 100%">
    <h5 style="font-size: 18px; text-align: center">{{ $titulo_correo }}</h5>
    <br>
</div>
<div style="width: 100%">
    <table class="table-lice">
        <thead>
            <tr>
                <th>Cód. Empleado</th>
                <th>Nombres</th>
                <th>Empresa</th>
                <th>H. del Turno</th>
                <th>H. Trabajadas</th>
                <th>H. No Trabajadas</th>
                <th>H. Extras</th>
                <th>Inasistencias</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listado as $lista)
            <tr>
                <td>{{ $lista->co_empleado }}</td>
                <td>{{ $lista->nb_empleado }}</td>
                <td>{{ $lista->nb_empresas }}</td>
                <td>{{ $lista->TotHorJorna }}</td>
                <td>{{ $lista->HorTrab }}</td>
                <td>{{ $lista->HorNoTrab }}</td>
                <td>{{ $lista->HorExtra }}</td>
                <td>{{ $lista->TotalIna }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection