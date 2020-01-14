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
        $name=$data['pass_name'];
        $res=Pass::where('pass_name','=',$name)->first();
        if($name==$res['pass_name']){
            $json=[
                'error'=>'6030',
                'msg'=>'用户名已存在'
            ];
            return $json;
        }
        $email=$data['pass_email'];
        $r=Pass::where('pass_email','=',$email)->first();
        if($email==$r['pass_email']){
            $json=[
                'error'=>'6033',
                'msg'=>'邮箱已存在'
            ];
            return $json;
        }
        $tel=$data['pass_tel'];
        $s=Pass::where('pass_tel','=',$tel)->first();
        if($tel==$s['pass_tel']){
            $json=[
                'error'=>'6035',
                'msg' => '手机号已存在'
            ];
            return $json;
        }
        if($data['pwds']!=$data['pass_pwd']){
            $json=[
                'error'=>'6020',
                'msg'=>'两次密码不一致'
            ];
            return $json;
        }
        unset($data['pwds']);
        $res=Pass::insert($data);
        if($res){
            $json=[
                'error'=>'ok',
                'msg'=>'注册成功'
            ];
        }
        return $json;
    }

    //登录
    public function login(){
        $name=$_POST['name']??'';
        $pwd=$_POST['pwd']??'';
        $strtoken=Str::random(32);
        // dd(11);
        $res=Pass::where('pass_name','=',$name)->first();
        if($res){
            $pwds=$res->pass_pwd;
            if($pwds==$pwd){
                $json=[
                    'error'=>'ok',
                    'msg'=>'登录成功',
                    'token'=>$strtoken
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
                        'msg'=>'登录成功',
                        'token'=>$strtoken
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
                            'msg'=>'登录成功',
                            'token'=>$strtoken
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
            cookie($name,$strtoken);
            $prive='ABCD';
            $token=md5($name.$prive);
            $key='str:u:'.$token;
            Redis::set($key,$strtoken);
            Redis::expire($key,3600*7);
       }
           return $json;
    }

        //获取用户信息
        public function Userinfo(){
            $name=$_GET['name']??'';
            $mid_params = ['name'=>$name];
            request()->attributes->add($mid_params);//添加参数
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

        //验证token
        public function token(){
            $json=[
                'error'=>'ok',
                'msg'=>'请求成功'
            ];
            return $json;
        }
        public function gitpush(){
            $b='cd /wwwroot/passport && git pull';
            shell_exec($b);

        }
        
}
