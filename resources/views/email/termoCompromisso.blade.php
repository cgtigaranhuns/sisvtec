<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Termo de Compromisso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2e7d32;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            font-size: 12px;
            color: #666;
        }
       
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('https://extensao.garanhuns.ifpe.edu.br/img/Logo-Garanhuns.png') }}" alt="Logo" style="max-width: 200px;">
        <h1>Termo de Compromisso</h1>
    </div>

    <div class="content">
        <p>Prezado(a) Estudante,</p>

        <h3>Segue anexo o termo de compromisso para a Atividade Extraclasse - {{$local}}, em: {{$dataSaida}} </h3>  

        <p>Caso tenha alguma dúvida, não hesite em nos contatar.</p>

        <p>Atenciosamente,<br>
        Equipe de Coordenação</p>
    </div>

    <div class="footer">
        <p>Este é um e-mail automático. Por favor, não responda.</p>
    </div>
</body>
</html>



