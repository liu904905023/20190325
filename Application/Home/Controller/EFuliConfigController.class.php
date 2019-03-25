<?php
namespace Home\Controller;


use Common\Compose\Base;

class EFuliConfigController extends Base {

    public function EFuli_config() {
        if(IS_POST){
            if(session('data')['CustomersType']==0&session('flag')==0){

            }else{
                $this->ajaxReturn(array('Code' => 1, 'Description' => "该角色无此权限！"));
                exit();
            }
            $Query_Data['CustomerTopSysNo'] = session( 'data' )['SysNo'];
            $Query_Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Cashvoucher_ConfigList';
            $Query_List = http($Query_Url, $Query_Data);
            $Data['APPID'] = I('appid');
            $Data['Version'] = I('versionNumber');
            $Data['APP_PUBLIC_KEY'] = I('publickey');
            $Data['APP_PRIVATE_KEY'] = I('privatekey');
            $Data['OPEN_PUBLIC_KEY'] = I('openkey');
            $Data['CustomerTopSysNo'] = session( 'data' )['SysNo'];
            if (deep_in_array($Data)) {
                $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请重新填写！"));
                exit();
            }
            if($Query_List['Data']!=''){
                $Data['SysNo'] = $Query_List['Data']['SysNo'];
                $Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Cashvoucher_ConfigUpdate';
                $List = http($Url, $Data);
                $this->ajaxreturn($List);
            }else{
                $Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Cashvoucher_ConfigInsert';
                $List = http($Url, $Data);
                $this->ajaxreturn($List);
            }

        }else{
            R("Base/getMenu");
            $Data['CustomerTopSysNo'] = session( 'data' )['SysNo'];
            $Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Cashvoucher_ConfigList';
            $List = http($Url, $Data);
            if($List['Data']==''){
                $isUorI= 1;
            }else{
                $isUorI= 0;
            }
            if(session('data')['CustomersType']==0&session('flag')==0){
                $RoleValue=0;
            }else{
                $RoleValue=1;
            }
            $this->assign('data',$List['Data']);
            $this->assign('isUorI',$isUorI);
            $this->assign('RoleValue',$RoleValue);
            $this->display();
        }

    }

}

