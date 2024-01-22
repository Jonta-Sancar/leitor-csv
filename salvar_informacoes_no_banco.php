<?php
require_once(__DIR__ . "/./auxiliares/funcoes.php");

$diretorio_arquivos = __DIR__ . DIRECTORY_SEPARATOR . "." . DIRECTORY_SEPARATOR . "avaliacoes" . DIRECTORY_SEPARATOR;
$arquivos = glob($diretorio_arquivos . "*.csv");

$arquivos_lidos = [];
$tudo_certo = true;
$messages = [];

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
        }
      }
    }

    if($tabela_existe && $csv_em_array === false && !$resultado_criacao_tabela && $resultado_cadastro_relacoes === false){
      $tudo_certo = false;
    }
    
    $arquivos_lidos[] = $nome_arquivo;
  } else {
    $tudo_certo = false;
    $messages[] = "Não foi possível ler o arquivo $nome_arquivo.csv, pois já existe uma tabela com o nome `$nome_arquivo`";
    break;
  }
}

foreach ($arquivos_lidos as $nome_arquivo) {
  $caminho_arquivo = $diretorio_arquivos . $nome_arquivo . ".csv";

  if(file_exists($caminho_arquivo)) {
    if(!unlink($caminho_arquivo)){
      $messages[] = "não foi possível excluir o arquivo $caminho_arquivo";
      break;
    }
  }
}

$todas_av = array_merge(retornaAvaliacoesUltimosTrintaDias(), $arquivos_lidos);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Salvamento de Respostas no Banco de Dados</title>
</head>
<body>
  <h3>Arquivos Lidos Nos últimos 30 dias</h3>
  <ul>
    <?php
      foreach($todas_av as $nome_arquivo){
        ?>
          <li><?= $nome_arquivo ?></li>
        <?php
      }
    ?>
  </ul>

  <?php
    if(!$tudo_certo){
      ?>
        <div><small><strong>Erros:</strong></small></div>
      <?php

      foreach($messages as $message){
        ?>
          <li><?= $message ?></li>
        <?php
      }
    }
  ?>
</body>
</html>