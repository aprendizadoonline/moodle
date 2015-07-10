//====================================================================================
// Classe: JogoDaMemória
// Um dos elementos do tabuleiro
//====================================================================================
function JogoDaMemoria_Elemento(text, template, callbackHandler, elementId, relationId) {
	//--------------------------------------------------------------------------------
	// INICIALIZAÇÃO
	//--------------------------------------------------------------------------------
	
	// Define funções
	this.toHtml = toHtml.bind(this);
	this.setState = setState.bind(this);
	
	// Define atributos
	this.text = text;
	this.template = template;
	this.state = 0; // 0 = Para baixo, 1 = Para cima
	this._callbackHandler = callbackHandler;
	this.elementId = elementId;
	this.relationId = relationId;
	
	// Cria o elemento virado para cima
	this._elem = $(this.template);
	this._elem.append(this.text);
	
	// Funções de clique
	this._elem.click({_this: this}, function(event){
		if (event.data._this.state == 0) {
			if (event.data._this._callbackHandler(event.data._this.elementId, event.data._this.relationId))
				event.data._this.setState(1);
		}
	});
	
	//--------------------------------------------------------------------------------
	// FUNÇÕES
	//--------------------------------------------------------------------------------
	
	// Retorna o código html deste elemento
	function toHtml() {
		return this._elem;
	}
	
	// Vira a carta
	// state = 0 => Para baixo
	// state = 1 => Para cima
	function setState(state) {
		if (state == 0) {
			this.state = 0;
			this._elem.addClass('down');
		} else if (state == 1) {
			this.state = 1;
			this._elem.removeClass('down');
		}
	}
}

//====================================================================================
// Classe: JogoDaMemoria
// Contraladora principal de um jogo da memória
//====================================================================================
function JogoDaMemoria(config) {
	//--------------------------------------------------------------------------------
	// INICIALIZAÇÃO
	//--------------------------------------------------------------------------------
	// Define as configurações iniciais
	defaultSettings = {
		container   : $('body'),			// Container do jogo
		tableWidth  : 4, 					// Largura do tabuleiro
		tableHeight : 4,					// Altura do tabuleiro
		cardData	: [],					// Cartas
		templates   : {						// Templates
			row: '<div class="row">',		// - Linha
			element: '<span class="card">'	// - Carta
		},
		settings	: {						// Configurações do jogo
			initialWaitTime : 5000,			// - Tempo de espera inicial em milissegundos
			errorWaitTime	: 2500,			// - Tempo de espera para visualizar as cartas após um erro em milissegundos
		}
	};
	this.config = $.extend({}, defaultSettings, config);
		
	// Define funções
	this.run = run.bind(this);
	this._message = _message.bind(this);
	this._elementsCallback = _elementsCallback.bind(this);
	this.updateCounters = updateCounters.bind(this);
	this.updateTimer = updateTimer.bind(this);
	this.restart = restart.bind(this);
	this._processEndGame = _processEndGame.bind(this);
	this.endGame = endGame.bind(this);
		
	// Tabuleiro de jogo
	this.table = {
		width: this.config.tableWidth, 
		height: this.config.tableHeight, 
		size: (this.config.tableWidth * this.config.tableHeight), 
		halfsize: (this.config.tableWidth * this.config.tableHeight / 2)
	};
	
	// Caso o tabuleiro contenha um número ímpar de elementos, não é válido.
	if (this.table.size % 2 == 1)
		this._message(1, "O tabuleiro contem um número ímpar de elementos! (" + this.table.width + "x" + this.table.height + ")");
	
	// Armazena e verifica se os elementos são válidos
	this.data = this.config.cardData;
	if (this.data.length < this.table.halfsize)
		this._message(1, "É necessário ter ao menos metade dos elementos do tabuleiro definidos. Existe(m) apenas " + this.data.length + " de " + this.table.halfsize);
	
	$.each(this.data, function(index, element){
		if ((!element[1]) || (element[1].length < 1))
			this._message(1, "É necessário ter ao menos uma correspondência em cada elemento. Elemento inválido: " + index);
	});
	
	// Trata o botão de reiniciar, se existir
	if (this.config.restartButton)
		this.config.restartButton.click(this.restart);
	
	// Trata o cronômetro, se existir
	if (this.config.timer)
		setInterval(this.updateTimer, 500);
	
	// Finaliza inicialização
	this._message(0, "Inicialização completa.");
	
	//--------------------------------------------------------------------------------
	// FUNÇÕES
	//--------------------------------------------------------------------------------
	
	// Faz a lógica de jogo, cria elementos, desenha o tabuleiro e roda o jogo
	function run() {
		// Cria as informações da sessão do jogo
		this.gameSessionData = {
			started			: false,
			waitTime		: false,
			elements		: [],
			selectedElements: [],
			attempts		: 0,
			successes		: 0,
			startTime       : Math.floor(Date.now() / 1000),
		};
		this.updateCounters();
		
		// Esconde a tela de fim de jogo
		this.config.endGameContainer.hide();
		
		// Elementos que serão colocados no tabuleiro e relação (correspondência).
		elements = [];
		relation = [];
		usedElementPositions = [-1];
		
		// Embaralha os elementos da array de informação
		this.data.shuffle();
		
		// Seleciona os elementos que serão colocados no tabuleiro e suas relações
		for (i = 0; i < this.table.halfsize; i++) {
			
			// Posição do primeiro elemento
			aPos = -1;
			while (usedElementPositions.contains(aPos)) {
				aPos = Math.floor(Math.random() * (this.table.size));
			}
			usedElementPositions.push(aPos);
			
			// Posição do segundo elemento
			bPos = -1;
			while (usedElementPositions.contains(bPos)) {
				bPos = Math.floor(Math.random() * (this.table.size));
			}
			usedElementPositions.push(bPos);
			
			// Posiciona os elementos na array
			this.gameSessionData.elements[aPos] = new JogoDaMemoria_Elemento(
				this.data[i][0], 
				this.config.templates.element, 
				this._elementsCallback, 
				aPos, 
				i
			);
			this.gameSessionData.elements[bPos] = new JogoDaMemoria_Elemento(
				this.data[i][1].shuffle()[0], 
				this.config.templates.element, 
				this._elementsCallback, 
				bPos, 
				i
			);
		}
		
		// Realiza o desenho dos elementos
		actualRow = undefined;
		$.each(this.gameSessionData.elements, function(index, element) {
			if ((index % this.table.width) == 0) {
				actualRow = $(this.config.templates.row);
				this.config.container.append(actualRow);
			}
			
			actualRow.append(element.toHtml());
		}.bind(this));
		
		// Jogo iniciado
		this._message(0, "Período de visualização dos elementos.");
		
		// Aguarda antes de iniciar o jogo
		setTimeout(function() {
			$.each(this.gameSessionData.elements, function(index, element) {
				element.setState(0);
			});
			this.gameSessionData.started = true;
			this.gameSessionData.startTime = Math.floor(Date.now() / 1000);
			this._message(0, "Jogo iniciado.");
		}.bind(this), this.config.settings.initialWaitTime);
	}
	
	// Exibe uma mensagem de erro ou informação
	function _message(type, message) {
		// Informação
		if (type == 0) {
			console.log("[?] Jogo da Memória: " + message);
		} else if (type == 1) {
			throw("[!] Jogo da Memória: " + message);
		}
	}
	
	// Atualiza os contadores
	function updateCounters() {
		if (!this.gameSessionData.started)
			return;
		
		if (this.config.attemptCounter)
			this.config.attemptCounter.html(this.gameSessionData.attempts);
		
		if (this.config.successCounter)
			this.config.successCounter.html(this.gameSessionData.successes);
	}
	
	// Atualiza o timer
	function updateTimer() {
		if (this.endGame())
			return;
		
		if (this.gameSessionData.started) {
			secs = Math.floor(Date.now() / 1000) - this.gameSessionData.startTime;
			
			hours   = (parseInt(secs / 60 / 60)).toString();
			if (hours.length == 1) hours = "0" + hours;
			minutes = (parseInt((secs / 60) % 60)).toString();
			if (minutes.length == 1) minutes = "0" + minutes;
			secs    = (parseInt(secs % 60)).toString();
			if (secs.length == 1) secs = "0" + secs;
		} else {
			hours = "00";
			minutes = "00";
			secs = "00";
		}
		
		this.config.timer.html("" + hours + ":" + minutes + ":" + secs);
	}
	
	// Reiniciar o jogo
	function restart() {
		// Se o jogo não iniciou, não é possível reiniciar
		if (!this.gameSessionData.started)
			return;
		
		// Destrói elementos da interface
		$.each(this.gameSessionData.elements, function(index, element) {
			delete element;
		});
		
		// Remove interface;
		this.config.container.html(this.config.endGameContainer);
		this.config.endGameContainer.hide();
		
		// Reinicia aspetos lógicos
		this.run();
		
		this._message(0, 'Jogo reinicializado.');
	}
	
	// Verifica o final do jogo
	function endGame() {
		return (this.gameSessionData.successes == this.table.halfsize);
	}
	
	// Processa o final do jogo
	function _processEndGame() {
		plural = function(n) { return (n == 1 ? '' : 's') }
		secs = Math.floor(Date.now() / 1000) - this.gameSessionData.startTime;
		
		hours   = parseInt(secs / 60 / 60);
		minutes = parseInt((secs / 60) % 60);
		secs    = parseInt(secs % 60);
		
		this.config.endGameContainer.find('#time').html(
			(hours > 0 ? (hours + ' hora' + plural(hours) + ', ') : "") +
			(minutes > 0 ? (minutes + ' minuto' + plural(minutes) + ', ') : '') +
			(secs > 0 ? (secs + ' segundo' + plural(secs)) : '')
		);
		this.config.endGameContainer.find('#attempts').html(this.gameSessionData.attempts + ' tentativa' + (this.gameSessionData.attempts == 1 ? '' : 's'));
		this.config.endGameContainer.show();
	}
	
	// Trata os callbacks dos elementos
	function _elementsCallback(elementId, relationId) {
		this._message(0, "Callback de: " + elementId + " (Relação " + relationId + ")");
		
		if ((!this.gameSessionData.started) || (this.gameSessionData.waitTime))
			return false;
		
		selected = this.gameSessionData.selectedElements;
		
		if (selected.contains(elementId))
			return false;
		
		selected.push(elementId);
		
		if (selected.length == 1) {
			return true;
		} else {
			this.gameSessionData.attempts++;
			if (this.gameSessionData.elements[selected[0]].relationId == relationId) {
				this.gameSessionData.successes++;
				this.gameSessionData.selectedElements = [];
				this._message(0, "Encontrou!");
				
				if (this.endGame()) {
					this._message(0, "Ganhou! Fim de jogo.");
					this._processEndGame();
				}
			} else {
				this._message(0, "Errou!");
				this._message(0, "Esperando " + this.config.settings.errorWaitTime + "ms antes de continuar...");
				this.gameSessionData.waitTime = true;
				setTimeout(function() {
					this.gameSessionData.elements[selected[0]].setState(0);
					this.gameSessionData.elements[selected[1]].setState(0);
					this.gameSessionData.selectedElements = [];
					this.gameSessionData.waitTime = false;
					this._message(0, "Continuando");
				}.bind(this), this.config.settings.errorWaitTime);
			}
			this.updateCounters();
		}
		
		return true;
	}
}