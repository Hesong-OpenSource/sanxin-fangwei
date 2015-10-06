$(document).ready(function(){
	
	bind_pay_form();
});



function bind_pay_form()
{
	var pay_status=false;
	var max_pay = parseFloat($(".pay_form").find("input[name='max_pay']").val());
	//var max_credit = $(".pay_form").find("input[name='max_credit']").val();
	//var max_val = parseFloat(max_pay)<parseFloat(max_credit)?parseFloat(max_pay):parseFloat(max_credit);
	
	/*
	$(".pay_form").find("input[name='credit']").bind("keyup blur",function(){
		var money = $(this).val();
		if(isNaN(money)||parseFloat(money)<=0)
		{
			$(".pay_form").find("input[name='credit']").val("0");
		}
		else
		{
			if(parseFloat(money)>max_val)
			{
				$(".pay_form").find("input[name='credit']").val(max_val);
			}
			if(parseFloat(money)>=max_pay)
			{
				$(".pay_form").find("input[name='payment']:checked").attr("checked",false);
			}
		}
	});
	*/
	$(".pay_form").find(".ui-button").bind("click",function(){
		$(".pay_form").submit();
	});
	$(".pay_form").bind("submit",function(){		
		var max_pay = $(".pay_form").find("input[name='max_pay']").val();
		//var max_credit = $(".pay_form").find("input[name='max_credit']").val();
		//var max_val = parseFloat(max_pay)<parseFloat(max_credit)?parseFloat(max_pay):parseFloat(max_credit);

 
		var money = $(".pay_form").find("input[name='credit']").val();
			money = isNaN(money)?0:parseFloat(money);
		var pay_score=$(".pay_form").find("input[name='pay_score']").val();
			pay_score=isNaN(pay_score)?0:parseInt(pay_score);
		var pay_score_money=parseFloat(parseInt(pay_score/trade_score*100)/100);//保留两位小数
		var pay_money_score=money+pay_score_money;
			pay_money_score=round2(pay_money_score,2);
		var paypassword=$("input[name='paypassword']").val();
		if(pay_money_score >0 )
		{
			if(pay_money_score<max_pay)
			{	
				if($(this).find("input[name='payment']:checked").length==0)
				{
					$.show_tip("请选择支付方式");
					return false;
				}	
			}
		}
		else{
			if($(this).find("input[name='payment']:checked").length==0)
				{
					$.show_tip("请选择支付方式");
					return false;
				}	
		}
		if(paypassword==''){
			$.show_tip("请输入支付密码");
			return false;
		}
		
		var ajaxurl =  APP_ROOT+"/index.php?ctl=ajax&act=check_paypassword";
		var query = $(this).serialize();
		$.ajax({ 
				url: ajaxurl,
				dataType: "json",
				data:query,
				async:false,
				type: "POST",
				success: function(ajaxobj){
					
					if(ajaxobj.status==1)
					{
 						pay_status= true;
					}
					else
					{
						$.showErr(ajaxobj.info,function(){
							if(ajaxobj.jump!="")
							{
								location.href = ajaxobj.jump;
							}
						});	
						pay_status= false;		
					}
				},
				error:function(ajaxobj)
				{
					if(ajaxobj.responseText!='')
					alert(ajaxobj.responseText);
				}
			});
		if(pay_status){
  			return true;
		}else{
  			return false;
		}
 		
	});
}