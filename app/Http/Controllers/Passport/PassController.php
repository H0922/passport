<?php

namespace App\Http\Controllers\Passport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PassUserModel as Pass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class PassController extends Controller
{
      //注册接口
    public function reg(){
        $data=$_POST;
        $res=Pass::insert($data);
        if($res){
            $json=[
                'error'=>'ok',
                'msg'=>'注册成功'
            ];
        }else{
            $json=[
                'error'=>'6001',
                'msg'=>'注册失败'
            ];
        }
        return $json;
    }

    //登录
    public function login(){
      
        $name=$_POST['name']??'';
        $pwd=$_POST['pwd']??'';

        $res=Pass::where('pass_name','=',$name)->first();
        if($res){
            $pwds=$res->pass_pwd;
            if($pwds==$pwd){
                $json=[
                    'error'=>'ok',
                    'msg'=>'登录成功'
                ];
            }else{
                $json=[
                    'error'=>'6002',
                    'msg'=>'您的密码有误，请重输入'
                ];
            }
        }else{
            $res=Pass::where('pass_tel', '=', $name)->first();
            if ($res) {
                $pwds=$res->pass_pwd;
                if ($pwds==$pwd) {
                    $json=[
                        'error'=>'ok',
                        'msg'=>'登录成功'
                    ];
                } else {
                    $json=[
                        'error'=>'6002',
                        'msg'=>'您的密码有误，请重输入'
                    ];
                }
            } else {
                $res=Pass::where('pass_email', '=', $name)->first();
                if ($res) {
                    $pwds=$res->pass_pwd;
                    if ($pwds==$pwd) {
                        $json=[
                        'error'=>'ok',
                        'msg'=>'登录成功'
                    ];
                    } else {
                        $json=[
                        'error'=>'6002',
                        'msg'=>'您的密码有误，请重输入'
                    ];
                    }
                }else{
                    $json=[
                        'error'=>'6004',
                        'msg'=>'您的用户有误，请重输入'
                    ];
                }
            }
        }
        
       if($json['error']=='ok'){
           $strtoken=Str::random(32);
           echo $strtoken;
           echo '<hr>';
            $prive='ABCD';
            $token=md5($name.$prive);
            $key='str:u:'.$token;
            Redis::set($key,$strtoken);
            Redis::expire($key,3600*7);
            // Redis::del($key);
            echo '<hr>';
            echo Redis::get($key);
            echo '<hr>';
       }
           return $json;
    }

        //获取用户信息
        public function Userinfo(){
            $name=$_GET['name']??'';
            if($name){
                $json=[
                    'error'=>'6012',
                    'msg'=>'您缺少name参数'
                ];
                return $json;
            }
            $token=$_GET['token']??'';
            if($token){
                $json=[
                    'error'=>'6018',
                    'msg'=>'您缺少token参数'
                ];
                return $json;
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
                return $json;
            }
            $res=Pass::where('pass_name', '=', $name)->first();  
            if($res){
                $json=json_encode($res);
                $json_data=json_decode($json,true);
                unset($json_data['pass_id']);
                unset($json_data['pass_pwd']);
                unset($json_data['created_at']);
                unset($json_data['updated_at']);
                dump($json_data);
            }else{
                $res=Pass::where('pass_email', '=', $name)->first(); 
                if($res){
                    $json=json_encode($res);
                    $json_data=json_decode($json,true);
                    unset($json_data['pass_id']);
                    unset($json_data['pass_pwd']);
                    unset($json_data['created_at']);
                    unset($json_data['updated_at']);
                    dump($json_data);
                }else{
                    $res=Pass::where('pass_tel', '=', $name)->first(); 
                    if ($res) {
                        $json=json_encode($res);
                        $json_data=json_decode($json, true);
                        unset($json_data['pass_id']);
                        unset($json_data['pass_pwd']);
                        unset($json_data['created_at']);
                        unset($json_data['updated_at']);
                        dump($json_data);
                    }
                }
            }          
        }

        
        public function gitpush(){
            $b='cd /wwwroot/passport && git pull';
            shell_exec($b);

        }
        
}
