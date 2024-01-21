<?php
require_once(__DIR__ . "/./auxiliares/funcoes.php");
require_once(__DIR__ . "/./Handlers/SQL_CRUD.php");

$arquivos_lidos = [];
foreach ($arquivos as $caminho_arquivo) {
  $nome_arquivo = retornaNomeDoArquivo($caminho_arquivo);

  $tabela_existe = verificarSeTabelaExiste($nome_arquivo);

  if(!$tabela_existe){
    $arquivo_aberto  = fopen($caminho_arquivo, "r");
    
    $csv_em_array = CDVParaArray($arquivo_aberto);
    
    if($csv_em_array){
      $av_perguntas = $csv_em_array["perguntas"];
      $av_respostas = $csv_em_array["respostas"];
    }
    
    $arquivos_lidos[] = $nome_arquivo;
  } else {
    echo "<br><br>";
    echo "Não foi possível ler o arquivo $nome_arquivo.csv, pois já existe uma tabela com o nome `$nome_arquivo`";
    echo "<br><br>";
    break;
  }
}