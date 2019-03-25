<?php

namespace Home\Controller;

use Common\Compose\Base;

class DownCouponCashController extends Base {

    protected function _initialize() {
        ini_set("memory_limit", "1024M"); // 不够继续加大
        set_time_limit(0);
    }

//商户订单统计

    public function download() {

        $data['Time_Start']= I('Time_Start');
        $data['Time_End']  = I('Time_End');
        $data['Out_trade_no']  = I('ordernum');
        $data['Pay_Type']  = I('Ordertype');


        if(session(data)['CustomersType']==0&session(flag)==0){

            $data['CustomerSysNo']=I("CustomerSysNo");
            $data['DisplayName']  = I('realName');
            $data['LoginName']  = I('employeeLoginName');

        }else if(session(data)['CustomersType']==1&session(flag)==0){

            $data['CustomerSysNo']=session(SysNO);
            $data['DisplayName']  = I('realName');
            $data['LoginName']  = I('employeeLoginName');


        }else if(session('servicestoretype')==1&session('flag')==1){

            exit();

        }else{
            exit();
        }
        if (deep_in_array($data, ["DisplayName","LoginName","Out_trade_no","Pay_Type","Time_End","Time_Start"])) {

            $this->ajaxReturn(array('Code' => 1, 'Description' => "参数错误，请稍后再试或重新登录！"));

            exit();
        }
        if(I('buttontype')==0) {
            $url = C('SERVER_HOST') . "IPP3WXCoupon/IPP3Order_RealtimeShopRate_WX_CouponCashSP";
            $list = exportpost($url, $data);//分次请求

            foreach ($list['model'] as $row => $val) {

                $info[$row]['CustomerName'] = $val['CustomerName'];
                $info[$row]['loginname'] = $val['LoginName'];
                $info[$row]['displayname'] = $val['DisplayName'];
//            $io'][$row]['CustomerName']=$val['CustomerName'];
                $info[$row]['Out_trade_no'] = '`' . $val['Out_trade_no'];
                $info[$row]['Total_fee'] = fee2yuan($val['Total_fee']);
                $info[$row]['Settlement_total_fee'] = fee2yuan($val['Settlement_total_fee']);
                $info[$row]['Settlement_refund_fee'] = fee2yuan($val['Settlement_refund_fee']);
                $info[$row]['fee'] = fee2yuan($val['fee']);
//            $info[$row]['Tradecount']=($val['Tradecount']);
                $info[$row]['Rate'] = ($val['Rate']);
                $info[$row]['Rate_fee'] = fee2yuan($val['Rate_fee']);
                $info[$row]['Total_Rate_fee'] = fee2yuan($val['Total_Rate_fee']);
                $info[$row]['Pay_Type'] = CheckOrderType($val['Pay_Type']);

                $info[$row]['Bank_type'] = '人民币';
                $info[$row]['Time_Start'] = ($val['Time_Start']);


            }


            foreach ($info[0] as $field => $v) {

                if ($field == 'CustomerName') {

                    $headArr[] = '商户名称';

                }
                if ($field == 'loginname') {

                    $headArr[] = '员工登录名';

                }

                if ($field == 'displayname') {

                    $headArr[] = '员工真实姓名';

                }

                if ($field == 'Out_trade_no') {

                    $headArr[] = '订单号';

                }


                if ($field == 'Total_fee') {

                    $headArr[] = '交易金额';

                }
                if ($field == 'Settlement_total_fee') {

                    $headArr[] = '应结订单金额';

                }
                if ($field == 'Settlement_refund_fee') {

                    $headArr[] = '实际退款金额';

                }
                if ($field == 'fee') {

                    $headArr[] = '实际交易金额';

                }


                if ($field == 'Rate') {

                    $headArr[] = '商户费率';

                }
                if ($field == 'Rate_fee') {

                    $headArr[] = '手续费';

                }
                if ($field == 'Total_Rate_fee') {

                    $headArr[] = '费率金额';

                }
                if ($field == 'Pay_Type') {

                    $headArr[] = '交易类型';

                }
                if ($field == 'Bank_type') {

                    $headArr[] = '交易币种';

                }
                if ($field == 'Time_Start') {

                    $headArr[] = '交易时间';

                }

            }
        }else{
            $url = C('SERVER_HOST') . "IPP3WXCoupon/IPP3Order_Fund_ShopSPRate_WX_CouponCash";
            $list = exportpost($url, $data);//分次请求

            foreach ($list['model'] as $row => $val) {

                $info[$row]['loginname']=$val['LoginName'];
                $info[$row]['displayname']=$val['DisplayName'];
//            $info['row]['Customer']=$val['Customer'];
                $info[$row]['Total_fee']=fee2yuan($val['Total_fee']);
                $info[$row]['Settlement_total_fee']=fee2yuan($val['Settlement_total_fee']);
                $info[$row]['Settlement_refund_fee']=fee2yuan($val['Settlement_refund_fee']);
                $info[$row]['fee']=fee2yuan($val['fee']);
                $info[$row]['Rate']=($val['Rate']);
                $info[$row]['Rate_fee']=fee2yuan($val['Rate_fee']);
                $info[$row]['Total_Rate_fee']=fee2yuan($val['Total_Rate_fee']);
                $info[$row]['Tradecount']=($val['Tradecount']);

                $info[$row]['Pay_Type']=CheckOrderType($val['Pay_Type']);
                $info[$row]['Bank_type']="人民币";


            }


            foreach ($info[0] as $field => $v) {


                if ($field == 'loginname') {

                    $headArr[] = '员工登录名';

                }

                if ($field == 'displayname') {

                    $headArr[] = '员工真实姓名';

                }




                if ($field == 'Total_fee') {

                    $headArr[] = '交易金额';

                }
                if ($field == 'Settlement_total_fee') {

                    $headArr[] = '应结订单金额';

                }
                if ($field == 'Settlement_refund_fee') {

                    $headArr[] = '实际退款金额';

                }
                if ($field == 'fee') {

                    $headArr[] = '实际交易金额';

                }

                if ($field == 'Rate') {

                    $headArr[] = '商户费率';

                }
                if ($field == 'Rate_fee') {

                    $headArr[] = '手续费';

                }
                if ($field == 'Total_Rate_fee') {

                    $headArr[] = '费率金额';

                }
                if ($field == 'Tradecount') {

                    $headArr[] = '交易笔数';

                }

                if ($field == 'Pay_Type') {

                    $headArr[] = '交易类型';

                }
                if ($field == 'Bank_type') {

                    $headArr[] = '交易币种';

                }


            }

        }
        $filename = "免充值费率订单查询";

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