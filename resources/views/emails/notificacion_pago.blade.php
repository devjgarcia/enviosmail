@extends('layouts.emails')

@section('content')
    
<div 
        style="width: 90%; 
                padding-left: 5%;
                padding-right: 5%;
                padding-top: 10px;
                padding-bottom: 10px;
                background-color: #FFFFFF;
                border-bottom: solid 2px #636b6f;
                text-align: center;
            "
    >
        <img src="https://newsm.sacamovil.com/public/images/logos/logosaca-grayblue.png" style="width: 250px; height: 190px;" alt="Logo SACA">
    </div>
    <div style="width: 100%">
        <h5 style="font-size: 18px; text-align: center">¡Nueva Notificacion de Pago - Saca Móvil!</h5>
        <br><br>

        <p style="font-size: 16px;">
            Se ha generado una Notificación de Pago. Datos de Licencia:
        </p>
        <ul>
            <li style="font-size: 16px;">
                <b>Licencia: </b> {{ $licencia->codigo }}
            </li>
            <li style="font-size: 16px;">
                <b>Empresa: </b> {{ $licencia->empresa }}
            </li>
            <li style="font-size: 16px;">
                <b>Correo: </b> {{ $licencia->correo }}
            </li>
            <li style="font-size: 16px;">
                <b>Contacto: </b> {{ $licencia->persona }}
            </li>
            <li style="font-size: 16px;">
                <b>Teléfono: </b> {{ $licencia->telefono }}
            </li>
        </ul>

        <br><br>

        <table class="table-detalle-dual w-100">
            <thead>
                <tr>
                    <th colspan="2">
                        <h4 style="font-size: 18px; color: white;">
                            Detalles de Pago:
                        </h4>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Código: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->codnotipago }}
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Fecha de Notificación: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->fecha_noti_format }}
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Fecha de Pago: </b> 
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->fecha_pago }}
                    </td>
                </tr>

                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Tipo de Pago: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->notiTipoPago->nombre }}
                    </td>
                </tr>

                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Cuenta Receptora: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->notiCuenta->nombre }} <small>({{ $pago->notiCuenta->codigo }})</small>
                    </td>
                </tr>

                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Moneda de Pago: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        ({{ $pago->notiCuenta->notiMoneda->codigo }}) {{ $pago->notiCuenta->notiMoneda->nombre }}
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>N Documento: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->n_doc }}
                    </td>
                </tr>

                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Monto: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->monto }}
                    </td>
                </tr>

                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Comentario: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->coment_usu }}
                    </td>
                </tr>

                <tr>
                    <td style="font-size: 16px; text-align: right; width: 35%;">
                        <b>Estado de Pago: </b>
                    </td>
                    <td style="font-size: 16px; width: 65%">
                        {{ $pago->estado_pago }}
                    </td>
                </tr>
            </tbody>
        </table>

        <br><br>
@endsection