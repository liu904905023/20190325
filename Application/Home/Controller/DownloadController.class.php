<?php

namespace Home\Controller;

use Common\Compose\Base;

class DownloadController extends Base {

    protected function _initialize() {
        ini_set("memory_limit", "1024M"); // 不够继续加大
        set_time_limit(0);
    }

    //导出交易订单

       public function downloadorder() {
        $storename = $_POST['storename'];//商户名称

        $Time_Start = $_POST['Time_Start'];
        $Time_end = $_POST['Time_End'];

        $out_trade_no = $_POST['ordernum'];//订单号

        $Customer = $_POST['Customer'];//跳转页商户名称

        $SystemUserSysNo = $_POST['SystemUserSysNo'];//员工主键

        $Customer = $_POST['Customer'];//跳转页商户名称

        $ordertype = $_POST['Ordertype'];

        $CustomerNames = $_POST['CustomerNames'];

        $ButtonType = I('input_hidden', "");


        $PageNumber = $_POST['PageNumber'];

        $PageSize = $_POST['PageSize'];

        $data = array(

            "Time_Start" => $Time_Start,

            "Time_end" => $Time_end,

            "Out_trade_no" => $out_trade_no,

            "CustomerName" => $CustomerNames,

            "Customer" => $storename,

            "Pay_Type" => $ordertype

        );

        $flag = session('flag');                            //0-服务商登陆  1-员工登陆

        $type = session(data)['CustomersType'];             //0-服务商  1-商户

        $servicestoretype = session('servicestoretype');    //0-服务商员工 1-商户员工

        //服务商登录

        if ($flag == 0) {

            //服务商

            if ($type == 0) {

                if ($Customer != "NaN") {

                    $data['Customer'] = $Customer;

                } else {

                    $data['Customer'] = $_POST['storename'];

                }


            } //商户

            else if ($type == 1) {


                if ($SystemUserSysNo != "NaN") {

                    $data['SystemUserSysNo'] = $SystemUserSysNo;

                } else {
                    $data['LoginName'] = $storename;//员工登录名 20170227

                    $data['DisplayName'] = $CustomerNames;//员工姓名 20170227

                    $data['CustomerSysNo'] = session(SysNO);


                }

            }

        } //员工登录

        else if ($flag == 1) {


            //服务商下员工

            if ($servicestoretype == 0) {

                if ($Customer != "NaN") {

					$data['CustomerName'] = $CustomerNames;

                    $data['Customer'] = $Customer;

                    $data['SystemUserTopSysNo'] = session('SysNO');

                    $data['CustomersTopSysNo'] = session('servicestoreno');

                } else {

                    // $data['Customer']=$_POST['storename'];

                    $data['SystemUserTopSysNo'] = session('SysNO');

                    $data['CustomersTopSysNo'] = session('servicestoreno');

                }

            }

            //商户下员工

            if ($servicestoretype == 1) {

                if ($SystemUserSysNo != "NaN") {

                    $data['SystemUserSysNo'] = $SystemUserSysNo;

                } else {

                    $data['SystemUserSysNo'] = session(SysNO);//20160726


                }

            }

        }


        if (($ButtonType == "a") || ($ButtonType == "")) {


            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆 传入主键

                $data['CustomersTopSysNo'] = session('SysNO');

                if ($out_trade_no) {

                    $url = C('SERVER_HOST') . "POS/POSOrderList";

                } else {

                    $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListSP ";

                }

            } else {

                $url = C('SERVER_HOST') . "POS/POSOrderList";

            }


        } else if ($ButtonType == "b") {


            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登录

                $data['CustomersTopSysNo'] = session('SysNO');

            }

            if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录

                $data['LoginName'] = $storename;//员工登录名 20170227

                $data['DisplayName'] = $CustomerNames;//员工姓名 20170227

                $data['CustomerSysNo'] = session('SysNO');

            }

            if ($servicestoretype == 0 & $flag == 1) {//服务商下员工

                $data['CustomersTopSysNo'] = session('servicestoreno');

            }

            if ($servicestoretype == 1 & $flag == 1) {

                $data['SystemUserSysNo'] = session('SysNO');

            }

            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登录

                $url = C('SERVER_HOST') . "IPP3Order/IPP3Order_Fund_Customer_DropSP";

            } else {

                $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListcollect";
            }

        }

   
        $list =exportpost($url,$data);//分次请求


        if (($ButtonType == "a") || ($ButtonType == "")) {

            foreach ($list['model'] as $row => $val) {

                $info[$row]['SysNo'] = $val['SysNo'];

                $info[$row]['LoginName'] = $val['LoginName'];

                $info[$row]['DisplayName'] = $val['DisplayName'];

                $info[$row]['CustomerName'] = $val['CustomerName'];

                $info[$row]['Out_trade_no'] = "'" . $val['Out_trade_no'];

                $info[$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);

                $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']); //交易金额

                $info[$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);   //折后金额

                $info[$row]['Cash_fee_type'] = "人民币";

                $info[$row]['Time_Start'] = $val['Time_Start'];

            }

            foreach ($info[0] as $field => $v) {

                if ($field == 'SysNo') {

                    $headArr[] = '序号';

                }

                if ($field == 'LoginName') {

                    $headArr[] = '登录名';

                }

                if ($field == 'DisplayName') {

                    $headArr[] = '真实姓名';

                }

                if ($field == 'CustomerName') {

                    $headArr[] = '商户名称';

                }

                if ($field == 'Out_trade_no') {

                    $headArr[] = '订单号';

                }

                if ($field == 'Pay_Type') {

                    $headArr[] = '交易类型';

                }

                if ($field == 'Total_fee') {

                    $headArr[] = '交易金额';

                }

                if ($field == 'Cash_fee') {

                    $headArr[] = '折后金额';

                }

                if ($field == 'Cash_fee_type') {

                    $headArr[] = '交易币种';

                }

                if ($field == 'Time_Start') {

                    $headArr[] = '交易时间';

                }

            }

            $filename = "交易订单报表";

        } else if ($ButtonType == "b") {

            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登录

                foreach ($list['Data']['model'] as $row => $val) {

                    $info[$row]['CustomerName'] = $val['CustomerName'];

                    $info[$row]['DisplayName'] = $val['DisplayName'];

                    $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);         //交易金额

                    $info[$row]['fee'] = fee2yuan($val['fee']);                   //实际交易金额

                    $info[$row]['Cash_fee_type'] = "人民币";                      //交易币种

                    $info[$row]['Tradecount'] = $val['Tradecount'];               //交易笔数
                }

            } else {

                foreach ($list['model'] as $row => $val) {

                    if ($type == 1 & $flag == 0) {

                        $info[$row]['LoginName'] = $val['LoginName'];

                        $info[$row]['DisplayName'] = $val['DisplayName'];

                    } else if ($servicestoretype == 0 & $flag == 1) {

                        $info[$row]['CustomerName'] = $val['CustomerName'];

                    } else if ($servicestoretype == 1 & $flag == 1) {

                        $info[$row]['DisplayName'] = $val['DisplayName'];

                    }

                    $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);         //交易金额

                    $info[$row]['fee'] = fee2yuan($val['fee']);                   //实际交易金额

                    $info[$row]['Cash_fee_type'] = "人民币";                      //交易币种

                    $info[$row]['Tradecount'] = $val['Tradecount'];               //交易笔数
                }
            }

            foreach ($info[0] as $field => $v) {
// var_dump($info[0]);exit;
                if ($type == 0 & $flag == 0) {

                    if ($field == 'CustomerName') {

                        $headArr[] = '商户名称';

                    }
                    if ($field == 'DisplayName') {

                        $headArr[] = '业务姓名';
                    }

                } else if ($type == 1 & $flag == 0) {
                    if ($field == 'LoginName') {

                        $headArr[] = '登录名称';

                    }
                    if ($field == 'DisplayName') {

                        $headArr[] = '员工名称';

                    }

                } else if ($servicestoretype == 0 & $flag == 1) {

                    if ($field == 'CustomerName') {

                        $headArr[] = '商户名称';

                    }

                } else if ($servicestoretype == 1 & $flag == 1) {

                    if ($field == 'DisplayName') {

                        $headArr[] = '员工名称';

                    }

                }


                if ($field == 'Total_fee') {

                    $headArr[] = '交易金额';

                }

                if ($field == 'fee') {

                    $headArr[] = '实际交易金额';

                }

                if ($field == 'Cash_fee_type') {

                    $headArr[] = '交易币种';

                }

                if ($field == 'Tradecount') {

                    $headArr[] = '交易笔数';

                }

            }

            $filename = "交易订单汇总报表";

        }

        $this->getExcel($filename, $headArr, $info);

    }


    //导出退款

    public function downloadrefund() {
        $Time_Start = $_POST['Time_Start'];

        $Time_end = $_POST['Time_End'];

        $out_trade_no = $_POST['ordernum'];                                                             //订单号

        $data = array(

            "Time_Start" => $Time_Start,

            "Time_end" => $Time_end,

            "Out_trade_no" => $out_trade_no

        );

        if(session(flag)==1&&session(servicestoretype)==1){//员工
            $data['SystemUserSysNo'] = session('SysNO');

        }else if(session(data)['CustomersType'] == 1){

        }

        $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderFundListSP";

        $list = exportpost($url,$data);//分次请求

        foreach ($list['model'] as $row => $val) {

            $info[$row]['SysNo'] = $val['SysNo'];                    //序号

            $info[$row]['Out_trade_no'] = "'" . $val['Out_trade_no'];  //订单号

            $info[$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']); //交易类型

            $info[$row]['Cash_fee_type'] = "人民币";                 //交易币种

            $info[$row]['Time_Start'] = $val['Time_Start'];          //交易时间

            $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);  //订单金额

            $info[$row]['refund_fee'] = fee2yuan($val['refund_fee']);//已退金额

            $info[$row]['fee'] = fee2yuan($val['fee']);              //可退金额

            $info[$row]['refundCount'] = $val['refundCount'];        //退款笔数

        }


        foreach ($info[0] as $field => $v) {

            if ($field == 'SysNo') {

                $headArr[] = '序号';

            }

            if ($field == 'Out_trade_no') {

                $headArr[] = '订单号';

            }

            if ($field == 'Pay_Type') {

                $headArr[] = '交易类型';

            }

            if ($field == 'Cash_fee_type') {

                $headArr[] = '交易币种';

            }

            if ($field == 'Time_Start') {

                $headArr[] = '交易时间';

            }

            if ($field == 'Total_fee') {

                $headArr[] = '订单金额';

            }

            if ($field == 'refund_fee') {

                $headArr[] = '已退金额';

            }

            if ($field == 'fee') {

                $headArr[] = '可退金额';

            }

            if ($field == 'refundCount') {

                $headArr[] = '退款笔数';

            }

        }


        $filename = "退款";

        $this->getExcel($filename, $headArr, $info);

    }

    //导出退款查询

    public function downloadrefundsearch() {

        $Time_Start = empty($_POST['sx_start1']) ? $_POST['sx_start1'] : $_POST['sx_start1'] . " 00:00:00";      //订单开始时间

        $Time_end = empty($_POST['sx_end1']) ? $_POST['sx_end1'] : $_POST['sx_end1'] . " 23:59:59";              //订单结束时间

        $Create_Time_Start =$_POST['Create_Time_Start'];

        $Create_Time_end = $_POST['Create_Time_end'];

        $Out_trade_no = $_POST['Out_trade_no'];                                                             //订单号

        $Transaction_id = $_POST['Transaction_id'];

        $CustomerNames = $_POST['CustomerNames'];

        $storename = $_POST['storename'];

        $Ordertype = $_POST['Ordertype'];


        $data = array(

            "Transaction_id" => $Transaction_id,

            "Out_trade_no" => $Out_trade_no,

            "Time_Start" => $Time_Start,

            "Time_end" => $Time_end,

            "Create_Time_Start" => $Create_Time_Start,

            "Create_Time_end" => $Create_Time_end,

            "CustomerName" => $CustomerNames,

            "Customer" => $storename,

            "Pay_Type" => $Ordertype

        );

        $flag = session(flag);

        $type = 3;

        if ($flag == 1) {

            $type = staffstoreorservice(session(SysNO));

        }

        if (session(data)['CustomersType'] == 0 & $flag == 0) {

            $data['CustomersTopSysNo'] = session(SysNO);

        }
        if (session(data)['CustomersType'] == 1 & $flag == 0) {

            $data['CustomerSysNo'] = session(SysNO);
        }

        if ($type == 1) {

            $data['SystemUserSysNo'] = session(SysNO);
        }
        if ($type == 0) {

            $data['Customer'] = $_POST['Customer'];
        }

        $url =  C('SERVER_HOST') . "IPP3Order/IPP3RMA_RequestSP";

        $list = exportpost($url,$data);//分次请求

        foreach ($list['model'] as $row => $val) {

            $info[$row]['LoginName'] = $val['LoginName'];

            $info[$row]['DisplayName'] = $val['DisplayName'];

            $info[$row]['Out_trade_no'] = "'" . $val['Out_trade_no'];       //订单号

            $info[$row]['CustomerName'] = $val['CustomerName'];

            $info[$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);    //交易类型

            $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);       //交易金额

            $info[$row]['Refund_fee'] = fee2yuan($val['Refund_fee']);     //退款金额

            $info[$row]['Cash_fee_type'] = "人民币";                      //交易币种

            $info[$row]['Time_Start'] = $val['Time_Start'];               //交易时间

            $info[$row]['CreateTime'] = $val['CreateTime'];  //退款时间CreateTime


        }

        foreach ($info[0] as $field => $v) {

            if ($field == 'LoginName') {

                $headArr[] = '登录名称';

            }
            if ($field == 'DisplayName') {

                $headArr[] = '真实姓名';

            }

            if ($field == 'Out_trade_no') {

                $headArr[] = '订单号';

            }
            if ($field == 'CustomerName') {

                $headArr[] = '商户名称';

            }

            if ($field == 'Pay_Type') {

                $headArr[] = '交易类型';

            }

            if ($field == 'Total_fee') {

                $headArr[] = '交易金额';

            }
            if ($field == 'Refund_fee') {

                $headArr[] = '退款金额';

            }

            if ($field == 'Cash_fee_type') {

                $headArr[] = '交易币种';

            }

            if ($field == 'Time_Start') {

                $headArr[] = '交易时间';

            }

            if ($field == 'CreateTime') {

                $headArr[] = '退款时间';

            }

        }

        $filename = "退款查询";

        $this->getExcel($filename, $headArr, $info);

    }


//服务商员工订单汇总

    public function downloadsummary() {

        $Time_Start = $_POST['Time_Start'];

        $Time_end = $_POST['Time_End'];

        $staffloginname = I('staffloginname', "");

        $realname = I('realname', "");

        $phone = I('phone', "");

        $PageNumber = I('PageNumber', "");

        $PageSize = I('PageSize', "");

        $Ordertype = $_POST['Ordertype'];

        $serviceno = session('SysNO');

        $servicestaffno = "";

        $data = array(

            "CustomersTopSysNo" => $serviceno,

            "SystemUserTopSysNo" => $servicestaffno,

            "LoginName" => $staffloginname,

            "DisplayName" => $realname,

            "PhoneNumber" => $phone,

            "Pay_Type" => $Ordertype,

            "Time_Start" => $Time_Start,

            "Time_end" => $Time_end

        );


        $url = C('SERVER_HOST') . "IPP3Order/IPP3Order_Group_CustomerUserList";

        $list = exportpost($url,$data);//分次请求

        foreach ($list['model'] as $row => $val) {

            $info[$row]['LoginName'] = $val['LoginName'];

            $info[$row]['DisplayName'] = $val['DisplayName'];

            $info[$row]['PhoneNumber'] = $val['PhoneNumber'];

            $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);

            $info[$row]['refund_fee'] = fee2yuan($val['refund_fee']);

            $info[$row]['Fee'] = fee2yuan($val['Fee']);

        }


        foreach ($info[0] as $field => $v) {

            if ($field == 'LoginName') {

                $headArr[] = '员工登录名';

            }

            if ($field == 'DisplayName') {

                $headArr[] = '真实姓名';

            }

            if ($field == 'PhoneNumber') {

                $headArr[] = '电话';

            }

            if ($field == 'Total_fee') {

                $headArr[] = '交易金额';

            }

            if ($field == 'refund_fee') {

                $headArr[] = '退款金额';

            }

            if ($field == 'Fee') {

                $headArr[] = '实际金额';

            }

        }

        $filename = "服务商员工订单汇总";

        $this->getExcel($filename, $headArr, $info);

    }

    public function DownOrderByDay(){
        if (session('data')['CustomersType'] == 1 & session('flag') == 0) {

        }else{
            return false;
        }
        $data['CustomerSysNo']=session('SysNO');
        $data['LoginName']=I('LoginName');
        $data['DisplayName']=I('DisplayName');
        $data['Time_End']=I('Time_End');
        $data['Time_Start']=I('Time_Start');
        $data['Pay_Type']=I('Ordertype');
        $data['Pay_Type']=I('Ordertype');
        $data['PagingInfo']['PageSize'] = I(PageSize);
        $data['PagingInfo']['PageNumber'] = I(PageNumber);
        $url = C('SERVER_HOST') ."/IPP3Order/IPP3OrderByDayList";
        $list = exportpost($url,$data);//

        foreach ($list['model'] as $row => $val) {
            $info[$row]['DisplayName'] = $val['DisplayName'];
            $info[$row]['LoginName'] = $val['LoginName'];
            $info[$row]['OrderTime'] = $val['OrderTime'];
            $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);
            $info[$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
            $info[$row]['Refund_fee'] = fee2yuan($val['Refund_fee']);
            $info[$row]['Fee'] = fee2yuan($val['Fee']);
            $info[$row]['Money'] = fee2yuan($val['Money']);
            $info[$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);
            $info[$row]['TradeCount'] = $val['TradeCount'];


        }
//        var_dump($info);exit;

        foreach ($info[0] as $field => $v) {

            if ($field == 'DisplayName') {
                $headArr[] = '真实姓名';
            }
            if ($field == 'LoginName') {
                $headArr[] = '登录名';
            }
            if ($field == 'OrderTime') {
                $headArr[] = '订单时间';
            }

            if ($field == 'Total_fee') {
                $headArr[] = '总金额';
            }
            if ($field == 'Cash_fee') {
                $headArr[] = '折后金额';
            }
            if ($field == 'Refund_fee') {
                $headArr[] = '退款金额';
            }
            if ($field == 'Fee') {
                $headArr[] = '实际交易金额';
            }
            if ($field == 'Money') {
                $headArr[] = '费率金额';
            }
            if ($field == 'Pay_Type') {
                $headArr[] = '支付类型';
            }
            if ($field == 'TradeCount') {
                $headArr[] = '交易总笔数';
            }

        }
        $filename = "商户日交易订单";

        $this->getExcel($filename, $headArr, $info);
    }


    //大学-交易订单查询

    public function OrderExtendListDownload() {

        $Time_Start = $_POST['Time_Start'];

        $Time_end = $_POST['Time_end'];

        $Out_trade_no = $_POST['ordernum'];

        $Ordertype = $_POST['Ordertype'];

        $data = array(


            "Out_trade_no" => $Out_trade_no,

            "Time_Start" => $Time_Start,

            "Time_end" => $Time_end,

            "Pay_Type" => $Ordertype

        );

        $flag = session('flag');                            //0-服务商登陆  1-员工登陆

        $servicestoretype = session('servicestoretype');    //0-服务商员工 1-商户员工

        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录

            $data['CustomerSysNo'] = session('SysNO');

        }

        if ($servicestoretype == 1 & $flag == 1) {//商户员工

            $data['SystemUserSysNo'] = session('SysNO');

        }

        $url = C('SERVER_HOST') . "IPP3Order/IPP3So_Master_ExtendList";

        $list = exportpost($url,$data);//分次请求

        foreach ($list['model'] as $row => $val) {

            $info[$row]['CustomerName'] = $val['CustomerName'];

            $info[$row]['Out_trade_no'] = "'" . $val['Out_trade_no'];

            $info[$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);

            $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);

            $info[$row]['Name'] = $val['Name'];

            $info[$row]['PhoneNumber'] = $val['PhoneNumber'];

            $info[$row]['Type'] = $val['Type'];

            $info[$row]['StudentID'] = $val['StudentID'];

            $info[$row]['Remarks'] = $val['Remarks'];

            $info[$row]['Num'] = $val['Num'];

            $info[$row]['Cash_fee_type'] = "人民币";

            $info[$row]['Time_Start'] = $val['Time_Start'];

        }

        foreach ($info[0] as $field => $v) {

            if ($field == 'CustomerName') {

                $headArr[] = '商户名称';

            }

            if ($field == 'Out_trade_no') {

                $headArr[] = '订单号';

            }

            if ($field == 'Pay_Type') {

                $headArr[] = '交易类型';

            }

            if ($field == 'Total_fee') {

                $headArr[] = '交易金额';

            }

            if ($field == 'Name') {

                $headArr[] = '姓名';

            }

            if ($field == 'PhoneNumber') {

                $headArr[] = '电话';

            }

            if ($field == 'Type') {

                $headArr[] = '宽带类型';

            }

            if ($field == 'StudentID') {

                $headArr[] = '学号';

            }

            if ($field == 'Remarks') {

                $headArr[] = '备注';

            }

            if ($field == 'Num') {

                $headArr[] = '数量';

            }

            if ($field == 'Cash_fee_type') {

                $headArr[] = '交易币种';

            }

            if ($field == 'Time_Start') {

                $headArr[] = '交易时间';

            }

        }

        $filename = "大学-交易订单查询报表";

        $this->getExcel($filename, $headArr, $info);
    }


    public function getExcel($fileName, $headArr, $info) {

        import("Org.Util.PHPExcel");

        import("Org.Util.PHPExcel.Writer.Excel5");

        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y-m-d", time());

        $fileName .= "_{$date}.xls";

        $objPHPExcel = new \PHPExcel();

        $objProps = $objPHPExcel->getProperties();

        $key = ord("A");

        foreach ($headArr as $v) {

            $colum = chr($key);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

            $key += 1;

        }


        $column = 2;

        $objActSheet = $objPHPExcel->getActiveSheet();

        foreach ($info as $key => $rows) { //行写入

            $span = ord("A");

            foreach ($rows as $keyName => $value) {// 列写入

                $j = chr($span);

                $objActSheet->setCellValue($j . $column, $value);

                $span++;

            }

            $column++;

        }


        $fileName = iconv("utf-8", "gb2312", $fileName);

        $objPHPExcel->setActiveSheetIndex(0);

        ob_end_clean();//清除缓冲区,避免乱码

        header('Content-Type: application/vnd.ms-excel');

        header("Content-Disposition: attachment;filename=\"$fileName\"");

        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output'); //文件通过浏览器下载

        exit;

    }


}