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

function converteEmArrayAssociativo($array){
  $new_array = [];

  foreach ($array as $key => $value) {
    $numero_pergunta = $key+1;

    if($numero_pergunta < 10){
      $numero_pergunta = "0" . $numero_pergunta;
    }

    $new_array["Pergunta $numero_pergunta"] = $value;
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