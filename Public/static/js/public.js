$(".tab-left").click(function () {
    $(this).addClass("active");
    $(".tab-right").removeClass("active");
    $(".tab-left-ul").removeClass("hidden");
    $(".tab-right-ul").removeClass("hidden").addClass("hidden");
    $(".status").attr('value', 'sign');
});

$(".tab-right").click(function () {
    $(this).addClass("active");
    $(".tab-left").removeClass("active");
    $(".tab-right-ul").removeClass("hidden");
    $(".tab-left-ul").removeClass("hidden").addClass("hidden");
    $(".status").attr('value', 'end');
});                                             //tab标签切换

var student_name = /^[\u4E00-\u9FA5]{2,7}$/;
var major_name = /^[\u4E00-\u9FA5]{2,10}$/;

function checkAuthForm() {
    if ($("form.data").find("input").val() == "" || $("form.data").find("textarea").val() == "") {
        alert("请填写完整的信息！");
        return false;
    }

    var student = $("input[name='student_name']").val();
    if (!student_name.test(student)) {
        alert("请填写正确的姓名！");
        return false;
    }

    var major = $("input[name='major']").val();
    if (!major_name.test(major)) {
        alert("请填写正确的专业！");
        return false;
    }

    var id = $("input[name='student_id']").val();
    if (id.length != 13) {
        alert("请填写正确的学号！");
        return false;
    }

    var phone = $("input[name='phone']").val();
    if (phone.length != 11 && phone.length != 8) {
        alert("请填写正确的电话号！");
        return false;
    }

    var email = $("input[name='email']").val();
    if (email.indexOf("@") <= 0 || email.indexOf(".") <= 0 ||
        email.indexOf("@") > email.indexOf(".") ||
        email.length == email.indexOf(".") + 1 ||
        email.indexOf(".") - email.indexOf("@") == 1) {
        alert("请填写正确的邮箱！");
        return false;
    }
    return checkForbiddenString();
}                                               //认证页面表单验证


$("#site").change(function () {
    if ($("#site option:selected").text() != "自定义地点") {
        $("#mysite").addClass("hidden");
        $("input#space").val($("#site option:selected").text());
    }
    else {
        $("#mysite").removeClass("hidden");
    }
});                                             //发起沙龙页面自定义地点显示与隐藏


function checkCreateSalonForm() {
    if ($("form.data").find("input").val() == "" || $("#mysite").find("input").val() == "" || $("form.data").find("textarea").val() == "") {
        alert("请填写完整的信息！");
        return false;
    }
    return checkForbiddenString();
}                                               //发起沙龙页面表单验证


function checkCreateAcademicForm() {
    if ($("form.data").find("input").val() == "" || $("form.data").find("textarea").val() == "" || $("form.data").find("textarea[name='good']").val() == "") {
        alert("请填写完整的信息！");
        return false;
    }
    return checkForbiddenString();
}                                               //发起学术页面表单验证


function checkContactForm() {
    if ($("form textarea").val() == "") {
        alert("请填写你的意见！");
        return false;
    }
    return checkForbiddenString();
}                                                //联系我们页面表单验证


var comment = false;

$(".stars").find("img").click(function () {
    comment = true;
    var index = $(this).parent().index();
    $("input[name='stars']").val(index);
    $(this).css({"background-color": "yellow"});
    $(this).parent().prevAll().find("img").css({"background-color": "yellow"});
    $(this).parent().nextAll().find("img").css({"background-color": "#fff"});
});                                             //总结界面评星

function checkSummary() {
    if ($("textarea").val() == "") {
        alert("请填写比赛总结！");
        return false;
    }
    //if ($("input#photo-path").val() == "") {
    //    alert("请上传比赛照片！");
    //    return false;
    //}
    if (!comment) {
        alert("请评价比赛！");
        return false;
    }
}                                               //总结页面表单验证


$(function () {
    var width = 0;
    for (var i = 0; i < $("ul.page").find("li").length; i++) {
        width += $("ul.page").find("li").eq(i).width() + 6;
    }
    $("ul.page").css({"width": width});
});                                             //分页ul宽度


var forbiddenArray = ['xx', '<', '>', '黄色'];
function checkForbiddenString() {
    var re = '';

    for (var i = 0; i < forbiddenArray.length; i++) {
        if (i == forbiddenArray.length - 1)
            re += forbiddenArray[i];
        else
            re += forbiddenArray[i] + "|";
    }

    var pattern = new RegExp(re, "g");

    if (pattern.test($("form input").val()) || pattern.test($("form textarea").val())) {
        alert("您输入的内容包含敏感字符，请修改！");
        return false;
    }
}                                               //屏蔽敏感字符