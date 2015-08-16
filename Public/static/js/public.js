$(".tab-left").click(function () {
    $(this).addClass("active");
    $(".tab-right").removeClass("active");
    $(".tab-right-ul").addClass("hidden");
    $(".tab-left-ul").removeClass("hidden");
    $(".status").attr('value','sign');
});

$(".tab-right").click(function () {
    $(this).addClass("active");
    $(".tab-left").removeClass("active");
    $(".tab-left-ul").addClass("hidden");
    $(".tab-right-ul").removeClass("hidden");
    $(".status").attr('value','end');
});                                             //tab标签切换


function checkAuthForm() {
    if ($("form.data").find("input").val() == "" || $("form.data").find("textarea").val() == "") {
        alert("请填写完整的信息！");
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
}                                               //认证页面表单验证


$("#site").change(function(){
    if($("#site option:selected").text()!="自定义地点"){
        $("#mysite").addClass("hidden");
        $("input#space").val($("#site option:selected").text());
    }
    else{
        $("#mysite").removeClass("hidden");
    }
});                                             //发起沙龙页面自定义地点显示与隐藏


function checkCreateSalonForm() {
    if ($("form.data").find("input").val() == "" || $("#mysite").find("input").val() == "" || $("form.data").find("textarea").val() == "") {
        alert("请填写完整的信息！");
        return false;
    }
}                                               //发起沙龙页面表单验证


function checkCreateAcademicForm() {
    if ($("form.data").find("input").val() == "" || $("form.data").find("textarea").val() == "" || $("form.data").find("textarea[name='good']").val() == "") {
        alert("请填写完整的信息！");
        return false;
    }
}                                               //发起学术页面表单验证


function checkContactForm() {
    if($("form textarea").val() == "") {
        alert("请填写你的意见！");
        return false;
    }
}                                                //联系我们页面表单验证


var comment = false;

$(".stars").find("img").click(function(){
    comment = true;
    var index=$(this).parent().index();
    $("input[name='stars']").val(index);
    $(this).css({"background-color":"yellow"});
    $(this).parent().prevAll().find("img").css({"background-color":"yellow"});
    $(this).parent().nextAll().find("img").css({"background-color":"#fff"});
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