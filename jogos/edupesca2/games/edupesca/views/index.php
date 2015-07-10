<div id="game">
	<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="modalMessage">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center" id="modalMessageTitle"></h4>
				</div>
				
				<div class="modal-body" id="modalMessageBody">
					
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="modalMessageAction" data-dismiss="modal"></button>
				</div>
			</div>
		</div>
	</div>
	
	<button type="button" class="btn btn-lg btn-success" id="ActionBtn" data-dismiss="modal"></button>
	
	<div id="successForm">
		<div>
			<h5>Você terminou em <span id="timeLabel"></span>!</h5>
			<p>Preencha o formulário abaixo para enviar sua pontuação!</p>
		</div>
		
		<form class="form-horizontal">
			<input type="hidden" id="timeInput"  name="time" value="0"/>
			
			<div class="form-group">
				<label for="name" class="col-sm-2 control-label">Nome</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="name" placeholder="Nome">
				</div>
			</div>
			
			<div class="form-group">
				<label for="email" class="col-sm-2 control-label">Email</label>
				<div class="col-sm-10">
					<input type="email" class="form-control" id="email" placeholder="Email">
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-default">Enviar!</button>
				</div>
			</div>
		</form>
	</div>

</div>
<span id="textMeasureAux" class="Peixe"></span>
<?php 
$gameData = $game->getViewData($level);
$this->registerJs('
	gameData = ' . json_encode($gameData) . ';
	gameData.gameContainer = "#game";
	gameData.gameWidth = $("#game").width();
	gameData.gameHeight = 600; 
	gameData.playerSpeed = 100;
	gameData.playerForce = 35;
	gameData.playerRolo = 15;
	gameData.fishWspan = 25;
	gameData.fishHspan = 18;
	
	gameData.user_id = ' . json_encode($user->id) . ';
	gameData.game_id = ' . json_encode($game->id) . ';
	gameData.level_id = ' . json_encode($level->id) . ';
	
	game = new EduPesca(gameData);', 
	
	\yii\web\View::POS_LOAD, 
	'gameCall'
);
?>
</script>