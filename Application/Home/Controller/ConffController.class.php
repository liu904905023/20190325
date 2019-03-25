<?php

namespace Home\Controller;

use Common\Compose\Base;

class ConffController extends Base{
    public function index(){
        $this->display();
    }

    public function wxConfig(){
        if( IS_POST ){
            $flag = session('flag');
            if ((session('data')['CustomersType'] == 0 & $flag == 0)||(session('data')['CustomersType'] == 1 & $flag == 0)) {
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerConfigEdit";
                $arr  = array(
                    "CustomerServiceSysNO" => session( 'data' )['SysNo'],
                    "APPID"                => I( 'sx_appid', '', 'htmlspecialchars' ),
                    "NCHID"                => I( 'sx_fwsbh', '', 'htmlspecialchars' ),
                    "KEY"                  => I( 'sx_shkey', '', 'htmlspecialchars' ),
                    "APPSECRET"            => I( 'sx_appsecret', '','htmlspecialchars' ),
                    "sub_mch_id"           => (int) I( 'sx_zshid', '','htmlspecialchars' ),
                    "SSLCERT_PATH"         => I( 'safe', '','htmlspecialchars' ),
                    "Status"               => 0,
                    "SSLCERT_PASSWORD"     => (int) I( 'sx_pass', '','htmlspecialchars' ),
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
            R("Base/getMenu");
            $flag = session('flag');
            if (session('data')['CustomersType'] == 0 & $flag == 0) {
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerConfig";
                $arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }elseif (session('data')['CustomersType'] == 1 & $flag == 0) {
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 102;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerConfig";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                    );
                    $arrData  = http( $url, $arr );
                    $this->assign( 'data', $arrData );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'commercial_tenant_config' );
    }

    public function infoDetail(){
        if( IS_AJAX ){
            $url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserUpdate";
            $arr = array(
                "SysNo"               => session('data')['SysNO'],
                "PhoneNumber"         => I('Phone'),
                "DisplayName"         => I('displayname'),
                "Alipay_store_id"     => I('store_id'),
                "Email"               => I('Email'),
                "Rate"                => (double)I('user_rate'),
                "DwellAddress"        => I( "address" ),
                "DwellAddressID"      => I( "AddressNum" ),
                "EditUser"            =>session('servicestoreno')
            );
            $data  = http($url, $arr);
            if ($data['Code']==0) {
                $Ali_Url=I('Ali_Url');
                $Notify_Url=I('Notify_Url');
                $select_ali_url = C( 'SERVER_HOST' ) . "IPP3Customers/SystemUser_Extend_AliList";
                $select_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendList";
                $arr_url["SystemUserSysNo"]  = session('data')['SysNO'];
                if (deep_in_array($arr_url)) {
                    $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
                    exit();
                }
                //口碑阿里域名参数
                $Insert_Ali_Data['Ali_url']=$Ali_Url;
                $Insert_Ali_Data['CustomerTopSysNo']='0';
                $Insert_Ali_Data['CustomerServiceSysNo']=session('servicestoreno');
                $Insert_Ali_Data['SystemUserSysNo']=session('data')['SysNO'];
                //查询商户所有信息



                $Insert_Ali_Data['LoginName']=session('data')['LoginName'];
                $Insert_Ali_Data['DisplayName']=session('data')['DisplayName'];
                $Insert_Ali_Data['InUser']=session('servicestoreno');


                if($Ali_Url){
                    //减少一次请求接口次数，此处必须这么写。
                    $CustomerInfoData['SysNo'] = session('servicestoreno');
                    $CustomerInfoUrl  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerList";
                    $CustomerInfoList  = http( $CustomerInfoUrl, $CustomerInfoData);
                    $Insert_Ali_Data['Customer']=$CustomerInfoList['model'][0]['Customer'];
                    $Insert_Ali_Data['CustomerName']=$CustomerInfoList['model'][0]['CustomerName'];


                    $return_ali_url = http($select_ali_url, $arr_url);
                    if (deep_in_array($Insert_Ali_Data)) {
                        $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
                        exit();
                    }
                    if($return_ali_url['Data']['Ali_url']){
                        $Update_Ali_Data['SystemUserSysNo']=session('data')['SysNO'];
                        $Update_Ali_Data['Ali_url']=$Ali_Url;
                        $Update_Ali_Data['EditUser']=session('servicestoreno');
                        //$return_ali_url['Data']['Ali_url']
                        if (deep_in_array($Update_Ali_Data)) {
                            $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
                            exit();
                        }
                        if($Ali_Url!=$return_ali_url['Data']['Ali_url']){
                            $Update_Ali_Url = C( 'SERVER_HOST' ) . "IPP3Customers/SystemUser_Extend_AliUpd";
                            $Result_Ali_Info = http($Update_Ali_Url, $Update_Ali_Data);
                        }else{
                            $Result_Ali_Info['Code'] = 0;

                        }

                    }else{
                        $Insert_Ali_Url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendAliInsert";
                        $Result_Ali_Info = http($Insert_Ali_Url, $Insert_Ali_Data);
                    }

                }else{

                    //减少一次请求接口次数，此处必须这么写。
                    /*
                     * 此处接口判断Ali_Url不允许为空，防止后期发动，留存。
                     * $CustomerInfoData['SysNo'] = session('servicestoreno');
                    $CustomerInfoUrl  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerList";
                    $CustomerInfoList  = http( $CustomerInfoUrl, $CustomerInfoData);
                    $Insert_Ali_Data['Customer']=$CustomerInfoList['model'][0]['Customer'];
                    $Insert_Ali_Data['CustomerName']=$CustomerInfoList['model'][0]['CustomerName'];

                    $return_ali_url = http($select_ali_url, $arr_url);
                    if($return_ali_url){
                        $Update_Ali_Url = C( 'SERVER_HOST' ) . "IPP3Customers/SystemUser_Extend_AliUpd";
                        $Result_Ali_Info = http($Update_Ali_Url, $Insert_Ali_Data);
                    }*/

                }
                if($Notify_Url){
                    $return_url = http($select_url, $arr_url);
                    if($return_url){
                        if ($Notify_Url != $return_url['Notify_url']) {
                            $arr_url["Notify_url"]  = $Notify_Url;
                            $update_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendUpdate";
                            $Result_Info = http($update_url, $arr_url);
                        }else{
                            $Result_Info['Code'] = 0;
                        }

                    }else{
                        $arr_url["Notify_url"]  = $Notify_Url;
                        $insert_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendInsert";
                        $Result_Info = http($insert_url, $arr_url);
                    }
                }else{
                    $return_url = http($select_url, $arr_url);
                    if($return_url){
                        $arr_url["Notify_url"]  = $Notify_Url;
                        $update_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendUpdate";
                        $Result_Info = http($update_url, $arr_url);
                    }
                }

                if($Result_Info['Code']==0&$Result_Ali_Info['Code']==0){
                    $Return_Info['Description']='员工资料修改成功!';
                }else{
                    $Return_Info['Description']='员工资料修改失败';
                }




            }else{
                $Return_Info['Description']='员工资料修改失败!';
            }
            $this->ajaxReturn( $Return_Info, 'json' );
            exit();
        }else{
            R("Base/getMenu");
            $url_staff_register  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserList";
            $arr_staff_register  = array(
                "SysNo" => session('data')['SysNO'],
            );
            $data  = http($url_staff_register, $arr_staff_register);
            //获取推送地址
            $url_get_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendList";
            $arr_get_url  = array(
                "systemUserSysNo" => session('data')['SysNO'],
            );
            $data_url  = http($url_get_url, $arr_get_url);
            //获取口碑地址

            $url_get_ali_url = C( 'SERVER_HOST' ) . "IPP3Customers/SystemUser_Extend_AliList";
            $arr_get_ali_url  = array(
                "systemUserSysNo" => session('data')['SysNO'],
            );
            $data_ali  = http($url_get_ali_url, $arr_get_ali_url);


            $DetailAddress = explode("-",$data['model'][0]['DwellAddress']);
            $Province=$this->GetAddress(0);
            $City=$this->GetAddress($data['model'][0]['Province']);
            $Country=$this->GetAddress($data['model'][0]['City']);
            $this->assign( 'data', $data['model'][0] );
            $this->assign( 'Ali_Url',$data_ali['Data']['Ali_url'] );
            $this->assign( 'Notify_Url',$data_url['Notify_url'] );
            if($data['model'][0]['DwellAddressID']){
                $this->assign( 'Province', $Province);
                $this->assign( 'Country', $Country );
                $this->assign( 'City', $City );
            }else{
                $this->assign( 'Province', $Province);
                $this->assign( 'Country', array(0=>array("region_id"=>'','region_name'=>"请选择区")) );
                $this->assign( 'City',array(0=>array("region_id"=>'','region_name'=>"请选择市")) );
            }
            $this->assign( 'DetailAddress', $DetailAddress[1] );
        }
        $this->display();
    }

    public function password(){
        if( IS_POST ){
            if(session('flag')==0){
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerUpdPwd";
                $arr  = array(
                    "SysNo"       => session( 'SysNO' ),
                    "OldPassWord" => I( 'oldpass' ),
                    "NewPassWord" => I( 'newpass' )
                );
            }else if (session('flag')==1){
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserUpdatePwd";
                $arr  = array(
                    "SysNo"       => session( 'SysNO' ),
                    "OldPassWord" => I( 'oldpass' ),
                    "Password" => I( 'newpass' )
                );
            }
            $data  = http( $url, $arr );
            $this->ajaxReturn( $data );
            exit();
        }else{
            R("Base/getMenu");
            if(session('flag')==0){
                $data = array(
                    'username' => session( 'data' )['Customer'],
                );

            }else if (session('flag')==1){
                $data = array(
                    'username' => session( 'data' )['LoginName'],
                );
            }
            $this->assign( 'data', $data );
        }
        $this->display( 'password' );
    }

    public function zfbConfig(){
        if( IS_POST ){
            $flag = session('flag');
            if ((session('data')['CustomersType'] == 0 & $flag == 0)||(session('data')['CustomersType'] == 1 & $flag == 0)) {
            $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerAliPayConfigEdit";
            $arr  = array(
                "CustomerServiceSysNO"  => session( 'data' )['SysNo'],
                "APPID"                 => I( 'sx_appid', '', 'htmlspecialchars' ),
                "PID"                   => I( 'sx_shid', '', 'htmlspecialchars' ),
                "sub_PID"               => I( 'sx_zshid', '', 'htmlspecialchars' ),
                "Merchant_private_key"  => I( 'sx_shsy', '', 'htmlspecialchars' ),
                "Alipay_public_key"     => I( 'sx_algy', '', 'htmlspecialchars' )
            );
            if (session('data')['CustomersType'] == 0 & $flag == 0) {
                $arr["Type"]= 0;
                $arr["Status"]=I( 'sh_status', '', 'htmlspecialchars' );
            }
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $arr["Type"]=I( 'sh_type', '', 'htmlspecialchars' );
            }
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
            R("Base/getMenu");
            $flag = session('flag');
            if (session('data')['CustomersType'] == 0 & $flag == 0) {
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerAliPayConfig";
                $arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }elseif (session('data')['CustomersType'] == 1 & $flag == 0) {
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 103;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerAliPayConfig";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                    );
                    $arrData  = http( $url, $arr );
                    $this->assign( 'data', $arrData );
                } else {
                    $this->assign( 'passtype', -1 );
                }
            }else{
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'commercial_tenant_alipay' );
    }

    public function xy_wxConfig(){
        if( IS_POST ){
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageConfigEdit";
                $arr  = array(
                    "CustomerServiceSysNo" => session( 'data' )['SysNo'],
                    "APPID"         => I( 'sx_appid', '', 'htmlspecialchars' ),
                    "MCHID"         => I( 'sx_fwsbh', '', 'htmlspecialchars' ),
                    "Key"           => I( 'sx_shkey', '', 'htmlspecialchars' )
                );
                $arrData = http($url,$arr);
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
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 104;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    foreach ($post_passageway_list as $row=>$val){
                        $data['model'][0]['SysNo']=$val['SysNo'];
                        $data['model'][0]['Type']=$val['Type'];
                    }
                    $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageConfigList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                        'CustomerPassageWaysSysNo' => $data['model'][0]['SysNo']
                    );
                    $arrData = http($url,$arr);
                    $this->assign( 'data', $arrData );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{
                $this->assign('passtype', -1);
            }
        }
        $this->display( 'commercial_tenant_config_xywx' );
    }

    public function xy_zfbConfig(){
        if( IS_POST ){
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageAndAliPayConfigInsert";
                $arr  = array(
                    "Remarks"                   => "AliPay",
                    "WFT_APPID"                 => I( 'yh_appid', '', 'htmlspecialchars' ),
                    "WFT_MCHID"                 => I( 'yh_fwsbh', '', 'htmlspecialchars' ),
                    "WFT_KEY"                   => I( 'yh_shkey', '', 'htmlspecialchars' ),
                    "CustomerServiceSysNo"      => session('data')['SysNo'],
                    "Ali_APPID"                 => I( 'ali_appid', '', 'htmlspecialchars' ),
//                    "Ali_PID"                   => I( 'sx_shid', '', 'htmlspecialchars' ),
//                    "Ali_sub_PID"               => I( 'sx_zshid', '', 'htmlspecialchars' ),
                    "Ali_merchant_private_key"  => $_POST['sx_shsy'],
                    "ALi_alipay_public_key"     => $_POST['sx_algy'],
                );
                $arrData = http($url,$arr);
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
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 105;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    foreach ($post_passageway_list as $row=>$val){
                        $data['model'][0]['SysNo']=$val['SysNo'];
                        $data['model'][0]['Type']=$val['Type'];
                    }
                    $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageAndAliPayConfigList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                        'CustomerPassageWaysSysNo' => $data['model'][0]['SysNo']
                    );
                    $arrData = http($url,$arr);
                    $this->assign( 'data', $arrData[0] );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{
                $this->assign('passtype', -1);
            }
        }
        $this->display( 'commercial_tenant_alipay_xyzfb' );
    }

    public function pf_wxConfig(){
        if( IS_POST ){
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageConfigEdit";
                $arr  = array(
                    "CustomerServiceSysNo" => session( 'data' )['SysNo'],
                    "APPID"         => I( 'sx_appid', '', 'htmlspecialchars' ),
                    "MCHID"         => I( 'sx_fwsbh', '', 'htmlspecialchars' ),
                    "Key"           => I( 'sx_shkey', '', 'htmlspecialchars' )
                );
                $arrData = http($url,$arr);
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
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 106;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    foreach ($post_passageway_list as $row=>$val){
                        $data['model'][0]['SysNo']=$val['SysNo'];
                        $data['model'][0]['Type']=$val['Type'];
                    }
                    $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageConfigList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                        'CustomerPassageWaysSysNo' => $data['model'][0]['SysNo']
                    );
                    $arrData = http($url,$arr);
                    $this->assign( 'data', $arrData );
                }else{
                    $this->assign( 'passtype', -1 );
                }
            }else{
                $this->assign('passtype', -1);
            }
        }
        $this->display( 'commercial_tenant_config_pfwx' );
    }

    public function pf_zfbConfig(){
        if( IS_POST ){
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageAndAliPayConfigInsert";
                $arr  = array(
                    "Remarks"         => "AliPay",
                    "WFT_APPID"         => I( 'yh_appid', '', 'htmlspecialchars' ),
                    "WFT_MCHID"         => I( 'yh_fwsbh', '', 'htmlspecialchars' ),
                    "WFT_KEY"           => I( 'yh_shkey', '', 'htmlspecialchars' ),
                    "CustomerServiceSysNo"      => session('data')['SysNo'],
                    "Ali_APPID"                 => I( 'ali_appid', '', 'htmlspecialchars' ),
                    /*
                     * 20180802因业务需求,暂不需要传递此值*/
//                    "Ali_PID"                   => I( 'sx_shid', '', 'htmlspecialchars' ),
//                    "Ali_sub_PID"               => I( 'sx_zshid', '', 'htmlspecialchars' ),
                    "Ali_merchant_private_key"  => $_POST['sx_shsy'],
                    "ALi_alipay_public_key"     => $_POST['sx_algy'],
                );
                $arrData = http($url,$arr);
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
            $flag = session('flag');
            if (session('data')['CustomersType'] == 1 & $flag == 0) {
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 107;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
                $post_passageway_list = http($post_passageway_url, $post_passageway_data);
                if($post_passageway_list){
                    foreach ($post_passageway_list as $row=>$val){
                        $data['model'][0]['SysNo']=$val['SysNo'];
                    }
                    $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageAndAliPayConfigList";
                    $arr  = array(
                        'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                        'CustomerPassageWaysSysNo' => $data['model'][0]['SysNo']
                    );
                    $arrData = http($url,$arr);
                    $this->assign( 'data', $arrData[0] );
                } else {
                    $this->assign('passtype', -1);
                }
            }else{
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'commercial_tenant_alipay_pfzfb' );
    }

    public function MerchantDetail(){
        if( IS_AJAX ){
            $url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerUpd";
            $data = array(
                "SysNo"         => session('SysNO'),
                "CustomerName"  => I('CustomerName'),
                "Phone"         => I('Phone'),
                "CellPhone"     => I('CellPhone'),
                "Email"         => I('Email'),
                "Rate"          => I('Rate'),
                "DwellAddress"  => I('DwellAddress'),
                "DwellAddressID"     => I( "AddressNum" ),
                "SystemClassID"     => I( "ClassId" ),
                "SystemClassName"     => I( "ClassName" ),
                "NickName"     => I( "nickname" ),
                "Customer_field_three" => I("IsAd")
            );
            $data  = http( $url, $data);
            $this->ajaxReturn( $data, 'json' );
            exit();
        }else{
            R("Base/getMenu");
            $SysNo = session( 'SysNO');
            $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerList";
            $data  = array(
                "SysNo" => $SysNo
            );
            $data  = http( $url, $data);
            $Province=$this->GetAddress(0);
            $City=$this->GetAddress($data['model'][0]['Province']);
            $Country=$this->GetAddress($data['model'][0]['City']);
            $firstclass=$this->GetClass(0,0);
            $secondclass=$this->GetClass($data['model'][0]['ClassOne'],1);
            $thirdclass=$this->GetClass($data['model'][0]['ClassTwo'],2);
            $post_rate_data['CustomerSysNo'] = $SysNo;
            $post_rate_url = C('SERVER_HOST') . 'IPP3Customers/CustomerServiceRateList';
            $post_rate_list = http($post_rate_url, $post_rate_data);
            if($post_rate_list){
                foreach ($post_rate_list as $row=>$val){
                    $data['model'][0]['typerate'][$val['Type']]=$val['Rate'];
                }
            }
            $post_passageway_data['CustomerSysNo'] = $SysNo;
            $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
            $post_passageway_list = http($post_passageway_url, $post_passageway_data);
            if($post_passageway_list){
                foreach ($post_passageway_list as $row=>$val){
                    foreach(json_decode($_COOKIE['passageway_list'],true ) as $k=>$v) {
                        if ($val['Type']==$v['T']) {
                            $data['model'][0]["typedisplay"][$val['Type']]['TypeName']=$v['N'];
                        }
                    }
                }
            }
            $this->assign( 'data', $data['model'][0] );
            if($data['model'][0]['DwellAddressID']){
                $DetailAddress = explode("-",$data['model'][0]['DwellAddress']);
                $this->assign( 'DetailAddress', $DetailAddress[1] );
                $this->assign( 'Province', $Province );
                $this->assign( 'Country', $Country );
                $this->assign( 'City', $City );
                $this->assign( 'firstclass', $firstclass );
                $this->assign( 'secondclass', $secondclass );
                $this->assign( 'thirdclass', $thirdclass );
            }else{
                $DetailAddress = $data['model'][0]['DwellAddress'];
                $this->assign( 'DetailAddress', $DetailAddress );
                $this->assign( 'Province', $Province );
                $this->assign( 'Country', array(0=>array("region_id"=>'','region_name'=>"请选择区")) );
                $this->assign( 'City',array(0=>array("region_id"=>'','region_name'=>"请选择市")) );
                $this->assign( 'firstclass', $firstclass );
                $this->assign( 'secondclass', array(0=>array("sysno"=>'','class_name'=>"请选择第二级类目")) );
                $this->assign( 'thirdclass',array(0=>array("sysno"=>'','class_name'=>"请选择第三级类目")) );
            }
        }
        $this->display();
    }

    private function GetAddress($parent_id,$type=0){
        if($type==1){

        }else{
            $data['Parent_id']=$parent_id;
        }
        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetAddress";
        $list = http($url,$data);
        foreach ($list as $row=>$val){
            $info[$row]['region_id'] = $val['SysNo'];
            $info[$row]['region_name']   = $val['AddressName'];
        }
        return $info;
    }

    private function GetClass($sysno,$class_id){
        if($class_id==0){
            $data['ClassID']=0;
        }else{
            $data['TopSysNO']=$sysno;
            $data['ClassID']=$class_id;
        }
        $url  = C('SERVER_HOST')."IPP3Customers/SystemClassList";
        $list = http($url,$data);
        foreach ($list as $row=>$val){
            $info[$row]['sysno'] = $val['SysNo'];
            $info[$row]['class_id'] = $val['ClassID'];
            $info[$row]['class_name']   = $val['ClassName'];
        }
        return $info ;
    }
}