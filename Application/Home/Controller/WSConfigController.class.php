<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class WSConfigController extends Base {

    /*服务商网商配置*/
    public function fws_ws_config(){
        R("Base/getMenu");
        if( IS_POST ){
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆
                $url = C('SERVER_HOST') . "IPP3Customers/WSConfigEdit";
                $arr = array(
                    "CustomerServiceSysNo" => session('data')['SysNo'],
                    "APPID" => I('sx_appid', '', 'htmlspecialchars'),
                    "IsvOrgId" => I('sx_isvorgid', '', 'htmlspecialchars'),
                    "ChannelId" => I('sx_channelid', '', 'htmlspecialchars'),
                    "PublicKey" => I('sx_gy', '', 'htmlspecialchars'),
                    "PrivateKey" => I('sx_sy', '', 'htmlspecialchars'),
                    "MyBankDevPublicKey" => I('sx_wsgy', '', 'htmlspecialchars')
                );
                $arrData = http($url, $arr);
                $this->ajaxReturn($arrData);
                exit();
            } else {
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        }else{
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/WSConfigList";
                $arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }else{
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'fws_ws_config' );

    }
    /*商户网商微信配置新增、修改、查询*/
    public function sh_ws_wx_config(){
        R("Base/getMenu");
        if( IS_POST ){
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                $url = C('SERVER_HOST') . "IPP3Customers/WS_PassageConfigWxEdit";
                $arr = array(
                    "CustomerServiceSysNo" => session('data')['SysNo'],
                    "APPID" => "",
                    "MCHID" => I('sx_mchid', '', 'htmlspecialchars'),
                    "IsvOrgId" => "",
                    "APPIDTwo" => I('sx_appidtwo', '', 'htmlspecialchars'),
                    "APPSECRET" => I('sx_appsecret', '', 'htmlspecialchars')
                );
                $arrData = http($url, $arr);
                $this->ajaxReturn($arrData);
                exit();
            }else{
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        }else{
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                //商户通道查询
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 108;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/WS_PassageConfigWxList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                    );
                    $arrData  = http( $url, $arr );
                    $this->assign( 'data', $arrData );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{//其他角色 禁止配置
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'sh_ws_wx_config' );

    }
    /*商户网商ali配置新增、修改、查询*/
    public function sh_ws_ali_config(){
        R("Base/getMenu");
        if( IS_POST ){
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/WS_PassageConfigAliEdit";
                $arr  = array(
                    "CustomerServiceSysNo"      => session( 'data' )['SysNo'],
                    "Remarks"                   => "AliPay",
                    "WS_APPID"                  => "",
                    "WS_MCHID"                  => I( 'sx_mchid', '', 'htmlspecialchars' ),
                    "WS_IsvOrgId"               => "",
                    "WS_APPSECRET"              => "",
                    "WS_APPIDTwo"               => "",
                    "ALi_APPID"                 => I( 'sx_appid', '', 'htmlspecialchars' ),
                    "ALi_alipay_public_key"     => I( 'sx_algy', '', 'htmlspecialchars' ),
                    "ALi_merchant_private_key"  => I( 'sx_shsy', '','htmlspecialchars' ),
                    "ALi_PID"                   => "",
                    "ALi_sub_PID"               => "",
                    "ALi_Status"                => "",
                    "ALi_merchant_public_key"   => ""
                );
                $arrData  = http( $url, $arr );
                $this->ajaxReturn( $arrData );
                exit();
            }else{
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        }else{
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                //商户通道查询
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 109;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/WS_PassageConfig_AliList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                    );
                    $arrData  = http( $url, $arr );
                    $this->assign( 'data', $arrData );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{//其他角色 禁止配置
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'sh_ws_ali_config' );

    }
    public function sh_ws_path_config() {
        R("Base/getMenu");
        $this->display();
    }
    public function sh_ws_appid_config() {
        R("Base/getMenu");
        $this->display();
    }
    public function UpdatePathConfig() {
        if( IS_POST ) {
            if (session('data')['CustomersType'] == 1 & session('flag') == 0) {//商户登录
            } else {
                $arrData['Code'] = 1;
                $arrData['Description'] = "该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
            $data['CustomerServiceSysNo'] = session('data')['SysNo'];
            $data['ReqModel']['MerchantId'] = I('Merchant_Id');
            $data['ReqModel']['Path'] = I('Wx_Path');
            $url = C('SERVER_HOST') . 'IPP3WSCustomer/WSAddMerchantPathConfig';
            $list = http($url, $data);
            $this->ajaxreturn($list);
            exit();
        }else{

        }
    }
    public function UpdateAppidConfig() {
        if(IS_POST){
            if (session('data')['CustomersType'] == 1 & session('flag') == 0) {//商户登录
            }else{
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
            $data['CustomerServiceSysNo']=session( 'data' )['SysNo'];
            $data['ReqModel']['MerchantId']=I('Merchant_Id');
            $data['ReqModel']['AppidPay']=I('AppId_Pay');
            $data['ReqModel']['SubscribeAppid']=I('Subscribe_Appid');
            $url = C('SERVER_HOST') . 'IPP3WSCustomer/WSAddMerchantSubscribeAppidConfig';
            $list = http($url, $data);
            $this->ajaxreturn ($list);
            exit();
        }else{
            R("Base/getMenu");
            $this->display();
        }

    }


}

