<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class CCBCConfigController extends Base {

    /*商网商配置*/
    public function ccbc_config(){
        if( IS_POST ){
            if (session('data')['CustomersType'] == 1 &  session('flag') == 0) {//商登陆
                $Query_Arr['CustomerSysNo'] = session('data')['SysNo'];
                $Query_Url = C('SERVER_HOST') . "IPP3Customers/CCBCustomerServiceConfigList";
                $ReturnData = http($Query_Url, $Query_Arr);
                $Arr = array(
                    "CustomerSysNo" => session('data')['SysNo'],
                    "hospid" => I('hospid', '', 'htmlspecialchars'),
                    "key" => I('key', '', 'htmlspecialchars')
                );
                if ($ReturnData['Code']==0) {
                    $Arr['SysNo'] = $ReturnData['Data']['SysNo'];
                    $Update_Url = C('SERVER_HOST') . "IPP3Customers/CCBCustomerServiceConfigUpdate";
                    $arrData = http($Update_Url, $Arr);
                }else{
                    $Insert_Url = C('SERVER_HOST') . "IPP3Customers/CCBCustomerServiceConfigInsert";
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
            if (session('data')['CustomersType'] == 1 &session('flag') == 0) {//服务商登陆
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/CCBCustomerServiceConfigList";
                $arr  = array(
                    'CustomerSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }else{
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'ccbc_config' );

    }
      /*员工配置*/
    public function ccbc_user_config(){
        if( IS_POST ){
            if (session('servicestoretype') == 1 &  session('flag') == 1) {
                $Query_Arr['SystemUserSysNo'] = session('SysNO');
                $Query_Arr['CustomerSysNo'] = session('servicestoreno');
                $Query_Url = C('SERVER_HOST') . "IPP3Customers/CCBSystemUserConfigList";
                $ReturnData = http($Query_Url, $Query_Arr);
                $Arr = array(
                    "SystemUserSysNo" => session('SysNO'),
                    "CustomerSysNo" => session('servicestoreno'),
                    "terminalid" => I('terminalid', '', 'htmlspecialchars')
                );
                if ($ReturnData['Data']) {
                    $Update_Url = C('SERVER_HOST') . "IPP3Customers/CCBSystemUserConfigUpdate";
                    $Arr['SysNo'] = $ReturnData['Data'][0]['SysNo'];
                    $arrData = http($Update_Url, $Arr);
                }else{
                    $Insert_Url = C('SERVER_HOST') . "IPP3Customers/CCBSystemUserConfigInsert";
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
            if (session('servicestoretype') == 1 &session('flag') == 1) {
                $url  = C( 'SERVER_HOST' ) . "IPP3Customers/CCBSystemUserConfigList";
                $arr  = array(
                    'CustomerSysNo' => session( 'data' )['SysNo'],
                );
                $arrData  = http( $url, $arr );
                $this->assign( 'data', $arrData );
            }else{
                $this->assign( 'passtype', -1 );
            }
        }
        $this->display( 'ccbc_user_config' );

    }

}

