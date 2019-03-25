$(function(){
    var name = $("#login-name");////1.事件选择
    var phone = $("#login-pass");
    var studentId = $("#login-num");
    var num = $("#num");
    var note = $("#login-note");
    var num = $("#num");
    var wifi = $("#KDS");
    var money = $("#money");
    var allMoney = $("#allMoney");
    var Total_Money = $("#Total_Money");
    var ProductCount = $("#ProductCount");
    var nums = 1;
    var m = 0;
    var trime;
    var flagName = false;
    allMoney.html(nums*m);
    Total_Money.val(nums*m);
    var reg = /^[\u2E80-\u9FFF]+$/;
    var data = {//2.数据在这
		"pay0.01":0.01,
        "pay100":100,
        "pay200":200,
        "pay500":500
    };
    var style1 = {
        "color":"#e74c3c",
        "border":"1px solid #e74c3c"
    };
    var style2 = {
        "color":"#2ecc71",
        "border":"none"
    };
//	name.on("blur",nameFn);
    name.on("input",nameFn);
    function nameFn(){
			var reg = /^[\u2E80-\u9FFF]+$/;
        if(!reg.test(name.val())){
            flagName = false;
            $(this).css(style1);
            $(".title1").remove();
            $(this).parent().append("<div class='title1'><span class='glyphicon glyphicon-info-sign'></span> 名字格式不正确</div>");
            $(".title1").animate({opacity:1},500);
            $(this).siblings().css("color","#e74c3c");
            checkbutton();
        }else{
            flagName = true;
            $(this).css(style2);
//			$(".title1").animate({width:"0px",height:"0px"},1000);
            $(".title1").remove();
            $(this).siblings().css("color","#2ecc71");
           checkbutton();
        }
    }
    var flagPhone = false;//这是所以的标志,正确的为true 错误的为false,全为true 才能过
	phone.on("blur",phoneFn);
//    phone.on("input",phoneFn);/
    function phoneFn(){
        var regPhone = /^(13|15|17|18)[0-9]{9}$/;//////这是正则
			if(!regPhone.test(phone.val())){//这是验证
				flagPhone = false;//这是所以的标志,正确的为true 错误的为false,全为true 才能过
				$(this).css(style1);
				$(".title2").remove();
				$(this).parent().append("<div class='title2'><span class='glyphicon glyphicon-info-sign'></span> 电话格式不正确</div>");
				$(".title2").animate({opacity:1},500);
				$(this).siblings().css("color","#e74c3c");
				checkbutton();
			}else{
				flagPhone = true;//这是所以的标志,正确的为true 错误的为false,全为true 才能过
				$(".title2").remove();
				$(this).css(style2);
				$(this).siblings().css("color","#2ecc71");
				checkbutton();
			}
    }
    var flagStudentId = false;
//	studentId.on("blur",studentIdFn);
    studentId.on("input",studentIdFn);
    function studentIdFn(){
        var regStudentId = /^\d+$/;
        if(studentId.val()==''){
            flagStudentId = false;//这是所以的标志,正确的为true 错误的为false,全为true 才能过
            $(this).css(style1);
            $(".title3").remove();
            $(this).parent().append("<div class='title3'><span class='glyphicon glyphicon-info-sign'></span> 话机编码格式不正确</div>");
            $(".title3").animate({opacity:1},500);
            $(this).siblings().css("color","#e74c3c");
            checkbutton();
        }else{
            flagStudentId = true;//这是所以的标志,正确的为true 错误的为false,全为true 才能过
            $(".title3").remove();
            $(this).css(style2);
            $(this).siblings().css("color","#2ecc71");
            checkbutton();
        }
    };
    note.on("input",function(){
        $(this).css(style2);
        $(this).siblings().css("color","#2ecc71");
    });
  
    var flagWifi = false;
    wifi.on("input",function(){
        if(wifi.val()=="选择金额"){
            flagWifi = false;//这是所以的标志,正确的为true 错误的为false,全为true 才能过
            wifi.css("color","#e74c3c");
            money.parent().css("color","#e74c3c");
            money.parent().siblings().css("color","#e74c3c");
            $(this).parent().siblings().css("color","#e74c3c");
            allMoney.html(0);
            checkbutton();
        }else{
            flagWifi = true;//这是所以的标志,正确的为true 错误的为false,全为true 才能过
            wifi.css("color","#2ecc71");
            money.parent().css("color","#2ecc71");
            money.parent().siblings().css("color","#2ecc71");
            $(this).parent().siblings().css("color","#2ecc71");
            allMoney.html(parseFloat(wifi.val()));//金额显示在家
            Total_Money.val(parseFloat(wifi.val()));
            checkbutton();
        }
    });
    var btn = $("#btn");
	btn.attr("disabled",false);
    btn.on("tap",function(){
        if(flagName&flagPhone&flagStudentId&flagWifi){
        }else{
            return false;
        }
    });
    function checkbutton(){
            if(flagName&flagPhone&flagStudentId&flagWifi){//这是所以的标志,正确的为true 错误的为false,全为true 才能过
                document.getElementById("btn").disabled = false;//
            }else{
				document.getElementById("btn").disabled = true;//,true);
            }
    }

});