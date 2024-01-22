<?php
  require_once(__DIR__ . "/./auxiliares/funcoes_gerais.php");
  require_once(__DIR__ . "/./auxiliares/funcoes_interacao_com_banco.php");
  $av = filter_input(INPUT_GET, "av");
  $editar_pontos = filter_input(INPUT_GET, "editar_pontos");
  $corrigir_respostas = filter_input(INPUT_GET, "corrigir_respostas");

  $perguntas_av_sem_pontos = retornaPerguntasAvaliacaoSemPontos($av);
  $perguntas_av = retornaPerguntasAvaliacao($av);
  $quem_respondeu    = retornaQuemRespondeu($av);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

  <div>
    <a href="?av=<?= $av ?>&editar_pontos=true">Editar Pontos da Avaliação</a>
    <span> || </span>
    <a href="?av=<?= $av ?>&corrigir_respostas=true">Corrigir Respostas</a>
  </div>
  <br>
  <br>

  <?php
    if($corrigir_respostas == 'true'){
      ?>
        <form method="get">
          <input type="hidden" name="av" value="<?= $av ?>">
          <div class="form-control">
            <label for="corrigir_respostas">Corrigir Prova de:</label>
            <select name="corrigir_respostas" id="corrigir_respostas">
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
          <div class="form-control">
            <input type="submit" value="Escolher">
          </div>
        </form>
      <?php
    }

    if ($perguntas_av_sem_pontos || $editar_pontos == 'true'){
      ?>
        <form action="post/aplicar_pontos_as_questoes.php" method="post">
          <input type="hidden" name="tabela" value="<?= $av ?>">

          <?php
            foreach($perguntas_av as $pergunta_info){
              ?>
                <div class="form-group">
                  <input type="number" name="<?= $pergunta_info['id'] ?>" id="<?= $pergunta_info['identificador_pergunta'] ?>" style="width:50px;">
                  <label for="<?= $pergunta_info['identificador_pergunta'] ?>"><?= $pergunta_info['texto_pergunta'] ?></label>
                </div>
              <?php
            }
          ?>

          <div class="form-group">
            <input type="submit" value="Salvar">
          </div>
        </form>
      <?php
    }

    if (!empty($corrigir_respostas) && $corrigir_respostas != 'true'){
      $respostas = retornaRespostasAvaliacao($av, $corrigir_respostas);

      ?>
        <form action="post/aplicar_correcao.php" method="post">
          <input type="hidden" name="tabela" value="<?= $av ?>">
          <input type="hidden" name="quem_respondeu" value="<?= $corrigir_respostas ?>">

          <?php
            foreach($respostas as $resposta_info){
              foreach($resposta_info as $identificador_pergunta => $resposta){
                if($identificador_pergunta != 'id' && $identificador_pergunta != 'quem_respondeu'){
                  $pergunta = retornaPerguntasAvaliacao($av, $identificador_pergunta)[0];
                  $correcao_busca = retornaCorrecaoPergunta($av, $corrigir_respostas, $identificador_pergunta);
                  $correcao = $correcao_busca ? $correcao_busca[0] : false;
                  ?>
                    <div class="form-group">
                      <select name="<?= $identificador_pergunta ?>" id="<?= $identificador_pergunta ?>" required>
                        <option selected disabled>Selecione</option>
                        <option <?php if(@$correcao['pontuacao_pergunta'].'' === '0'){echo "selected";} ?> value="zero">Errado</option>
                        <option <?php if(@$correcao['pontuacao_pergunta'].'' === '0.5'){echo "selected";} ?> value="meio">Meio Certo</option>
                        <option <?php if(@$correcao['pontuacao_pergunta'].'' === '1'){echo "selected";} ?> value="um">Certo</option>
                      </select>
                      <label for="<?= $identificador_pergunta ?>"><?= $pergunta['texto_pergunta'] ?></label>
                    </div>
                    <p><small>resposta:<br></small><code><?= $resposta ?></code></p>
                  <?php
                }
              }
            }
          ?>

          <div class="form-group">
            <input type="submit" value="Salvar">
          </div>
        </form>
      <?php
    }
  ?>
</body>
</html>