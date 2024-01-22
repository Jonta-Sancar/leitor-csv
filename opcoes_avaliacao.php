<?php
  require_once(__DIR__ . "/./auxiliares/funcoes_gerais.php");
  require_once(__DIR__ . "/./auxiliares/funcoes_interacao_com_banco.php");
  $av = filter_input(INPUT_GET, "av");
  $editar_pontos = filter_input(INPUT_GET, "editar_pontos");
  $corrigir_respostas = filter_input(INPUT_GET, "corrigir_respostas");

  $perguntas_av_sem_pontos = retornaPerguntasAvaliacaoSemPontos($av);
  $perguntas_av = retornaPerguntasAvaliacao($av);
  $quem_respondeu    = retornaQuemRespondeu($av);

  $arquivos_lidos = retornaAvaliacoesUltimosTrintaDias();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <?php include_once(__DIR__ . "/./modulos/head.php") ?>
  <title>Opções da Avaliação</title>
</head>
<body>
  <?php
    if (!empty($_GET['msg'])) {
      if($_GET['msg'] == "success"){
        ?>
          <div><strong>Tudo certo!</strong></div>
          <?php
      } else {
        ?>
        <div><strong>Algo deu errado.</strong></div>
        <?php
      }
    }
  ?>
  <div class="container">
    <h3>Arquivos Lidos Nos últimos 30 dias</h3>
    <ul>
      <?php
        foreach($arquivos_lidos as $nome_arquivo){
          $complemento_url = '';
          if(isset($_GET['editar_pontos']) && $_GET['editar_pontos'] == 'true'){
            $complemento_url = '&editar_pontos=true';
          }
          ?>
            <li><a href="opcoes_avaliacao.php?av=<?= $nome_arquivo . $complemento_url ?>"><?= $nome_arquivo ?></a></li>
          <?php
        }
      ?>
    </ul>
    <?php
      if(!empty($_GET['av'])){
        ?>
          <div>
            <a href="?av=<?= $av ?>&editar_pontos=true" class="btn btn-light">Editar Pontos da Avaliação</a>
            <a href="?av=<?= $av ?>&corrigir_respostas=true" class="btn btn-light">Corrigir Respostas</a>
          </div>
          <br>
          <br>
        <?php
      }
    ?>

    <?php
      if ($perguntas_av_sem_pontos || $editar_pontos == 'true'){
        ?>
          <div class="container-sm" style="max-width: 600px;">
            <form action="post/aplicar_pontos_as_questoes.php" method="post">
              <h3>Aplicar Pontuação da Avaliação</h3>
              <input class="form-control" type="hidden" name="tabela" value="<?= $av ?>">

              <?php
                foreach($perguntas_av as $pergunta_info){
                  ?>
                    <div class="form-group mb-4">
                      <label for="<?= $pergunta_info['identificador_pergunta'] ?>"><input class="form-control" type="number" name="<?= $pergunta_info['id'] ?>" id="<?= $pergunta_info['identificador_pergunta'] ?>" value="<?= $pergunta_info['pontuacao_pergunta'] ?>" style="width:100px;display:inline-block;">
                      <?= $pergunta_info['texto_pergunta'] ?></label>
                    </div>
                  <?php
                }
              ?>

              <div class="form-group mb-4">
                <input class="form-control" type="submit" value="Salvar">
              </div>
            </form>
          </div>
        <?php
      } else {
        if($corrigir_respostas == 'true'){
          ?>
            <div class="container-sm" style="max-width: 600px;">
              <form method="get" class="mb-4">
                <input class="form-control" type="hidden" name="av" value="<?= $av ?>">
                <div class="form-group">
                  <label for="corrigir_respostas">Corrigir Prova de:</label>
                  <select class="form-control" name="corrigir_respostas" id="corrigir_respostas">
                    <option selected disabled>Selecione</option>
                    <?php
                      foreach($quem_respondeu as $quem_respondeu_info){
                        ?>
                          <option value="<?= $quem_respondeu_info['quem_respondeu'] ?>"><?= $quem_respondeu_info['quem_respondeu'] ?></option>
                        <?php
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <input class="form-control" type="submit" value="Escolher">
                </div>
              </form>
            </div>
          <?php
        } else if (!empty($corrigir_respostas) && $corrigir_respostas != 'true'){
          $respostas = retornaRespostasAvaliacao($av, $corrigir_respostas);
  
          ?>
            <div class="container-sm" style="max-width: 600px;">
              <form action="post/aplicar_correcao.php" method="post">
                <h3>Aplicar Correção de Respostas</h3>
                <input class="form-control" type="hidden" name="tabela" value="<?= $av ?>">
                <input class="form-control" type="hidden" name="quem_respondeu" value="<?= $corrigir_respostas ?>">
  
                <?php
                  foreach($respostas as $resposta_info){
                    foreach($resposta_info as $identificador_pergunta => $resposta){
                      if($identificador_pergunta != 'id' && $identificador_pergunta != 'quem_respondeu'){
                        $pergunta = retornaPerguntasAvaliacao($av, $identificador_pergunta)[0];
                        $correcao_busca = retornaCorrecaoPergunta($av, $corrigir_respostas, $identificador_pergunta);
                        $correcao = $correcao_busca ? $correcao_busca[0] : false;
                        ?>
                          <div class="form-group mb-4 border-bottom">
                            <label for="<?= $identificador_pergunta ?>">
                              <select class="form-control d-inline-block" name="<?= $identificador_pergunta ?>" id="<?= $identificador_pergunta ?>" required style="width: 120px;">
                                <option selected disabled>Selecione</option>
                                <option <?php if(@$correcao['pontuacao_pergunta'].'' === '0'){echo "selected";} ?> value="zero">Errado</option>
                                <option <?php if(@$correcao['pontuacao_pergunta'].'' === '0.5'){echo "selected";} ?> value="meio">Meio Certo</option>
                                <option <?php if(@$correcao['pontuacao_pergunta'].'' === '1'){echo "selected";} ?> value="um">Certo</option>
                              </select>
                              <?= $pergunta['texto_pergunta'] ?>
                            </label>
                            <p><small>resposta:<br></small><code><?= $resposta ?></code></p>
                          </div>
                        <?php
                      }
                    }
                  }
                ?>
  
                <div class="form-group mb-4">
                  <input class="form-control" type="submit" value="Salvar">
                </div>
              </form>
            </div>
          <?php
        }
      }
    ?>
  </div>
  <?php include_once(__DIR__ . "/./modulos/footer.php") ?>
</body>
</html>