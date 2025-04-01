
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
        <td style="width: 30%; text-align: center; border: 0px solid;">
            <img src="{{ asset('img/logo-ifpe.png') }}" alt="Logo" style="max-width: 120px;">
        </td>
        
        <td style="width: 70%; text-align: center; border: 0px solid; color:gray; padding: 0px;">
            <h3>MINISTÉRIO DA EDUCAÇÃO</h3>
            <h4>SECRETARIA DE EDUCAÇÃO PROFISSIONAL E TECNOLÓGICA</h4>
            <h4>INSTITUTO FEDERAL DE EDUCAÇÃO, CIÊNCIA E TECNOLOGIA</h4>
            
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; border: 0px solid;">
            <h4>RELATÓRIO FINAL</h4>
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
            <td><label>Quantidade de Estudantes:</label> {{$visitaTecnica->quantidade_estudantes}}</td>
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
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Descrição da Visita Técnica
            </td>
        </tr>
        @foreach($visitaTecnica->RelatorioFinalVisitaTecnica as $relatorio)
        <tr>
            <td><label>Descrição:</label> {!! str($relatorio->descricao)->sanitizeHtml() !!}</td>
            
        </tr>
        @endforeach

        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Ocorrências da Visita Técnica
            </td>
        </tr>
        @foreach($visitaTecnica->RelatorioFinalVisitaTecnica as $relatorio)
        <tr>
            <td><label>Ocorrência:</label> {!! str($relatorio->ocorrencia)->sanitizeHtml() !!}</td>
            
        </tr>
        @endforeach
        
    </table>

    <div style="margin-top: 50px;">
        <table style="width: 100%; border: 0px;">
            <tr>
                <td style="width: 50%; text-align: center; border: 0px;">
                    _______________________________________<br>
                    <label>{{$visitaTecnica->professor->name}}</label><br>
                    <label>Professor Responsável</label>
                </td>
                <td style="width: 50%; text-align: center; border: 0px;">
                    _______________________________________<br>
                    <label>{{$visitaTecnica->coordenacao->user->name}}</label><br>
                    <label>Coordenador de Curso</label>
                </td>
            </tr>
        </table>
    </div>

            