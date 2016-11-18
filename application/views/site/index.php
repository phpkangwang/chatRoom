<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>登录</title>
    <script src="<?php echo base_url('public')?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>

<div id="container">
	<h1>登录</h1>

	<div id="body">
	     <label>用户名:</label>
	     <input type="text" id="username" name="username">
	     </br>
	     <label>密码:</label>
	     <input type="password" id="password" name="password"> 
	     <input type="button" id="login" value="登录">
	     <a href="<?php echo base_url('site/regist')?>">注册</a>
	</div>

</div>

</body>

<script type="text/javascript">

$('#login').click(function(){
    data = {};
    data.username = $("#username").val();
    data.password = $("#password").val();
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: '<?php echo base_url('site/login') ?>',
      data: data,
      success: function(res) {
        if(res.code == 1)
        {
      	    alert("登录成功");
        	window.location.href="<?php echo base_url('site/chatRoom')?>";
            return true;
        }
        else
        {
        	alert("登录失败");
        	return false;
        }
      }
    })
});
</script>
</html>