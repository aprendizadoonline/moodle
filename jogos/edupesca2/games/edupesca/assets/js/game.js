/****************************************
 *          Variáveis Globais           *
 ****************************************/
// Razão de pixel para metro
var PTM_RATIO = 30;

// Colisões
var ColAgua     =  0x0001;
var ColBarco    = -0x0002;
var ColPalavra  = -0x0004;
var ColAnzol    = -0x0008;
var ColParede   = -0x0010;
var ColSPalavra = -0x0020;
var ColSAnzol   = -0x0040;
var ColSParede  = -0x0080;

// Categorias de colisão
var ColCatAgua     = 0x0001;
var ColCatBarco    = 0x0002;
var ColCatPalavra  = 0x0004;
var ColCatAnzol    = 0x0008;
var ColCatParede   = 0x0010;
var ColCatSPalavra = 0x0020;
var ColCatSAnzol   = 0x0040;
var ColCatSParede  = 0x0080;

// Máscara de colisão
var ColMaskAgua     = 0xFFFF;
var ColMaskBarco    = ColCatAgua|ColCatParede;
var ColMaskPalavra  = ColCatAgua|ColCatAnzol|ColCatParede|ColCatSParede;
var ColMaskAnzol    = ColCatAgua|ColCatPalavra|ColCatParede;
var ColMaskParede   = 0xFFFF;
var ColMaskSPalavra = ColCatAgua|ColCatSAnzol|ColCatParede;
var ColMaskSAnzol   = ColCatAgua|ColCatSPalavra|ColCatParede;
var ColMaskSParede  = ColCatPalavra;

/**
 * Início de Jogo
 * @param Object config Configuração. Pode conter as chaves (marcadas com asterisco são obrigatórias):
 *		gameContainer*	: Seletor do container do jogo
 *		gameWidth*		: Largura do jogo
 *		gameHeight*		: Altura do jogo
 */
var EduPesca = function(config) {
	/****************************************
	 *        Variáveis de Instância        *
	 ****************************************/
	
	// - Configuração do jogo
	this.config = {}
	
	// - Assets do jogo
	this._assets = {
		images: [
			config.basePath + "images/barco.png",
			config.basePath + "images/encosta1.png",
			config.basePath + "images/encosta2.png",
			config.basePath + "images/n1.png",
			config.basePath + "images/n2.png",
			config.basePath + "images/n3.png",
			config.basePath + "images/campo.png",
			config.basePath + "images/pier.png",
			config.basePath + "images/sol.png",
			config.basePath + "images/agua-fundo.png",
			config.basePath + "images/ceu.png",
			config.basePath + "images/edu.png",
		],
		audio: [
		],
		sprites: {}
	};
	
	this._assets.sprites[config.basePath + "images/nMsg.png"] = {
		tile: 1155,
		tileh: 411,
		map: {
			nMsg1: [0,0],
			nMsg2: [1,0],
			nMsg3: [2,0],
			nMsg4: [3,0]
		}
	},
	
	this._assets.sprites[config.basePath + "images/agua-cima.png"] = {
		tile: 1523,
		tileh: 82,
		map: {
			aguaCima1: [0,0],
			aguaCima2: [0,0],
			aguaCima3: [0,0],
			aguaCima4: [0,0],
		}
	},
	
	this._assets.sprites[config.basePath + "images/motor.png"] = {
		tile: 95,
		tileh: 89,
		map: {
			motorMove1:  [0,0],
			motorMove2:  [1,0],
			motorParado: [0,1]
		}
	},
	
	this._assets.sprites[config.basePath + "images/braco.png"] = {
		tile: 160,
		tileh: 35,
		map: {
			braco1: [0,0],
			braco2: [1,0]
		}
	},
	
	this._assets.sprites[config.basePath + "images/anzol.png"] = {
		tile: 25,
		tileh: 37,
		map: {
			anzolVazio: [0,0],
			anzolIsca3: [1,0],
			anzolIsca2: [2,0],
			anzolIsca1: [3,0]
		}
	}
			
	// - Entidades necessárias em várias rotinas da instância
	this.instanceEntities = {};
	
	/****************************************
	 *               Funções                *
	 ****************************************/
	
	/**
	 * Gerencia o processo de inicialização do jogo. Inicia o Crafty e inicia o cenário.
	 */
	this.init = function() {
		// Hack para o callback acessar o objeto this, já que estão em escopo diferente
		_this = this;
		
		// Inicia o jogo no servidor
		this.sessionCallback = false;
		Score.registerGameStart(this.config.user_id, this.config.game_id, this.config.level_id, function(success, data) { 
			_this.sessionCallback = true;
			
			if (success) {
				_this.session = data;
			} else {
				_this.sessionError = data;
			}
		});
		
		// Início do jogo
		if (!this.config.stageElement)
			this.config.stageElement = $(config.gameContainer)[0];
			
		Crafty.init(config.gameWidth, config.gameHeight, config.stageElement);
		
		// Inicializa o viewport
		Crafty.viewport.init(config.gameWidth, config.gameHeight, config.stageElement);
		
		// Inicializa a biblioteca Box2D, que trata da física
		Crafty.box2D.init(0, 9.8, 30, true); 
		if (this.config.debug) Crafty.box2D.showDebugInfo();
		
		// Jogo não está rodando ainda
		this.running = false;
		
		// Define os componentes adicionais
		this.initComponents();
		
		// Cena de jogo
		this.sceneGame();
	}
	
	/**
	 * Define os componentes do jogo
	 */
	this.initComponents = function() {
		// Nuvem
		Crafty.c("Nuvem", {
			/**
			 * Inicialização do componente na instância
			 */
			init: function() {
				// Escuta o evento de troca de frame
				this.bind("EnterFrame", this.EnterFrame);
				// Identifica o tamanho da janela de jogo
				this.gameWidth = config.gameWidth;
			},
			
			/**
			 * Entrada de cada frame
			 */
			EnterFrame: function() {
				// Move no eixo x
				this.x += 0.2;
				// Se passou do tamanho da janela, volta para o início
				if (this.x >= this.gameWidth)
					this.x = -this.w;
			}
		});
		
		// Estáticos
		Crafty.c("Static", {
			/**
			 * Inicialização do componente na instância
			 */
			init: function() {
				this.bind("EnterFrame", this.EnterFrame);
				this._keepOnX = undefined;
				this._keepOnY = undefined;
			},
			
			/**
			 * Mantem o objeto parado na posição x,y
			 * @param Number x
			 * @param Number y
			 * @return this for chaining
			 */
			keepOnXY: function(x, y) {
				this.keepOnX(x);
				this.keepOnY(y);
				return this;
			},
			
			/**
			 * Mantem o objeto parado na posição x
			 * @param Number x
			 * @return this for chaining
			 */
			keepOnX: function(x) {
				this._keepOnX = x;
				return this;
			},
			
			/**
			 * Mantem o objeto parado na posição y
			 * @param Number y
			 * @return this for chaining
			 */
			keepOnY: function(y) {
				this._keepOnY = y;
				return this;
			},
			
			/**
			 * Mantem o objeto parado na posição x,y atual
			 * @return this for chaining
			 */
			lockXY: function() {
				return this.keepOnXY(this.x, this.y);
			},
			
			/**
			 * Mantem o objeto parado na posição x atual
			 * @return this for chaining
			 */
			lockX: function() {
				return this.keepOnX(this.x);
			},
			
			/**
			 * Mantem o objeto parado na posição y atual
			 * @return this for chaining
			 */
			lockY: function() {
				return this.keepOnY(this.y);
			},
			
			/**
			 * Entrada de cada frame
			 */
			EnterFrame: function() {
				// Mantem X parado se estiver definido
				if (this._keepOnX != undefined)
					this.x = this._keepOnX - Crafty.viewport.x;
				// Mantem Y parado se estiver definido	
				if (this._keepOnY != undefined)
					this.y = _this.keepOnY - Crafty.viewport.y;
			}
		});
		
		// Movimento do Barco
		Crafty.c("Barco", {
			/**
			 * Inicialização do componente na instância
			 */
			init: function() {
				// Dependências
				this.requires("Box2D");
				
				// Variáveis
				this.lateralForce = 0;
				
				// Escuta o evento de troca de frame
				this.bind("EnterFrame", this.EnterFrame);
			},
			
			/**
			 * Método que realiza a movimentação lateral
			 * @param Number force Força do movimento. Positivo move para a direita, negativo para a esquerda.
			 */
			moveLateral: function(force) {
				this.lateralForce = Number(force);
			},
			
			/**
			 * Entrada de cada frame
			 */
			EnterFrame: function() {
				// Retorna se não houver corpo criado
				if (!this.body) return;
				
				// Move no eixo x
				if (this.lateralForce != 0)
					this.body.ApplyForce(new Box2D.Common.Math.b2Vec2(this.lateralForce, 0), this.body.GetWorldCenter());
			}
		});
		
		// Movimento do Braço
		Crafty.c("Braco", {
			/**
			 * Inicialização do componente na instância
			 */
			init: function() {
				// Dependências
				this.requires("Box2D");
				
				// Variáveis
				this.verticalForce = 0;
				this.hookJoint = false;
				
				// Escuta o evento de troca de frame
				this.bind("EnterFrame", this.EnterFrame);
			},
			
			/**
			 * Método que realiza a movimentação vertical
			 * @param Number force Força do movimento. Positivo move para a baixo, negativo para cima.
			 * @param b2RopeJoint Joint da vara com o anzol
			 */
			moveVertical: function(force, hookJoint) {
				this.verticalForce = Number(force);
				this.hookJoint = hookJoint;
			},
			
			/**
			 * Entrada de cada frame
			 */
			EnterFrame: function() {
				// Retorna se não houver corpo criado, ou não houver o joint da vara
				if ((!this.body) || (!this.hookJoint) || (this.verticalForce == 0)) return;
				
				// Move no eixo y
				if (this.verticalForce < 0) {
					/*this.body.ApplyTorque(
						new Box2D.Common.Math.b2Vec2(0, -0.000005), 
						new Box2D.Common.Math.b2Vec2(10/PTM_RATIO, 5/PTM_RATIO)
					);*/
					
					// ???
				}
				
				// Puxa ou solta o anzol
				this.hookJoint.m_maxLength += (this.verticalForce > 0 ? (2.0/Crafty.timer.FPS()) : -(2.0/Crafty.timer.FPS()));
				
				if (this.hookJoint.m_maxLength < 0.1)
					this.hookJoint.m_maxLength = 0.1;
					
				if (this.hookJoint.m_maxLength > config.playerRolo)
					this.hookJoint.m_maxLength = config.playerRolo;
			}
		});
		
		// Movimento do Peixe
		Crafty.c("Peixe", {
			/**
			 * Inicialização do componente na instância
			 */
			init: function() {
				// Dependências
				if (config.debug) {
					this.requires("Box2D, DebugRectangle");
					this.debugStroke("green");
				} else {
					this.requires("Box2D")
				}
				
				// Destino
				this.destination = false;
				
				// Escuta o evento de troca de frame
				this.bind("EnterFrame", this.EnterFrame);
			},
			
			/**
			 * Identifica os limites do peixe
			 * @param Number minx X Mínimo
			 * @param Number miny Y Mínimo
			 * @param Number maxx X Máximo
			 * @param Number maxy Y Máximo
			 * @return this for chaining
			 */
			setLimits: function(minx, miny, maxx, maxy) {
				// Determina os limites
				this.limits = {
					minx: minx,
					miny: miny,
					maxx: maxx,
					maxy: maxy
				};
				
				// Gera um novo destino
				this.generateDestination();
				
				// Retorna o próprio objeto
				return this;
			},
			
			/**
			 * Gera um destino para o peixe dentro dos limites impostos
			 */
			generateDestination: function() {
				// Se não houverem limites, não há destino
				if (this.limits) {
					this.destination = {
						x: this.limits.minx + (this.limits.maxx - this.limits.minx) * Math.random(),
						y: this.limits.miny + (this.limits.maxy - this.limits.miny) * Math.random()
					};
					
					if (config.debug)
						this.debugRectangle({_x: this.destination.x, _y: this.destination.y, _w: 5, _h: 5});
				} else {
					this.destination = false;
				}
			},
			
			/**
			 * Função de entrada em cada frame
			 */
			EnterFrame: function() {
				// Retorna se não houver um corpo ou destino
				if (!this.body || !this.destination) return;
				
				// 80% de chance do peixe não mover
				if (Math.random() < 0.8) return;
				
				// Distância do destino
				cx = Math.max(Math.min(this.destination.x, this.x + this.w), this.x);
				cy = Math.max(Math.min(this.destination.y, this.y + this.h), this.y);
				
				distanceX = (this.destination.x - cx);
				distanceY = (this.destination.y - cy);
				distance = Math.sqrt(distanceX * distanceX + distanceY * distanceY);
				
				// Se o destino está próximo demais, é provável que o peixe já chegou, então gera um novo
				if (distance < 5) {
					this.generateDestination();
					return;
				}
				
				// Aplica uma força na direção do destino
				force = new b2Vec2(
					(distanceX/distance) * this.body.GetMass(), 
					(distanceY/distance) * this.body.GetMass()
				);								
					
				this.body.ApplyForce(new b2Vec2(0, 0.5 * this.body.GetMass()), this.body.GetWorldCenter());
				this.body.SetLinearVelocity(new b2Vec2(distanceX/distance, distanceY/distance));
			},
			
		});
	},
	
	/**
	 * Carrega elementos necessários para o jogo. Preenche toda a tela com preto e exibe uma mensagem enquanto isso.
	 */
	this.sceneGame = function() {
		// Hack para as funções do listener acessarem o objeto this, já que estão em escopo diferente
		_this = this;
		
		// Carrega os assets
		Crafty.load(this._assets, function () {	
			// Constrói o cenário
			_this.buildScenario();
			
			continuePlaying = function () {
				// Destrói a mensagem de carregamento
				_this.instanceEntities.loadingText.destroy();
				if (_this.config.debug) {
					_this.running = true;
					_this.startTime = Date.now();
				} else {
					// Passa para a cena de ajuda
					_this.showHelp();
				}
			}
			
			checkSessionCallback = function() {
				if (_this.sessionCallback) {
					if (_this.session) {
						continuePlaying(_this);
					} else {
						_this.showMessage('error', undefined, _this.sessionError, undefined, function(){
							window.history.go(-1);
						});
					}
				} else {
					setTimeout(checkSessionCallback, 1000);
				}
			}
			
			checkSessionCallback();
		});       
		
		// Fundo
		Crafty.background("#000000");
		
		// Cria o texto de carregamento
		this.instanceEntities.loadingText = Crafty.e("2D, DOM, Text")
			.attr({ w: 100, h: 20, x: (config.gameWidth - 100)/2, y: (config.gameHeight - 20)/2 })
			.text("Carregando...")
			.css({ "text-align": "center", "color": "#ffffff"});
	}
	
	/**
	 * Cena de ajuda. Mostra a instrução em destaque e tem um botão para iniciar o jogo
	 */
	this.showHelp = function() {
		// Hack para as funções do listener acessarem o objeto this, já que estão em escopo diferente
		_this = this;
		
		this.showMessage('info', this.config.levelTitle, $("<h2 class='text-center'>").html(this.config.levelInfo), 'Jogar!', function(ref) {
			_this.running = true;
			_this.startTime = Date.now();
			ref.close();
		});
	};
	
	/**
	 * Constrói o cenário a partir das imagens carregadas
	 */
	this.buildScenario = function() {
		// Cria as entidades
		this.createEntities();
		// Define a física
		this.definePhysics();
		// Registra listeners de eventos
		this.registerListeners();
	}
	
	/**
	 * Constrói o cenário a partir das imagens carregadas
	 */
	this.createEntities = function() {
		// Fundo
		Crafty.background("#E4F29B");
		
		// Encosta esquerda
		this.instanceEntities.encostaEsquerda = Crafty.e("2D, DOM, Image")
			.attr({
				x: -400, 
				y: (this.config.gameHeight - 400), 
				w: 1013, 
				h: 500, 
				z: 9
			})
			.image(this.config.basePath + "images/encosta1.png", "no-repeat");
		
		// Escosta direita
		this.instanceEntities.encostaDireita = Crafty.e("2D, DOM, Image")
			.attr({
				x: 1730, 
				y: (this.config.gameHeight - 425), 
				w: 434, 
				h: 526,
				z: 9
			})
			.image(this.config.basePath + "images/encosta2.png", "no-repeat");
			
		// Paredes
		this.instanceEntities.encostaParede = Crafty.e("2D, DOM, Box2D")
			.attr({
				x: 1175, 
				y: (this.config.gameHeight - 5),
				w: 1200,
				h: 5
			})
			.box2d({
				bodyType: 'static',
				density: 3,
				friction: 1,
				restitution: 0.5,
				groupIndex: ColParede,
				categoryBits: ColCatParede,
				maskBits: ColMaskParede
			});
			
		// Lateral esquerda (Barco)
		this.instanceEntities.encostaParede.addFixture({
			bodyType: 'static',
			density: 3,
			friction: 1,
			restitution: 0.5,
			groupIndex: ColParede,
			categoryBits: ColCatParede,
			maskBits: ColMaskParede,
			shape: [
				[-850, -500],
				[-840, -500],
				[-840, 0],
				[-850, 0],
			]
		})
		
		// Lateral esquerda (Peixes)
		this.instanceEntities.encostaParede.addFixture({
			bodyType: 'static',
			density: 3,
			friction: 1,
			restitution: 0.5,
			groupIndex: ColSParede,
			categoryBits: ColCatSParede,
			maskBits: ColMaskSParede,
			shape: [
				[-600, -400],
				[-590, -400],
				[-590, 0],
				[-600, 0],
			]
		})
		
		// Lateral direita (Peixes)
		this.instanceEntities.encostaParede.addFixture({
			bodyType: 'static',
			density: 3,
			friction: 1,
			restitution: 0.5,
			groupIndex: ColParede,
			categoryBits: ColCatParede,
			maskBits: ColMaskParede,
			shape: [
				[600, -300],
				[610, -300],
				[610, 0],
				[600, 0],
			]
		});
		
		// Lateral direita (Barco)
		this.instanceEntities.encostaParede.addFixture({
			bodyType: 'static',
			density: 3,
			friction: 1,
			restitution: 0.5,
			groupIndex: ColParede,
			categoryBits: ColCatParede,
			maskBits: ColMaskParede,
			shape: [
				[700, -500],
				[710, -300],
				[600, -290]
			]
		})
		
		// Nuvens
		this.instanceEntities.nuvem1 = Crafty.e("2D, DOM, Image, Nuvem")
			.attr({w: 453, h: 183, y: 0, x: -200, z: 3})
			.image(this.config.basePath + "images/n1.png", "no-repeat");
		this.instanceEntities.nuvem2 = Crafty.e("2D, DOM, Image, Nuvem")
			.attr({w: 453, h: 183, y: 0, x: this.config.gameWidth * 0.5, z: 3})
			.image(this.config.basePath + "images/n2.png", "no-repeat");
		this.instanceEntities.nuvem3 = Crafty.e("2D, DOM, Image, Nuvem")
			.attr({w: 708, h: 178, y: 0, x: this.config.gameWidth * 0.9, z: 3})
			.image(this.config.basePath + "images/n2.png", "no-repeat");
			
		// Nuvem de mensagem
		this.instanceEntities.nMsg = Crafty.e("2D, DOM, Sprite, Static, SpriteAnimation, nMsg1")
			.attr({w: 1155, h: 411, x: (this.config.gameWidth - 1155)/2, y: -150, z: 4})
			.lockX();
		// Define a animação
		this.instanceEntities.nMsg.reel("Opening", 1000, [[0,0], [1,0], [2,0], [3,0]]);
		// Anima
		this.instanceEntities.nMsg.animate("Opening", 1);
		// Adiciona o conteúdo da janela
		this.addLevelData($(this.instanceEntities.nMsg._element));
			
		// Campo
		this.instanceEntities.campo = Crafty.e("2D, DOM, Image, Static")
			.attr({
				x: (this.config.gameWidth - 2460) / 2, 
				y: (this.config.gameHeight - 550), 
				z: 2,
				w: 2460, 
				h: 180, 
			})
			.image(this.config.basePath + "images/campo.png", "repeat-x")
			.lockX();
		
		// Sol
		this.instanceEntities.sol = Crafty.e("2D, DOM, Image, Static")
			.attr({
				x: 150, 
				y: (this.config.gameHeight - 600), 
				w: 251, 
				h: 232, 
				z: 1
			})
			.image(this.config.basePath + "images/sol.png", "no-repeat")
			.lockX();
			
		// Água (Baixo)
		this.instanceEntities.aguaBaixo = Crafty.e("2D, DOM, Image, Static")
			.attr({
				x: 350, 
				y: (this.config.gameHeight - 303), 
				w: 1523, 
				h: 303, 
				z: 8
			})
			.image(this.config.basePath + "images/agua-fundo.png", "repeat-x")
			
		// Água (Cima)
		this.instanceEntities.aguaCima = Crafty.e("2D, DOM, Sprite, Static, SpriteAnimation, aguaCima1")
			.attr({
				x: 350, 
				y: (this.config.gameHeight - 385), 
				w: 1523, 
				h: 82, 
				z: (this.instanceEntities.aguaBaixo.z)
			})
		// Define a animação
		this.instanceEntities.aguaCima.reel("Moving", 1000, [[0,0], [1,0], [2,0], [3,0]]);
		// Anima
		this.instanceEntities.aguaCima.animate("Moving", -1);
		
		// Pier
		this.instanceEntities.pier = Crafty.e("2D, DOM, Image")
			.attr({
				x: 370, 
				y: (this.config.gameHeight - 395), 
				w: 1013, 
				h: 500, 
				z: 7
			})
			.image(this.config.basePath + "images/pier.png", "no-repeat");
			
		// Céu
		this.instanceEntities.ceu = Crafty.e("2D, DOM, Image, Static, Color")
			.attr({
				x: 0, 
				y: 0,
				w: this.config.gameWidth, 
				h: (this.config.gameHeight - 395), 
				z: -2
			})
			.image(this.config.basePath + "images/ceu.png", "repeat-x")
			.lockX()
			.color("#74DAF3");
			
		// Barco
		this.instanceEntities.barco = Crafty.e("2D, DOM, Image, Box2D, Barco")
			.attr({
				x: 450, 
				y: (this.instanceEntities.aguaCima.y - 200), 
				z: 6, 
			})
			.image(this.config.basePath + "images/barco.png", "no-repeat")
			.box2d({
				bodyType: 'dynamic',
				density: 0.9,
				friction: 0,
				resititution: 0,
				shape: [
					[0,15], [130, 10], [265, 0], [265, 130], [20, 82]
				],
				groupIndex: ColBarco,
				categoryBits: ColCatBarco,
				maskBits: ColMaskBarco
			});
			
			
		// Auxiliar do barco
		this.instanceEntities.barcoAux = Crafty.e("2D, DOM, Box2D")
			.attr({
				x: 500,
				y: 30,
				h: 100,
				w: 14,
				z: 1000
			})
			.box2d({
				bodyType: "dynamic",
				density: 0.5,
				friction: 0.05,
				restitution: 0,
				groupIndex: ColBarco,
				categoryBits: ColCatBarco,
				maskBits: ColMaskBarco
			});
			
		// Edu
		this.instanceEntities.edu = Crafty.e("2D, DOM, Image, Box2D")
			.attr({
				x: (this.instanceEntities.barco.x + 25),
				y: (this.instanceEntities.aguaCima.y - 270),
				z: (this.instanceEntities.barco.z + 1),
			})
			.box2d({
				bodyType: 'dynamic',
				density: 0.1,
				friction: 0,
				restitution: 0,
				groupIndex: ColBarco,
				categoryBits: ColCatBarco,
				maskBits: ColMaskBarco,
				shape: [
					[0, 0],
					[74, 0],
					[74, 90],
					[0, 90]
				]
			})
			.image(this.config.basePath + "images/edu.png", "no-repeat");
		
		// Motor
		this.instanceEntities.motor = Crafty.e("2D, DOM, Sprite, SpriteAnimation, Box2D, motorParado")
			.attr({
				x: (this.instanceEntities.barco.x - 50),
				y: (this.instanceEntities.aguaCima.y - 200),
				z: (this.instanceEntities.barco.z),
			})
			.box2d({
				bodyType: 'dynamic',
				density: 0.2,
				friction: 0,
				restitution: 0,
				groupIndex: ColBarco,
				categoryBits: ColCatBarco,
				maskBits: ColMaskBarco,
				shape: [
					[15, 0],
					[95, 0],
					[95, 85],
					[15, 85],
				]
			});
			
		this.instanceEntities.motor.reel("Moving", 250, [[0,0], [1,0]]);
		this.instanceEntities.motor.reel("Stopped",  01, [[0,1]]);
		
		// Braço
		this.instanceEntities.braco = Crafty.e("2D, DOM, Sprite, SpriteAnimation, Box2D, braco1, Braco")
			.attr({
				x: (this.instanceEntities.barco.x + 45),
				y: (this.instanceEntities.barco.y - 40),
				z: (this.instanceEntities.barco.z + 1),
			})
			.box2d({
				bodyType: 'dynamic',
				density: 0.1,
				friction: 0.3,
				restitution: 0.5,
				groupIndex: ColBarco,
				categoryBits: ColCatBarco,
				maskBits: ColMaskBarco,
				shape: [
					[0, 0],
					[0, 35],
					[160, 35],
					[160, 0],
				]
			});
		this.instanceEntities.braco.reel("Moving", 250, [[0,0], [1,0]]);
		this.instanceEntities.braco.reel("Stopped",  1, [[0,0]]);
		
		// Anzol
		this.instanceEntities.anzol = Crafty.e("2D, DOM, Sprite, SpriteAnimation, Collision, Box2D, anzolIsca3, Anzol")
			.attr({
				x: (this.instanceEntities.braco.x + 200),
				y: (this.instanceEntities.braco.y + 50),
				z: (this.instanceEntities.braco.z),
			})
			.box2d({
				bodyType: 'dynamic',
				density: 2.5,
				friction: 0,
				restitution: 0,
				groupIndex: ColAnzol,
				categoryBits: ColCatAnzol,
				maskBits: ColMaskAnzol,
				shape: [
					[3, 5],
					[0, 0],
					[6, 0]
				]
			})
			.collision(
				[0, 0], [26, 0], [26, 36], [0, 36]
			);
			
			
			this.instanceEntities.anzol.addFixture({
				bodyType: "dynamic",
				density: 2.5,
				friction: 0,
				restitution: 0,
				groupIndex: ColAnzol,
				categoryBits: ColCatAnzol,
				maskBits: ColMaskAnzol,
				shape: [
					[3, 5],
					[6, 5],
					[6, 27],
					[3, 27]
				]
			})
			
			
			this.instanceEntities.anzol.addFixture({
				bodyType: "dynamic",
				density: 2.5,
				friction: 0,
				restitution: 0,
				groupIndex: ColAnzol,
				categoryBits: ColCatAnzol,
				maskBits: ColMaskAnzol,
				shape: [
					[12, 32],
					[12, 36],
					[-2, 22],
					[2, 22]
				]
			})
			
			
			this.instanceEntities.anzol.addFixture({
				bodyType: "dynamic",
				density: 2.5,
				friction: 0,
				restitution: 0,
				groupIndex: ColAnzol,
				categoryBits: ColCatAnzol,
				maskBits: ColMaskAnzol,
				shape: [
					[12, 36],
					[12, 32],
					[22, 22],
					[26, 22]
				]
			})
			
		// Cria as entidades "pescáveis"
		this.createFishableEntities();
	}
	
	/**
	 * Define a física das entidades
	 */
	this.definePhysics = function() {
		// Controlador de flutuabilidade
		this.createBuoyancyController();
		
		// Registra o listener de contato
		this.registerContactListener();
		
		// Cria a física da água
		this.createWaterPhysics();
		
		// Cria os vínculos do cenário
		this.createJoints();
	}
	
	/**
	 * Cria os vínculos do cenário
	 */
	this.createJoints = function() {
		// Vínculo do barco com o auxiliar que o faz mover na água
		Crafty.box2D.revoluteJoint({
			revolute_bodyA: this.instanceEntities.barco.body,
			revolute_bodyB: this.instanceEntities.barcoAux.body,
			anchorA: [75/PTM_RATIO, 100/PTM_RATIO],
			anchorB: [0/PTM_RATIO, 0/PTM_RATIO],
			torque: 800,
			speed: 2,
			enableMotor: true
		});
		
		// Vínculo do barco com o motor
		Crafty.box2D.revoluteJoint({
			revolute_bodyA: this.instanceEntities.motor.body,
			revolute_bodyB: this.instanceEntities.barco.body,
			anchorA: [48/PTM_RATIO, -6/PTM_RATIO],
			anchorB: [0, 0],
			enableLimit: true,
			lowerAngle: 0,
			upperAngle: 0
		});
		
		// Braço com o edu
		this.bracoJoint = Crafty.box2D.revoluteJoint({
			revolute_bodyA: this.instanceEntities.braco.body,
			revolute_bodyB: this.instanceEntities.edu.body,
			anchorB: [27/PTM_RATIO, 47/PTM_RATIO],
			anchorA: [0, -6/PTM_RATIO],
		});
		
		// Barco com o Edu
		Crafty.box2D.revoluteJoint({
			revolute_bodyA: this.instanceEntities.barco.body,
			revolute_bodyB: this.instanceEntities.edu.body,
			anchorA: [26/PTM_RATIO, -70/PTM_RATIO],
			anchorB: [0, 0/PTM_RATIO],
			enableLimit: true,
			lowerAngle: -2,
			upperAngle: 2
		});
		
		// Anzol com a vara
		this.ropeJoint = Crafty.box2D.ropeJointDef({
			revolute_bodyA: this.instanceEntities.braco.body,
			revolute_bodyB: this.instanceEntities.anzol.body,
			anchorA: [160/PTM_RATIO, 0],
			anchorB: [0, 0],
			maxLength: 10/PTM_RATIO
		});
	}
	
	/**
	 * Cria o objeto físico para a água
	 */
	this.createWaterPhysics = function() {
		// Definições de tamanho
		width = this.instanceEntities.aguaCima.w;
		height = this.config.gameHeight - this.instanceEntities.aguaCima.y;
		px = this.instanceEntities.aguaCima.x + width/2;
		py = this.instanceEntities.aguaCima.y + height/2;
		
		Crafty.e("Box2D")
			.attr({
				x: px,
				y: py,
				w: width,
				h: height
			})
			.box2d({
				bodyType: 'static',
				sensor: true,
				density: 1.0,
				friction: 10,
				restitution: 0,
				categoryBits: ColCatAgua,
				maskBits: ColMaskAgua,
				groupIndex: ColAgua,
				
			});
		return;
		// Definições de tamanho
		width = this.instanceEntities.aguaCima.w;
		height = this.config.gameHeight - this.instanceEntities.aguaCima.y;
		px = this.instanceEntities.aguaCima.x + width/2;
		py = this.instanceEntities.aguaCima.y + height/2;
		
		// Cria a definição do corpo
		bodyDef = new Box2D.Dynamics.b2BodyDef();
		bodyDef.type = Box2D.Dynamics.b2Body.b2_staticBody;
		bodyDef.position.x = px/PTM_RATIO;
		bodyDef.position.y = py/PTM_RATIO;
		
		// Cria a fixture para o corpo
		fixtureDef = new Box2D.Dynamics.b2FixtureDef();
		fixtureDef.isSensor = true;
		fixtureDef.density = 1.0;
		fixtureDef.friction = 10;
		fixtureDef.restitution = 0;
		fixtureDef.filter.categoryBits = ColCatAgua;
		fixtureDef.filter.maskBits = ColMaskAgua;
		fixtureDef.filter.groupIndex = ColAgua;

		// Cria a forma física para o objeto
		fixtureDef.shape = new Box2D.Collision.Shapes.b2PolygonShape();
		fixtureDef.shape.SetAsBox(width/2/PTM_RATIO, height/2/PTM_RATIO);
		
		// Cria o corpo físico
		this.waterBody = Crafty.box2D.world.CreateBody(bodyDef);
		// Adiciona a fixture ao corpo
		this.waterBody.CreateFixture(fixtureDef);
	}
	
	/**
	 * Cria o controlador de flutuabilidade
	 */
	this.createBuoyancyController = function() {
		b = new b2BuoyancyController();
		b.normal.Set(0, -1);
		b.offset = -230/PTM_RATIO;
		b.useDensity = true;
		b.density = 2;
		b.linearDrag = 5;
		b.angularDrag = 2;
		
		// Adiciona o controlador ao mundo e guarda a instância
		Crafty.box2D.world.AddController(b);
		this.buoyancyController = b;
	}
	
	/**
	 * Registra o listener que controla o contato
	 */
	this.registerContactListener = function() {
		// Hack para as funções do listener acessarem o objeto this, já que estão em escopo diferente
		_this = this;
		
		// Cria um novo listener
		listener = new Box2D.Dynamics.b2ContactListener;
		
		// Callback de início de contato
		listener.BeginContact = function(contact) {
			// Fixtures que se tocaram
			var fixtureA = contact.GetFixtureA();
			var fixtureB = contact.GetFixtureB();
			
			// Corpo das fixtures
			bodyA = fixtureA.GetBody();
			bodyB = fixtureB.GetBody();
			
			// Identifica qual (e se uma) das fixtures é um sensor
			if (fixtureA.IsSensor()){
				// Se for a A, adiciona a fixture B ao controlador caso já não possua
				if (!bodyB.GetControllerList()) 
					_this.buoyancyController.AddBody(bodyB);
			} else if (fixtureB.IsSensor()) {
				// Se for a B, adiciona a fixture A ao controlador caso já não possua
				if (!bodyA.GetControllerList())
					_this.buoyancyController.AddBody(bodyA);
			}
		}

		// Callback de final de contato
		listener.EndContact = function(contact) {
			// Fixtures que se tocaram
			fixtureA = contact.GetFixtureA();
			fixtureB = contact.GetFixtureB();
			
			// Corpo das fixtures
			bodyA = fixtureA.GetBody();
			bodyB = fixtureA.GetBody();
			
			// Identifica qual (e se uma) das fixtures é um sensor
			if (fixtureA.IsSensor()) {
				// Se for a A, remove-a do controlador
				if (bodyA.GetControllerList())
					_this.buoyancyController.RemoveBody(bodyA);
			} else if (fixtureB.IsSensor()) {
				// Se for a B, remove-a do controlador
				if (bodyB.GetControllerList())
					_this.buoyancyController.RemoveBody(bodyB);
			}
		}
		
		// Registra o listener
		Crafty.box2D.world.SetContactListener(listener);
	},
	
	/**
	 * Registra os listeners de eventos
	 */
	this.registerListeners = function() {
		// Hack para as funções do listener acessarem o objeto this, já que estão em escopo diferente
		_this = this;
		
		// Registra listener para pressionamento de teclas
		this.instanceEntities.edu.bind("KeyDown", function(e) {
			// Só processa as teclas se o jogo estiver rodando
			if (!_this.running) return;
			
			// Processa a tecla
			switch (e.keyCode) {
			case Crafty.keys.A: // A (Esquerda)
				_this.instanceEntities.barco.moveLateral(-_this.config.playerSpeed);
				_this.instanceEntities.motor.animate("Moving", -1);
				break;
			case Crafty.keys.D: // D (Direita)
				_this.instanceEntities.barco.moveLateral(_this.config.playerSpeed);
				_this.instanceEntities.motor.animate("Moving", -1);
				break;
			case Crafty.keys.W: // W (Cima)
				_this.instanceEntities.braco.moveVertical(-_this.config.playerForce, _this.ropeJoint);
				_this.instanceEntities.braco.animate("Moving", -1);
				break;
			case Crafty.keys.S: // S (Baixo)
				_this.instanceEntities.braco.moveVertical(_this.config.playerForce, _this.ropeJoint);
				_this.instanceEntities.braco.animate("Moving", -1);
				break;
			}
		});
		
		// Registra listener para des-pressionamento de teclas
		this.instanceEntities.edu.bind("KeyUp", function(e) {
			// Só processa as teclas se o jogo estiver rodando
			if (!_this.running) return;
			
			// Processa a tecla
			switch (e.keyCode) {
			case Crafty.keys.A: // A (Esquerda)
				_this.instanceEntities.barco.moveLateral(0);
				_this.instanceEntities.motor.animate("Stopped", 1);
				break;
			case Crafty.keys.D: // D (Direita)
				_this.instanceEntities.barco.moveLateral(0);
				_this.instanceEntities.motor.animate("Stopped", 1);
				break;
			case Crafty.keys.W: // W (Cima)
				_this.instanceEntities.braco.moveVertical(0, false);
				_this.instanceEntities.braco.animate("Stopped", 1);
				break;
			case Crafty.keys.S: // S (Baixo)
				_this.instanceEntities.braco.moveVertical(0, false);
				_this.instanceEntities.braco.animate("Stopped", 1);
				break;
			}
		});
		
		// Listener do movimento de tela
		this.instanceEntities.barco.bind("Move", function(e) {
			minX = -(_this.instanceEntities.encostaDireita.x + _this.instanceEntities.encostaDireita.w - _this.config.gameWidth);
			maxX = 0;
			newX = Math.round((_this.config.gameWidth / 2) - _this.instanceEntities.barco.x);
			
			if (newX < minX) newX = minX; 
			if (newX > maxX) newX = maxX;
			
			Crafty.viewport.scroll('_x', newX);
		});
		
		// Peixes
		Crafty("Peixe").bind("Move", function(e) {
			// Só processa a pesca se o jogo estiver rodando
			if (!_this.running) return;
			
			// Limite de altura para indicar que pescou de fato
			limitY = _this.instanceEntities.aguaCima.y + 10;
			
			// Se pescou
			if ((this.y < limitY) && (_this.instanceEntities.anzol.y < limitY) && (this.hit("Anzol"))) {
				// Processa o texto
				_this.processText(this.text());
				// Destrói a entidade e o corpo
				this.destroy();
			}
		});
	}
	
	/**
	 * Processa o conteúdo da janela de informações do nível
	 * @param jQuery Elemento jQuery que aponta para o DOM no qual o conteúdo deve estar
	 */
	this.addLevelData = function(e) {
		// Identifica o container e adiciona o container do conteúdo
		e.attr("id", "nMsg");
		content = $("<div class='content'>");
		e.append(content);
		
		// Informações do nível 
		content.append(
			$("<div class='info'>").html(this.config.levelInfo)
		);
		
		// Traços para palavra
		this.wordSelector = $("<div id='word'>").html("_".repeat(this.config.levelWord.length));
		content.append(
			this.wordSelector
		);
		
		// String de destino
		this.levelString = "";
		// Controlador da posição
		this.wordPosition = 0;
	}
	
	/**
	 * Cria as entidades "pescáveis"
	 */
	this.createFishableEntities = function() {
		// Identifica as bordas da água
		minX = this.instanceEntities.encostaEsquerda.x + this.instanceEntities.encostaEsquerda.w;
		maxX = this.instanceEntities.encostaDireita.x;
		minY = this.instanceEntities.aguaCima.y + 50;
		maxY = this.config.gameHeight - 25;
		dltX = (maxX - minX);
		dltY = (maxY - minY);
		
		// Auxiliar
		textMeasureAux = $("#textMeasureAux");
		
		// Cria o container lógico para os "peixes"
		this.fishes = [];
		
		// Hack para as funções do listener acessarem o objeto this, já que estão em escopo diferente
		_this = this;
		
		// Para cada elemento
		$.each(this.config.levelData, function(index, element) {
			// Determina a posição aleatoriamente
			posX = minX + Math.round(Math.random() * dltX);
			posY = minY + Math.round(Math.random() * dltY);
			
			// Determina o tamanho
			textMeasureAux.html(element);
			sizW = textMeasureAux.width();
			sizH = textMeasureAux.height();
			
			// Ajusta posições
			if (posX + sizW > maxX) posX = maxX - sizW;
			if (posY + sizH > maxY) posY = maxY - sizH;
				
			// Cria uma entidade
			_this.fishes[index] = Crafty.e("2D, DOM, Text, Collision, Box2D, Peixe")
				.attr({
					w: sizW, 
					h: sizH, 
					x: posX, 
					y: posY,
					z: 20
				})
				.textFont({
					type: null, 
					family: null,
					size: null,
					weight: null,
					lineHeight: null,
					variant: null
				})
				.box2d({
					bodyType: "dynamic",
					density: 2,
					friction: 0,
					restitution: 0,
					groupIndex: ColPalavra,
					categoryBits: ColCatPalavra,
					maskBits: ColMaskPalavra,
					shape: [
						[0, 0],
						[5, 0],
						[5, 5],
						[0, 5]
					]	
				})
				.collision()
				.setLimits(minX, minY, maxX, maxY)
				.text(element);
				
			// Adiciona várias fixtures
			spansX = Math.floor(sizW / _this.config.fishWspan) + 1;
			spansY = Math.floor(sizH / _this.config.fishHspan) + 1;
			for (i = 0; i < spansX; i++) {
				for (j = 0; j < spansY; j++) {
					// Pula o mais a esquerda/acima, já que já foi criado
					if (i == 0 && j == 0) continue;
					x = _this.config.fishWspan * i;
					y = _this.config.fishHspan * j;
					
					// Adiciona a fixture ao "peixe"
					_this.fishes[index].addFixture({
						bodyType: "dynamic",
						density: 2,
						friction: 0,
						restitution: 0,
						groupIndex: ColPalavra,
						categoryBits: ColCatPalavra,
						maskBits: ColMaskPalavra,
						shape: [
							[x + 0, y + 0],
							[x + 5, y + 0],
							[x + 5, y + 5],
							[x + 0, y + 5]
						]
					})
				}
			}
			
			// Adiciona a classe de peixes
			$(_this.fishes[index]._element).addClass("Peixe");
		});
		
		// Limpa o auxilizar
		textMeasureAux.html("");
	}
	
	/**
	 * Processa o texto pescado
	 * @param String text
	 */
	this.processText = function(text) {
		// Envia para o servidor
		if (this.session) Score.sendProgress(this.session, 'catch', {word: text});
			
		// Adiciona os elementos do texto pescado na string de destino
		i = 0;
		for (i; i < text.length; i++) {
			this.levelString += text[i];
			
			
			if (this.wordPosition >= this.config.levelWord.length) {
				break;
			}
			
			this.wordPosition++;	
		}
		
		// Estado de finalização (0 = Não terminou | 1 = Perdeu | 2 = Ganhou)
		finishState = 0;
		
		// Se terminou de processar a última pesca
		if (i == text.length) {
			// Se a palavra for igual, ganhou
			if (this.levelString == this.config.levelWord) {
				finishState = 2;
			// Se terminou o processamento total
			} else if (this.levelString.length >= this.config.levelWord.length) {
				finishState = 1;
			} 						
		// Se não terminou, perdeu
		} else {
			finishState = 1;
		}
		
		this.running = false;
		if (finishState == 1) {
			/*
			// Implementar mensagem de perdeu
			Crafty.e("2D, DOM, Text, Static")
				.attr({ w: 300, h: 48, x: (this.config.gameWidth - 300)/2, y: (this.config.gameHeight - 48)/2 })
				.text("Você não conseguiu! Tente novamente.")
				.textFont({ size: '48px', weight: 'bold' })
				.css({"font-size": "48px", "text-align": "center", "color": "red"})
				.lockXY();
			*/
		} else if (finishState == 2) {
			/*
			Crafty.e("2D, DOM, Text, Static")
				.attr({ w: 300, h: 48, x: (this.config.gameWidth - 300)/2, y: (this.config.gameHeight - 48)/2 })
				.text("Parabéns, você conseguiu! (:")
				.textFont({ size: '48px', weight: 'bold' })
				.css({"text-align": "center", "color": "green"})
				.lockXY();
			*/
		} else {
			this.running = true;
		}
		
		if (!this.running) {
			this.endTime = Date.now();
			this.instanceEntities.barco.moveLateral(0);
			this.instanceEntities.motor.animate("Stopped", 1);
			this.instanceEntities.braco.moveVertical(0, false);
			this.instanceEntities.braco.animate("Stopped", 1);
			this.postFinishMessage(finishState);
		}
		
		// Atualiza o visual da palavra
		add = ((this.config.levelWord.length > this.levelString.length) ? "_".repeat(this.config.levelWord.length - this.levelString.length) : "");
		this.wordSelector.html(this.levelString + add);
	}
	
	/**
	 * Mostra uma mensagem para o jogador
	 */
	this.showMessage = function(type, title, message, buttonLabel, callback) {
		if (buttonLabel == undefined) 
			buttonLabel = 'Ok';
		
		switch (type){
		case 'error':
			if (title == undefined)
				title = 'Erro! ):';
			
			type = BootstrapDialog.TYPE_DANGER;
			break;
		case 'success':
			if (title == undefined)
				title = 'Sucesso! (:';
			type = BootstrapDialog.TYPE_SUCCESS;
			break;
		case 'info':
		default:
			if (title == undefined)
				title = 'Informação';
			type = BootstrapDialog.TYPE_INFO;
			break;
		}
		
		buttonConfig = { 
			id: 'btn', 
			label: buttonLabel,
			action: function(dialogRef) {
				if (callback != undefined) 
					callback(dialogRef);
				
				dialogRef.close();
			}
		};
		
		return BootstrapDialog.show({
			type: type,
			title: title,
			message: message,
			closable: false,
			buttons: [buttonConfig]
		});     
	}
	
	/**
	 * Processa o final de jogo
	 * @param Integer state Se 1, jogador perdeu. Se 2, jogador ganhou.
	 */
	this.postFinishMessage = function(state) {
		// Se perdeu
		if (state == 1) {
			// Mostra o botão de tentar novamente
			/*
			$("#ActionBtn")
				.css('display', 'block')
				.html('Tentar novamente')
				.click(function() { window.location.reload() });
			*/
			dialog = this.showMessage('error', undefined, 'Você não conseguiu! Tente novamente.', 'Tentar novamente', function() { 
				window.location.reload() 
			});
			
			if (this.session) {
				button = dialog.getButton('btn');
				button.disable();
				button.spin();
				
				Score.registerGameEnd(this.session, function() {
					button.stopSpin();
					button.enable();
				});
			}
			
		// Caso contrário
		} else {
			// Mostra o botão de continuar
			plural = function(n) { return (n == 1 ? '' : 's') }
			secs = (_this.endTime - _this.startTime);
			secs    = Math.floor(secs/1000);
			hours   = parseInt(secs / 60 / 60);
			minutes = parseInt((secs / 60) % 60);
			secs    = parseInt(secs % 60);
			msg = "Você terminou em " +
				(hours > 0 ? (hours + ' hora' + plural(hours) + ', ') : "") +
				(minutes > 0 ? (minutes + ' minuto' + plural(minutes) + ', ') : '') +
				(secs > 0 ? (secs + ' segundo' + plural(secs)) : '') +
				"!";
			
			dialog = this.showMessage('success', undefined, msg, (this.session ? 'Próximo nível' : 'Continuar jogando'), function() {
				window.location.reload()
			});
			
			if (this.session) {
				button = dialog.getButton('btn');
				button.disable();
				button.spin();
				
				footerMessage = $("<span class='pull-left'>")
					.html("Seu resultado está sendo computado...")
					.css({
						'display':'inline-block',
						'vertical-align':'middle',
						'line-height':'35px'
					});
				footer = dialog.getModalFooter();
				footer.find(".bootstrap-dialog-footer").prepend(footerMessage);
			
				Score.registerGameEnd(this.session, function() {
					button.stopSpin();
					button.enable();
					footerMessage.remove();
				});
			}
		}
	};
	
	/**
	 * Inicialização do jogo
	 */
	this.config = config;
	this.init();
};