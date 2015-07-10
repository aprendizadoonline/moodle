<!DOCTYPE html>
<html>
	<head>
		<title>EduPesca | Painel de Controle</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-tokenfield.min.css">
		<script src="js/jquery-2.1.4.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-tokenfield.min.js"></script>
		<style>
			p { font-size: 95%; }
		</style>
		<script>
			$(function() {
				$("#palpesca").tokenfield({createTokensOnBlur: true});
			});
		</script>
	</head>

	<body>
<?php
	if(!@mysql_connect("localhost","jogos","jogos@aprendizado")) {
		echo "<h2>Error</h2>";
		die();
	}
	mysql_select_db("jogo_edupesca");
	
	$flashMessages = [];
	
	if (isset($_REQUEST['action'])) {
		switch ($_REQUEST['action']) {
		// ----------------------------------------
		// Adiciona um novo nível
		// ----------------------------------------
		case 'newLevel':
			// Mensagem de novo nível
			$message = new stdClass;
			
			// Faz a verificação do arquivo enviado
			
			// Nome do arquivo de origem
			$originFilename = $_FILES["fileToUpload"]["tmp_name"];
			
			// Se não enviou arquivo
			if (empty($originFilename)) {
				$uploadOk = true;
				$targetFilename = '';
				
			// Se enviou arquivo
			} else {
				// Verifica se é de fato uma imagem
				$uploadOk = @getimagesize($originFilename);
				
				// Se for uma imagem
				if ($uploadOk) {
					// Gera um hash a partir do arquivo para que seja seu nome de arquivo
					$targetFilename = hash_file('sha512', $originFilename);
					// Caminho de destinho
					$targetPath = "upload/" . $targetFilename;
					
					// Se o arquivo não existir, move o recebido
					if (!file_exists($targetPath)) {
						$uploadOk = move_uploaded_file($originFilename, $targetFilename);
					}
				}
			}
			
			// Se o upload ocorreu com sucesso
			if ($uploadOk) {
				// Seleciona o maior precessor
				$res = mysql_query("SELECT ordem FROM gamepesca ORDER BY ordem DESC LIMIT 1 ");
				if ($res === FALSE) die(mysql_error());
				$row = mysql_fetch_array($res);
				
				$tmpPalpesca = implode(',', explode(', ', $_REQUEST['palpesca']));
				
				$mensagem = mysql_real_escape_string($_REQUEST['mensagem']);
				$palavras = mysql_real_escape_string($_REQUEST['palavras']);
				$palpesca = mysql_real_escape_string($tmpPalpesca);
				$tempo    = intval($_REQUEST['tempo']);
				$imagem   = mysql_real_escape_string($targetFilename);
				$ordem    = $row['ordem'] + 1;
			
				mysql_query("INSERT INTO gamepesca (mensagem,palavras,palpesca,tempo, imagem, ordem) VALUES('$mensagem','$palavras','$palpesca',$tempo,'$imagem',$ordem);");
			
				if (mysql_affected_rows()) {
					$message->message = "Nível adicionado com sucesso.";
					$message->type = 'success';
				} else {
					$message->message = "Ocorreu um erro e o nível não pode ser adicionado.";
					$message->type = 'danger';
				}
			
			// Se o upload falhou
			} else {
				$message->message = "A imagem é inválida ou não pode ser enviada.";
				$message->type = 'danger';
			}
			
			// Insere a mensagem nos flashs
			$flashMessages[] = $message;
			
			// Para o switch
			break;
			
		// ----------------------------------------
		// Move uma instância para cima
		// ----------------------------------------
		case 'moveup':
			// Mensagem
			$message = new stdClass;
			
			// Encontra a instância que será movida para cima
			$result1 = mysql_query("SELECT id, ordem FROM gamepesca WHERE id=".round($_REQUEST['id']));
			if ($result1 === FALSE) {
				// Registra mensagem flash
				$message->message = "Ocorreu um erro e o nível não pode ser movido para cima.";
				$message->type = 'danger';
				$flashMessages[] = $message;
				break;
			}
			$row1 = mysql_fetch_array($result1);
			
			// Se a tentativa de mover para cima for no primeiro, não move
			if ($row1['ordem'] == 0) break;
			
			// Move o precessor para baixo
			mysql_query("UPDATE gamepesca SET ordem=ordem+1 WHERE ordem = ". ($row1['ordem'] - 1));
			// Move a instância para cima
			mysql_query("UPDATE gamepesca SET ordem=ordem-1 WHERE id = " . $row1['id']);
			
			// Insere a mensagem de sucesso nos flashs
			$message->message = "Nível movido com sucesso.";
			$message->type = 'success';
			$flashMessages[] = $message;
			
			// Para o switch
			break;
			
		// ----------------------------------------
		// Move uma instância para baixo
		// ----------------------------------------
		case 'movedown':
			// Mensagem
			$message = new stdClass;
			
			// Encontra a instância que será movida para cima
			$result1 = mysql_query("SELECT id, ordem FROM gamepesca WHERE id=".round($_REQUEST['id']));
			if ($result1 === FALSE) {
				// Registra mensagem flash
				$message->message = "Ocorreu um erro e o nível não pode ser movido para baixo.";
				$message->type = 'danger';
				$flashMessages[] = $message;
				break;
			}
			$row1 = mysql_fetch_array($result1);
			
			// Move o sucessor para cima
			mysql_query("UPDATE gamepesca SET ordem=ordem-1 WHERE ordem = ". ($row1['ordem'] + 1));
			// Se houveram linhas afetadas
			if (mysql_affected_rows() != 0) {
				// Move a instância para baixo
				mysql_query("UPDATE gamepesca SET ordem=ordem+1 WHERE id = " . $row1['id']);
			}
			// Insere a mensagem de sucesso nos flashs
			$message->message = "Nível movido com sucesso.";
			$message->type = 'success';
			$flashMessages[] = $message;
			
			// Para o switch
			break;
			
		// ----------------------------------------
		// Deleta uma instância
		// ----------------------------------------
		case 'del':
			// Encontra a instância que será deletada
			$res = mysql_query("SELECT id, ordem FROM gamepesca WHERE id=".round($_REQUEST['id']));
			if ($res === FALSE) {
				// Registra mensagem flash
				$message->message = "Ocorreu um erro e o nível não pode ser deletado.";
				$message->type = 'danger';
				$flashMessages[] = $message;
				break;
			}
			$row = mysql_fetch_array($res);
			
			// Deleta a instância
			mysql_query("DELETE FROM gamepesca WHERE id=" . $row['id']);
			
			// Atualiza a ordem das instâncias abaixo
			mysql_query("UPDATE gamepesca SET ordem=ordem-1 WHERE ordem > ". $row['ordem']);
			
			// Insere a mensagem de sucesso nos flashs
			$message->message = "Nível removido com sucesso.";
			$message->type = 'success';
			$flashMessages[] = $message;
			
			// Para o switch
			break;
		}								
	}
?>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1 class="text-center">Painel de Controle</h1>
				</div>
				
				<div class="col-xs-12">
					<?php foreach ($flashMessages as $flash): ?>
						<?php 
							switch ($flash->type) {
								case 'success': 
								case 'warning':
								case 'danger':
								case 'info':
									$alertClass = $flash->type;
									break;
								default:
									$alertClass= 'info';
							}
						?>
						
						<div class="alert alert-dismissible alert-<?= $alertClass ?>" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
							<?= $flash->message ?>
						</div>
					<?php endforeach; ?>
				</div>
				
				<div class="col-md-9">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Palavras Cadastradas</h2>
						</div>
						<div class="panel-body">	
							<div class="table-responsive">
								<table class="table table-condensed table-bordered">
									<thead>
										<tr class="active">
											<td><strong>#</strong></td>
											<td><strong>Mensagem</strong></td>
											<td><strong>Palavras</strong></td>
											<td><strong>Letras para Pescar</strong></td>
											<td><strong>Tempo</strong></td>
											<td><strong>Imagem</strong></td>
											<td></td>
										</tr>
									</thead>
									
									<tbody>
										<?php
											// Seleciona os níveis cadastrados atualmente
											$result = mysql_query("SELECT id, mensagem, palavras, palpesca, tempo, imagem FROM gamepesca ORDER BY ordem ASC;");
											if ($result === FALSE) 
												die(mysql_error());
											$i = 0;
										?>
										<?php while ($row = mysql_fetch_array($result)): ?>
											<tr>
												<td><?= ++$i ?></td>
												<td><?= $row['mensagem'] ?></td>
												<td><?= $row['palavras'] ?></td>
												<td><?= $row['palpesca'] ?></td>
												<td class='text-center'><?= $row['tempo'] ?></td>
												<td class='text-center'>
													<?php if (empty($row['imagem'])): ?>
														-
													<?php else: ?>
														<a href="<?= $row['imagem'] ?>" target="_blank">Ver</a>
													<?php endif; ?>
												</td>
												<td>
													<a href="painel.php?action=moveup&id=<?= $row['id'] ?>">
														<span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span>
													</a>
													<a href="painel.php?action=movedown&id=<?= $row['id'] ?>">
														<span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></span>
													</a>
													<a onclick="return confirm('Deseja apagar esta palavra?');" href="painel.php?action=del&id=<?= $row['id'] ?>">
														<span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
													</a>
												</td>
											</tr>
										<?php endwhile; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Ações</h2>
						</div>
						<div class="panel-body">		
							<a href="user.php?action=reset" class="btn btn-primary">Reiniciar nível do jogador</a>
							<a href="user.php?action=deleteAll" class="btn btn-danger" onclick="confirm('Tem certeza?')">Deletar todos os níveis</a>
						</div>
					</div>
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Inserir Palavras</h2>
						</div>
						<div class="panel-body">				
							<form action="painel.php" method="post" enctype="multipart/form-data"  class="form-horizontal">
								<input type="hidden" name="action" value="newLevel">
								
								<div class="form-group">
									<label for="mensagem" class="col-sm-3 control-label">Mensagem</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="mensagem" name="mensagem" placeholder="Mensagem">
									</div>
									<p class="col-sm-offset-3 col-sm-9">Instrução para o jogador</p>
								</div>
								
								<div class="form-group">
									<label for="palavras" class="col-sm-3 control-label">Letra, Palavra ou Frase</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="palavras" name="palavras" placeholder="Palavras">
									</div>
									<p class="col-sm-offset-3 col-sm-9">Letra, palavra ou frase que deve ser pescada</p>
								</div>
								
								<div class="form-group">
									<label for="palpesca" class="col-sm-3 control-label">Elementos para Pescar</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="palpesca" name="palpesca" placeholder="Palavras para Pescar">
									</div>
									<p class="col-sm-offset-3 col-sm-9">Digite, separados por vírgulas, quais os elementos aparecerão na água para pescar</p>
								</div>
								
								<div class="form-group">
									<label for="tempo" class="col-sm-3 control-label">Tempo</label>
									<div class="col-sm-9">
										<input type="number" min="0" step="10" value="0" class="form-control" id="tempo" name="tempo" placeholder="Tempo">
									</div>
									<p class="col-sm-offset-3 col-sm-9">Tempo máximo para pescar em segundos</p>
								</div>
								
								<div class="form-group">
									<label for="fileToUpload" class="col-sm-3 control-label">Imagem</label>
									<div class="col-sm-9">
										<input type="file" class="form-control" id="fileToUpload" name="fileToUpload" placeholder="Imagem">
									</div>
									<p class="col-sm-offset-3 col-sm-9">(Opcional) Imagem que aparecerá junto ao texto</p>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-9">
										<input type="submit" name="submit" class="btn btn-default" value="Enviar"/>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Alunos</h2>
						</div>
						<div class="panel-body">
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
