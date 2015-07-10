<?php
//header("Content-Type: text/html; charset=UTF-8",true)

	if(!@mysql_connect("localhost","jogos","jogos@aprendizado")) {
		echo "<h2>Error</h2>";
		die();
	}
	mysql_select_db("jogo_edupesca");

	//mysql_set_charset('utf8');
	mysql_query('SET CHARACTER SET utf8');
	$result=mysql_query("SELECT id, mensagem, palavras, palpesca, tempo, imagem FROM gamepesca ORDER BY ordem;") or die(mysql_error());
	
    $result2=mysql_query("SELECT score, nivelGamepesca FROM user;");
	
	function raw_json_encode($input) {
		return preg_replace_callback(
			'/\\\\u([0-9a-zA-Z]{4})/',
			function ($matches) {
				return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
			},
			json_encode($input)
		);
	}

	$i=0;
	$palavra = [];
	$pImagem = [];
	$palPesca = []; 
	$pInformacao = [];
	$tempo = 0;
	$nivel = 0;
	$score = 0;
	
	while ($row=mysql_fetch_array($result2)) {
		$nivel = $row['nivelGamepesca'];
		$score = $row['score'];
	}
	
	while ($row=mysql_fetch_array($result)) {
		$palavra[$i] = $row['palavras'];
		$pImagem[$i] = htmlspecialchars($row['imagem']);
		$palPesca[$i] = htmlspecialchars($row['palpesca']);
		$pInformacao[$i] = htmlspecialchars($row['mensagem']);
		$tempo = $row['tempo'];
		$i++;
	}
	
	$arr = array(
		'palavra' => $palavra, 
		'pImagem' => $pImagem, 
		'informacao' => $pInformacao, 
		'pTotal' => $palPesca, 
		'tempo' => $tempo, 
		'score' => $score, 
		'nivel' => $nivel
	);
?> 

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>EduPesca | Aprendizado Online</title>
		<meta name="description" content=".">
		<meta name="author" content="Israel Gonçalves, Gabriel Teles, Aprendizado Online">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css"/> 
		<script src="libs/Box2dWeb-2.1.a.4.js"></script> 
		<script src="libs/crafty.js"></script> 
		<script src="libs/jquery-1.7.2.min.js" charset="utf-8"></script> 
		<script src="libs/jquery.lettering.js"></script> 
		<script src="libs/underscore-min.js"> </script> 
		<script src="libs/box2d.js"> </script> 
		<script src="libs/kdtreeCrafty.js"> </script> 
	</head> 
	<body>
		<script>
			// Adicional
			var
				// Define o movimento das nuvens em [Ponto inicial, Velocidade]
				movimentoNuvens = [[-453,0.2], [-619,0.1], [-708,0.2]];
			
			// ORIGINAL
			var world, 
				PTM_RATIO = 30,
				w = 2100,
				h = 625,
				left, right = !0,
				ePl = [],
				score = 0,
				nivel = 0,
				tempo = 0,
				stopTempo = false;
				window.onload = function() {
					gameInit()
				}, 
				gameInit = function() {
					Crafty.init(w, h), 
					Crafty.box2D.init(0, 9.5, PTM_RATIO, !0), 
					world = Crafty.box2D.world, 
					Crafty.scene("main", function() {
						server.startFood(), 
						generateWorld()
					}), 
					Crafty.scene("main")
				}, 
				generateWorld = function() {
					function e(e) {
						e ? (o.animate("motor", 0, 0, 1), o.animate("motor", 5, -1)) : (o.animate("motor", 0, 1, 0), o.animate("motor", 5, 0))
					};

					function r(e) {
						e ? (n.animate("braco", 0, 0, 1), n.animate("braco", 5, -1)) : (n.animate("braco", 0, 0, 0), n.animate("braco", 5, 0))
					};
    
					Crafty.box2D.buoyancy(), Crafty.box2D.listenForContact(), Crafty.e("2D, DOM, Box2D").attr({
						x: 0,
						y: 0
					}).box2d({
						bodyType: "static",
						density: 1,
						friction: 10,
						restitution: 0,
						groupIndex: -2,
						shape: [
							[0, h - 10],
							[w, h - 10],
							[w, h],
							[0, h]
						]
					}), 
					
					Crafty.e("2D, DOM, Box2D").attr({
						x: 0,
						y: 0,
						w: 1074,
						h: 534,
						z: 13
					}).css({
						margin: "230px 0 0 -20px",
						background: "url('images/encosta1.png') 0 0 no-repeat"
					}).box2d({
						bodyType: "static",
						density: 1,
						friction: 10,
						restitution: 0,
						shape: [
							[0, 200],
							[776, 200],
							[776, 600],
							[0, 600]
						]
					}), 
					
					Crafty.e("2D, DOM, Box2D").attr({
						x: 776,
						y: 0,
						w: 236,
						h: 175,
						z: 11
					}).css({
						margin: "245px 0 0 20px",
						background: "url('images/pier.png') 0 0 no-repeat"
					}).box2d({
						bodyType: "static",
						density: 1,
						friction: 10,
						restitution: 0,
						groupIndex: -2,
						shape: [
							[0, 204],
							[234, 204],
							[5, 400],
							[0, 400]
						]
					}), 
					
					Crafty.e("2D, DOM, Box2D").attr({
						x: 0,
						y: 0,
						w: 400,
						h: 526,
						z: 14
					}).css({
						margin: "245px 0 0 1935px",
						background: "url('images/encosta2.png') 0 0 no-repeat"
					}).box2d({
						bodyType: "static",
						density: 1,
						friction: 10,
						restitution: 0,
						shape: [
							[2130, 200],
							[2460, 200],
							[2460, 600],
							[2130, 600]
						]
					}), 
					
					Crafty.e("2D, DOM").attr({
						x: 770,
						y: 0,
						w: 1407,
						h: 400,
						z: 12
					}).css({
						margin: "342px 0 0 0",
						background: "url('images/r2b.png') 0 0 repeat"
					}), 
					
					Crafty.sprite(1525, server.agua.altura, server.agua.imagem, server.agua.mapa), 
					
					Crafty.e("2D, DOM, SpriteAnimation, Box2D, agua").attr({
						x: 770,
						y: 0,
						z: 12
					}).css({
						margin: "260px 0 0 0"
					}).animate("agua", 0, 0, 3).animate("agua", 30, -1).sprite(0, 100, 0, 0).box2d({
						bodyType: "static",
						sensor: !0,
						shape: [
							[0, h / 3 + 40],
							[w, h / 3 + 40],
							[w, h],
							[0, h]
						]
					}), 
					
					Crafty.sol = Crafty.e("2D, DOM").attr({
						w: 251,
						h: 232,
						x: 700,
						y: 50,
						z: 1
					}).css({
						background: "url('images/raios.png') 0 0 repeat"
					}), 
					
					Crafty.e("2D, DOM").attr({
						w: 2328,
						h: 400,
						x: 0,
						y: -200,
						z: 0
					}).css({
						background: "url('images/c2b.jpg') 0 0 repeat"
					}), 
					
					Crafty.nuvens = [
						Crafty.e("2D, DOM").attr({
							w: 453, h: 183, x: 100, y: -30, z: 1
						}).css({
							background: "url('images/n1.png') 0 0 no-repeat"
						}), 
					
						Crafty.e("2D, DOM").attr({
							w: 619, h: 228, x: 600, y: -100, z: 1
						}).css({
							background: "url('images/n2.png') 0 0 no-repeat"
						}), 
					
						Crafty.e("2D, DOM").attr({
							w: 708, h: 178, x: 1900, y: -50, z: 1
						}).css({
							background: "url('images/n3.png') 0 0 no-repeat"
						}), 
					],
					
					Crafty.sprite(server.nMsg.largura, server.nMsg.altura, server.nMsg.imagem, server.nMsg.mapa), 
					
					Crafty.nMsg = Crafty.e("2D, DOM, SpriteAnimation, nMsg").attr({
						x: 2000,
						y: -185,
						z: 4
					}), 
					
					Crafty.e("2D, DOM").css({
						background: "url('images/campo.png') 0 0 no-repeat"
					}).attr({
						x: 0,
						y: 110,
						w: 2463,
						h: 180,
						z: 2
					}), 
					
					//$("#ent132").html('<div id="about" style="margin:180px 0 0 230px; z-index:12;"><h3 class="main-heading"><div><img src="images/logo.png"/></span><span><p style="font-size:14px; text-align: center" id="idInf"></p></span></div><div id="gameinfo" > <img style="margin:0 0 0 10px;float:left;" id="idMg" src="" with="50" height="50"> <span style="font-size:52px; float:left; color:red; margin-top: -50px" id="idEpl"></span> <span id="gamevitoria" style="display:none; width:450px; float:left;"><h2 style="color:green; margin-left:20px;   margin-top: 0px;">Parabéns você conseguiu!<br><a style="font-size:18px;" href=javascript:location.reload(); >Próximo</a></h2></span><span id="gamederrota" style="display:none; width:550px; float:left; margin-top: -40px"><h2 style="color:red; margin-left:20px;   margin-top: 0;">Não conseguiu, tente novamente! <br> <a style="font-size:18px;" href=javascript:location.reload(); >Repetir</a></h2></span></div> <div style="position:absolute; right:0px;top:200px; font-size:21px;">Pontuação: ' + server.bd[0].score + '</br>Nivel: ' + server.bd[0].nivel + '</br>Tempo: <b id="idTempo">0</b></div>'), 
					$("#ent132").html('<div id="about" style="margin:180px 0 0 230px; z-index:12;"><h3 class="main-heading"><div class="info"><p style="font-size:14px; text-align: center" id="idInf"></p></div><div id="gameinfo"> <img style="margin:0 0 0 10px;" id="idMg" src="" with="50" height="50"> <span style="font-size:52px; color:red;" id="idEpl"></span> <span id="gamevitoria" style="display:none; margin-top: -10px; margin-left: 5px;"><h2 style="color:green; margin-left:20px;   margin-top: -10px;">Parabéns você conseguiu!<br><a style="font-size:18px;" href=javascript:location.reload(); >Próximo</a></h2></span><span id="gamederrota" style="display:none; margin-top: -10px; margin-left: 5px;"><h2 style="color:red; margin-left:20px;   margin-top: 0;">Não conseguiu, tente novamente! <br> <a style="font-size:18px;" href=javascript:location.reload(); >Repetir</a></h2></span></div> <div style="position:absolute; right:0px;top:200px; font-size:21px;">Pontuação: ' + server.bd[0].score + '</br>Nivel: ' + server.bd[0].nivel + '</br>Tempo: <b id="idTempo">0</b></div>'), 
					
					Crafty.nMsg.animate("nMsgb", 0, 0, 3), 
					
					Crafty.nMsg.animate("nMsgb", 10, 0);
					
					var a = Crafty.e("2D, DOM").attr({
							x: -350,
							y: -150,
							w: 200,
							h: 59
						}).css({
							background: "url('images/airplane.png') 0 0 no-repeat"
						});
						
					Crafty.sprite(server.barco.largura, server.barco.altura, server.barco.imagem, server.barco.mapa), 
					server.barco.crafty = Crafty.e("2D, DOM, Multiway, Keyboard, Box2D, barco_pcs").origin("center").attr({
						x: 1040,
						y: 10,
						z: 4
					}).css({
						margin: "0 0 0 -60px"
					}).box2d({
						bodyType: "dynamic",
						density: .8,
						friction: 0,
						restitution: .3,
						groupIndex: -2,
						shape: [
							[0, 0],
							[240, 0],
							[220, 40],
							[-70, 40],
							[-80, 0]
						]
					}).bind("KeyDown", function(a) {
						if (server.barco.chave && stopTempo == false) {
							if (a.keyCode == Crafty.keys.A) {
								player.moveBoat = -75//-25
								e(!0)
								left = !0
							} else if (a.keyCode == Crafty.keys.D) {
								player.moveBoat = 75//25
								e(!0)
								right = !0
							}
							
							if (a.keyCode == Crafty.keys.W) {
								player.moveVara = -1
								r(!0)
							} else if (a.keyCode == Crafty.keys.S) {
								player.moveVara = 1
								r(!0)
							}
						}
							
					}).bind("KeyUp", function(a) {
						if (a.keyCode == Crafty.keys.A) {
							player.moveBoat = 0, 
							e(!1), 
							left = !1
						} else if (a.keyCode == Crafty.keys.D) {
							player.moveBoat = 0
							e(!1)
							right = !1
						}

						if (a.keyCode == Crafty.keys.W) {
							player.moveVara = 0 
							r(!1)
						} else if (a.keyCode == Crafty.keys.S) {
							player.moveVara = 0,
							r(!1)
						}
					}), 
					
					Crafty.addEvent(this, "mousedown", function(r) {
						if (server.barco.chave && stopTempo == false) {
							player.scroll = $(window).scrollLeft();
							var a = server.barco.crafty._x - player.scroll - r.clientX;
							player.mousePos = r.clientX, 0 > a ? (player.moveBoat = 25, e(!0), right = !0, left = !1) : (player.moveBoat = -25, e(!0), left = !0, right = !1)
						} else {
							var a = server.karl._x - $(window).scrollLeft() - r.clientX;
							player.mousePos = r.clientX, 0 > a ? (server.karl.animate("PlayerMovingStopRight", 0), right = !0, left = !1) : (server.karl.animate("PlayerMovingStopLeft", 0), left = !0, right = !1)
						}
					}), 
					
					Crafty.addEvent(this, "mouseup", function() {
						server.barco.chave || (left ? server.karl.animate("PlayerMovingLeft", 25, -1) : server.karl.animate("PlayerMovingRight", 25, -1))
					}); 
					
					{
						var t = Crafty.e("2D, DOM, Box2D").origin("center").attr({
							x: 300,
							y: 30,
							h: 80,
							w: 14,
							z: 4
						}).box2d({
							bodyType: "dynamic",
							density: 1.5,
							friction: 2.5,
							restitution: 1.3,
							groupIndex: -2
						});
						Crafty.box2D.revoluteJoint({
							revolute_bodyA: server.barco.crafty.body,
							revolute_bodyB: t.body,
							anchorA: [6, 1],
							anchorB: [0, 0],
							torque: 800,
							speed: -2,
							enableMotor: !0
						})
					}
					
					Crafty.sprite(server.motor.largura, server.motor.altura, server.motor.imagem, server.motor.mapa);
					var o = Crafty.e("2D, DOM, Color, Box2D, SpriteAnimation, motor").origin("center").css({
						margin: "10px 0 0 -60px"
					}).attr({
						x: 1200,
						y: 30,
						z: 4
					}).box2d({
						bodyType: "dynamic",
						density: .1,
						friction: 0,
						restitution: 0,
						shape: [
							[0, 0],
							[10, 0],
							[10, 30],
							[0, 30]
						],
						groupIndex: -2
					});
    
					Crafty.box2D.revoluteJoint({
						revolute_bodyA: o.body,
						revolute_bodyB: server.barco.crafty.body,
						anchorA: [1.6, -.2],
						anchorB: [0, 0],
						enableLimit: !0,
						lowerAngle: 0,
						upperAngle: 0
					}), 
					
					Crafty.sprite(server.karl.largura, server.karl.altura, server.karl.imagem, server.karl.mapa); 
					
					{
						var i = Crafty.e("2D, DOM, Color, Box2D, karl_pcs").origin("center").attr({
							x: 1200,
							y: 0,
							z: 4
						}).css({
							margin: "-2px 0 0 -29px"
						}).box2d({
							bodyType: "dynamic",
							density: 1,
							friction: 0,
							restitution: 0,
							groupIndex: -2,
							shape: [
								[0, 0],
								[10, 0],
								[10, 50],
								[0, 50]
							]
						});
						Crafty.box2D.revoluteJoint({
							revolute_bodyA: i.body,
							revolute_bodyB: server.barco.crafty.body,
							anchorA: [.1, 0],
							anchorB: [0, -2],
							enableLimit: !0,
							lowerAngle: 0,
							upperAngle: 0
						})
					}
					
					Crafty.sprite(server.braco.largura, server.braco.altura, server.braco.imagem, server.braco.mapa); 
					{
						var n = Crafty.e("2D, DOM, Color, SpriteAnimation, Box2D, braco_pcs").attr({
							x: 300,
							y: 30,
							z: 4
						}).origin(0, 9).css({
							margin: "-2px 0 0 -53px"
						}).box2d({
							bodyType: "dynamic",
							density: 3.5,
							friction: .3,
							restitution: .5,
							shape: [
								[0, 0],
								[50, 0],
								[50, 5],
								[0, 5]
							],
							sensor: !0,
							groupIndex: -1
						});
						Crafty.box2D.revoluteJoint({
							revolute_bodyA: server.barco.crafty.body,
							revolute_bodyB: n.body,
							anchorA: [1.4, -1],
							anchorB: [0, -.2],
							enableLimit: !0,
							lowerAngle: -45,
							upperAngle: -5.5
						})
					}
    
					Crafty.sprite(server.anzol.largura, server.anzol.altura, server.anzol.imagem, server.anzol.mapa);
					var s = Crafty.anzol = Crafty.e("2D, DOM, SpriteAnimation, Anzol, Box2D, anzol_pcs").origin(0, 0).attr({
						x: 1200,
						y: 0,
						z: 4
					}).css({
						margin: "30px 0 0 0"
					}).box2d({
						bodyType: "dynamic",
						density: 2.5,
						friction: 0,
						restitution: 0,
						groupIndex: -2,
						shape: [
							[-1, 5],
							[-3, 0],
							[-0, -0]
						]
					});
					s.addFixture({
						bodyType: "dynamic",
						density: 2.5,
						friction: 0,
						restitution: 0,
						groupIndex: -2,
						shape: [
							[-1.11491136, 5.86],
							[-.11491136, 18.86],
							[-2.11491136, 18.86],
							[-3.11491136, -.14]
						]
					}), 
					
					s.addFixture({
						bodyType: "dynamic",
						density: 2.5,
						friction: 0,
						restitution: 0,
						groupIndex: -2,
						shape: [
							[-.11491136, 18.86],
							[5.88508864, 23.86],
							[2.88508864, 24.86],
							[-2.11491136, 18.86]
						]
					}), 
					
					s.addFixture({
						bodyType: "dynamic",
						density: 2.5,
						friction: 0,
						restitution: 0,
						groupIndex: -2,
						shape: [
							[12.88508864, 10.86],
							[12.88508864, 21.86],
							[11.88508864, 20.86],
							[11.88508864, 8.86]
						]
					}), 
					
					s.addFixture({
						bodyType: "dynamic",
						density: 2.5,
						friction: 0,
						restitution: 0,
						groupIndex: -2,
						shape: [
							[12.88508864, 21.86],
							[5.88508864, 23.86],
							[11.88508864, 20.86]
						]
					});
    
					var l = Crafty.box2D.ropeJointDef({
						revolute_bodyA: n.body,
						revolute_bodyB: s.body,
						anchorA: [5, 0],
						anchorB: [0, 0],
						maxLength: player.rolo
					});
    
					server.barco.crafty.__coord[0] = 264, 
					i.__coord[0] = 0, 
					n.__coord[0] = 0, 
					
					s.css({
						margin: "-8px 0 0 -50px"
					});
					Crafty.box2D.PTM_RATIO;
					
					Crafty.bind("EnterFrame", function() {
						if (a.x > 2500) a.x = -600 
						a.x += 5
						
						_.each(movimentoNuvens, function(mov, index) {
							if (Crafty.nuvens[index].x > 2340)
								Crafty.nuvens[index].x = mov[0];
								
							Crafty.nuvens[index].x += mov[1];
						});
						
						/*Crafty.sol.x = $(window).scrollLeft() + 500*/

						if (server.boias.about || server.boias.contato || server.boias.portfolio) {
							if (server.move_c <= 200)
								server.move_c++

							Crafty.nMsg.x = $(window).scrollLeft() + (($(window).width() - Crafty.nMsg.w) * 0.75);
						}

						server.camera()
						server.player.move()
						server.barco.chave = !0

						if (player.moveBoat && (server.barco.crafty._x < 1850)) 
							server.barco.crafty.body.ApplyForce(new b2Vec2(player.moveBoat, 0), server.barco.crafty.body.GetWorldCenter())
								
						if (player.moveVara) {
							var e = player.moveVara < 0 ? -.05 : .05;
							
							if (!stopTempo) {
								player.rolo = player.rolo + e
								if (player.rolo.toFixed(0) > 0) {
									if (l.m_maxLength > 13)
										player.rolo = 13
									l.m_maxLength = player.rolo
									player.an = !1
								} else {
									if (player.rolo.toFixed(0) < -1)
										player.rolo = 0
								}
								
								n.body.ApplyTorque(player.moveVara < 0 ? -35 : 35)
							}
						}
					}), 
					
					Crafty.viewport.y = 0
					server.createFishPort()
				};
				
				var player = {
					id: null,
					peixe: [],
					mousePos: 0,
					scroll: 0,
					name: null,
					score: null,
					isca: {
						quant: 2,
						mord: 20,
						pont: 1e5,
						sub: 0
					},
					rolo: 1,
					moveBoat: 5,
					moveVara: null,
					an: !1,
					w: !1
				},
				server = {
					move_c: 200,
					cabeca: null,
					letraSoma: 0,
					ls: 0,
					lct: null,
					letra: [],
					LetraObj: [],
					bd: [ <?php echo json_encode($arr); ?> ],
					prods: {
						prod_1: {
							id: "001",
							categ: null,
							desc: "a",
							preco: "",
							density: .8,
							panic: [0, 5],
							sprite: {
								largura: 118,
								altura: 56,
								imagem: "images/a_.png",
								mapa: {
									peixe_pcs: [0, 0]
								}
							}
						},
						prod_2: {
							id: "002",
							categ: null,
							desc: "c",
							preco: "",
							density: .8,
							panic: [0, 5],
							sprite: {
								largura: 118,
								altura: 56,
								imagem: "images/c_.png",
								mapa: {
									peixe_pcs: [0, 0]
								}
							}
						},
						prod_3: {
							id: "003",
							categ: null,
							desc: "s",
							preco: "",
							density: .8,
							panic: [0, 5],
							sprite: {
								largura: 118,
								altura: 56,
								imagem: "images/s_.png",
								mapa: {
									peixe_pcs: [0, 0]
								}
							}
						},
						prod_4: {
							id: "004",
							categ: null,
							desc: "a",
							preco: "",
							density: .8,
							panic: [0, 5],
							sprite: {
								largura: 118,
								altura: 56,
								imagem: "images/a_.png",
								mapa: {
									peixe_pcs: [0, 0]
								}
							}
						},
						prod_5: {
							id: "005",
							categ: null,
							desc: "b",
							preco: "",
							density: .8,
							panic: [0, 5],
							sprite: {
								largura: 118,
								altura: 56,
								imagem: "images/b_.png",
								mapa: {
									peixe_pcs: [0, 0]
								}
							}
						},
						prod_6: {
							id: "005",
							categ: null,
							desc: "e",
							preco: "",
							density: .8,
							panic: [0, 5],
							sprite: {
								largura: 118,
								altura: 56,
								imagem: "images/e_.png",
								mapa: {
									peixe_pcs: [0, 0]
								}
							}
						}
					},
					barco: {
						crafty: null,
						chave: !1,
						speedM: 0,
						largura: 262,
						altura: 83,
						imagem: "images/barco.png",
						mapa: {
							barco_pcs: [0, 0]
						}
					},
					karl: {
						largura: 76,
						altura: 96,
						imagem: "images/karl.png",
						mapa: {
							karl_pcs: [0, 0]
						}
					},
					karl2: {
						largura: 85,
						altura: 141,
						imagem: "images/player.png",
						mapa: {
							karl: [0, 0]
						}
					},
					boias: {
						portfolio: !1,
						about: !0,
						contato: !1
					},
					braco: {
						largura: 161,
						altura: 35,
						imagem: "images/braco.png",
						mapa: {
							braco_pcs: [0, 0]
						}
					},
					anzol: {
						largura: 26,
						altura: 37,
						imagem: "images/anzol.png",
						mapa: {
							anzol_pcs: [1, 0]
						}
					},
					nMsg: {
						click: !1,
						largura: 1155,
						altura: 408,
						imagem: "images/nMsg.png",
						mapa: {
							nMsg: [0, 1]
						}
					},
					agua: {
						largura: 304,
						altura: 82,
						imagem: "images/rio.png",
						mapa: {
							agua: [0, 0]
						}
					},
					motor: {
						largura: 104,
						altura: 97,
						imagem: "images/motor.png",
						mapa: {
							motor: [0, 1]
						}
					},
					win: {
						largura: 800,
						altura: 328,
						imagem: "null",
						mapa: {
							win: [0, 0]
						}
					},
					foods: {
						food: [],
						quant: function() {
							return 20 * _.size(server.prods)
						}
					},
					iniciarGame: function() {
						if (server.bd[0].pImagem[server.bd[0].nivel] == "") 
							$("#idMg").hide();
							
						var obj;
						var c = 0;
						_.each(server.bd[0].palavra, function(t, v) {
							if (v == server.bd[0].nivel) obj = [{
								pq: [t.split('')],
								inf: server.bd[0].informacao[c],
								lq: 0,
								pn: t,
								n: c
							}];
							c++;
						});
						var e = 0;
						_.each(server.bd[0].pTotal, function(t, v) {
							if (v == server.bd[0].nivel) obj[0].lq = [t.split(',')];
							e++;
						});
						return obj[0];
					},
					createFishPort: function() {
						var obj = this.iniciarGame();
						server.LetraObj = obj;
						$("#idInf").html(server.LetraObj.inf);
						server.win_tempo();
						var e = 0,
							r = 1,
							a = 1;
						$("#idEpl").html("");
						_.each(obj.pq[0], function(t, v) {
							$("#idEpl").append(" _ ");
						});
						$("#idMg").attr({
							src: server.bd[0].pImagem[server.bd[0].nivel]
						});
						
						_.each(obj.lq[0], function(t, v) {
							var 
								o = w / (100 * r + 1),
								i = 900 + (150 * r + 1);
								1 == o.toFixed() && (r = 0, a++), 
								r++;
								
							var n = i,
								s = 30 * a + (h / 2 + 200),
								l = '<div id="peixe_' + e + '" style="display:block;" class="peixe"><div id="desc_' + e + '" class="desc"><span style="font-size:51px;color:red;">' + t + '</span></div></div>';
							player.peixe[e] = Crafty.e("2D, DOM, SpriteAnimation, Peixe_" + e + ", Box2D, Peixe").attr({
								x: n,
								y: s,
								z: 5
							}).css({
								margin: "-20px 0 0 -40px",
							}).origin(10, 0).box2d({
								bodyType: "dynamic",
								density: t.density,
								friction: .2,
								restitution: 0,
								groupIndex: -1,
								shape: [
									[32, -22],
									[32, -18],
									[3, -18],
									[3, -22]
								]
							}).addFixture({
								bodyType: "dynamic",
								density: t.density,
								friction: .5,
								restitution: 0,
								groupIndex: -1,
								shape: [
									[32, 17],
									[32, 21],
									[3, 21],
									[3, 17]
								]
							}).addFixture({
								bodyType: "dynamic",
								density: t.density,
								friction: .5,
								restitution: 0,
								groupIndex: -1,
								shape: [
									[0, 8],
									[0, -3],
									[7, -3],
									[8, 3]
								]
							}), 
							$(player.peixe[e]._element).append(l), 
							player.peixe[e].KDtree({
								id: player.peixe[e]._element.id,
								idobj: t.id,
								crafty: null,
								x: n,
								y: s,
								move: !0,
								caractere: t,
								modeRote: [],
								panic: [0, 5],
								flip: 1,
								comport: 900
							}), 
							e++
						})
					},
					startFood: function() {
						for (var e = 0; e < server.foods.quant(); e++) {
							var r = Math.random() * Crafty.viewport.width + 1000,
								a = Math.random() * Crafty.viewport.height / 2 + 300;
							Crafty.e("2D, DOM, Color").attr({
								color: "red",
								x: r,
								y: a,
								w: 10,
								h: 10
							});
							var t = {
								id: 1e3 * Math.random(),
								mode: !0,
								x: r,
								y: a
							};
							server.foods.food.push(t)
						}
					},
					createFood: function() {
						return;
						var e = Math.random() * Crafty.viewport.width + 1000,
							r = Math.random() * Crafty.viewport.height / 2 + 300;
						Crafty.e("2D, DOM, Color").attr({
							color: "red",
							x: e,
							y: r,
							w: 10,
							h: 10,
							z: 10000
						});
						var a = {
							id: 1e3 * Math.random(),
							mode: !0,
							x: e,
							y: r
						};
						server.foods.food.push(a)
					},
					win_tempo: function() {
						setInterval(function() {
							tempo = tempo + 1;
							if (tempo > server.bd[0].tempo && !stopTempo) {
								$("#idEpl").html(server.bd[0].palavra[server.bd[0].nivel]);
								$("#idMg").hide();
								$("#gamederrota").show();
								$("#idTempo").html(0);
								return;
							}
							if (!stopTempo) $("#idTempo").html(tempo);
						}, 1000);
					},
					win_pesca: function(e) {
						if (server.lct !== e.id) {
							var a = e.caractere.split("");
							
							_.each(a, function(letra, index) {
								server.letra[server.letraSoma++] = letra;
							});
							
							if (server.letra.length == server.LetraObj.pq[0].length) {
								world.DestroyBody(e.crafty.body);
								e.crafty.destroy();
								
								var palavra = "";
								for (var i = 0; i < server.letra.length; i++) {
									palavra = palavra + server.letra[i];
								}
								
								if (palavra == server.bd[0].palavra[server.bd[0].nivel]) {
									$("#idEpl").html(palavra);
									$("#idMg").hide();
									$("#gamevitoria").show();
									server.bd[0].nivel++;
									server.bd[0].score++;
									stopTempo = true;
									$.ajax({
										type: "POST",
										url: "user.php",
										data: "nivel=" + server.bd[0].nivel + "&score=" + server.bd[0].score,
										success: function(a) {
											console.log("sucess");
										}
									});
								} else {
									stopTempo = true;
									$("#idEpl").html(server.bd[0].palavra[server.bd[0].nivel]);
									$("#idMg").hide();
									$("#gamederrota").show();
								}
							}
							var c = 0;
							if (!stopTempo) {
								$("#idEpl").html("");
								_.each(server.LetraObj.pq[0], function(t, v, l) {
									if (typeof server.letra[c] !== "undefined") {
										$("#idEpl").append(' ' + server.letra[c] + ' ');
									} else {
										$("#idEpl").append(' _ ');
									}
									c++;
								});
								world.DestroyBody(e.crafty.body);
								e.crafty.destroy();
							}
							server.lct = e.id;
						}
					},
					camera: function() {
						if (server.barco.chave && (left || right)) {
							var e = server.barco.crafty._x.toFixed() - 400;
							server.barco.crafty._x.toFixed() < 1400 && $(window).scrollLeft(e), server.move_c >= 0 && !(server.boias.about || server.boias.contato || server.boias.portfolio) && (server.move_c--)
						} else if (left || right) {
							if (server.player.subir) var e = server.barco.crafty._x.toFixed() - 400;
							0 > -e && server.barco.crafty._x.toFixed() < 1400 && $(window).scrollLeft(e), server.move_c <= 200 && (console.log(server.move_c), server.move_c++)
						}
					},
					player: {
						subir: !1,
						
						criarKarl: function(e) {
							server.barco.chave = !1, 
							server.player.subir = !1, 
							
							Crafty.sprite(server.karl2.largura, server.karl2.altura, server.karl2.imagem, server.karl2.mapa), 
							
							server.karl = Crafty.e("2D, DOM, SpriteAnimation, Box2D, karl, Movable, Twoway, Mouse, Keyboard").attr({
								x: e,
								y: 130,
								z: 15
							}).css({
								margin: "-20px 0 0 0"
							}).box2d({
								bodyType: "dynamic",
								density: 2,
								friction: .8,
								restitution: 0,
								shape: [
									[60, -25],
									[60, 54],
									[25, 60],
									[-10, 54],
									[-10, -25]
								]
							})
							.twoway(3)
							.animate("PlayerMovingRight", 0, 0, 7)
							.animate("PlayerMovingLeft", 0, 1, 7)
							.animate("PlayerMovingStopRight", 1, 0, 1)
							.animate("PlayerMovingStopLeft", 6, 1, 1), 
							
							server.karl.bind("KeyDown", function(e) {
								if (left && right) {
									console.log("stop")
									server.karl.animate("PlayerMovingStopRight", 0)
								} else {
									if (e.keyCode === Crafty.keys.A)
										server.karl.animate("PlayerMovingLeft", 25, -1)
									
									left = !0
									
									if (e.keyCode === Crafty.keys.D)
										server.karl.animate("PlayerMovingRight", 25, -1)
										
									right = !0
								
								}
								
							}).bind("KeyUp", function(e) {
								e.keyCode === Crafty.keys.A && (server.karl.animate("PlayerMovingStopLeft", 0), left = !1), e.keyCode === Crafty.keys.D && (right = !1, server.karl.animate("PlayerMovingStopRight", 0))
							}), 
							
							server.karl.body.SetFixedRotation(!0)
						},
						
						move: function() {
							if (!server.barco.chave && stopTempo == false) {
								if (left && right) return void server.karl.animate("PlayerMovingStopRight", 0);
								if (0 != player.mousePos) {
									var e = server.karl._x - $(window).scrollLeft() - player.mousePos;
									if (e.toFixed() > 0 && right) 
										return server.karl.animate("PlayerMovingStopRight", 0), void(player.mousePos = 0);
										
									if (e.toFixed() < 0 && left) return server.karl.animate("PlayerMovingStopLeft", 0), void(player.mousePos = 0)
								}
							}
						}
					}
				};
		</script>
		<div id="cr_"></div>
	</body>
</html> 