<?php
require_once(__DIR__ . "/./auxiliares/funcoes.php");
require_once(__DIR__ . "/./Handlers/SQL_CRUD.php");

$arquivos = glob(__DIR__ . DIRECTORY_SEPARATOR . "." . DIRECTORY_SEPARATOR . "avaliacoes" . DIRECTORY_SEPARATOR . "*.csv");

$arquivos_lidos = [];
foreach ($arquivos as $caminho_arquivo) {
  $nome_arquivo = retornaNomeDoArquivo($caminho_arquivo);

  $tabela_existe = verificarSeTabelaExiste($nome_arquivo);

  if(!$tabela_existe){
    $arquivo_aberto  = fopen($caminho_arquivo, "r");
    
    $csv_em_array = CDVParaArray($arquivo_aberto);
    
    if($csv_em_array !== false){
      $av_perguntas = $csv_em_array["perguntas"];
      $av_respostas = $csv_em_array["respostas"];

      $resultado_criacao_tabela = criarTabela($nome_arquivo, array_keys($av_perguntas));

      if($resultado_criacao_tabela){
        $resultado_cadastro_relacoes = cadastraRelacaoPerguntas($nome_arquivo, $av_perguntas);
        
        if($resultado_cadastro_relacoes !== false){
          $resultado_cadastro_respostas = cadastrarRespostas($nome_arquivo, $av_respostas);
          echo $nome_arquivo . "<br>";
          var_dump($resultado_cadastro_respostas);
        }
      }
    }
    
    $arquivos_lidos[] = $nome_arquivo;
  } else {
    echo "<br><br>";
    echo "Não foi possível ler o arquivo $nome_arquivo.csv, pois já existe uma tabela com o nome `$nome_arquivo`";
    echo "<br><br>";
    break;
  }
}