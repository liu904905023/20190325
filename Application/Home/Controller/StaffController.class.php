<?php

namespace Home\Controller;

use Common\Compose\Base;

class StaffController extends Base{

    public function index(){

    }
    public function staff_register(){


        if( IS_POST ){
            $Ali_Url    =   I('Ali_Url');
            $Notify_Url =   I('Notify_Url');
            $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserInsert";
            $arr  = array(
                "CustomerServiceSysNo" => session( 'data' )['SysNo'],
                "loginname"            => I( 'sx_dlm' ),
                "displayname"          => I( 'sx_name' ),
                "departmentname"       => '',
                "phonenumber"          => I( 'mobile' ),
                "email"                => I( 'email' ),
                "password"             => I( 'sx_pass' ),
                "Alipay_store_id"      => I( 'store_id' ),
                "inuser"               => session( 'data' )['SysNo'],
                "edituser"             => session( 'data' )['SysNo'],
                "DwellAddress"         => I( "address" ),
                "DwellAddressID"       => I( "AddressNum" )
            );

//            \Think\log::record(json_encode($arr));
            $data  = http($url, $arr);
//            \Think\log::record(json_encode($data));
                if($data['Code']==0){
                    $Insert_Data['SystemUserSysNo']=$data['Data']['SystemUserSysNo'];
                    if (deep_in_array($Insert_Data)) {
                        $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
                        exit();
                    }
                    if (($Ali_Url=="")&&($Notify_Url=="")) {
                        $result['Description']='员工注册成功!';
                        $result['Code']=0;
                    }else{
                        if ($Notify_Url){
                            $Insert_Data['Notify_url']=$Notify_Url;
                            $Insert_Url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendInsert";
                            $Result_Ali_InFO = http($Insert_Url,$Insert_Data);

                        }
                        if($Ali_Url){
                            $Insert_Ali_Data['Ali_url']=$Ali_Url;
                            $Insert_Ali_Data['CustomerTopSysNo']='0';
                            $Insert_Ali_Data['CustomerServiceSysNo']=session( 'data' )['SysNo'];
                            $Insert_Ali_Data['SystemUserSysNo']=$data['Data']['SystemUserSysNo'];
                            $Insert_Ali_Data['Customer']=session( 'data' )['Customer'];
                            $Insert_Ali_Data['CustomerName']=session( 'data' )['CustomerName'];
                            $url_staff_register  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserList";
                            $arr_staff_register  = array(
                                "SysNo" => $data['Data']['SystemUserSysNo'],
                            );
                            $SystemUserInfo  = http($url_staff_register, $arr_staff_register);
                            $Insert_Ali_Data['LoginName']=$SystemUserInfo['model'][0]['LoginName'];
                            $Insert_Ali_Data['DisplayName']=$SystemUserInfo['model'][0]['DisplayName'];
                            $Insert_Ali_Data['InUser']=session( 'data' )['SysNo'];
                            if (deep_in_array($Insert_Ali_Data)) {
                                $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
                                exit();
                            }
                            $Insert_Ali_Url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendAliInsert";

                            $Result_InFo = http($Insert_Ali_Url,$Insert_Ali_Data);
                        }

                        if ($Result_InFo['Code']==0&&$Result_Ali_InFO['Code']==0) {
                            $result['Description']='员工注册成功!';
                            $result['Code']=0;
                        } else {
                            $result['Description']='员工注册失败';
                            $result['Code']=1;
                        }

                    }
                }else{
                    $result['Description']=$data['Description'];
                    $result['Code']=1;
                }

            $this->ajaxReturn( $result, 'json' );

            exit();

        }
        R("Base/getMenu");

        $this->display( 'staff_register' );

    }


    public function staff_list(){

//		var_dump( SESSION(data));

        R("Base/getMenu");

        $this->display();

    }





    public function stafflist(){

        if(session(flag)==1){

            exit;

        }



        $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserListCSsysno";

        $data  = array(

            "CustomerServiceSysNo" => session( 'data' )['SysNo'],

            "CustomersType" => session('data')['CustomersType'],

            "LoginName"            => I( 'username', '', 'htmlspecialchars' ),

            "PhoneNumber"          => I( 'phone', '', 'htmlspecialchars' ),

        );

        $data['PagingInfo']['PageSize'] = I( 'PageSize', '' );

        $data['PagingInfo']['PageNumber'] =I( 'PageNumber', 0 );

        $data = json_encode( $data );

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data )

        );



        $res = http_request( $url, $data, $head );

        $result = json_decode( $res, TRUE );

        $Systemnos = $result['model'][0]['SysNO'];

        if( session('AliAppId')==null){

            $AliAppId=$this->QueryCustomerAppId($Systemnos);

        }else{



            $AliAppId=session('AliAppId');

        }

        $ids = I('id');

        if($ids){

            $info = QueryCustomerSysNo($ids);

            $info = json_encode($info);

            $info = json_decode( $info, TRUE );

            $result['info']['topid']=$info['model'][0]['SysNO'];

            $result['info']['topname']=$info['model'][0]['DisplayName'];

        }

        $result['type'] =session(data)['CustomersType'] ;

        $result['AccessFlag'] = $this->checkasstoken();

        $result['AliAppId'] = $AliAppId;





        $this->ajaxReturn( $result, 'json');







    }



    public function query_staff(){

        $customerServiceSysNo = I( "customerServiceSysNo" );



        $arr = array(

            "customerServiceSysNo" => $customerServiceSysNo

        );



        $data['PagingInfo']['PageSize']   = 2;

        $data['PagingInfo']['PageNumber'] = 1;



        $url  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserByCSsysNo";

        $data = json_encode( $arr );

        //var_dump($data);

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            //"X-Ywkj-Authentication:" . strlen( $data ),

        );



        $list = http_request( $url, $data, $head );

        $list = json_decode( $list );

        $this->ajaxReturn( $list, 'json' );

    }



    public function querycustomer(){



        $SystemUserSysNo = I("SystemUserSysNo");

        $data = array(

            "SystemUserSysNo" => $SystemUserSysNo

        );

        $data['PagingInfo']['PageSize']   = I("PageSize");

        $data['PagingInfo']['PageNumber'] = I("PageNumber");

        $url  = C('SERVER_HOST')."IPP3Customers/IPP3CustomerShopList";

        $data = json_encode($data);

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            //"X-Ywkj-Authentication:" . strlen( $data ),

        );

        $list = http_request( $url, $data, $head );

        $list = json_decode( $list );

        $this->ajaxReturn( $list, 'json' );

        //$this->assign($list,"list");

        //$this -> display('Business/business');





    }





    public function servicequerycustomer(){

        //$CustomerSysNo = empty($_POST['CustomerSysNo'])? session(data)['SysNO']:$_POST['CustomerSysNo'];

        //$data['SystemUserSysNo']=I("SystemUserSysNo");

        if(isset($_POST['SystemUserSysNo'])){

            $data['SystemUserSysNo']=I("SystemUserSysNo");

        }



        if(session(flag)==0){

            $data['CustomersTopSysNo'] = session(data)['SysNo'];

        }

        if(session(flag)==1){

            $data['SystemUserSysNo'] = session(data)['SysNO'];

        }
        $Time_Start = $_POST['Time_Start'];

        $Time_end = $_POST['Time_End'];

        $data['RegisterTimeStart'] = $Time_Start;

        $data['RegisterTimeEnd'] = $Time_end;

        $data['Customer']   = I("CustomersName");

        $data['CustomerName']   = I("CustomersTrueName");

        $data['PagingInfo']['PageSize']   = I("PageSize");

        $data['PagingInfo']['PageNumber'] = I("PageNumber");

        $url  = C('SERVER_HOST')."IPP3Customers/IPP3CustomerShopList";

        $data = json_encode($data);

//         var_dump($data);echo $url;exit;

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            //"X-Ywkj-Authentication:" . strlen( $data ),

        );

        $list = http_request( $url, $data, $head );

        $list = json_decode( $list ,true);


        if(session(data)['CustomersType']){

            $list['type']=session(data)['CustomersType'];

        }

        if(session(flag)==1){

            $list['flag']= staffstoreorservice(session(SysNO));

        }

        $this->ajaxReturn( $list, 'json' );



    }



    public function staffquerystore($id= 16){

        //$data['SystemUserSysNo'] = session(data)['SysNO'];

        $data['SystemUserSysNo'] = $id;

        $data['PagingInfo']['PageSize']   = 1;

        $data['PagingInfo']['PageNumber'] = 0;

        $url  = C('SERVER_HOST')."IPP3Customers/IPP3GetCustomerServiceSysNo";

        $data = json_encode($data);

        //var_dump($data);

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            //"X-Ywkj-Authentication:" . strlen( $data ),

        );

        $list = http_request( $url, $data, $head );

        $list = json_decode( $list ,true);

        /*foreach ($list['model'] as $row=>$val){

        $info['model'][$row]['SysNo']=$val['SysNo'];

        $info['model'][$row]['CustomerName']=$val['CustomerName'];

        $info['model'][$row]['Phone']=$val['Phone'];

        $info['model'][$row]['CellPhone']=$val['CellPhone'];

        $info['model'][$row]['CustomersType']=$val['CustomersType'];

        $info['model'][$row]['CreateTime']=substr($val['CreateTime'],6,13);

        }

        $info['totalCount'] =$list['totalCount'];*/

        $this->ajaxReturn( $list, 'json' );





    }





    private function checkasstoken(){



        $url = C('SERVER_HOST')."IPP3Customers/IPP3CustomerAliPayConfig";

        $arr = array(

            "CustomerServiceSysNo"=> session('SysNO'),



        );

        $data = json_encode( $arr );

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data )

        );

        $res  = http_request( $url, $data, $head );

        $data = json_decode( $res, TRUE );

        $Code=0;

        if(strlen($data['app_auth_token'])>0){



            $Code=1;

        }

        return $Code;





    }



    private function QueryCustomerAppId($Systemnos){



        $data['systemUserSysNo'] = $Systemnos;

        $data = json_encode($data);

        $urls = C('SERVER_HOST') . "IPP3Customers/IPP3AliPayConfigBySUsysNo";

        $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data), "X-Ywkj-Authentication:" . strlen($data));

        $list = http_request($urls, $data, $head);

        $list = json_decode($list, true);

        session('AliAppId',$list['AppID']);

        return $list['AppID'];

    }

    public function selecturl(){//查询推送及阿里口碑
        $arr_url['SystemUserSysNo'] =I('SysNo');
        $Type=I('Type');
        if($Type==7){
            $Select_Url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendList";
        }else if($Type==8){
            $Select_Url = C( 'SERVER_HOST' ) . "IPP3Customers/SystemUser_Extend_AliList";
        }
        $data = http($Select_Url, $arr_url);
        if($Type==7){
            $Result_url =$data['Notify_url'];
        }else if($Type==8){
            $Result_url = $data['Data']['Ali_url'];
        }
        $this ->ajaxreturn($Result_url);
    }
    public function updateurl(){//修改推送
        $AliOrURL = I('AliOrURL');
        $arr_temp['SystemUserSysNo'] =I('SysNo');
        if (deep_in_array($arr_temp)) {
            $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
            exit();
        }
        if ($AliOrURL == 7) {
            $arr_temp['Notify_url'] = I('DataUrl');

            $select_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendList";
            $insert_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendInsert";
            $update_url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendUpdate";
            $data_url  = http($select_url, $arr_temp);//查询推送

            if ($data_url!=null) {
                if($data_url['Notify_url']==I('DataUrl')){
                    $data['Description']='推送地址修改成功！';
                    $data['Code']=0;
                }else{
                    $data = http($update_url, $arr_temp);
                    if ($data['Code']==0) {
                        $data['Description']='推送地址修改成功！';
                        $data['Code']=0;
                    } else {
                        $data['Description']=$data['Description'];
                        $data['Code']=1;
                    }
                }

            }else if ($data_url==null){
                $data = http($insert_url, $arr_temp);
                if ($data['Code']==0) {
                    $data['Description']='推送地址修改成功！';
                    $data['Code']=0;
                } else {
                    $data['Description']=$data['Description'];
                    $data['Code']=1;
                }
            }
        }else if ($AliOrURL == 8) {
            $Ali_Url = I('DataUrl');
            if($Ali_Url){
                $select_ali_url = C( 'SERVER_HOST' ) . "IPP3Customers/SystemUser_Extend_AliList";
                $return_ali_url = http($select_ali_url, $arr_temp);
                //减少一次请求接口次数，此处必须这么写。
                if($return_ali_url['Data']['Ali_url']){
                    $Update_Ali_Data['SystemUserSysNo']=I('SysNo');
                    $Update_Ali_Data['Ali_url']=$Ali_Url;
                    $Update_Ali_Data['EditUser']=session('data')['SysNo'];
                    if (deep_in_array($Update_Ali_Data)) {
                        $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
                        exit();
                    }
                    //$return_ali_url['Data']['Ali_url']
                    if($Ali_Url!=$return_ali_url['Data']['Ali_url']){
                        $Update_Ali_Url = C( 'SERVER_HOST' ) . "IPP3Customers/SystemUser_Extend_AliUpd";
                        $Result_Ali_Info = http($Update_Ali_Url, $Update_Ali_Data);
                    }else{
                        $Result_Ali_Info['Code'] = 0;
                    }
                }else{
                    $Insert_Ali_Data['Customer']=session('data')['Customer'];
                    $Insert_Ali_Data['CustomerName']=session('data')['CustomerName'];
                    $Insert_Ali_Data['CustomerTopSysNo']='0';
                    $Insert_Ali_Data['CustomerServiceSysNo']=session('data')['SysNo'];
                    $Insert_Ali_Data['SystemUserSysNo']=I('SysNo');
                    //查询商户所有信息

                    $Url_StaffInfo  = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserList";
                    $Arr_StaffInfo  = array(
                        "SysNo" => I('SysNo'),
                    );
                    $Result_StaffInfo  = http($Url_StaffInfo, $Arr_StaffInfo);
                    $Insert_Ali_Data['LoginName']=$Result_StaffInfo['model'][0]['LoginName'];
                    $Insert_Ali_Data['DisplayName']=$Result_StaffInfo['model'][0]['DisplayName'];
                    $Insert_Ali_Data['InUser']=session('data')['SysNo'];
                    $Insert_Ali_Data['Ali_url']=$Ali_Url;
                    if (deep_in_array($Insert_Ali_Data)) {
                        $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));
                        exit();
                    }
                    $Insert_Ali_Url = C( 'SERVER_HOST' ) . "IPP3Customers/IPP3SystemUserExtendAliInsert";
                    $Result_Ali_Info = http($Insert_Ali_Url, $Insert_Ali_Data);
                }

                if ($Result_Ali_Info['Code']==0) {
                    $data['Code']=$Result_Ali_Info['Code'];
                    $data['Description']='口碑地址修改成功！';
                }else{
                    $data['Code']=$Result_Ali_Info['Code'];
                    $data['Description']='口碑地址修改失败！';
                }

            }else{
                $data['Description']='口碑地址不允许为空！';
            }
        }

        $this ->ajaxreturn($data);
    }



}

