<?php
//定义多进程全局变量
$table = new swoole_table(1024);
$table->column('fd', swoole_table::TYPE_INT);
$table->column('id', swoole_table::TYPE_INT);
$table->column('name', swoole_table::TYPE_STRING, 64);
$table->column('X', swoole_table::TYPE_INT);
$table->column('Y', swoole_table::TYPE_INT);
$table->create();

//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("10.10.20.128", 9503);
$ws->table = $table;

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    //执行定时器
//     swoole_timer_tick(2000, function () {
//         echo "tick-2000ms\n";
//     });
    
    $data = array('protocol'=>11,'data'=>array());
    $ws->push($request->fd, json_encode($data)."\n");
});


//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $message = json_decode(jsonToArr($frame->data),true);
    //print_r($message);
    //protocol 1说话  2用户游戏连线获取信息  3游戏1发送自己的位置  4游戏1获取所有用户其他的位置  5用户离线  11用户连线成功  12获取所有在线的用户的位置
    switch ($message['protocol'])
    {
    case 1:
        $data = array('protocol'=>1,'data'=>array('message'=>$message['data']['message']));
        //echo json_encode($data);
        foreach($ws->connections as $fds)
        {
            //$ws->push($fds, "客户端9501端口".$frame->fd."说的话: ".$frame->data."\n");
            $ws->push($fds, json_encode($data)."\n");
        }
      break;  
    case 2:
        $X = rand(10,999);
        $Y = rand(10,999);
        $data = array('protocol'=>2,
            'data'=>array('id'=>$message['data']['id'],
                'name'=>$message['data']['name'],
                'X'=>$X,
                'Y'=>$Y,
            ));
        //保存到全局table里面
        $ws->table->set($frame->fd, array('id'=>$message['data']['id'], 'name'=>$message['data']['name'], "X"=>$X, "Y"=>$Y));
        //1告诉所有人我上线了
        foreach($ws->connections as $fds)
        {
            $ws->push($fds, json_encode($data)."\n");
        }
        
        //获取在线的其他人
        foreach($ws->connections as $fds)
        {
            if($fds != $frame->fd){
                $ret = $ws->table->get($fds);
                $data1 = array('protocol'=>2,
                    'data'=>array('id'=>$ret['id'],
                        'name'=>$ret['name'],
                        'X'=>$ret['X'],
                        'Y'=>$ret['Y'],
                    ));
                $ws->push($frame->fd, json_encode($data1)."\n");
            }
        }
      break;
    case 3:
        $ws->table->set($frame->fd, array('id'=>$message['data']['id'], "X"=>$message['data']['X'], "Y"=>$message['data']['Y']));
        //$ret = $ws->table->get($frame->fd);
        $data = array('protocol'=>4,'data'=>array('id'=>$message['data']['id'], "X"=>$message['data']['X'], "Y"=>$message['data']['Y']));
        foreach($ws->connections as $fds)
        {
            //$ws->push($fds, "客户端9501端口".$frame->fd."说的话: ".$frame->data."\n");
            $ws->push($fds, json_encode($data)."\n");
        }
      break;
    case 5:
      break;
    default:
    }
    
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    //echo "client-{$fd} is closed\n";
    $ret = $ws->table->get($fd);
    $data = array('protocol'=>5,'data'=>array('id'=>$ret['id'],'name'=>$ret['name']));
    $ws->table->del($fd);
    print_r($data);
    //告诉所有人这个用户离开了
    foreach($ws->connections as $fds)
    {
        //$ws->push($fds, "客户端9501端口".$frame->fd."说的话: ".$frame->data."\n");
        $ws->push($fds, json_encode($data)."\n");
    }
});

//客户端传过来的json转化成数组
function jsonToArr($jsonData){
    $jsonData = str_replace("\\","",$jsonData);
    $jsonData=substr($jsonData,1,strlen($jsonData)-2);
    return $jsonData;
}

$ws->start();