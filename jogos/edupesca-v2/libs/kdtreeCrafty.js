Crafty.c("Peixe", {
    objs: [], 
	destino: [], 
	
	init: function () {
        this.requires("2D");

        function loaded(e) {
            if (e == "photo") 
				$("#cr").css("padding-top", "500px");
        }
		
        var img = $('<img id="photo"  onload="loaded(this.id)" width="1" height="1">');
        img.attr('src', 'http://www.vistosamerica.com.br/pescaEnsino/mad.gif');
        img.appendTo('#cr');
        var o = this;
		
        this.bind("EnterFrame", function () {
            o.renderObject()
        })
    }, 
	
	KDtree: function (o) {
        this.objs.push(o);
        var e = this.objs.length - 1;
        this.objs[e].crafty = this
		this.destino[e] = this.findNearest(this.objs[e])
		this.sprite(1, e)
    }, 
	
	sprite: function () {}, 
		
	distance: function (o, e) {
        var t = o.x - e.x, r = o.y - e.y;
        return t * t + r * r
    }, 
	
	findNearest: function (o) {
        var e = this;
        server.foods.food.sort(function (t, r) {
            return e.distance(t, o) - e.distance(r, o)
        });
        var t = server.foods.food.slice(0, 1);
        return t
    }, 
	
	renderObject: function () {
        for (var o = 0; o < this.objs.length; o++) {
			this.nadar(this.destino[o][0], this.objs[o], o)
			this.getInsertIsca(this.objs[o])
		}
    }, 
	
	nadar: function (o, e, t) {
        var 
			r = e.x / PTM_RATIO, 
			i = e.y / PTM_RATIO, 
			a = p_x2 = e.crafty.body.GetPosition().x, 
			s = p_y2 = e.crafty.body.GetPosition().y, 
			n = new b2Vec2(o.x / PTM_RATIO, o.y / PTM_RATIO), 
			d = e.crafty.body.GetAngle();
		
        if (s.toFixed(0) < 14)
			s = 0;
		
        var c = {
                x: (a - n.x), y: (s - n.y)
            }, 
			y = Math.atan2(-c.x, c.y), 
			f = c.x - c.y, 
			h = 10 * Math.random(1), 
			l = 10 * Math.random(1);
			
        if (Crafty.anzol) {
            var 
				p = new b2Vec2(Crafty.anzol.x / PTM_RATIO, Crafty.anzol.y / PTM_RATIO), 
				x = {
					x: (p_x2 - p.x), y: (p_y2 - p.y)
                };
            
			this.panico(e, x, t);
            var u = this.mordida(p, t, e, x, h, l);
            if (u) return
        }
		
        if (f >= -1 && 0 >= f && o.mode) {
			o.mode = !1;
			
			server.foods.food = _.reject(server.foods.food, function (o) {
					return 0 == o.mode
			})
			
			server.createFood()
			if (this.peixeParado(h, l)) 
				this.destino[t] = this.findNearest(e)
			
			e.modeRote = [];
		}
			
		if (e.modeRote[0]) {
			if (!e.modeRote[3])  e.modeRote[3] = 0
			e.modeRote[3]++	
		}
					
		if (e.modeRote[0] && e.modeRote[3] > e.comport.toFixed()) {
			//if (server.foods.food.length < 50) 
			//	server.createFood();
			
			var v = this;
			server.foods.food = _.reject(server.foods.food, function (o) {
				return o.id == v.destino[t][0].id
			});
			
			this.destino[t] = this.findNearest(e);
			e.modeRote = [];
		}
		
        e.modeRote[0] = f;
        
		for (var m = y - d; m < -180 * PTM_RATIO;) 
			m += 360 * PTM_RATIO;
		
        for (; m > 180 * PTM_RATIO;) 
			m -= 360 * PTM_RATIO;
		
        var b = Math.atan2(c.y, c.x);
        r = -1 * Math.cos(b)
		i = -1 * Math.sin(b)
		e.crafty.body.SetAngularVelocity(0);
		
        var T = new b2Vec2(0, 1 * e.crafty.body.GetMass());
        
		e.crafty.body.ApplyForce(T, e.crafty.body.GetWorldCenter())
		e.crafty.body.SetLinearVelocity(new b2Vec2(r, i));
        
		e.crafty.body.GetAngle()
    }
    , spriteFlip: function (o, e, t, r) {
        var i = 0 > o ? 1 : -1;
        if (e.flip != i && 0 != r.toFixed() && 1 != r.toFixed()) {
            this.sprite(i, t);
            e.flip = i
        }
    }
    , peixeParado: function (o, e) {
        return o.toFixed() == e.toFixed() ? !1 : !0
    }
    , panico: function (o, e) {
        e.x.toFixed(0) != -0 && 0 != e.x.toFixed(0) || 1 != e.y.toFixed(0) && 0 != e.y.toFixed(0) && e.y.toFixed(0) != -0 ? o.panic[0] = 0 : -1 == player.rolo.toFixed(0) && Crafty.anzol._y.toFixed(0) < 250 && server.win_pesca(o)
    }
    , minhoca: function () {
        player.isca.mord--, 0 == player.isca.mord ? (Crafty.anzol.__coord[0] = 0, player.isca.quant--) : player.isca.mord > 0 && (player.isca.mord < 20 && player.isca.mord > 10 ? Crafty.anzol.__coord[0] = 52 : player.isca.mord < 5 && player.isca.mord > 0 && (Crafty.anzol.__coord[0] = 78))
    }
    , mordida: function (o, e, t, r, i, a) {
        var s = i.toFixed() == a.toFixed() ? !1 : !0;
        return 1 == r.x.toFixed(0) && 0 == r.y.toFixed(0) && s && player.isca.mord > 0 ? (this.minhoca(), t.crafty.body.SetPosition(new b2Vec2(o.x, o.y)), !0) : void 0
    }
    , getInsertIsca: function () {
        if (player.isca.sub > 5e3 && player.isca.mord > 0 && player.isca.quant > 0) {
            if (player.isca.sub = 0, player.isca.pont >= 0 && Crafty.anzol) {
                var o = (1e3 * Math.random(), Crafty.anzol.y)
                    , e = Crafty.anzol.x
                    , t = {
                        id: 0
                        , mode: !0
                        , x: e
                        , y: o
                    };
                server.foods.food.push(t), server.foods.food.length > 100 && _.reject(server.foods.food, function (o) {
                    return 0 == o.id
                })
            }
            player.isca.pont--
        }
        player.isca.sub++
    }
});
