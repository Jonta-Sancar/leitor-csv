<?php
require_once(__DIR__ . "/./constantes.php");
require_once(__DIR__ . "/../Handlers/SQL_CRUD.php");

use Handlers\SQL_CRUD;

function retornaDBHandler(){
  $db_handler = new SQL_CRUD(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  return $db_handler;
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
      $SQL = "ALTER TABLE `$tabela` ADD COLUMN `$coluna` VARCHAR(500) NOT NULL;";

      $resultado = $db_handler->executeSQL($SQL);

      if($resultado === false){
        return false;
        break;
      }
    }
    return $resultado;
  } else {
    return false;
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
  } else {
    return false;
  }
}

function converteEmArrayAssociativo($array){
  $new_array = [];

  foreach ($array as $key => $value) {
    $numero_pergunta = $key+1;

    if($numero_pergunta < 10){
      $numero_pergunta = "0" . $numero_pergunta;
    }

    $new_array["pergunta_$numero_pergunta"] = $value;
  }

  return $new_array;
}

function retornaNomeDoArquivo($caminho_arquivo){
  $caminho_arquivo_em_array = explode(DIRECTORY_SEPARATOR, $caminho_arquivo);
  $nome_arquivo_com_extensao = $caminho_arquivo_em_array[count($caminho_arquivo_em_array)-1];
  $nome_arquivo = explode(".", $nome_arquivo_com_extensao)[0];
  
  return $nome_arquivo;
}

function CDVParaArray($arquivo_csv_aberto){
  if ($arquivo_csv_aberto !== FALSE) {
    $row = 1;
  
    $array_perguntas = [];
    $csv_em_array = [];
    while (($data = fgetcsv($arquivo_csv_aberto, null, ",")) !== FALSE) {
      $dados_removidos = array_splice($data, 0, 2);
      $new_data = converteEmArrayAssociativo($data);
  
      $email = $dados_removidos[1];
      
      if($row == 1){
        $array_perguntas = $new_data;
      } else {
        $csv_em_array[$email] = $new_data;
      }
      $row++;
    }
  
    fclose($arquivo_csv_aberto);

    return [
      "perguntas" => $array_perguntas,
      "respostas" => $csv_em_array
    ];
  } else {
    return FALSE;
  }
}