<?php

namespace Home\Controller;

use Common\Compose\Base;

class DownloadOrderStatisticsController extends Base {

	protected function _initialize() {
        ini_set("memory_limit", "1024M"); // 不够继续加大
        set_time_limit(0);
    }

//商户订单统计

    public function downloadorderstatistics() {

        $Time_Start 	= $_POST['Time_Start'];
        $Time_end   	= $_POST['Time_End'];
        $staffloginname = I('staffloginname',"");
        $realname 		= I('realname',"");
        $ordernum 	    = I('ordernum',"");
        $Ordertype 		= I('Ordertype',"");
        if(session(flag)==1&&session(servicestoretype)==1){//员工

        }else if(session(data)['CustomersType'] == 1){
            $serviceno = session('SysNO');
        }

        $data = array(

            "CustomerSysNo"	    => $serviceno,
            "LoginName"			=> $staffloginname,
            "DisplayName"		=> $realname,
            "out_trade_no"		=> $ordernum,
            "PhoneNumber"		=> $phone,
            "Pay_Type"			=> $Ordertype,
            "Time_Start"		=> $Time_Start,
            "Time_end"			=> $Time_end
        );

        $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderFundList_Shop_SP";
        $list = exportpost($url,$data);//分次请求

        foreach ($list['model'] as $row => $val) {

            $info[$row]['LoginName'] = $val['LoginName'];
            $info[$row]['DisplayName'] = $val['DisplayName'];
            $info[$row]['Out_trade_no'] = "'" .$val['Out_trade_no'];
            $info[$row]['Pay_Type'] = CheckOrderType($val['Pay_Type']);
            $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);
            $info[$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
            $info[$row]['refund_fee'] = fee2yuan($val['refund_fee']);
            $info[$row]['fee'] = fee2yuan($val['fee']);
            $info[$row]['refundCount'] = $val['refundCount'];
            $info[$row]['Time_Start'] = $val['Time_Start'];

        }


        foreach ($info[0] as $field => $v) {

            if ($field == 'LoginName') {

                $headArr[] = '员工登录名';

            }

            if ($field == 'DisplayName') {

                $headArr[] = '员工真实姓名';

            }

            if ($field == 'Out_trade_no') {

                $headArr[] = '订单号';

            }

            if ($field == 'Pay_Type') {

                $headArr[] = '交易类型';

            }

            if ($field == 'Total_fee') {

                $headArr[] = '总金额';

            }

            if ($field == 'Cash_fee') {

                $headArr[] = '折后金额';

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
            if ($field == 'Time_Start') {

                $headArr[] = '交易时间';

            }

        }

        $filename = "订单详情";

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