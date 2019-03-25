<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class ConffController extends Base{
//class ConffController extends Controller{
    public function index(){

        $this->display();

    }
    /*服务商配置*/
    //微信配置
    public function wxConfig(){
        // var_dump(SESSION('data'));
        R("Base/getMenu");
        if( IS_POST ){
            $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerConfigEdit";
            $arr  = array(
//                "CustomerServiceSysNO" => 1,
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
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerConfig";
                $arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                //商户通道查询
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 102;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
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
            }
        }
        $this->display( 'commercial_tenant_config' );

    }
    //查询页
    public function infoDetail(){

        R("Base/getMenu");
        if( IS_AJAX ){
            $url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserUpdate";
            $arr = array(
                "SysNo"            => session('data')['SysNO'],
                "PhoneNumber"         => I('Phone'),
                "DisplayName"         => I('displayname'),
                "Alipay_store_id"         => I('store_id'),
                "Email"         => I('Email'),
                "Rate"         => (double)I('user_rate'),
                "DwellAddress"     => I( "address" ),
                "DwellAddressID"     => I( "AddressNum" ),
                "EditUser" =>session('servicestoreno')
            );
            $data  = http($url, $arr);
            if ($data['Code']==0) {
                //浦发口碑url修改
                $select_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendList";
                $insert_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendInsert";
                $update_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendUpdate";
                $arr_url["SystemUserSysNo"]  = session('data')['SysNO'];
                $data_ali  = http($select_url, $arr_url);
                $Ali_Url=I('Ali_Url');
                $Notify_Url=I('Notify_Url');
                if ($data_ali) {
                    if(($data_ali['Ali_url']!="")&&($data_ali['Notify_url']!="")){//查询- 口碑：有 微信：有
                        $arr_url["Type"]  = 102103;
                        $arr_url["Ali_url"]  = $Ali_Url;
                        $arr_url["Notify_url"]  = $Notify_Url;
                        $data = http($update_url, $arr_url);
                        if ($data['Code']==0) {
                            $Return_Info['Description']='员工资料修改成功！';
                        } else {
                            $Return_Info['Description']=$data['Description'];
                        }
                    }else if(($data_ali['Ali_url']=="")&&($data_ali['Notify_url']!="")){//查询-浦发口碑：空 微信推送：有
                        if ($Ali_Url=="") {//接收-浦发口碑：空
                            $arr_url["Type"]  = 102;
                            $arr_url["Notify_url"]  = $Notify_Url;
                            $data = http($update_url, $arr_url);
                            if ($data['Code']==0) {
                                $Return_Info['Description']='员工资料修改成功！';
                            } else {
                                $Return_Info['Description']=$data['Description'];
                            }
                        }
                        if($Ali_Url!=""){//接收-浦发口碑：有
                            $arr_url["Type"]  = 102103;
                            $arr_url["Ali_url"]  = $Ali_Url;
                            $arr_url["Notify_url"]  = $Notify_Url;
                            $data = http($update_url, $arr_url);
                            if ($data['Code']==0) {
                                $Return_Info['Description']='员工资料修改成功！';
                            } else {
                                $Return_Info['Description']=$data['Description'];
                            }
                        }
                    }else if(($data_ali['Ali_url']!="")&&($data_ali['Notify_url']=="")){//查询-浦发口碑：有 微信推送：空
                        if ($Notify_Url=="") {
                            $arr_url["Type"]  = 103;
                            $arr_url["Ali_url"]  = $Ali_Url;
                            $data = http($update_url, $arr_url);
                            if ($data['Code']==0) {
                                $Return_Info['Description']='员工资料修改成功！';
                            } else {
                                $Return_Info['Description']=$data['Description'];
                            }
                        }
                        if ($Notify_Url!="") {
                            $arr_url["Type"]  = 102103;
                            $arr_url["Ali_url"]  = $Ali_Url;
                            $arr_url["Notify_url"]  = $Notify_Url;
                            $data = http($update_url, $arr_url);
                            if ($data['Code']==0) {
                                $Return_Info['Description']='员工资料修改成功！';
                            } else {
                                $Return_Info['Description']=$data['Description'];
                            }
                        }
                    }else if(($data_ali['Ali_url']=="")&&($data_ali['Notify_url']=="")){//查询-浦发口碑：空 微信推送：空
                        if(($Ali_Url=="")&&($Notify_Url=="")){
                            $Return_Info['Description']='员工资料修改成功!';
                        }
                        if(($Ali_Url!="")&&($Notify_Url=="")){
                            $arr_url["Type"]  = 103;
                            $arr_url["Ali_url"]  = $Ali_Url;
                            $data = http($update_url, $arr_url);
                            if ($data['Code']==0) {
                                $Return_Info['Description']='员工资料修改成功！';
                            } else {
                                $Return_Info['Description']=$data['Description'];
                            }
                        }
                        if(($Ali_Url=="")&&($Notify_Url!="")){
                            $arr_url["Type"]  = 102;
                            $arr_url["Notify_url"]  = $Notify_Url;
                            $data = http($update_url, $arr_url);
                            if ($data['Code']==0) {
                                $Return_Info['Description']='员工资料修改成功！';
                            } else {
                                $Return_Info['Description']=$data['Description'];
                            }
                        } else if (($Ali_Url!="")&&($Notify_Url!="")) {
                            $arr_url["Type"]  = 102103;
                            $arr_url["Ali_url"]  = $Ali_Url;
                            $arr_url["Notify_url"]  = $Notify_Url;
                            $data = http($update_url, $arr_url);
                            if ($data['Code']==0) {
                                $Return_Info['Description']='员工资料修改成功！';
                            } else {
                                $Return_Info['Description']=$data['Description'];
                            }
                        }
                    }
                }else{
                    if (($Ali_Url)&&($Notify_Url=="")) {
                        $arr_url['Ali_url'] = $Ali_Url;
                    } else if (($Notify_Url)&&($Ali_Url=="")) {
                        $arr_url['Notify_url'] = $Notify_Url;
                    } else if ($Ali_Url && $Notify_Url) {
                        $arr_url['Ali_url'] = $Ali_Url;
                        $arr_url['Notify_url'] = $Notify_Url;
                    }
                    $data = http($insert_url, $arr_url);
                    if(($Ali_Url)&&($Notify_Url=="")){
                        if ($data['Code']==0) {
                            $Return_Info['Description']='员工资料修改成功！';
                            $Return_Info['Code']=0;
                        } else {
                            $Return_Info['Description']=$data['Description'];
                            $Return_Info['Code']=1;
                        }
                    }else if(($Notify_Url)&&($Ali_Url=="")){
                        if ($data['Code']==0) {
                            $Return_Info['Description']='员工资料修改成功！';
                            $Return_Info['Code']=0;
                        } else {
                            $Return_Info['Description']=$data['Description'];
                            $Return_Info['Code']=1;
                        }
                    }else if ($Ali_Url&&$Notify_Url){
                        if ($data['Code']==0) {
                            $Return_Info['Description']='员工资料修改成功！';
                            $Return_Info['Code']=0;
                        } else {
                            $Return_Info['Description']=$data['Description'];
                            $Return_Info['Code']=1;
                        }
                    }
                }
            }else{
                $Return_Info['Description']='员工资料修改失败!';
            }
            $this->ajaxReturn( $Return_Info, 'json' );
            exit();
        }else{
            $url_staff_register  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserList";
            $arr_staff_register  = array(
                "SysNo" => session('data')['SysNO'],
            );
            $data  = http($url_staff_register, $arr_staff_register);
            $url_get_ali_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendList";
            $arr_get_ali_url  = array(
                "systemUserSysNo" => session('data')['SysNO'],
            );
            $data_ali  = http($url_get_ali_url, $arr_get_ali_url);
            $DetailAddress = explode("-",$data['model'][0]['DwellAddress']);
            $Province=$this->GetAddress(0);
            $City=$this->GetAddress($data['model'][0]['Province']);
            $Country=$this->GetAddress($data['model'][0]['City']);
            $this->assign( 'data', $data['model'][0] );
            $this->assign( 'Ali_Url',$data_ali['Ali_url'] );
            $this->assign( 'Notify_Url',$data_ali['Notify_url'] );
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

        R("Base/getMenu");
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
    /*支付宝服务商配置*/
    public function zfbConfig(){

        R("Base/getMenu");
        if( IS_POST ){
            $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerAliPayConfigEdit";
            $arr  = array(
                "CustomerServiceSysNO"  => session( 'data' )['SysNo'],              //商户服务商编号
                "APPID"                 => I( 'sx_appid', '', 'htmlspecialchars' ), //APIID
                "PID"                   => I( 'sx_shid', '', 'htmlspecialchars' ),  //商户ID
                "sub_PID"               => I( 'sx_zshid', '', 'htmlspecialchars' ), //子商户ID
                "Merchant_private_key"  => $_POST['sx_shsy'], //商户私钥
                "Merchant_public_key"   => $_POST['sx_shgy'], //商户公钥
                "Alipay_public_key"     => $_POST['sx_algy'], //阿里公钥
                "Type"					=> $_POST['sh_type'], //阿里公钥
            );
            $arrData  = http( $url, $arr );
            $this->ajaxReturn( $arrData );
            exit();
        }else{
            $flag = session('flag');//服务商商户0 或员工1
            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆商户
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3CustomerAliPayConfig";
                $arr  = array(
                    'CustomerServiceSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }
            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
                //商户通道查询
                $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
                $post_passageway_data['Type'] = 103;
                $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
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
            }

        }
        $this->display( 'commercial_tenant_alipay' );

    }
    /*兴业微信服务商配置*/
    public function xy_wxConfig(){
        R("Base/getMenu");
        if( IS_POST ){
            //银行通道配置新增
            $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageConfigEdit";
            $arr  = array(
                "CustomerServiceSysNo" => session( 'data' )['SysNo'],
                "APPID"         => I( 'sx_appid', '', 'htmlspecialchars' ), //APPID
                "MCHID"         => I( 'sx_fwsbh', '', 'htmlspecialchars' ), //服务商编号
                "Key"           => I( 'sx_shkey', '', 'htmlspecialchars' )  //商户Key
            );
            $arrData = http($url,$arr);
            $this->ajaxReturn( $arrData );
            exit();
        }else{
            //商户通道查询
            $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
            $post_passageway_data['Type'] = 104;
            $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
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
        }
        $this->display( 'commercial_tenant_config_xywx' );

    }
    /*兴业支付宝服务商配置*/
    public function xy_zfbConfig(){

        R("Base/getMenu");
        if( IS_POST ){
            //银行通道与支付宝配置新增
            $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageAndAliPayConfigInsert";
            $arr  = array(
                "Remarks"         => "AliPay",
                "WFT_APPID"         => I( 'yh_appid', '', 'htmlspecialchars' ), //APPID
                "WFT_MCHID"         => I( 'yh_fwsbh', '', 'htmlspecialchars' ), //服务商编号
                "WFT_KEY"           => I( 'yh_shkey', '', 'htmlspecialchars' ),  //商户Key
                "CustomerServiceSysNO"  => session( 'data' )['SysNo'],                  //商户服务商编号
                "Ali_APPID"                 => I( 'ali_appid', '', 'htmlspecialchars' ), //APIID
                "Ali_PID"                   => I( 'sx_shid', '', 'htmlspecialchars' ),  //商户ID
                "Ali_sub_PID"               => I( 'sx_zshid', '', 'htmlspecialchars' ), //子商户ID
                "Ali_merchant_private_key"  => $_POST['sx_shsy'], //商户私钥
                "Ali_merchant_public_key"   => $_POST['sx_shgy'], //商户公钥
                "ALi_alipay_public_key"     => $_POST['sx_algy'], //阿里公钥
//                "ALi_Status"			=> $_POST['sh_type'],
            );
            $arrData = http($url,$arr);
            $this->ajaxReturn( $arrData );
            exit();
        }else{
            //商户通道查询
            $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
            $post_passageway_data['Type'] = 105;
            $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
            $post_passageway_list = http($post_passageway_url, $post_passageway_data);
            if($post_passageway_list){
                foreach ($post_passageway_list as $row=>$val){
                    $data['model'][0]['SysNo']=$val['SysNo'];
                    $data['model'][0]['Type']=$val['Type'];
                }
                //银行通道与支付宝配置查询
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
        }
        $this->display( 'commercial_tenant_alipay_xyzfb' );

    }
    /*浦发微信服务商配置*/
    public function pf_wxConfig(){

        R("Base/getMenu");
        if( IS_POST ){
            //银行通道配置新增
            $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageConfigEdit";
            $arr  = array(
                "CustomerServiceSysNo" => session( 'data' )['SysNo'],
                "APPID"         => I( 'sx_appid', '', 'htmlspecialchars' ), //APPID
                "MCHID"         => I( 'sx_fwsbh', '', 'htmlspecialchars' ), //服务商编号
                "Key"           => I( 'sx_shkey', '', 'htmlspecialchars' )  //商户Key
            );
            $arrData = http($url,$arr);
            $this->ajaxReturn( $arrData );
            exit();
        }else{
            //商户通道查询
            $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
            $post_passageway_data['Type'] = 106;
            $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
            $post_passageway_list = http($post_passageway_url, $post_passageway_data);
            if($post_passageway_list){
                foreach ($post_passageway_list as $row=>$val){
                    $data['model'][0]['SysNo']=$val['SysNo'];
                    $data['model'][0]['Type']=$val['Type'];
                }
                //银行通道配置查询
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
        }
        $this->display( 'commercial_tenant_config_pfwx' );

    }
    /*浦发支付宝服务商配置*/
    public function pf_zfbConfig(){

        R("Base/getMenu");
        if( IS_POST ){
            //银行通道与支付宝配置新增
            $url  = C('SERVER_HOST')."IPP3Customers/SwiftPassageAndAliPayConfigInsert";
            $arr  = array(

                "Remarks"         => "AliPay",
                "WFT_APPID"         => I( 'yh_appid', '', 'htmlspecialchars' ), //APPID
                "WFT_MCHID"         => I( 'yh_fwsbh', '', 'htmlspecialchars' ), //服务商编号
                "WFT_KEY"           => I( 'yh_shkey', '', 'htmlspecialchars' ),  //商户Key
                "CustomerServiceSysNO"  => session( 'data' )['SysNo'],                  //商户服务商编号
                "Ali_APPID"                 => I( 'ali_appid', '', 'htmlspecialchars' ), //APIID
                "Ali_PID"                   => I( 'sx_shid', '', 'htmlspecialchars' ),  //商户ID
                "Ali_sub_PID"               => I( 'sx_zshid', '', 'htmlspecialchars' ), //子商户ID
                "Ali_merchant_private_key"  => $_POST['sx_shsy'], //商户私钥
                "Ali_merchant_public_key"   => $_POST['sx_shgy'], //商户公钥
                "ALi_alipay_public_key"     => $_POST['sx_algy'], //阿里公钥
//                "ALi_Status"			=> $_POST['sh_type'],
            );
            $arrData = http($url,$arr);
            $this->ajaxReturn( $arrData );
            exit();
        }else{
            //商户通道查询
            $post_passageway_data['CustomerSysNo'] = session( 'data' )['SysNo'];
            $post_passageway_data['Type'] = 107;
            $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
            $post_passageway_list = http($post_passageway_url, $post_passageway_data);
            if($post_passageway_list){
                foreach ($post_passageway_list as $row=>$val){
                    $data['model'][0]['SysNo']=$val['SysNo'];
                }
                //银行通道与支付宝配置查询
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
        }
        $this->display( 'commercial_tenant_alipay_pfzfb' );

    }
    // 商户资料读取修改
    public function MerchantDetail(){
        R("Base/getMenu");
        //修改
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
            //查询
        }else{
            //商户查询-实时
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
            /*商户费率查询(翼惠,兴业,浦发)*/
            $post_rate_data['CustomerSysNo'] = $SysNo;
            $post_rate_url = C('SERVER_HOST') . 'IPP3Customers/CustomerServiceRateList';
            $post_rate_list = http($post_rate_url, $post_rate_data);
            if($post_rate_list){
                foreach ($post_rate_list as $row=>$val){
                    $data['model'][0]['typerate'][$val['Type']]=$val['Rate'];
                }
            }
            //商户通道查询
            $post_passageway_data['CustomerSysNo'] = $SysNo;
            $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';//通道查询
            $post_passageway_list = http($post_passageway_url, $post_passageway_data);
            if($post_passageway_list){
                foreach ($post_passageway_list as $row=>$val){
                    foreach(json_decode($_COOKIE['passageway_list'],true ) as $k=>$v) {
                        if ($val['Type']==$v['Type']) {
                            $data['model'][0]["typedisplay"][$val['Type']]['TypeName']=$v['TypeName'];
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