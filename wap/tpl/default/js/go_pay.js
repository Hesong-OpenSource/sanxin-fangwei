$(document).ready(function(){
	bind_pay_form();
});



function bind_pay_form()
{
	$(".pay_form").find(".ui-button").bind("click",function(){
		$(".pay_form").submit();
	});
	$(".pay_form").bind("submit",function(){		
		input_money = $(this).find("input[name='money']").val();
 		if($.trim(input_money) == "" || input_money<=0)
		{
			$.show_tip("请输入充值金额");
			return false;
		}		
		is_tg=$("input[name='is_tg']").val();
		if($(this).find("input[name='payment']:checked").length==0&&is_tg==0)
		{
			$.show_tip("请选择支付方式");
			return false;
		}		
		else
		{
 			return true;
		}
		
	});
}

