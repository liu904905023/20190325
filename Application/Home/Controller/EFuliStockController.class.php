<?php
namespace Home\Controller;
use Common\Compose\Base;
class EFuliStockController extends Base{

    public function stocklist() {
        if(IS_POST){
            if(session('data')['CustomersType']==0&session('flag')==0){

            }else{
                $this->ajaxReturn(array('Code' => 1, 'Description' => "该角色无此权限！"));
                exit();
            }
            $Data['CustomerTopSysNo']=session( 'data' )['SysNo'];
            $Data['Product_no'] = I('productNumber');
            $Url = C('SERVER_HOST') . 'IPP3EFuli/EFuli_StockRealtimeList';
            $List = http($Url, $Data);
            foreach ($List['Data'] as $Row=>$Key) {
                $Return_List['model'][$Row]['Product_no'] = $Key['Product_no'];
                $Return_List['model'][$Row]['SysNo'] = $Key['SysNo'];
                $Return_List['model'][$Row]['Money_TypeSysNo'] = $Key['EFuli_Money_TypeSysNo'];
                $Return_List['model'][$Row]['Purchase_Amount'] = fee2yuan($Key['Purchase_Amount']);
                $Return_List['model'][$Row]['Purchase_Num'] = $Key['Purchase_Num'];
                $Return_List['model'][$Row]['Selling_Amount'] = fee2yuan($Key['Selling_Amount']);
                $Return_List['model'][$Row]['Selling_Num'] = $Key['Selling_Num'];
                $Return_List['model'][$Row]['Amount'] = $Key['EFuli_Money_TypeSysNo'];
                $Return_List['model'][$Row]['Purchase_Time'] = str_replace('/','-',$Key['Purchase_Time']);
                $Return_List['model'][$Row]['Selling_Time'] =str_replace('/','-',$Key['Selling_Time']);
            }
//            $Return_List['totalCount'] = count($List['Data']);
            $this->ajaxreturn($Return_List);
        }else{
            R("Base/getMenu");
            $this->assign('data',$this->queryStockAmount());
            $this->display();
        }

    }

    public function addStock() {
        if(IS_POST){
            if(session('data')['CustomersType']==0&session('flag')==0){

            }else{
                $this->ajaxReturn(array('Code' => 1, 'Description' => "该角色无此权限！"));
                exit();
            }
            $Query_Data['CustomerTopSysNo'] = session( 'data' )['SysNo'];
            $Query_Config_Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Cashvoucher_ConfigList';
            $Query_Config_List = http($Query_Config_Url, $Query_Data);

            $Query_Stock_Url = C('SERVER_HOST').'IPP3EFuli/EFuli_StockRealtimeList';
            $Query_Stock_List = http($Query_Stock_Url, $Query_Data);
            foreach ($Query_Stock_List['Data'] as $value) {
                if ($value['EFuli_Money_TypeSysNo'] == I('money')) {
                    $Return_Info['Code']=1;
                    $Return_Info['Description']='此库存信息已存在，请勿重复填加！';
                    $this->ajaxReturn($Return_Info);
                }
            }

            $Insert_Data['EFuli_Money_TypeSysNo'] = I('money');
            $Insert_Data['Product_no'] = I('productNumber');
            $Insert_Data['Purchase_Amount'] = yuan2fee(I('stockMoney'));
            $Insert_Data['Purchase_Num'] = I('stockCount');
            $Insert_Data['Selling_Amount'] = yuan2fee(I('shipmentMoney'));
            $Insert_Data['Selling_Num'] = I('stockCount');
            $Insert_Data['APPID'] = $Query_Config_List['Data']['APPID'];
            $Insert_Data['CustomerTopSysNo'] = session( 'data' )['SysNo'];
            if (deep_in_array($Insert_Data)) {

                $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));

                exit();
            }
            $Insert_Url = C('SERVER_HOST').'IPP3EFuli/EFuli_StockInsert';
            $Insert_List = http($Insert_Url, $Insert_Data);
            $this->ajaxReturn($Insert_List);
        }

    }

    public function queryStockAmount() {
        $Query_Data['SysNo'] =I('SysNo');
        $Query_Data['CustomerTopSysNo'] = session( 'data' )['SysNo'];
        $Query_Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Money_TypeList';
        $Query_List = http($Query_Url, $Query_Data);
        foreach($Query_List['Data'] as $key=>$row){
            foreach($row as $k =>$v) {
                if($k=='Money'){
                    $temp[$key]['SysNo'] = $row['SysNo'];
                    $temp[$key]['Money'] = fee2yuan($v);
                }
            }
        }
        if(I('SysNo')){
            $this->ajaxreturn($temp);
        }else{
            return $temp;
        }

    }

    public function updateStock() {
        if(session('data')['CustomersType']==0&session('flag')==0){

        }else{
            $this->ajaxReturn(array('Code' => 1, 'Description' => "该角色无此权限！"));
            exit();
        }
        $Data = array (
            'SysNo' =>I('SysNo'),
            'Number' =>I('stockCount')
        );
        if (deep_in_array($Data)) {
            $this->ajaxReturn(array('Code' => 1, 'Description' => "进货数量参数错误，请重新填写！"));
            exit();
        }
        $Url = C('SERVER_HOST') . 'IPP3EFuli/EFuli_StockUpdPurchase_NumSP';
        $List = http($Url, $Data);
        $this->ajaxreturn($List);
    }

}

