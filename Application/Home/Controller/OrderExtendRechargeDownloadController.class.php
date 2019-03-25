<?php
namespace Home\Controller;
//use Think\Controller;
use Common\Compose\Base;
class OrderExtendRechargeDownloadController extends Base {
	public function orderextendrechargedownload(){
        $Time_Start =$_POST['Time_Start'];//开始时间
        $Time_end   =$_POST['Time_End'];//截止时间
        $Out_trade_no = $_POST['ordernum'];//订单号
		$Ordertype = $_POST['Ordertype'];
		$data = array(
			"Out_trade_no"=>$Out_trade_no,
			"Time_Start"=>$Time_Start,
			"Time_end"=>$Time_end,
			"Pay_Type"=>$Ordertype
		);

        // var_dump($data);exit();
        $flag = session('flag');                            //0-服务商登陆  1-员工登陆
        $servicestoretype = session('servicestoretype');    //0-服务商员工 1-商户员工
        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
            $data['CustomerSysNo'] = session('SysNO');
        }
        if ($servicestoretype == 1 & $flag == 1) {//商户员工
            $data['Old_SysNo'] = session('SysNO');
        }
		$url=C('SERVER_HOST')."IPP3Order/So_Master_Extend_DYC_PhoneList";
		$list = exportpost($url,$data);//分次请求
 		foreach ($list['model'] as $row => $val) {
			$info[$row]['loginname']=$val['LoginName'];					//登录名
			$info[$row]['displayname']=$val['DisplayName'];				//真实姓名
			$info[$row]['Customer']=$val['Customer'];					//商户登录名
			$info[$row]['CustomerName']=$val['CustomerName'];			//商户真实姓名
			$info[$row]['Out_trade_no']="`".$val['Out_trade_no'];		//订单号
			$info[$row]['Total_fee']=fee2yuan($val['Total_fee']);		//交易金额
			$info[$row]['Pay_Type'] = CheckOrderType($val['Pay_Type']);	//交易类型
			$info[$row]['company']=$val['Company'];						//公司
			$info[$row]['name']=$val['Name'];							//联系人
			$info[$row]['phonenumber']=$val['PhoneNumber'];				//电话
			$info[$row]['phonecode']=$val['PhoneCode'];					//电话编码
			$info[$row]['Time_Start']=$val['Time_Start'];				//交易时间
        }
        foreach ($info[0] as $field => $v) {
            if ($field == 'loginname') {
                $headArr[] = '登录名';
            }
            if ($field == 'displayname') {
                $headArr[] = '真实姓名';
            }
            if ($field == 'Customer') {
                $headArr[] = '商户登录名';
            }
            if ($field == 'CustomerName') {
                $headArr[] = '商户名称';
            }
            if ($field == 'Out_trade_no') {
                $headArr[] = '订单号';
            }
            if ($field == 'Total_fee') {
                $headArr[] = '交易金额';
            }
            if ($field == 'Pay_Type') {
                $headArr[] = '交易类型';
            }
            if ($field == 'company') {
                $headArr[] = '公司';
            }
            if ($field == 'name') {
                $headArr[] = '联系人';
            }
            if ($field == 'phonenumber') {
                $headArr[] = '电话';
            }
            if ($field == 'phonecode') {
                $headArr[] = '电话编码';
            }
            if ($field == 'Time_Start') {
                $headArr[] = '交易时间';
            }
        }
        $filename = "订单扩展查询报表";
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