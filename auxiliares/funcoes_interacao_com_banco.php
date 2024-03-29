<?php
require_once(__DIR__ . "/./constantes.php");
require_once(__DIR__ . "/../Handlers/SQL_CRUD.php");

use Handlers\SQL_CRUD;

function retornaDBHandler(){
  $db_handler = new SQL_CRUD(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  return $db_handler;
}

function retornaAvaliacoesUltimosTrintaDias(){
  $db_handler = retornaDBHandler();

  $data_hoje = date("Y-m-d");
  $data_amanha = date("Y-m-d", strtotime("+1 day", strtotime($data_hoje)));
  $data_um_mes_atras = date("Y-m-d", strtotime("-30 days", strtotime($data_hoje)));

  $resultado = $db_handler->execSelect("relaciona_perguntas_a_tabela_de_respostas", "tabela", ["CAST(data_cadastro as date) BETWEEN '$data_um_mes_atras' AND '$data_amanha'"], 'tabela')['RESULT'];

  $resposta = array_map(function($v){
    return $v['tabela'];
  }, $resultado);

  if(!is_array($resposta)){
    $resposta = [];
  }
  
  return $resposta;
}

function retornaPerguntasAvaliacaoSemPontos($av){
  $db_handler = retornaDBHandler();

  $resultado = $db_handler->execSelect("relaciona_perguntas_a_tabela_de_respostas", null, [["tabela", $av], " pontuacao_pergunta IS NULL "])['RESULT'];
  
  return (bool)count($resultado);
}

function retornaPerguntasAvaliacao($av, $identificador_pergunta = null){
  $db_handler = retornaDBHandler();

  $condicoes = [["tabela", $av]];
  if(!empty($identificador_pergunta)){
    array_push($condicoes, ["identificador_pergunta", $identificador_pergunta]);
  }

  $resultado = $db_handler->execSelect("relaciona_perguntas_a_tabela_de_respostas", null, $condicoes)['RESULT'];
  
  return $resultado;
}

function retornaQuemRespondeu($av){
  $db_handler = retornaDBHandler();

  $resultado = $db_handler->execSelect($av, "quem_respondeu", null, "quem_respondeu")['RESULT'];
  
  return $resultado;
}

function retornaRespostasAvaliacao($av, $quem_respondeu){
  $db_handler = retornaDBHandler();

  if(!empty($quem_respondeu)){
    $condicoes = [["quem_respondeu", $quem_respondeu]];
  } else {
    $condicoes = null;
  }

  $resultado = $db_handler->execSelect($av, null, $condicoes)['RESULT'];
  
  return $resultado;
}

function retornaCorrecaoPergunta($av, $quem_respondeu, $identificador_pergunta){
  $db_handler = retornaDBHandler();

  $condicoes = [
    ['tabela', $av],
    ['email', $quem_respondeu],
    ['identificador_pergunta', $identificador_pergunta],
  ];

  $resultado = $db_handler->execSelect("pontuacao_respostas", null, $condicoes)['RESULT'];

  return $resultado;
}

function verificarSeTabelaExiste($nome_tabela){
  $db_handler = retornaDBHandler();

  $busca = $db_handler->executeSQL("SHOW TABLES LIKE ?", $nome_tabela);

  return (bool)$busca->rowCount();
}

function criarTabela($tabela, $colunas){
  $SQL = "CREATE TABLE `$tabela` (
    `id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `quem_respondeu` VARCHAR(200) NOT NULL
    );
  ";

  $db_handler = retornaDBHandler();

  $resultado = $db_handler->executeSQL($SQL);
  
  if($resultado){
    foreach($colunas as $coluna){
      $SQL = "ALTER TABLE `$tabela` ADD COLUMN `$coluna` TEXT NOT NULL;";
      $resultado = $db_handler->executeSQL($SQL);

      if($resultado == false){
        break;
        return false;
      }
    }
    return $resultado;
  } else {
    return "else";
  }
}

function cadastraRelacaoPerguntas($tabela, $av_perguntas){
  $db_handler = retornaDBHandler();

  $resultado_delete = $db_handler->execDelete("relaciona_perguntas_a_tabela_de_respostas", [["tabela", $tabela]]);

  if($resultado_delete){
    foreach($av_perguntas as $k => $v){
      $dados_insert = [
        "tabela" => $tabela,
        "identificador_pergunta" => $k,
        "texto_pergunta" => $v
      ];
  
    
      $resultado_insert = $db_handler->execInsert("relaciona_perguntas_a_tabela_de_respostas", $dados_insert);
  
      if($resultado_insert === false){
        break;
        return false;
      }
    }

    return $resultado_insert;
  } else {
    return false;
  }
}

function cadastrarRespostas($tabela, $av_respostas){
  $db_handler = retornaDBHandler();

  foreach($av_respostas as $k => $v){
    $dados_insert = [
      "quem_respondeu" => $k,
      ...$av_respostas[$k]
    ];

    $resultado_insert = $db_handler->execInsert($tabela, $dados_insert);

    if($resultado_insert === false){
      break;
      return false;
    }
  }

  return $resultado_insert;
}