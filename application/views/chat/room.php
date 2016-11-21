<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>聊天群</title>
    <script src="<?php echo base_url('public')?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
<div>
    <div id="msg"></div>
    <input type="text" id="text">
    <input type="submit" value="发送数据">
<div>
        <div id="gameRange">
        </div>
</body>
<script>
$(document).ready(function(){
	var movePx = 10;
	var myId = "<?php echo $_SESSION['chatRoom']['id']?>";
	if(myId == "")myId = -1;
	var name = "<?php echo $_SESSION['chatRoom']['nickname']?>";
	if(name == "")name = "无名氏";
	var X,Y;
	
	document.onkeydown=keyDownSearch;  
	   //鼠标监听
	   function keyDownSearch(e) {  
	       // 兼容FF和IE和Opera    
	       var theEvent = e || window.event;    
	       var code = theEvent.keyCode || theEvent.which || theEvent.charCode;    
	       if (code == 13) {//回车事件 
	    	   var text = document.getElementById('text').value;
	    	   text = name+" 说:"+text;
	    	   song(text);//具体处理函数   
	    	   var data = '{"message":"'+text+'"}';
	    	   sendPackage(1,data); 
	           return false;    
	       }else if (code == 37)//左键盘点击事件
	       {
		       if(Y>movePx){
		    	   Y -= movePx;
			   }
	    	   sendMyPostion(myId,X,Y);
	    	   setPostion(myId,X,Y);
	       }else if(code==38)//上键盘点击事件
	       {
	    	   if(X>movePx){
	    		   X -= movePx;
			   }
	    	   sendMyPostion(myId,X,Y);
	    	   setPostion(myId,X,Y);
	       }else if (code==39)//右键盘点击事件
	       {
	    	   Y += movePx;
	    	   sendMyPostion(myId,X,Y);
	    	   setPostion(myId,X,Y);
	       }else if (code==40)//下键盘点击事件
	       {
	    	   X += movePx;
	    	   sendMyPostion(myId,X,Y);
	    	   setPostion(myId,X,Y);
	       }
	       return true;    
	   } 


	   var msg = document.getElementById("msg");
	    //var wsServer = 'ws://10.10.20.128:9500';
	    var websocket = new WebSocket("ws://10.10.20.128:9503");
	    websocket.onopen = function(e) {
		    
	        //用户连接服务器
	        //{"name":1,"age":2,"info":{"heigth":170,"weigth":140}}
	        //console.log("10.10.20.128:9503 Connection open...");
	    }; 

	    //监听连接关闭
	    websocket.onclose = function (evt) {
	        console.log("Disconnected");
	    };

	     //onmessage 监听服务器数据推送
	    websocket.onmessage = function (evt) {
	    	acceptPackage(evt.data);
	        //msg.innerHTML += evt.data +'<br>';
	        console.log('Retrieved data from server: ' + evt.data);
	     };
	     
	    //监听连接错误信息
	    websocket.onerror = function (evt, e) {
	        console.log('Error occured: ' + evt.data);
	    };


    function song(text){
       text = "<?php echo isset($_SESSION['chatRoom']['nickname']) ? $_SESSION['chatRoom']['nickname'] : "无名氏  "?>说:"+text;
       document.getElementById('text').value = '';
       // 向服务器发送数据
       websocket.send(text);
    }

    //发送自己的坐标给服务器
    function sendMyPostion(id,X,Y)
    {
 	   var data = '{"id":'+id+',"X":'+X+',"Y":'+Y+'}';
  	   sendPackage(3,data);
    } 

   //设置坐标
   function setPostion(id,X,Y)
   {
	   $("#role_"+id).offset({ top: X, left: Y});
   }
   
   

   //接受服务器上所有用户的坐标 解析所有用户的位置 
   //var jsonObj = [{"id":"id1","X":"男1","Y":"1001"},{"id":"id2","X":"男2","Y":"1002"},{"id":"id3","X":"男3","Y":"1003"}];
   function acceptPostion(jsonObj)
   {
	   $.each(jsonObj,function() {
		   setPostion(this.id,this.X,this.Y);
	   });
   }

   //用户解析包
   function acceptPackage(Obj)
   {
	   console.log(Obj);
	   Obj = JSON.parse(Obj);
	   var data = Obj.data;
	   switch(Obj.protocol)
	   {
	     case 1://说话
	    	 msg.innerHTML += data.message +'<br>'; 
	         break;
	     case 2://有新用户连线了
	    	 if(myId == data.id){
	    		 $("#gameRange").append('<img  id="role_'+data.id+'" src="<?php echo base_url('public')?>/img/icon/red_circle.png" style="position: relative"/>');
		    	  X = $("#role_"+myId).offset().top;//x轴的坐标
		          Y = $('#role_'+myId).offset().left;//y轴的坐标     
			 }else{
				 $("#gameRange").append('<img  id="role_'+data.id+'" src="<?php echo base_url('public')?>/img/icon/green_circle.png" style="position: relative"/>');
		     }
	    	 
	    	 setPostion(data.id,data.X,data.Y);
		     msg.innerHTML += data.name+'用户上线了<br>';
		     
		     break;
	     case 4://获取游戏所有用户的位置
	    	 //acceptPostion(data);
	    	 setPostion(data.id,data.X,data.Y);
		     break;
	     case 5:
		     $("#role_"+data.id).remove();
	    	 msg.innerHTML += data.name+'用户离线了<br>'; 
	    	 break;
	     case 11://用户连线成功  把用户id和名称传给服务器
	    	 var data = '{"id":"'+myId+'","name":"'+name+'"}';
		     sendPackage(2,data);
		     break;
	   default:
	         alert("接受了未知的数据，请联系管理员");
	   }
   }

   //发包给服务器
   //protocol 1说话  2用户游戏1连线  3游戏1发送自己的位置  4游戏1获取所有用户其他的位置  5用户离线
   function sendPackage(protocol,data)
   {
	   var obj = JSON.stringify('{"protocol":"'+protocol+'","data":'+data+'}');
	   console.log(obj);
	   websocket.send(obj);
   }

   //获取所有用户位置信息
   function getAllUserPosition()
   {
	   var data = '{"id":"'+myId+'"}';
	   sendPackage(12,data);
   }
   
   //定时按每秒？次发送自己的坐标
   //alert(myId+"~~~"+X+"~~~"+Y);
   
//    setInterval(function () { 
// 	   sendMyPostion(myId,X,Y);
// 	   }, 500);

});

</script>
</html>