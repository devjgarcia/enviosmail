<!DOCTYPE html>
<html>
    <head>
        <title>Iseweb</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <!-- Styles -->
        <style type="text/css">
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
            .content { text-align: center; }
            .title { font-size: 84px; }

            .table-lice {
                width: 100%;
                border: 1px solid #afafaf;
                border-collapse: collapse;
            }
            .table-lice th, .table-lice td {
                text-align: left;
                border: 1px solid #afafaf;
                border-collapse: collapse;
                padding-top: 5px;
                padding-bottom: 5px;
            }
            .table-lice thead th {
                background-color: #60c2d8;
                color: #FFF;
                padding: 10px;
                font-size: 16px;
            }

            .table-lice tbody td {
                font-size: 14px;
            }

            h4, h5, h2, h3 {
                color: #636b6f;
            }

            button.btn-action, a.btn-action {
                margin-top: 1.5rem;
                text-decoration: none;
                padding: 1rem;
                border-radius: 8px;
                background-color: #60c2d8;
                color: #636b6f;
                font-size: 1.4rem;
                border: solid 1.5px #afafaf;
                margin: .5rem;
                font-weight: 600;
            }

            div.w-100{
                width: 100%;
            }

            div.div-header {
                width: 90%; 
                padding-left: 5%;
                padding-right: 5%;
                padding-top: 20px;
                padding-bottom: 10px;
                background-color: #60c2d8;
                border-bottom: solid 2px #636b6f;
                text-align: center;
            }

            .titulo {
                font-size: 18px; 
                text-align: center;
                list-style: none;
            }

            .lista li{
                list-style: none;
                padding: 10px;
                font-size: 18px;
                color: #636b6f;
            }

            p.p-body{
                font-size: 16px;
                color: #636b6f;
            }

            table.table-detalle-dual tbody tr td{
                padding: 6px;
                border: 1.5px solid #afafaf;
                border-collapse: collapse;
            }

            table.table-detalle-dual tbody tr td:first {
                color: #60c2d8;
            }

            table.table-detalle-dual thead tr th {
                background-color: #60c2d8;
                color: #FFF;
            }

            .w-100 {
                width: 100%;
            }

            .btn-enlace {
                color: white !important;
                background-color: #5ec1d7;
                padding: 12px;
                border-radius: 4.5px;
                font-size: 16px;
                font-weight: 700;
                text-decoration: none;
            }

            .text-center {
                text-align: center;
            }

            .py-2 {
                padding-top: 15px;
                padding-bottom: 15px;
            }

            div.div-leyenda p b:first-child{
                font-weight: 800;
                font-size: 13px;
            }

            div.div-leyenda p {
                font-weight: 400;
                font-size: 11px;
                text-align: justify;
            }

            p.text-title-content {
                font-size: 12px;
                text-align: justify;
                width: auto;
            }

            p.text-title-content b:first-child {
                font-weight: 800;
                font-size: 14px;
            }
            
            .text-blue {
                color: #1f91f3;
            }

            .text-red {
                color: #F44336;
            }

        </style>
    </head>
    @php( $idNumber = rand(1, 10000) )
    <body id="email-ID-{{ $idNumber }}">
        <br />
        <div class="container box" style="width: 100%">
            @yield('content')
        </div>
        <br />
        @include('emails.copyright')
        <br />
        @include('emails.confidencialidad')
    </body>
</html>