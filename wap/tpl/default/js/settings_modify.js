$(document).ready(function(){
	var code_timeer = null;
	var code_lefttime = 0;
	$(function(){
		$("#J_send_sms_verify").bind("click",function(){
			send_mobile_verify_sms();
		});
		$("#user_register_form").find("input[name='verify_coder']").bind("blur",function(){
			check_register_verifyCoder();
		});
	});
	function send_mobile_verify_sms(){
		$("#J_send_sms_verify").unbind("click");
		if(!$.checkMobilePhone($("#mobile").val())){
			$.showErr("手机号码格式错误");
			$("#J_send_sms_verify").bind("click",function(){
				send_mobile_verify_sms();
			});
			return false;
		}
		if(!$.maxLength($("#mobile").val(),11,true)){
			$.showErr("手机号码长度不能超过11位");
			$("#J_send_sms_verify").bind("click",function(){
				send_mobile_verify_sms();
			});
			return false;
		}
			if($.trim($("#mobile").val()).length == 0){				
			$.showErr("手机号码不能为空！");
			$("#J_send_sms_verify").bind("click",function(){
				send_mobile_verify_sms();
			});
			return false;
		}
		var sajaxurl = '{url_wap r="ajax#send_mobile_verify_code" p="is_only=1"}';
		var squery = new Object();
		squery.mobile = $.trim($("#mobile").val());
		$.ajax({ 
			url: sajaxurl,
			data:squery,
			type: "POST",
			dataType: "json",
			success: function(sdata){
				if(sdata.status==1)
				{
					code_lefttime = 60;
					code_lefttime_func();
					$.showSuccess(sdata.info);
					//alert(sdata.info);
					return false;
				}
				else
				{
					$("#J_send_sms_verify").bind("click",function(){
						send_mobile_verify_sms();
					});
					$.showErr(sdata.info);
					return false;
				}
			}
		});
	}
	function code_lefttime_func(){
		clearTimeout(code_timeer);
		$("#J_send_sms_verify").val(code_lefttime+"秒后重新发送");
		$("#J_send_sms_verify").css({"color":"#f1f1f1"});
		code_lefttime--;
		if(code_lefttime >0){
			$("#J_send_sms_verify").attr("disabled","true");
			code_timeer = setTimeout(code_lefttime_func,1000);
		}
		else{
			code_lefttime = 60;
			$("#J_send_sms_verify").val("发送验证码");
			$("#J_send_sms_verify").attr("disabled","false");
			$("#J_send_sms_verify").css({"color":"#fff"});
			$("#J_send_sms_verify").bind("click",function(){
				send_mobile_verify_sms();
			});
		}
	}
	//检查验证码
	function check_register_verifyCoder(){
 		if($.trim($("#user_register_form").find("input[name='verify_coder']").val())==""){
			$.showErr("请输入验证码");
		}
		else
		{
			var mobile = $.trim($("#user_register_form").find("input[name='mobile']").val());
			var code = $.trim($("#user_register_form").find("input[name='verify_coder']").val());
			if(mobile!=""||code!=""){
				var ajaxurl ='{url_wap r="user#check_verify_code"}';//APP_ROOT+"/index.php?ctl=user&act=check_verify_code";
				var query = new Object();
				query.mobile = mobile;
				query.code = code;
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					data:query,
					type: "POST",
					success:function(ajaxobj){
						if(ajaxobj.status==0)
						{
							$.showErr("验证码不正确!");
						}
					}
				});
			}
		}
	}
	function save_modify(){
		var province=$("#province").val();
		var city=$("#city").val();
		var intro=$("#intro").val();
		var mobile=$("#mobile").val();
		var verify_coder=$("#verify_coder").val();
		var sex=$("input[name='sex']:checked").val();
		var weibo_url=$("#weibo_url").val();
		var post_url='{url_wap r="settings#save_modify"}';
		var query =new Object();
			query.province=province;
			query.city=city;
			query.intro=intro;
			query.weibo_url=weibo_url;
			query.sex=sex;
			if(typeof(verify_coder)!="undefined"){
				query.verify_coder=verify_coder;
				query.mobile=mobile;
			}
		$.ajax({
			url:post_url,
			dataType:"json",
			data:query,
			type:"post",
			cache:false,
			success:function(data){
				if(data.info!=null){
					$.showErr(data.info);
				}else{
					$.showSuccess(data.info);
				}
				window.location.reload();
			},
			error:function(){
				$.showErr("服务器繁忙，请您稍后再试！");
			}
		});
		return false;
	}
	
function upd_file(obj,file_id)
{	
	$("input[name='"+file_id+"']").bind("change",function(){			
		$(obj).hide();
		$(obj).parent().find(".fileuploading").removeClass("hide");
		$(obj).parent().find(".fileuploading").removeClass("show");
		$(obj).parent().find(".fileuploading").addClass("show");
		  $.ajaxFileUpload
		   (
			   {
				    url:'{url_wap r="avatar#upload"}&uid={$user_info.id}',
				    secureuri:false,
				    fileElementId:file_id,
				    dataType: 'json',
				    success: function (data, status)
				    {
				   		$(obj).show();
				   		$(obj).parent().find(".fileuploading").removeClass("hide");
						$(obj).parent().find(".fileuploading").removeClass("show");
						$(obj).parent().find(".fileuploading").addClass("hide");
				   		if(data.status==1)
				   		{
				   			document.getElementById("avatar").src = data.middle_url+"?r="+Math.random();
				   		}
				   		else
				   		{
				   			$.showErr(data.msg);
				   		}
				   		
				    },
				    error: function (data, status, e)
				    {
						$.showErr(data.responseText);;
				    	$(obj).show();
				    	$(obj).parent().find(".fileuploading").removeClass("hide");
						$(obj).parent().find(".fileuploading").removeClass("show");
						$(obj).parent().find(".fileuploading").addClass("hide");
				    }
			   }
		   );
		  $("input[name='"+file_id+"']").unbind("change");
	});	
}
});
