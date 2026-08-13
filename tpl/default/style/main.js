function rain(id='rain'){
	var canvas = document.getElementById(id);
	if(!canvas) return;
	var ctx = canvas.getContext('2d');
	ctx.strokeStyle='rgba(255,255,255,0.5)';
	ctx.lineWidth=1;
	function create(){
		this.width = 1;
		//初始化随机值
		this.init = function(){
			this.x = parseInt(Math.random()*canvas.width);
			this.y = -10-Math.random()*100;
			this.s = 5+Math.random()*3;
			this.height = Math.random()*50;
		}
		this.init();
		//雨线
		this.render = function(){
			ctx.beginPath();
			ctx.moveTo(this.x,this.y);
			ctx.lineTo(this.x-this.height/6,this.y+this.height);
			ctx.stroke();
			ctx.closePath();
			this.y += this.s;
			this.x -= 1;
			this.y > canvas.height+100 && this.init();
		}
	}
	var struct=[];
	for(var i=0;i<100;i++){
		struct[i] = new create();
	}
	//渲染雨
	!function run(){
		ctx.clearRect(0,0,canvas.width,canvas.height);
		struct.forEach(v=>{v.render()});
		window.requestAnimationFrame(run);
	}();
}
sx(function(){
	rain();
})