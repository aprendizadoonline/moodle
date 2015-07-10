Score = {
	/**
	 * Inicialize os dados de pontuação
	 * @param string url URL do controlador responsável pela pontuação
	 */
	initialize: function(url) {
		this.url = url;
	},
	
	/**
	 * Registra o início de um novo jogo
	 * @param int userid Identificador de usuário
	 * @param int gameid Identificador de jogo
	 * @param int levelid Identificador de nível
	 * @return string Identificador da sessão de jogo
	 */
	registerGameStart: function(userid, gameid, levelid, callback) {
		console.log(userid, gameid, levelid);
		$.ajax({
			url: (this.url + '/ajax-register-game-start'),
			data: {user_id: userid, game_id: gameid, level_id: levelid},
			dataType: 'json'
		})
		.done(function(data) {
			if (data.error) {
				callback(false, data.error);
			} else {
				callback(true, data.session);
			}
		});
	},
	
	/**
	 * Envia um passo de progresso para o servidor
	 * @param string session Identificador da sessão de jogo
	 * @param string step Passo de progresso do jogo
	 * @param array data Informações necessárias
	 */
	sendProgress: function(session, step, data, callback) {
		if (data == undefined)
			data = [];
		
		$.ajax({
			url: (this.url + '/ajax-send-progress'),
			data: {session_id: session, step: step, data: data},
			dataType: 'json'
		})
		.done(function(data) {
			if (callback) {
				if (data.error) {
					callback(false, data.error);
				} else {
					callback(true);
				}
			}
		});
	},
	
	
	/**
	 * Registra o final de uma sessão de jogo
	 * @param string session Identificador da sessão de jogo
	 */
	registerGameEnd: function(session, callback) {
		$.ajax({
			url: (this.url + '/ajax-register-game-end'),
			data: {session_id: session}, 
			dataType: 'json'
		})
		.done(function(data) {
			if (data.error) {
				callback(false, data.error);
			} else {
				callback(true);
			}
		});
	}
}