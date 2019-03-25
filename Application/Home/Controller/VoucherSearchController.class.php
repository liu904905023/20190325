<?php
namespace Home\Controller;

use Think\Controller;

//use Common\Compose\Base;

class VoucherSearchController extends Controller {

    public function voucherlist() {
//        array (
//            'Time_Start' => '2018-08-13 00:00:00',
//            'Time_End' => '2018-08-13 23:59:59',
//            'employeeLoginName' => '',
//            'realName' => '',
//            'squeak' => '',
//            'orderNumber' => '',
//        )
        if(IS_POST){
            $Data['Time_Start'] = I('Time_Start');
            $Data['Time_End'] = I('Time_End');
            $Data['Ali_Token'] = I('squeak');
            $Data['Out_trade_no'] = I('orderNumber');
            $Data['LoginName'] = I('employeeLoginName');
            $Data['DisplayName'] = I('realName');
            $Data['PagingInfo']['PageSize'] = I('PageSize');
            $Data['PagingInfo']['PageNumber'] = I('PageNumber');
            if(session('data')['CustomersType']==1&session('flag')==0){
                $Data['CustomerSysNo'] = session('SysNO');
            }else if(session('servicestoretype')==1&session('flag')==1){
                $Data['SystemUserSysNo'] = session('SysNO');
                $Data['CustomerSysNo'] = session('servicestoreno');
            }else if(session('data')['CustomersType']==0&session('flag')==0){
                $Data['CustomerName'] = I("CustomerNames");
                $Data['Customer'] = I("Customer");
                $Data['CustomersTopSysNo'] = session('SysNO');

            }else{
                exit();
            }
            $Url = C("SERVER_HOST") . 'IPP3EFuli/EFuli_Order_CashvoucherDetailList';
            $List = http($Url, $Data);
            foreach ($List['Data']['model'] as $Row=>$Key) {
                $Return_List['model'][$Row]['SysNo'] = $Key['SysNo'];
                $Return_List['model'][$Row]['LoginName'] = $Key['LoginName'];
                $Return_List['model'][$Row]['DisplayName'] = $Key['DisplayName'];

                $Return_List['model'][$Row]['Customer'] = $Key['Customer'];
                $Return_List['model'][$Row]['CustomerName'] = $Key['CustomerName'];

                $Return_List['model'][$Row]['Ali_Token'] = $Key['Ali_Token']?$Key['Ali_Token']:'';
                $Return_List['model'][$Row]['Out_trade_no'] = $Key['Out_trade_no'];
                $Return_List['model'][$Row]['Time_Start'] = str_replace('/','-',$Key['Time_Start']);
                $Return_List['model'][$Row]['Total_fee'] = fee2yuan($Key['Total_fee']);
                $Return_List['model'][$Row]['Purchase_Num'] = $Key['Purchase_Num'];
                $Return_List['model'][$Row]['Selling_Amount'] = fee2yuan($Key['Selling_Amount']);
                $Return_List['model'][$Row]['SuccessCreateTime'] = str_replace('/','-',$Key['SuccessCreateTime']);
                $Return_List['model'][$Row]['SuccessStutes'] = $Key['SuccessStutes']==0?'失败':'成功';


            }
            $Return_List['totalCount'] = $List['Data']['totalCount'];
            $this->ajaxreturn($Return_List);
//            {"Time_Start":"2018-08-13 00:00:00","Time_End":"2018-08-13 23:59:59","squeak":"","orderNumber":""}
//            echo 1;

//            $Query_Data['CustomerTopSysNo'] = session( 'data' )['SysNo'];
//            $Query_Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Cashvoucher_ConfigList';


        }else{
            R("Base/getMenu");
            $this->display();
        }

    }

}

