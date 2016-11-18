<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>聊天群</title>
</head>
<body>
<div id="msg"></div>
<input type="text" id="text">
<input type="submit" value="发送数据" onclick="song()">
</body>
<script>
    var msg = document.getElementById("msg");
    //var wsServer = 'ws://10.10.20.128:9500';
    var websocket = new WebSocket("ws://10.10.20.128:9503");
    websocket.onopen = function(e) {
        console.log("10.10.20.128:9503 Connection open...");
    };
    //调用websocket对象建立连接：
    //参数：ws/wss(加密)：//ip:port （字符串）
    //var websocket = new WebSocket(wsServer);
    //onopen监听连接打开
    //websocket.onopen = function (evt) {
        //websocket.readyState 属性：
        /*
        CONNECTING    0    The connection is not yet open.
        OPEN    1    The connection is open and ready to communicate.
        CLOSING    2    The connection is in the process of closing.
        CLOSED    3    The connection is closed or couldn't be opened.
        */
    	//console.log(websocket.protocol);
        //msg.innerHTML = websocket.readyState;
    //};

    function song(){
       var text = document.getElementById('text').value;
       document.getElementById('text').value = '';
       // 向服务器发送数据
       websocket.send(text);
   }
      //监听连接关闭
   websocket.onclose = function (evt) {
       console.log("Disconnected");
   };

    //onmessage 监听服务器数据推送
   websocket.onmessage = function (evt) {
       msg.innerHTML += evt.data +'<br>';
       console.log('Retrieved data from server: ' + evt.data);
    };
//监听连接错误信息
   websocket.onerror = function (evt, e) {
       console.log('Error occured: ' + evt.data);
   };



   document.onkeydown=keyDownSearch;  
   
   function keyDownSearch(e) {    
       // 兼容FF和IE和Opera    
       var theEvent = e || window.event;    
       var code = theEvent.keyCode || theEvent.which || theEvent.charCode;    
       if (code == 13) {    
    	   song();//具体处理函数    
           return false;    
       }    
       return true;    
   }  
</script>
</html>