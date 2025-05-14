
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
            <h4>RELATÓRIO FINAL DA ATIVIDADE EXTRACLASSE</h4>
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
            <td><label>Ocorrências:</label> {!! str($relatorio->ocorrencia)->sanitizeHtml() !!}</td>
            
        </tr>
        @endforeach      
        
    </table>
    <br>
        @foreach($visitaTecnica->discenteVisitas->where('falta', 1)->groupBy('discente.turma.nome') as $turmaNome => $discentes)
        <table>
            <tr>
                <td colspan="15" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(241, 16, 16); text-align:center; font-weight: bold;">
                    Estudantes Faltosos - Turma: {{ $turmaNome }}
                </td>
            </tr>
            <tr>
                <th style="font-size: 8px">Nome</th>
                <th style="font-size: 8px">Nome Social</th>
                <th style="font-size: 8px">CPF</th>
                <th style="font-size: 8px">Email</th>
                <th style="font-size: 8px">Banco</th>
                <th style="font-size: 8px">Agência</th>
                <th style="font-size: 8px">Conta</th>
                <th style="font-size: 8px">Tipo de Conta</th>
            </tr>
            @foreach($discentes as $discenteVisita)
            <tr style="font-size: 8px">
                <td style="font-size: 8px">{{ $discenteVisita->discente->nome }}</td>
                <td style="font-size: 8px">{{ $discenteVisita->discente->nome_social }}</td>
                <td style="font-size: 8px; text-align: center">{{ $discenteVisita->discente->cpf }}</td>
                <td style="font-size: 8px">{{ $discenteVisita->discente->email }}</td>
                <td style="font-size: 8px; text-align: center">{{$discenteVisita->discente->banco->numero ?? 'N/A'}} - {{$discenteVisita->discente->banco->nome ?? 'N/A' }}</td>
                <td style="font-size: 8px; text-align: center">{{ $discenteVisita->discente->agencia }}</td>
                <td style="font-size: 8px; text-align: center">{{ $discenteVisita->discente->conta }}</td>
                <td style="font-size: 8px; text-align: center">{{ $discenteVisita->discente->tipo_conta == 1 ? 'Conta Corrente' : 'Poupança' }}</td>
            </tr>
            @endforeach
        </table>
        <br>
    @endforeach
    <br>
    @foreach($visitaTecnica->RelatorioFinalVisitaTecnica as $relatorio)
        
        @if($relatorio->fotos)
            <tr>
                <td>
                    <label>Fotos:</label>
                    <div>
                        @foreach($relatorio->fotos as $foto)
                            <img src="{{ asset('storage/' . $foto) }}" alt="Foto da Visita Técnica" style="max-width: 200px; margin: 5px;">
                        @endforeach
                    </div>
                </td>
            </tr>
        @endif
    @endforeach



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

            