<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Validación</title>
    <style>
        .center{
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 50%;
            padding-top: 50px;
        }
        .grid-container {
            display: grid;
            height: 600px;
            align-content: center;
            grid-gap: 10px;
            background-color: #ffff00;
            padding: 10px;
        }

        .grid-container > div {
            text-align: center;
            padding: 5px 0;
            font-size: 25px;
        }
    </style>
</head>
<body>
    <div class="grid-container">
            <img class="center" src="{{ $message->embed('C:\xampp\htdocs\rokstar\rokstar\storage\app\public\img\logo.png') }}"/>
        <div>
            ¡BIENVENIDO {{ $msg['name'] }}!
        </div>
        <div>
            Gracias por haber creado tu cuenta Rokstar con tu correo {{ $msg['email'] }}
        </div>
        <div>
            Para darte de alta ingresa el siguiente codigo:
        </div>
        <div>
            <b style="font-size: 35px">{{ $msg['code'] }}</b>
        </div>
    </div>

</body>
</html>