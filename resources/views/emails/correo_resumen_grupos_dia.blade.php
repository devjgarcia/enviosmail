@extends('layouts.emails')

@section('content')

@include('emails.logo_correos')

<div style="width: 100%">
    <h5 style="font-size: 18px; text-align: center">{{ $titulo_correo }}</h5>
    <br>
</div>
<div style="width: 100%">
    <table class="table-lice" id="table_listado_{{ $titulo_correo }}_{{ rand(1,200000) }}">
        <thead>
            <tr>
                <th style="width: 20%;">
                    <small style="font-size: 11px !important;">Empleado</small>
                </th>
                <th style="width: 10%;">
                    <small style="font-size: 11px !important;">Empresa</small>
                </th>
                <th title="Horas del Turno">
                    <small style="font-size: 11px !important;">H. del Turno</small>
                </th>
                <th title="Total de Horas en rango de fecha">
                    <small style="font-size: 11px !important;">Total H. Periodo</small>
                </th>
                <th title="Horas trabajadas en este rango de fecha">
                    <small style="font-size: 11px !important;">H. Trabajadas Reales</small>
                </th>
                <th title="Horas extras en este rango de fecha">
                    <small style="font-size: 11px !important;">H. Extras</small>
                </th>
                <th title="Horas adicionales fuera de horario de trabajo">
                    <small style="font-size: 11px !important;">H. Excedentes</small>
                </th>
                <th>
                    <small style="font-size: 11px !important;">H. No Trabajadas</small>
                </th>                
                <th title="Horas no trabajadas con incidencia remunerada">
                    <small style="font-size: 11px !important;">H. No Trabajadas Remu.</small>
                </th>                
                <th title="Cantidad de Horas de inasistencias en Dias (Inasistencias)">
                    <small style="font-size: 11px !important;">H. Remu. Por Incidencia en Días</small>
                </th>                
                <th>
                    <small style="font-size: 11px !important;">Total Inasistencias</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listado as $lista)
            <tr>
                <td><small><b>{{ $lista->co_empleado }}</b> {{ $lista->nb_empleado }}</small></td>
                <td><small>{{ $lista->nb_empresas }}</small></td>
                <td>{{ $lista->HorasTurno }}</td>
                <td>{{ $lista->TotHorJorna }}</td>
                <td>{{ $lista->HorTrab }}</td>
                <td>{{ $lista->HorExtra }}</td>
                <td>{{ $lista->HorSalTard }}</td>
                <td>{{ $lista->HorNoTrab }}</td>
                <td>{{ $lista->HornotTrabRemu }}</td>
                <td>{{ $lista->TohorRemuporIna }}</td>
                <td>{{ $lista->TotalIna }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%; display: flex;">
        @if( !empty($finde['sabado']) )
            <p class="text-title-content text-blue"><b>Sábado: </b> {{ $finde['sabado'] }}</p>
        @endif

        @if( !empty($finde['domingo']) )
            <p class="text-title-content text-red">&nbsp;&nbsp;<b>Domingo: </b> {{ $finde['domingo'] }}</p>
        @endif
    </div>
</div>
<div style="width: 100%" class="div-leyenda" id="div_leyenda_{{ date('Y-m-d h:i:s') }}">
    <br>
    <h4><b>Leyenda de Datos</b></h4>
    <p>
        <b>H. del Turno: </b> 
        Total de Horas del Turno asignado en el día al empleado
    </p>
    <p>
        <b>Total. H Periodo: </b> 
        Total de horas de cada Turno  por (*) el  Total de días indicados en el periodo Seleccionado
    </p>
    <p>
        <b>H. Trabajadas Reales: </b> 
        Total de Horas Trabajadas reales por el empleado entre los dias del periodo seleccionado
    </p>
    <p>
        <b>H. Extras: </b> 
        Total de Horas Extras generadas por cada   empleado entre el periodo seleccionado, si el turno tiene esta opción 
        activa y el total de horas sobre pasa la tolerancia al salir
    </p>
    <p>
        <b>H.Excedentes: </b> 
        Total de Horas Excedentes o salidas  tarde de cada Periodo entre los dias  seleccionados
    </p>
    <p>
        <b>H. No Trabajadas: </b>
        Total de Horas no trabajadas en cada periodo de fechas seleccionadas
    </p>
    <p>
        <b>H. No Trabajadas Remu: </b>
        Total de Horas No trabajadas,  Remuneradas por Incidencias en  horas de cada periodo de las fechas seleccionadas
    </p>
    <p>
        <b>H. Remu. por Incidencias en Días: </b>
        Total de Horas Remuneradas por dias de inasistencia. Convertidas por Incidencias en dias Remunerados 
        entre el periodo de las fechas seleccionadas
    </p>
    <p>
        <b>Total Inasistencias: </b>
        Total de dias de Inasistencia entre el periodo de dias  seleccionados
    </p>
</div>
@endsection