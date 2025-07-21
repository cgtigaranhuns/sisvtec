
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
            <h4>PROJETO DA ATIVIDADE EXTRACLASSE</h4>
        </td>
    </tr>
</table>
<body>
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Informações Gerais
            </td>
        </tr>
        <tr>
            <td><label>Categoria:</label> {{$visitaTecnica->categoria->nome}}</td>
            <td><label>Sub Categoria:</label> {{$visitaTecnica->subcategoria->nome}}</td>
        </tr>
        <tr>
            <td><label>Custo:</label> {{$visitaTecnica->custo == 1 ? 'Sim' : 'Não'}}</td>
            <td><label>Compensação:</label> {{$visitaTecnica->compensacao == 1 ? 'Sim' : 'Não'}}</td>
        </tr>
        <tr>
            <td colspan="0"><label>Local:</label> {{$visitaTecnica->emp_evento}}</td>
            <td><label>Coordenação/Setor:</label> {{$visitaTecnica->coordenacao->nome}}</td>
        </tr>
        <tr>
            <td><label>Curso:</label> 
                @foreach($nomeCursos as $curso)
                    {{$curso}}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td><label>Turma:</label>
                @foreach($nomeTurmas as $turma)
                    {{$turma}}@if(!$loop->last), @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <td><label>Disciplinas:</label>
                @foreach($nomeDisciplinas as $disciplina)
                    {{$disciplina}}@if(!$loop->last), @endif
                @endforeach
            </td>
            
            <td><label>Professor/Servidor Responsável:</label> {{$visitaTecnica->professor->name}}</td>
        </tr>
        <tr>
            <td><label>Servidores Participantes:</label>
                @foreach($nomeParticipantes as $participante)
                    {{$participante}}@if(!$loop->last), @endif
                    
                @endforeach
            </td>
            <td><label>Justificativa Servidores:</label> {{$visitaTecnica->justificativa_servidores}}</td>
        </tr>
        <tr>
            <td><label>Estado:</label> {{$visitaTecnica->estado->nome}}</td>
            <td><label>Cidade:</label> {{$visitaTecnica->cidade->nome}}</td>
        </tr>
        <tr>
            <td><label>Data/Hora Saída:</label> {{ \Carbon\Carbon::parse($visitaTecnica->data_hora_saida)->format('d/m/Y H:i') }}</td>
            <td><label>Data/Hora Retorno:</label> {{\Carbon\Carbon::parse($visitaTecnica->data_hora_retorno)->format('d/m/Y H:i')}}</td>
        </tr>
        <tr>
            <td><label>Carga Horária Total:</label> {{$visitaTecnica->carga_horaria_total}}</td>
            <td><label>Carga Horária da Visita:</label> {{$visitaTecnica->carga_horaria_visita}}</td>
        </tr>
        <tr>
            
            <td><label>Quantidade de Estudantes Informado:</label> {{$visitaTecnica->qtd_estudantes}}</td>
            <td><label>Quantidade de Estudantes Adicionados:</label> <span style="background-color: {{ $visitaTecnica->qtd_estudantes != $visitaTecnica->discenteVisitas->count() ? '#f3adab' : 'black' }}">{{$visitaTecnica->discenteVisitas->count()}}</span></td>
        </tr>
        <tr>
    </table>
    <br>
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Informações Financeiras
            </td>
        </tr>
            <td><label>Haverá Hospedagem?:</label> {{$visitaTecnica->hospedagem == 1 ? 'Sim' : 'Não'}}</td>
            <td><label>Justificativa da Hospedagem:</label> {{$visitaTecnica->justificativa_hospedagem}}</td>
            
        </tr>
        <tr>
            <td><label>Haverá Passagens?:</label> {{$visitaTecnica->passagens == 1 ? 'Sim' : 'Não'}}</td>
            <td><label>Justificativa das Passagens:</label> {{$visitaTecnica->justificativa_passagens}}</td>    
        <tr>
            <td><label>Valor da Diária por Estudante R$:</label> {{ number_format(($visitaTecnica->valor_total_diarias / $visitaTecnica->qtd_estudantes), 2, ',', '.') }}</td> 
             <td><label>Valor Total das Diárias R$:</label> {{number_format(($visitaTecnica->valor_total_diarias), 2, ',', '.')}}</td>
            
        </tr>
        <tr>
            <td><label>Valor da Hospedagem por Estudante R$:</label> {{number_format(($visitaTecnica->menor_valor_hospedagem / $visitaTecnica->qtd_estudantes), 2, ',', '.') }}</td> 
            <td><label>Valor Total das Hospedagens R$:</label> {{number_format(($visitaTecnica->menor_valor_hospedagem), 2, ',', '.')}}</td>
            
            
        <tr>
            <td><label>Valor da Passagens por Estudante R$:</label> {{number_format(($visitaTecnica->menor_valor_passagens / $visitaTecnica->qtd_estudantes), 2, ',', '.')}}</td>
            <td><label>Valor Total das Passagens R$:</label> {{number_format(($visitaTecnica->menor_valor_passagens), 2, ',', '.')}}</td>
           
            
            
        </tr>
        <tr>
            <td><label>Valor Total da Ajuda de Custo Por Estudante R$:</label> <span style="color: brown; font-weight: bold">{{number_format(($visitaTecnica->custo_total / $visitaTecnica->qtd_estudantes), 2, ',', '.')}}</span></td>
            <td><label>Valor Total Geral da Ajuda de Custo R$:</label> <span style="color: brown; font-weight: bold">{{number_format(($visitaTecnica->custo_total), 2, ',', '.')}}</span></td>
           
        </tr>
    </table>
    <br>
    <div style="page-break-after: always;"></div>
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Objetivos e Justificativas
            </td>
        </tr>
        <tr>
            <td><label>Conteúdo Programático/Resumo:</label> {{$visitaTecnica->conteudo_programatico}}</td>
            <td><label>Justificativa:</label> {{$visitaTecnica->justificativa}}</td>
        </tr>
        <tr>
            <td><label>Justificativa Outra Disciplina:</label> {{$visitaTecnica->just_outra_disciplina}}</td>
            <td><label>Objetivos:</label> {{$visitaTecnica->objetivos}}</td>
        </tr>
        <tr>
            <td><label>Metodologia:</label> {{$visitaTecnica->metodologia}}</td>
            <td><label>Forma de Avaliação da Aprendizagem:</label> {{$visitaTecnica->form_avalia_aprend}}</td>
        </tr>
    </table>
   
    <br>
    <table>
        <tr>
            <td colspan="7" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Compensação Docente Não Envolvido
            </td>
        </tr>
        <tr>
            <th style="font-size: 8px">Professor</th>
            <th style="font-size: 8px">Disciplina</th>
            <th style="font-size: 8px">Turma</th>
            <th style="font-size: 8px">Data/Hora Reposição</th>
            <th style="font-size: 8px">Professor que Assumirá</th>
            

        </tr>
        @foreach($visitaTecnica->compensacaoDocenteNaoEnvolvido as $compensacao)
        <tr style="font-size: 8px">
            <td style="font-size: 8px">{{ $compensacao->user->name }}</td>
            <td style="font-size: 8px; text-align: center">{{ $compensacao->disciplina->nome }}</td>
            <td style="font-size: 8px; text-align: center">{{ $compensacao->turma->nome }}</td>
            <td style="font-size: 8px; text-align: center">{{ \Carbon\Carbon::parse($compensacao->data_hora_reposicao)->format('d/m/Y H:i') }}</td>
            <td style="font-size: 8px">{{ $compensacao->user2->name }}</td>
           
        </tr>
        @endforeach
    </table>
    <br>
    <table>
        <tr>
            <td colspan="7" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Compensação Turma Não Envolvida
            </td>
        </tr>
        <tr>
            <th style="font-size: 8px">Professor</th>
            <th style="font-size: 8px">Disciplina</th>
            <th style="font-size: 8px">Turma</th>
            <th style="font-size: 8px">Data/Hora Reposição</th>
            
        </tr>
        @foreach($visitaTecnica->compensacaoTurmaNaoEnvolvido as $compensacao)
        <tr style="font-size: 8px">
            <td style="font-size: 8px">{{ $compensacao->user->name }}</td>
            <td style="font-size: 8px; text-align: center">{{ $compensacao->disciplina->nome }}</td>
            <td style="font-size: 8px; text-align: center">{{ $compensacao->turma->nome }}</td>
            <td style="font-size: 8px; text-align: center">{{ \Carbon\Carbon::parse($compensacao->data_hora_reposicao)->format('d/m/Y H:i') }}</td>
            
        </tr>
        @endforeach
    </table>
    <br>
    <div style="page-break-after: always;"></div>
    @foreach($visitaTecnica->discenteVisitas->groupBy('discente.turma.nome') as $turmaNome => $discentes)
        <table>
            <tr>
                <td colspan="15" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                    Estudantes - Turma: {{ $turmaNome }}
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
                <th style="font-size: 8px">Status</th>
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
                <td style="font-size: 8px; text-align: center; color: {{ $discenteVisita->discente->status == 3 ? 'green' : 'red' }}">
                    {{ $discenteVisita->discente->status == 3 ? 'OK' : 'Pendência' }}
                </td>
            </tr>
            @endforeach
        </table>
        <br>
    @endforeach
    

<br>
<table style="margin: 40px auto 0 auto; border: none; width: 50%;">
    <tr>
        <td style="border: none; text-align: center;">
            <hr style="width: 60%; border-top: 1px solid #888;">
            <div style="font-size: 12px; font-weight: bold;">
                {{$visitaTecnica->professor->name}}
            </div>
            <div style="font-size: 11px;">
                SIAPE: {{$visitaTecnica->professor->username ?? '__________'}}
            </div>
            <div style="font-size: 11px; margin-top: 5px;">
                Professor Responsável
            </div>
        </td>
    </tr>
</table>

</body>
</html>
