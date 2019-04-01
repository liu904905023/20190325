<?php

namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller{



    public function login(){

        if( IS_POST ){

            $RememberService = I('RememberService');

            $ServiceUsername = I('username','','trim');
            $ServicePassword = I('password');

            if ($RememberService == 1) {

                if($ServiceUsername == cookie('serviceusername')&&$ServicePassword==cookie('servicepassword'))

                {

                    $ServicePassword = $this->decrypt(cookie('servicepassword'));//解密当前cookie密码

                }else{



                }

            }else if ($RememberService == 0) {

                if($ServiceUsername == cookie('serviceusername')&&$ServicePassword==cookie('servicepassword'))

                {

                    $ServicePassword = $this->decrypt(cookie('servicepassword'));//解密当前cookie密码

                }else{

                    $ServiceUsername = I('username','','trim');

                    $ServicePassword = I('password');

                }

            }

            $url  = C('SERVER_HOST')."IPP3Customers/IPP3CustomerLogin";
//            $url  = "https://pos.yunlaohu.cn/IPP3Customers/IPP3CustomerLogin";


            $arr  = array(

                "UserName" => $ServiceUsername,

                "PassWord" => $ServicePassword,

            );

            $result = http($url,$arr);

            if( $result['Code'] == 0 ){

                //码表查询

                $info = $this->passagewaylist();

                if ($info) {

                    cookie('passageway_list',$info);

                }else{

                    $result['Code'] = 1;
                    $result['Description']="登录失败！";
                    $this->ajaxReturn( $result );
                    return false;
                }

                cookie( 'status', 1);

                session( 'status', 1);

                session( 'data', $result['Data'] );

                session( 'SysNO', $result['Data']['SysNo'] );

                session( 'password', $result['Data']['Pwd'] );

                session( 'flag', 0 );//服务商登录flag = 0

                if ($RememberService == 1) {

                    cookie('serviceusername',$ServiceUsername );

                    cookie('servicepassword',$this->encrypt($ServicePassword));

                }else if($RememberService == 0){

                    cookie('servicepassword',null);

                }

            }

            $this->ajaxReturn( $result );

            exit();

        }

        $this->display( 'login' );

    }



    public function login_staff(){

        if( IS_POST ){

            $RememberStaff = I('RememberStaff');

            $StaffUsername = I('username','','trim');

            $StaffPassword = I('password');

            if ($RememberStaff == 1) {

                if($StaffUsername == cookie('staffusername')&&$StaffPassword==cookie('staffpassword'))

                {

                    $StaffPassword = $this->decrypt(cookie('staffpassword'));//解密当前cookie密码

                }else{



                }

            }else if ($RememberStaff == 0) {

                if($StaffUsername == cookie('staffusername')&&$StaffPassword==cookie('staffpassword'))

                {

                    $StaffPassword = $this->decrypt(cookie('staffpassword'));//解密当前cookie密码

                }else{



                }

            }

            $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3Login";
//            $url  =  "https://pos.yunlaohu.cn/IPP3Customers/IPP3Login";


            $arr  = array(

                "UserName" => $StaffUsername,

                "PassWord" => $StaffPassword,

            );

            $result = http( $url, $arr );

            if( $result['Code'] == 0 ){

                //码表查询

                $info= $this->passagewaylist();

                if ($info) {

                    cookie('passageway_list',$info);

                }else{
                    $result_list['Code'] = 1;
                    $result_list['Description']="登录失败！";
                    $this->ajaxReturn( $result_list );
                    return false;
                }
                cookie( 'status', 1);

                session( 'status', 1);

                session( 'data', $result['Data'] );

                session( 'SysNO', $result['Data']['SysNO'] );

                session('servicestoreno',staffquerystore($result['Data']['SysNO']));

                session('servicestoretype',staffstoreorservice($result['Data']['SysNO'])); //员工类型  服务商为0 或 商户为1

                session( 'password', $result['Data']['Password'] );

                session( 'flag', 1 );

                if ($RememberStaff == 1) {

                    cookie('staffusername',$StaffUsername );

                    cookie('staffpassword',$this->encrypt($StaffPassword));

                }else if($RememberStaff == 0){

                    cookie('staffpassword',null);

                }

            }

            $this->ajaxReturn( $result );

            exit();

        }

        $this->display( 'login_staff' );

    }



    public function logout(){

        if (session(flag)==0){
            foreach($_COOKIE as $k=>$v) {
                if ($k!='serviceusername'&&$k!='servicepassword'){
                    cookie($k, null);
                }
            }
        }
        if (session(flag)==1){
            foreach($_COOKIE as $k=>$v) {
                if ($k!='staffusername'&&$k!='staffpassword'){
                    cookie($k, null);
                }
            }
        }
        session( null );
        header( 'Location:login' );

    }
    //AES加密
    public function  encrypt($data)
    {
        $privateKey = "1234qwer5678asda";
        $iv = "yCJXKLv4GvySreYK";
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        $tmp=base64_encode($encrypted);
        return $tmp;
    }
    //AES解密
    public function  decrypt($data)
    {
        $privateKey = "1234qwer5678asda";
        $iv = "yCJXKLv4GvySreYK";
        $encryptedData = base64_decode($data);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        return $decrypted;
    }

    //码表查询方法

    public function  passagewaylist(){
		$passagewaylist_data['state'] = 0;

        $passagewaylist_url = C('SERVER_HOST').'IPP3Customers/SystemPassageWayList';//码表查询

        $passageway_list = http($passagewaylist_url);

        foreach ($passageway_list as $row=>$val) {

            $info[$row]['T'] = $val['Type'];

            $info[$row]['N'] = $val['TypeName'];

            $info[$row]['W'] = $val['PassageWay'];

            $info[$row]['R'] = $val['Remarks'];

        }

        return $info=json_encode($info);

    }



}