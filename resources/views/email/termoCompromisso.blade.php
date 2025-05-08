<!DOCTYPE html>
<html>
<head>
    <title>Proposta da Atividade Extraclasse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #f8fff8;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #2e7d32;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #e0f2e0;
        }
        .field-row {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0f2e0;
            padding: 10px 0;
        }
        .field-label {
            width: 150px;
            font-weight: bold;
            color: #2e7d32;
        }
        .field-value {
            flex: 1;
            color: #333;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="{{ asset('https://extensao.garanhuns.ifpe.edu.br/img/Logo-Garanhuns.png') }}" alt="Logo" style="max-width: 200px;">
    </div>
    <div class="container">
        <div class="header">
           <h3>Segue anexo o termo de compromisso para a Atividade Extraclasse - {{$local}}, em: {{$dataSaida}} </h3>
                
        </div>
</html>
