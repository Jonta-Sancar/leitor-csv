<?php
require_once(__DIR__ . "/./auxiliares/funcoes.php");
require_once(__DIR__ . "/./Handlers/SQL_CRUD.php");

use Handlers\SQL_CRUD;

$arquivos = glob(__DIR__ . DIRECTORY_SEPARATOR . "." . DIRECTORY_SEPARATOR . "avaliacoes" . DIRECTORY_SEPARATOR . "*.csv");

$csvs = [];
foreach ($arquivos as $caminho_arquivo) {

  echo $nome_arquivo;

  $arquivo_aberto  = fopen($caminho_arquivo, "r");
  
  $csv_em_array = CDVParaArray($arquivo_aberto);

  $csvs[] = $csv_em_array;
  
  if($csv_em_array){
    $av_perguntas = $csv_em_array["perguntas"];
    $av_respostas = $csv_em_array["respostas"];
  }
}