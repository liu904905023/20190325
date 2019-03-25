var alertCallbacks = '';
var confirmCallbacks = '';
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}
function StopKey() {
    document.onkeydown = function(e) {
        if (e.keyCode == '9') {
            return false;
        }
    }
}


function show_alert(e,a) {
    var alert_msg = document.createElement('div');
    alert_msg.id = "alert_msg";
    var bg_black = document.createElement('div');
    bg_black.id  = "bg_black";
    alertHtml = "<div id='alert_msg_top'>提示信息<span onclick=alertClose()>×</span></div><div id='alert_msg_cont'>" + e + "</div><div class='alert_btns'><button id='alert_msg_clear' class='btn btn-danger' onclick=alertClose()>确定</button></div>";
    if(a){
        alertCallbacks = a;
    }else{
        alertCallbacks = null;
    }
    alert_msg.innerHTML = alertHtml;
    document.body.appendChild(alert_msg);
    document.body.appendChild(bg_black);
    document.getElementById('alert_msg_clear').focus();
    StopKey();
}

function alertClose() {
    document.body.removeChild(alert_msg);
    document.body.removeChild(bg_black);
    alertCallbacks!='null'?eval(alertCallbacks): '';
    document.onkeydown = function(e) {
        if (e.keyCode == '9') {
            return true;
        }
    }
}
function show_confirm(even,c) {
    var alert_msg = document.createElement('div');
    alert_msg.id = "alert_msg";
    var bg_black = document.createElement('div');
    bg_black.id = "bg_black";
    alertHtml = "<div id='alert_msg_top'>" + "提示信息" +
        "<span onclick='alertClose()'>×</span>" +
        "</div>" +
        "<div id='alert_msg_cont'>" + even + "</div>" +
        "<div class='alert_btns'>" +
        "<button class='btn  btn-danger' id='confirm_msg_clear' onclick=confirmYN(0)>确定</button>" +
        "<button class='btn btn-grey' onclick=confirmYN(1)>取消</button>" +
        "</div>";
    if(c){
        confirmCallbacks = c;
    }else{
        confirmCallbacks = null;
    }
    alert_msg.innerHTML = alertHtml;
    document.body.appendChild(alert_msg);
    document.body.appendChild(bg_black);
    document.getElementById('confirm_msg_clear').focus();
    StopKey();
}
function confirmYN(r) {
    if (r == 0) {
        alertClose();
        eval(confirmCallbacks);
    } else {
        alertClose();
    }

}