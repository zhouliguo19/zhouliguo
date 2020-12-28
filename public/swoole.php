<?php
namespace Swoole;
$n = 4;
/*for($i=0; $i<$n; $i++){
    sleep(1);
    echo microtime(true).":hello $i \n";
    
};*/


// go(function() use($n){
//     for($i=0; $i<$n; $i++){
//         Coroutine::sleep(1);
//         echo microtime(true).":hello $i \n";
//     }
// });

/*for($i=0; $i<$n; $i++){
    go(function() use ($i){
        Coroutine::sleep(1);
        echo microtime(true).":hello $i \n";
    });
}

echo "hello main \n";*/


$cnt =2000;
/*for ($i = 0;  $i < $cnt; $i++){
    $redis = new \Redis();
    $redis->connect('127.0.0.1',6379);
    $redis->auth('123456');
    
    $key = $redis->get('keys');
    
}*/


/*go(function() use($cnt){
    for($i=0; $i<$n; $i++){
        $redis = new Coroutine\Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->auth('123456');
        
        $key = $redis->get('keys');
    }
});*/


for($i=0; $i<$n; $i++){
    go(function() use ($cnt){
        $redis = new Coroutine\Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->auth('123456');
        
        $key = $redis->get('keys');
    });
}
