
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

        li, h1, h2, h3, h4, p {
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 12px;
            color: rgb(95, 94, 94)
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
            <h4>TERMO DE COMPROMISSO E AUTORIZAÇÃO PARA REALIZAÇÃO DA ATIVIDADE EXTRACLASSE</h4>
        </td>
    </tr>
</table>
<div>
    <table>
        <td><label style="font-weight: bold; font-size: 12px;">Categoria: </label> <span style="font-size: 12px">{{$visitaTecnica->categoria->nome}}</span></td>
            <td><label style="font-weight: bold; font-size: 12px;">Subcategoria: </label> <span style="font-size: 12px">{{$visitaTecnica->subcategoria->nome}}</span></td>
        </tr>
    </table>
</div>
<body>
    
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Dados do Estudante 
            </td>
        </tr>
        <tr>
            <td><label>Local: </label> {{$visitaTecnica->emp_evento}}</td>
            <td><label>Estudante: </label> {{$discente->discente->nome}} </td>
        </tr>
        <tr>
            <td><label>Matrícula: </label>{{$discente->discente->matricula}} </td>
            <td><label>RG: </label>{{$discente->discente->rg}} </td>
        </tr>
        <tr>
            <td><label>CPF: </label>{{$discente->discente->cpf}} </td>
            <td><label>Contato:</label> </td>
        </tr>
        <tr>
            <td><label>Endereço Completo: </label> {{$discente->discente->endereco}} </td>
            <td><label>CEP: </label>{{$discente->discente->cep}} </td>
        </tr>
        <tr>
            @if($discente->discente->estado)
                <td><label>Estado: </label>{{$discente->discente->estado->nome}}  </td>
            @endif
            @if($discente->discente->cidade)
                <td><label>Cidade: </label>{{$discente->discente->cidade->nome}} </td>
            @endif
        </tr>
    </table>

    
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Dados de saúde do estudante
            </td>
        </tr>
        <tr>
            <td class="header">O estudante possui algum problema de saúde?</td>
            <td>( ) SIM ( ) NÃO<br>Se sim, qual?</td>
        </tr>
        <tr>
            <td class="header">O estudante toma alguma medicação controlada?</td>
            <td>( ) SIM ( ) NÃO<br>Se sim, especifique qual a medicação e os horários em que os medicamentos devem ser administrados.</td>
        </tr>
        <tr>
            <td class="header">Existe alguma medicação que NÃO possa ser recebida pelo estudante?</td>
            <td>( ) SIM ( ) NÃO<br>Se sim, qual?</td>
        </tr>
        <tr>
            <td class="header">Demais observações (opcional)</td>
            <td></td>
        </tr>
    </table>

  
    <table>
        <tr>
            <td colspan="2" style="background-color: rgb(226, 223, 223); font-size: 12px; color: rgb(62, 62, 62); text-align:center; font-weight: bold;">
                Dados do responsável legal
            </td>
        <tr>
            <td colspan="2"><label>Nome:</label> </td>            
        </tr>

            <td><label>Grau de parentesco:</label> </td>           
            <td><label>RG:</label> </td>
            
        </tr>
            <td><label>CPF:</label> </td>            
             <td><label>Telefone:</label> </td>
            
        </tr>
    </table>

    <h3>TERMO DE COMPROMISSO DO(A) ESTUDANTE PARA VISITA TÉCNICA</h3>
    <p>As visitas técnicas são atividades pedagógicas com vistas a promover o desenvolvimento do processo ensino aprendizagem objetivando a complementação didático-pedagógica de disciplinas teóricas e práticas que concorrem para a formação profissional do estudante. Vale ressaltar que as visitas técnicas não são excursões de lazer.</p>
    <p>Para o bom andamento das mesmas, todo aluno participante concordará em cumprir todas as normas abaixo, assinando o presente termo:</p>
    <ol>
        <li>Portar documento de identificação;</li>
        <li>Ser pontual nos horários de saída e de retorno ao IFPE Campus Garanhuns;</li>
        <li>Levar roupa de cama e banho e colchonete, se for o caso;</li>
        <li>Usar calça comprida, fardamento e tênis durante todo o período da visita, inclusive no decorrer da viagem;</li>
        <li>Não fazer uso de quaisquer substâncias nocivas à saúde (bebida alcoólica, etc.) durante todo o período de realização da visita, inclusive nas viagens de ida e volta;</li>
        <li>Atender às solicitações e normas da empresa/local durante a visita;</li>
        <li>Não tocar em máquinas e equipamentos nas áreas da empresa;</li>
        <li>Respeitar os transeuntes durante todo o percurso da viagem;</li>
        <li>Zelar pela conservação e limpeza do ônibus;</li>
        <li>Contribuir para a tranquilidade do motorista na realização do trabalho;</li>
        <li>Zelar pela manutenção da boa imagem do IFPE Campus Garanhuns;</li>
        <li>Zelar pelo bom relacionamento entre o IFPE Campus Garanhuns e a empresa;</li>
        <li>Zelar pelo bom relacionamento entre os participantes da visita técnica;</li>
        <li>Não será tolerado nenhum tipo de indisciplina durante a atividade;</li>
        <li>O aluno não poderá separar-se do grupo durante a visita técnica para realizar atividades particulares;</li>
        <li>O aluno, ou seu responsável legal (no caso de estudantes menores de idade), será responsável pela reposição ou pagamento de qualquer objeto quebrado, danificado ou desaparecido do ônibus, alojamento, estabelecimento hoteleiro ou local visitado;</li>
        <li>O IFPE Campus Garanhuns não se responsabilizará por quaisquer objetos pessoais dos alunos (aparelhos celulares, máquinas fotográficas, etc);</li>
        <li>O aluno que não for à visita técnica se compromete a devolver ao IFPE Campus Garanhuns o auxílio financeiro que tenha recebido;</li>
        <li>Não será permitido participar da visita técnica pessoas estranhas ao grupo;</li>
        <li>A condução de quaisquer decisões que tenham que ser tomadas diante de algum transtorno, será feito pelo(s) responsável(is) da visita.</li>
        <li>O cumprimento das normas acima estipuladas será observado com rigor pelo(s) responsável(is) acompanhante(s) da visita. O aluno estará sujeito às sanções previstas na organização acadêmica institucional, no caso de desacato às normas.</li>
    </ol>
    
    
    <p style="text-align: center">Declaro ter ciência do deslocamento e das atividades a serem realizadas, horários de saída e retorno, bem como das demais normas acima elencadas.</p>

    <p style="text-align: center;">Garanhuns, _____ de ____________________ de 20____</p>
    <p style="text-align: center;">_________________________________________<br>
    Assinatura do estudante</p><br>   
    

    <h3 style="text-align: center;">QUANDO O ESTUDANTE FOR MENOR DE IDADE</h3>
    
    
    <div style="border: 1px solid black; padding: 15px; border-radius: 5px;">
        <h4 style="text-align: center">TERMO DE AUTORIZAÇÃO DE VISITA TÉCNICA</h4>

        @if($visitaTecnica->hospedagem)
        <p style="text-align: center">AUTORIZO o(a) menor __________________________________________________________________, nascido(a) em _____/_____/________, CPF: _____________________________________, a se deslocar e se hospedar na companhia do(a) Sr.(a) _____________________________________________________, CPF n° _______________________, durante o período de ____/_____/_____ à ____/_____/_____, consoante estabelece o Art. 82 da Lei Federal nº. 8.069/1990 (Estatuto da Criança e do Adolescente).</p>
        @else
        <p style="text-align: center">AUTORIZO o(a) menor __________________________________________________________________, nascido(a) em _____/_____/________, CPF: _____________________________________, a se deslocar na companhia do(a) Sr.(a) _____________________________________________________, CPF n° _______________________, durante o período de ____/_____/_____ à ____/_____/_____, consoante estabelece o Art. 82 da Lei Federal nº. 8.069/1990 (Estatuto da Criança e do Adolescente).</p>
        @endif

        <p style="text-align: center">OBS: O retorno está previsto para ____:____ horas.</p>
        <p style="text-align: center">Para tanto, é necessário que o responsável assinale abaixo o local pertinente para deixar o estudante:</p>
        <p><input type="checkbox"> IFPE<br>
        <input type="checkbox"> Parada no trajeto da visita com o responsável à espera - especificar local: __________________________</p>

        <p style="text-align: center">______________________________________________<br>
        Assinatura do responsável legal pelo estudante</p>
    </div>
</body>
</html>
