
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
            <h4>FORMULÁRIO DE VISITA TÉCNICA</h4>
        </td>
    </tr>
</table>
<body>
    <table>
        <tr>
            <td><label>Categoria:</label> {{$visitaTecnica->categoria->nome}}</td>
            <td><label>Sub Categoria:</label> {{$visitaTecnica->subcategoria->nome}}</td>
        </tr>
        <tr>
            <td><label>Custo:</label> {{$visitaTecnica->custo == 1 ? 'Sim' : 'Não'}}</td>
            <td><label>Compensação:</label> {{$visitaTecnica->compensacao == 1 ? 'Sim' : 'Não'}}</td>
        </tr>
        <tr>
            <td><label>Empresa/Evento:</label> {{$visitaTecnica->emp_evento}}</td>
            <td><label>Coordenação:</label> {{$visitaTecnica->coordenacao->nome}}</td>
        </tr>
        <tr>
            <td><label>Curso:</label> {{$visitaTecnica->curso->nome}}</td>
            <td><label>Turma:</label> {{$visitaTecnica->turma->nome}}</td>
        </tr>
        <tr>
            <td><label>Disciplinas:</label>
                @foreach($nomeDisciplinas as $disciplina)
                    {{$disciplina}}@if(!$loop->last), @endif
                @endforeach
            </td>
            
            <td><label>Professor:</label> {{$visitaTecnica->professor->name}}</td>
        </tr>
        <tr>
            <td><label>Servidor Participante:</label> {{$visitaTecnica->servidor_participante}}</td>
            <td><label>Justificativa Servidores:</label> {{$visitaTecnica->justificativa_servidores}}</td>
        </tr>
        <tr>
            <td><label>Estado:</label> {{$visitaTecnica->estado->nome}}</td>
            <td><label>Cidade:</label> {{$visitaTecnica->cidade->nome}}</td>
        </tr>
        <tr>
            <td><label>Data/Hora Saída:</label> {{$visitaTecnica->data_hora_saida}}</td>
            <td><label>Data/Hora Retorno:</label> {{$visitaTecnica->data_hora_retorno}}</td>
        </tr>
        <tr>
            <td><label>Carga Horária Total:</label> {{$visitaTecnica->carga_horaria_total}}</td>
            <td><label>Carga Horária visitaTecnica:</label> {{$visitaTecnica->carga_horaria_visitaTecnica}}</td>
        </tr>
        <tr>
            <td><label>Conteúdo Programático:</label> {{$visitaTecnica->conteudo_programatico}}</td>
            <td><label>Quantidade de Estudantes:</label> {{$visitaTecnica->quantidade_estudantes}}</td>
        </tr>
        <tr>
            <td><label>Hospedagem:</label> {{$visitaTecnica->hospedagem}}</td>
            <td><label>Cotação Hospedagem:</label></td> 
        </tr>
        <tr>
            <td><label>Menor Valor Hospedagem:</label> {{$visitaTecnica->menor_valor_hospedagem}}</td>
            <td><label>Valor Total Diárias:</label> {{$visitaTecnica->valor_total_diarias}}</td>
        </tr>
        <tr>
            <td><label>Custo Total:</label> {{$visitaTecnica->custo_total}}</td>
            <td><label>Justificativa Hospedagem:</label> {{$visitaTecnica->justificativa_hospedagem}}</td>
        </tr>
        <tr>
            <td><label>Status:</label> {{$visitaTecnica->status}}</td>
            <td><label>Justificativa:</label> {{$visitaTecnica->justificativa}}</td>
        </tr>
        <tr>
            <td><label>Justificativa Outra Disciplina:</label> {{$visitaTecnica->justificativa_outra_disciplina}}</td>
            <td><label>Objetivos:</label> {{$visitaTecnica->objetivos}}</td>
        </tr>
        <tr>
            <td><label>Metodologia:</label> {{$visitaTecnica->metodologia}}</td>
            <td><label>Forma de Avaliação da Aprendizagem:</label> {{$visitaTecnica->forma_avaliacao_aprendizagem}}</td>
        </tr>
    </table></body>
</html>
