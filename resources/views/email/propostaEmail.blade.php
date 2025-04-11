<!DOCTYPE html>
<html>
<head>
    <title>Proposta de Visita Técnica</title>
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
    <div class="container">
        <div class="header">
            <h2>Detalhes da Visita Técnica</h2>
        </div>
        <div class="content">
            <div class="field-row">
                <div class="field-label">Local:</div>
                <div class="field-value">{{ $local }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Estado:</div>
                <div class="field-value">{{ $estado }}</div>
            </div>
            <div class="field-row">
                    <div class="field-label">Cidade:</div>
                    <div class="field-value">{{ $cidade }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Data de Saída:</div>
                <div class="field-value">{{ $dataSaida }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Data de Retorno:</div>
                <div class="field-value">{{ $dataRetorno }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Responsável:</div>
                <div class="field-value">{{ $responsavel }}</div>
            </div>
            <div class="field-row">
                <div class="field-label">Turma:</div>
                <div class="field-value">{{ $turma }}</div>
            </div>
        </div>
    </div>
</body>
</html>
