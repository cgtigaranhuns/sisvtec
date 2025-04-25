
<!DOCTYPE html>
<html>
<head>
    <style>
        table {
           
            width: 100%;
        }
        
        td, th {
            border: 1px solid rgb(197, 194, 194);
            border-radius: 2px;
            padding: 8px;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            padding: 3px;
            color: rgb(95, 94, 94);
            font-size: 10px;
        }
        
        label {
            font-weight: bold;
        }
    </style>
</head>
<table>
    <tr>              
        <td style="width: 70%; text-align: center; border: 0px solid; color:gray; padding: 0px;">
            <img src="{{ asset('img/brasao.png') }}" alt="Logo" style="max-width: 100px;">
            <h3>Ministério da Educação</h3>
            <h4>Secretaria de Educação Profissional e Tecnológica</h4>
            <h4>Instituto Federal de Educação, Ciência e Tecnologia de Pernambuco</h4>
            <h4>Campus Garanhuns/Direção-geral/Diretoria de Ensino/</h4>
            
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; border: 0px solid;">
            <h4>ATA DE COMPARECIMENTO DA VISITA TÉCNICA</h4>
        </td>
    </tr>
</table>
<body>
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Dados Básicos da Visita Técnica
            </td>
        </tr>
        <tr>
            <td><label>Professor Responsável:</label> {{$visitaTecnica->professor->name}}</td>
            <td><label>Coordenação:</label> {{$visitaTecnica->coordenacao->user->name}}</td>
        </tr>
        <tr>
            <td><label>Curso:</label> {{$visitaTecnica->curso->nome}}</td>
            <td><label>Turma:</label> {{$visitaTecnica->turma->nome}}</td>
        </tr>
        <tr>
            <td><label>Quantidade de Estudantes:</label> {{$visitaTecnica->qtd_estudantes}}</td>
            <td><label>Local:</label> {{$visitaTecnica->emp_evento}}</td>
        </tr>
        <tr>
            <td><label>Cidade:</label> {{$visitaTecnica->cidade->nome}}</td>
            <td><label>Estado:</label> {{$visitaTecnica->estado->nome}}</td>
        </tr>
        <tr>
            <td><label>Período de :</label> {{ \Carbon\Carbon::parse($visitaTecnica->data_hora_saida)->format('d/m/Y') }} a {{\Carbon\Carbon::parse($visitaTecnica->data_hora_retorno)->format('d/m/Y')}}</td>
            <td><label>Carga Horária da Visita:</label> {{$visitaTecnica->carga_horaria_visita}} horas</td>
        </tr>
    </table>
    <br>
    @foreach($visitaTecnica->discenteVisitas->where('status', 3)->groupBy('discente.turma.nome') as $turmaNome => $discentes)
    <table>
        <tr>
            <td colspan="15" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Estudantes - Turma: {{ $turmaNome }}
            </td>
        </tr>
        <tr>
            <th style="font-size: 8px">Nome</th>
            <th style="font-size: 8px">Nome Social</th>
            <th style="font-size: 8px">Matrícula</th>
            <th style="font-size: 8px; width: 300px;">Assinatura</th>
            
        </tr>
        @foreach($discentes as $discenteVisita)
        <tr style="font-size: 8px">
            <td style="font-size: 8px">{{ $discenteVisita->discente->nome }}</td>
            <td style="font-size: 8px">{{ $discenteVisita->discente->nome_social }}</td>
            <td style="font-size: 8px; text-align: center">{{ $discenteVisita->discente->matricula }}</td>
            <td style="font-size: 8px; width: 300px;"></td>
            
        </tr>
        @endforeach
    </table>
    <br>
@endforeach
            