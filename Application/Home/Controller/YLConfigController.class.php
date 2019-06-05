<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class YLConfigController extends Base {

    /*服务商网商配置*/
    public function fws_yl_config(){
        if( IS_POST ){
            if (session('data')['CustomersType'] == 0 &  session('flag') == 0) {//服务商登陆
                $Query_Arr['CustomerServiceSysNo'] = session('data')['SysNo'];
                $Query_Url = C('SERVER_HOST') . "IPP3Customers/YLConfigList";
                $ReturnData = http($Query_Url, $Query_Arr);
                $Arr = array(
                    "CustomerServiceSysNo" => session('data')['SysNo'],
                    "SerProvId" => I('serprovid', '', 'htmlspecialchars'),
                    "Public_Key" => I('sx_gy', '', 'htmlspecialchars'),
                    "Private_key" => I('sx_sy', '', 'htmlspecialchars'),
                    "YL_public_key" => I('sx_ylgy', '', 'htmlspecialchars')
                );
                if ($ReturnData['Code']==0) {
                    $Update_Url = C('SERVER_HOST') . "IPP3Customers/YLConfigUpdate";
                    $arrData = http($Update_Url, $Arr);
                }else{
                    $Insert_Url = C('SERVER_HOST') . "IPP3Customers/YLConfigInsert";
                    $arrData = http($Insert_Url, $Arr);
                }




                $this->ajaxReturn($arrData);
                exit();
            } else {
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        }else{
            R("Base/getMenu");
            if (session('data')['CustomersType'] == 0 &session('flag') == 0) {//服务商登陆
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/YLConfigList";
                $arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }else{
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'fws_yl_config' );

    }
    /*商户网商微信配置新增、修改、查询*/
    public function sh_yl_wx_config(){
        if( IS_POST ){
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录

                $CustomerPassageWaysSysNo = I('CustomerPassageWaysSysNo', '', 'htmlspecialchars');
                $CustomerServiceSysNo =  session('data')['SysNo'];

                $Query_Url  = C( 'SERVER_HOST' ) . "IPP3Customers/YL_PassageConfigList";
                $Query_Arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                    'CustomerPassageWaysSysNo' => $CustomerPassageWaysSysNo,
                );
                $Return_Arr = http($Query_Url, $Query_Arr);
                $arr = array(
                    "CustomerServiceSysNo" => $CustomerServiceSysNo,
                    "MCHID" => I('sx_mchid', '', 'htmlspecialchars'),
                    "APPID" => I('sx_appid', '', 'htmlspecialchars'),
                    "APPSECRET" => I('sx_appsecret', '', 'htmlspecialchars'),
                    "CustomerPassageWaysSysNo" => $CustomerPassageWaysSysNo
                );
                if ($Return_Arr['Code'] == 0) {
                    $url = C('SERVER_HOST') . "IPP3Customers/YL_PassageConfigUpdate";

                    $arrData = http($url, $arr);
                }else{
                    $url = C('SERVER_HOST') . "IPP3Customers/YL_PassageConfigInsert";

                    $arrData = http($url, $arr);
                }

                $this->ajaxReturn($arrData);
                exit();
            }else{
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        }else{
            R("Base/getMenu");
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                //商户通道查询
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 116;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/YL_PassageConfigList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                        'CustomerPassageWaysSysNo' => $post_passageway_list[0]['SysNo'],
                    );
                    $arrData  = http( $url, $arr );
                        $this->assign( 'data', $arrData );
                        $this->assign( 'CustomerPassageWaysSysNo', $post_passageway_list[0]['SysNo'] );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{//其他角色 禁止配置
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'sh_yl_wx_config' );

    }
    /*商户网商微信配置新增、修改、查询*/
    public function sh_ysf_config(){
        if( IS_POST ){
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录

                $CustomerPassageWaysSysNo = I('customerpassagewayssysno', '', 'htmlspecialchars');
                $CustomerServiceSysNo =  session('data')['SysNo'];

                $Query_Url  = C( 'SERVER_HOST' ) . "IPP3Customers/YL_PassageConfigList";
                $Query_Arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                    'CustomerPassageWaysSysNo' => $CustomerPassageWaysSysNo,
                );
                $Return_Arr = http($Query_Url, $Query_Arr);
                $arr = array(
                    "CustomerServiceSysNo" => $CustomerServiceSysNo,
                    "MCHID" => I('sx_mchid', '', 'htmlspecialchars'),
                    "CustomerPassageWaysSysNo" => $CustomerPassageWaysSysNo
                );
                if ($Return_Arr['Code'] == 0) {
                    $url = C('SERVER_HOST') . "IPP3Customers/YL_PassageConfigUpdate";

                    $arrData = http($url, $arr);
                }else{
                    $url = C('SERVER_HOST') . "IPP3Customers/YL_PassageConfigInsert";

                    $arrData = http($url, $arr);
                }

                $this->ajaxReturn($arrData);
                exit();
            }else{
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        }else{
            R("Base/getMenu");
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                //商户通道查询
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 118;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/YL_PassageConfigList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                        'CustomerPassageWaysSysNo' => $post_passageway_list[0]['SysNo'],
                    );
                    $arrData  = http( $url, $arr );
                    $this->assign( 'data', $arrData );
                    $this->assign( 'CustomerPassageWaysSysNo', $post_passageway_list[0]['SysNo'] );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{//其他角色 禁止配置
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'sh_ysf_config' );

    }
    /*商户网商ali配置新增、修改、查询*/
    public function sh_yl_ali_config(){
        if( IS_POST ){
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                $CustomerPassageWaysSysNo = I('customerpassagewayssysno', '', 'htmlspecialchars');
                $Query_Url  = C( 'SERVER_HOST' ) . "IPP3Customers/YL_PassageConfigList_Ali";
                $Query_Arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                    'CustomerPassageWaysSysNo' => $CustomerPassageWaysSysNo,
                );
                $Return_Arr = http($Query_Url, $Query_Arr);
                $arr  = array(
                    "CustomerServiceSysNo"     => session('data')['SysNo'],
                    "MCHID"                    => I('sx_mchid', '', 'htmlspecialchars'),
                    "ALi_APPID"                => I('sx_appid', '', 'htmlspecialchars'),
                    "ALi_alipay_public_key"    => I('sx_algy', '', 'htmlspecialchars'),
                    "ALi_merchant_private_key" => I('sx_shsy', '', 'htmlspecialchars'),
                    "ALi_Status"               => I('sh_status', '', 'htmlspecialchars'),
                    "CustomerPassageWaysSysNo" => I('customerpassagewayssysno'),
                );
                if ($Return_Arr['Code'] == 0) {
                    $url = C('SERVER_HOST') . "IPP3Customers/YL_PassageConfigUpdate_Ali";

                    $arrData = http($url, $arr);
                }else{
                    $url = C('SERVER_HOST') . "IPP3Customers/YL_PassageConfigInsert_Ali";

                    $arrData = http($url, $arr);
                }
                $this->ajaxReturn( $arrData );
                exit();
            }else{
                $arrData['Code'] = 1;
                $arrData['Description'] ="该角色无权限,进行该操作!";
                $this->ajaxReturn($arrData);
                exit();
            }
        }else{
            R("Base/getMenu");
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                //商户通道查询
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 117;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/YL_PassageConfigList_Ali";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                        'CustomerPassageWaysSysNo' => $post_passageway_list[0]['SysNo'],
                    );
                    $arrData  = http( $url, $arr );
                    $this->assign( 'data', $arrData );
                    $this->assign( 'CustomerPassageWaysSysNo', $post_passageway_list[0]['SysNo'] );

                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{//其他角色 禁止配置
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'sh_yl_ali_config' );

    }



}

