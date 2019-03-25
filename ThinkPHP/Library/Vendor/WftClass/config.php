<?php
class Config{
    private $cfg = array(
		//�ӿ������ַ���̶����䣬�����޸�
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
		//�����̻��ţ��̻����Ϊ�Լ���
        'mchId'=>'101550025741',
		//������Կ���̻����Ϊ�Լ���
        'key'=>'daa17a90a34aef190bb9085234cb52d3',
		//�汾��Ĭ��Ϊ2.0
        'version'=>'2.0'
       );
    
    public function Cof($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>