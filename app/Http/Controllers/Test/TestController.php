<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class TestController extends Controller
{

    //测试
    public function test(){
      // dump($_SERVER);
        $url=$_SERVER['REQUEST_URI'];
        $token=$_SERVER['HTTP_TOKEN'];
        $n=$url.$token;
        $key='str:url:'.md5($n);
        $g=Redis::get($key);
        $time=Redis::ttl($key);
        if($g>=5){
            if($time>=0){
            echo '请您不要频繁请求此接口'.$time.'秒后重试';
            die;
            }
            $time=10;
            echo '请您不要频繁请求此接口'.$time.'秒后重试';
            Redis::expire($key,$time);
            die;
        }
        echo $key;
        Redis::incr($key);
        echo '<hr>';
        $g=Redis::get($key);
        echo $g;
    }
}
