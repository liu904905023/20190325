$(document).bind("click", function (e) {

    if ($(e.target).closest(".list").length == 0) {

        $(".l-list").hide();
    }
})

if ($("#weChat").parents().attr("title")) {
    $('#weChat .list').click(function () {
        $('#weChat .l-list').show();
        $("#Alipay .l-list").css("display", "none");
        //   window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty(); //无法选中拖拽中的文字
    });
    $('#weChat .l-list').on('mousedown', 'li', function (event) {
        var cur = $(this).index();
        var one = $('#weChat .l-list li .lbl').eq(cur).text();
        $(this).find("input:radio").attr("checked", "checked");
        $('#weChat .list ').val(one);
        $('#weChat .l-list').hide();
    })


    $('#Alipay .list').click(function () {
        $('#Alipay .l-list').show();
        $("#weChat .l-list").css("display", "none");
        //   window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty(); //无法选中拖拽中的文字
    });
    $('#Alipay .l-list').on('mousedown', 'li', function (event) {
        var cur = $(this).index();
        var one = $('#Alipay .l-list li .lbl').eq(cur).text();
        $(this).find("input:radio").attr("checked", "checked");
        $('#Alipay .list ').val(one);
        $('#Alipay .l-list').hide();
    })
} else {
    $('.list').click(function () {
        $('.l-list').show();
        //   window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty(); //无法选中拖拽中的文字
    });
    $('.l-list').on('mousedown', 'li', function (event) {
        var cur = $(this).index();
        var one = $('.l-list li .lbl').eq(cur).text();
        $(this).find("input:radio").attr("checked", "checked");
        $('.list ').val(one);
        $('.l-list').hide();
    })
}

