<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>注册</title>
    <script src="<?php echo base_url('public')?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>

<div id="container">
	<h1>注册</h1>

	<div id="body">
	     <label>用户名:</label>
	     <input type="text" id="username" name="username">
	     </br>
	     <label>密码:</label>
	     <input type="password" id="password" name="password">
	     </br>
	     <label>昵称:</label>
	     <input type="text" id="nickname" name="nickname">  
	     <input type="button" id="regist" value="注册">
	     <a href="<?php echo base_url('site/index')?>">登录</a>
	</div>

</div>


<script type="text/javascript">

$('#regist').click(function(){
    data = {};
    data.username = $("#username").val();
    data.password = $("#password").val();
    data.nickname = $("#nickname").val();
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: '<?php echo base_url('site/ajaxRegist') ?>',
      data: data,
      success: function(res) {
        if(res.code == 1)
        {
      	    alert("注册成功");
        	window.location.href="<?php echo base_url('site/chatRoom')?>";
            return true;
        }
        else
        {
        	alert("注册失败");
        	return false;
        }
      }
    })
});
</script>
</body>
</html>