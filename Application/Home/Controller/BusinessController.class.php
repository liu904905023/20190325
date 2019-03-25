<?php

namespace Home\Controller;

use Common\Compose\Base;

class BusinessController extends Base {
    public function Business() {
        R("Base/getMenu");
        $this->display();
    }

    public function business_register() {
        R("Base/getMenu");
        $this->display();
    }

    public function business_detail() {
        $SysNO = I('SysNo', '');
        $data['sysno'] = $SysNO;
        $data['CustomersTopSysNo'] = session('SysNO');
        $url = C('SERVER_HOST') . "IPP3Customers/IPP3CustomerShopList";
        $list = http($url, $data);
        $info['customer'] = $list['model'][0]['Customer'];
        $info['phone'] = $list['model'][0]['Phone'];
        $info['CustomerName'] = $list['model'][0]['CustomerName'];
        $info['Email'] = $list['model'][0]['Email'];
        $info['Fax'] = $list['model'][0]['Fax'];
        $info['DwellAddress'] = $list['model'][0]['DwellAddress'];
        $info['DwellZip'] = $list['model'][0]['DwellZip'];
        $info['user_rate'] = $list['model'][0]['UserRate'];
        $info['rate'] = $list['model'][0]['Rate'];
        $info['RegisterTime'] = date("Y-m-d H:i:s", substr($list['model'][0]['RegisterTime'], 6, 10));
        $StaffSysNo = $this->QueryStaff($SysNO);
        $info['TopStaffId'] = $this->QueryStaffInfo($StaffSysNo);
        $post_rate_data['CustomerSysNo'] = $SysNO;
        $post_rate_url = C('SERVER_HOST') . 'IPP3Customers/CustomerServiceRateList';
        $post_rate_list = http($post_rate_url, $post_rate_data);
        if ($post_rate_list) {
            foreach ($post_rate_list as $row=>$val){
                $info['typerate'][$val['Type']][Rate]=$val['Rate'];
            }
        }
        $this->ajaxreturn($info);
    }

    public function QueryStaff($id) {
        $data['CustomerServiceSysNo'] = $id;
        $list = http(C('SERVER_HOST') . "IPP3Customers/IPP3CustomerUsersList", $data);
        return $list['model'][0]['SystemUserSysNo'];
    }

    public function QueryStaffInfo($id) {
        $data['SysNo'] = $id;
        $list = http(C('SERVER_HOST') . "IPP3Customers/IPP3SystemUserList", $data);
        return $list['model'][0]['DisplayName'];
    }

    public function customerrateupdate() {
        $data['SysNo'] = I('SysNo');
        $data['UserRate'] = I('Rate');
        $list = http(C('SERVER_HOST') . "IPP3Customers/IPP3CustomerUserRateUpdate", $data);
        $this->ajaxreturn($list);
    }

    public function businessregister (){
        $Wx_Type    = I("PassageWay_WX");
        $Zfb_Type   = I("PassageWay_AliPay");
        $Wx_Rate    = I("wx_rate");
        $Zfb_Rate   = I("zfb_rate");
        if((preg_match("/^\d*$/", I("SystemUserSysNo")))&&I('SystemUserSysNo')!=''){
        }else{
            $data['Code']=1;
            $data['Description']="商户注册失败！";
            $this->ajaxReturn( $data );
            return false;
        }


        foreach(json_decode($_COOKIE['passageway_list'],true ) as $key=>$val){
            if ($Wx_Type==$val['T'])  {
                $Wx_PassageWay=$val['W'];
                $Wx_Remarks=$val['R'];
            }
            if ($Zfb_Type==$val['T'])  {
                $Zfb_PassageWay=$val['W'];
                $Zfb_Remarks=$val['R'];
            }
        }
        $url  = C('SERVER_HOST')."IPP3Customers/IPP3CustomerShopInsert";
        $arr  = array(
            "Customer"         => I( "username" ),
            "Pwd"              => I( "passwd" ),
            "CustomerName"     => I( "realname" ),
            "Email"            => I( "email" ),
            "Phone"            => I( "username" ),
            "Fax"              => I( "fax" ),
            "SystemClassID"    => I( "systemclassid" ),
            "SystemClassName"  => I( "systemclassname" ),
            "DwellAddress"     => I( "address" ),
            "DwellAddressID"     => I( "AddressNum" ),
            "DwellZip"         => I( "zipcode" ),
            "Rate"         => (double)I( "sx_rate" ),
            "CustomersType"    => "1",
            "Vip_CustomerType" => "1",
            "Customer_field_one" => I("Customer_Wft"),
            "Customer_field_two"=>$_SERVER['HTTP_HOST'],
            "SystemUserSysNo" => I("SystemUserSysNo"),
            "NickName" => I("nickname")
        );
        if ($Wx_Rate==""&&$Zfb_Rate==""){
            $data['Code']=1;
            $data['Description']="请选择至少一种支付方式，且费率不能为空";
        }else{
            $data  = http($url, $arr);
        }
        if($data['Code']==0){
            $CustomerSysno = $data['Data']['CustomerServiceSysNo'];
            $Rate_Insert_Url  = C( 'SERVER_HOST' ) . "IPP3Customers/CustomerServiceRateADD";
            $PassageWay_Insert_Url  = C('SERVER_HOST')."IPP3Customers/CustomerServicePassageWayInsert";
            if ($Wx_Rate!=""&&$Zfb_Rate!=""){
                $Wx_DefaultRoles_Insert_Data  = array(
                    "CustomerServiceSysNo" => $CustomerSysno,
                    "InUser" => $CustomerSysno,
                    "EditUser" => $CustomerSysno,
                    "Type"=>$Wx_Type
                );
                $Zfb_DefaultRoles_Insert_Data  = array(
                    "CustomerServiceSysNo" => $CustomerSysno,
                    "InUser" => $CustomerSysno,
                    "EditUser" => $CustomerSysno,
                    "Type"=>$Zfb_Type
                );
                $DefaultRoles_Insert_Data =array($Wx_DefaultRoles_Insert_Data,$Zfb_DefaultRoles_Insert_Data);
                $Wx_Rate_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "Rate"            => $Wx_Rate,
                    "Type"            => $Wx_Type,
                    "PassageWay"      => $Wx_PassageWay
                );
                $Zfb_Rate_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "Rate"            => $Zfb_Rate,
                    "Type"            => $Zfb_Type,
                    "PassageWay"     => $Zfb_PassageWay
                );
                $Rate_Insert_Data =array($Zfb_Rate_Insert_Data,$Wx_Rate_Insert_Data);
                $Wx_PassageWay_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "PassageWay"     => $Wx_PassageWay,
                    "Type"            => $Wx_Type,
                    "Remarks"     => $Wx_Remarks
                );
                $Zfb_PassageWay_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "PassageWay"     => $Zfb_PassageWay,
                    "Type"            => $Zfb_Type,
                    "Remarks"     => $Zfb_Remarks
                );
            }
            else if ($Wx_Rate!=""&&$Zfb_Rate==""){
                $Wx_DefaultRoles_Insert_Data  = array(
                    "CustomerServiceSysNo" => $CustomerSysno,
                    "InUser" => $CustomerSysno,
                    "EditUser" => $CustomerSysno,
                    "Type"=>$Wx_Type
                );
                $DefaultRoles_Insert_Data =array($Wx_DefaultRoles_Insert_Data);
                $Wx_Rate_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "Rate"            => $Wx_Rate,
                    "Type"            => $Wx_Type,
                    "PassageWay"      => $Wx_PassageWay
                );
                $Rate_Insert_Data =array($Wx_Rate_Insert_Data);
                $PassageWay_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "PassageWay"     => $Wx_PassageWay,
                    "Type"            => $Wx_Type,
                    "Remarks"     => $Wx_Remarks
                );
            }
            else if ($Wx_Rate==""&&$Zfb_Rate!=""){
                $Zfb_DefaultRoles_Insert_Data  = array(
                    "CustomerServiceSysNo" => $CustomerSysno,
                    "InUser" => $CustomerSysno,
                    "EditUser" => $CustomerSysno,
                    "Type"=>$Zfb_Type
                );
                $DefaultRoles_Insert_Data =array($Zfb_DefaultRoles_Insert_Data);
                $Zfb_Rate_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "Rate"            => $Zfb_Rate,
                    "Type"            => $Zfb_Type,
                    "PassageWay"     => $Zfb_PassageWay
                );
                $Rate_Insert_Data =  array($Zfb_Rate_Insert_Data);
                $PassageWay_Insert_Data  = array(
                    "CustomerSysNo"   => $CustomerSysno,
                    "PassageWay"     => $Zfb_PassageWay,
                    "Type"            => $Zfb_Type,
                    "Remarks"     => $Zfb_Remarks
                );
            }else{

            }
            $this -> CustomerAndSystemDefaultRoles($DefaultRoles_Insert_Data);
            $ratedata = http($Rate_Insert_Url, $Rate_Insert_Data);
            if ($ratedata['Code']==0){
                $data['Code']=0;
                $data['Description']="商户注册成功";
            }else{
                $data['Code']=1;
                $data['Description']="商户注册失败！";
                $this->ajaxReturn( $data );
                return false;
            }
            if ($Wx_Rate!=""&&$Zfb_Rate!=""){
                $wxpassagewaydata = http($PassageWay_Insert_Url, $Wx_PassageWay_Insert_Data);
                if ($wxpassagewaydata['Code']==0){
                    $data['Code']=0;
                    $data['Description']="商户注册成功！";
                    $zfbpassagewaydata = http($PassageWay_Insert_Url, $Zfb_PassageWay_Insert_Data);
                    if ($zfbpassagewaydata['Code']==0){
                        $data['Code']=0;
                        $data['Description']="商户注册成功！";
                    }else{
                        $data['Code']=1;
                        $data['Description']="商户注册失败！";
                        $this->ajaxReturn( $data );
                        return false;
                    }
                }else{
                    $data['Code']=1;
                    $data['Description']="商户注册失败！";
                    $this->ajaxReturn( $data );
                    return false;
                }
            }else{
                $passagewaydata = http($PassageWay_Insert_Url, $PassageWay_Insert_Data);
                if ($passagewaydata['Code']==0){
                    $data['Code']=0;
                    $data['Description']="商户注册成功！";
                }else{
                    $data['Code']=1;
                    $data['Description']="商户注册失败！";
                    $this->ajaxReturn( $data );
                    return false;
                }
            }
        }else{
            $data['Code']=1;
            $data['Description']="商户注册失败！";
            $this->ajaxReturn( $data );
            return false;
        }
        $this->ajaxReturn( $data );
    }

    public function customeruserupdate() {
        $data = array(
            "CustomerServiceSysNo" => I("customerid"),
            "SystemUserSysNo" => I("staffid")
        );
        $url = C('SERVER_HOST') . "IPP3Customers/IPP3CustomerUserUpdate";
        $res = http($url, $data);
        $this->ajaxReturn($res);
    }

    private function servicequerycustomer($Customers) {
        if (session(flag) == 0) {
            $data['CustomersTopSysNo'] = session(data)['SysNo'];
        }
        $data['Customer'] = $Customers;
        $data['PagingInfo']['PageSize'] = 1;
        $data['PagingInfo']['PageNumber'] = 0;
        $url = C('SERVER_HOST') . "IPP3Customers/IPP3CustomerList";
        $list = http($url, $data);
        return $list['model'][0]['SysNo'];
    }
    private function CustomerAndSystemDefaultRoles($DefaultRoles_Insert_Data) {
        $data=$DefaultRoles_Insert_Data;
        $url=C('SERVER_HOST') . "IPP3Customers/IPP3CustomerAndSystemDefaultRoles";
        $list = http($url,$data);
    }
}



