<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recadastramento para Visitas Técnicas</title>
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
        .btn-link {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50; /* Verde */
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-link:hover {
            background-color: #2e7d32; /* Verde mais escuro no hover */
        }
        a.btn-link {
            text-decoration: none; /* Remove underline */
            color: white; /* Garante que o texto do link seja branco */
            
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('https://extensao.garanhuns.ifpe.edu.br/img/Logo-Garanhuns.png') }}" alt="Logo" style="max-width: 200px;">
        <h1>Recadastramento Necessário</h1>
    </div>

    <div class="content">
        <p>Prezado(a) Estudante,</p>

        <p>Esperamos que esta mensagem o(a) encontre bem. Gostaríamos de informar que é necessário realizar seu recadastramento para continuar participando das visitas técnicas oferecidas pela instituição.</p>

        <p>O recadastramento é um processo importante que nos ajuda a:</p>
        <ul>
            <li>Manter seus dados atualizados</li>
            <li>Garantir sua segurança durante as atividades extraclasse</li>
            <li>Organizar melhor as atividades propostas</li>
            <li>Assegurar sua participação em futuras atividades</li>
        </ul>

        <p>Por favor, acesse o sistema e atualize seus dados o mais breve possível.</p>

        <center>
            <p><strong>Link para recadastramento:</strong></p>
           <a href="https://sisvtec.garanhuns.ifpe.edu.br/admin/discentes" class="btn-link">Realizar recadastramento</a>
        </center>

        <p>Caso tenha alguma dúvida, não hesite em nos contatar.</p>

        <p>Atenciosamente,<br>
        Equipe de Coordenação</p>
    </div>

    <div class="footer">
        <p>Este é um e-mail automático. Por favor, não responda.</p>
    </div>
</body>
</html>
