<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Redis;
use Closure;

class Token
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        echo '这是中间件验证';
        echo '<hr>';
        $name = $request->get('name');
        // echo $name;
        if(empty($name)){
            $json=[
                'error'=>'6012',
                'msg'=>'您缺少name参数'
            ];
            return  response()->json($json);
        }
        $token=$_SERVER['HTTP_TOKEN']??'';
        if(empty($token)){
            $json=[
                'error'=>'6018',
                'msg'=>'您缺少token参数'
            ];
            return response()->json($json);
        }
        echo $token;
        echo '<hr>';
        $prive='ABCD';
        $key=md5($name.$prive);
        $key='str:u:'.$key;
        $tokens=Redis::get($key);
        echo $tokens;
        if($token!=$tokens){
            $json=[
                'error'=>'6009',
                'msg'=>'您的token有误，请重获取'
            ];
            return  response()->json($json);
        }
        return $next($request);
    }
}
