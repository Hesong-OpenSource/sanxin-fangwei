$(function(){
	$("#identify_business_name").bind("blur",function(){
		if(!$("#identify_business_name").val()){
			$.showErr("机构名称不能为空!");
		}
	});
});
function check_data(){
	if($("#identify_business_name").val()==''){
		$.showErr("请输入机构名称！");
		return false;
	}
	if($("#identify_business_licence_u").val()==''){
		$.showErr("请上传营业执照！");
		return false;
	}
	if($("#identify_business_code_u").val()==''){
		$.showErr("请上传组织机构代码证照片！");
		return false;
	}
	if($("#identify_business_tax_u").val()==''){
		$.showErr("请上传税务登记证照片！");
		return false;
	}
	var result_url=$("#result_url").val();
	var ajax=$("#ajax").val();
	var ajaxurl=$("#ajaxurl").val();
	var identify_business_name=$("#identify_business_name").val();
	var identify_business_licence_u=$("#identify_business_licence_u").val();
	var identify_business_code_u=$("#identify_business_code_u").val();
	var identify_business_tax_u=$("#identify_business_tax_u").val();
	var query =new Object();
	query.ajax=ajax;
	query.identify_name=$("#identify_name").val();
	query.identify_number=$("#identify_number").val();
	
	query.identify_business_name=identify_business_name;	
	query.identify_business_licence_u=identify_business_licence_u;
	query.identify_business_code_u=identify_business_code_u;
	query.identify_business_tax_u=identify_business_tax_u;
	$.ajax({
		url:ajaxurl,
		data:query,
		dataType:"json",
		type:"post",
		success:function(ajaxobj){
			if(ajaxobj.status==0){
				$.showErr(ajaxobj.info);
				return false;
			}else{
				window.location.href=result_url;
			}
		},
		error:function(){
			$.showErr("系统繁忙，请您稍后重试！");
			return false;
		}
	});	
}
