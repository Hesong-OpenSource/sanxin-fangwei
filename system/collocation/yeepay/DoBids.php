<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function DoBidsXml($IpsSubject,$pWebUrl,$pS2SUrl){		

		$strxml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
				."<pReq>"
				."<pMerBillNo>".$IpsSubject['pMerBillNo'] ."</pMerBillNo>"
				."<pBidNo>".$IpsSubject['pBidNo']."</pBidNo>"
				."<pRegDate>".$IpsSubject['pRegDate']."</pRegDate>"
				."<pLendAmt>".$IpsSubject['pLendAmt'] ."</pLendAmt>"
				."<pGuaranteesAmt>".$IpsSubject['pGuaranteesAmt']."</pGuaranteesAmt>"
				."<pTrdLendRate>".$IpsSubject['pTrdLendRate']."</pTrdLendRate>"
				."<pTrdCycleType>".$IpsSubject['pTrdCycleType']."</pTrdCycleType>"
				."<pTrdCycleValue>".$IpsSubject['pTrdCycleValue']."</pTrdCycleValue>"
				."<pLendPurpose>".$IpsSubject['pLendPurpose']."</pLendPurpose>"
				."<pRepayMode>".$IpsSubject['pRepayMode']."</pRepayMode>"
				."<pOperationType>".$IpsSubject['pOperationType']."</pOperationType>"
				."<pLendFee>".$IpsSubject['pLendFee']."</pLendFee>"
				."<pAcctType>".$IpsSubject['pAcctType']."</pAcctType>"
				."<pIdentNo>".$IpsSubject['pIdentNo']."</pIdentNo>"
				."<pRealName>".$IpsSubject['pRealName']."</pRealName>"
				."<pIpsAcctNo>".$IpsSubject['pIpsAcctNo']."</pIpsAcctNo>"
				."<pWebUrl><![CDATA[".$pWebUrl."]]></pWebUrl>"
				."<pS2SUrl><![CDATA[".$pS2SUrl."]]></pS2SUrl>"
				."<pMemo1><![CDATA[".$IpsSubject['pMemo1']."]]></pMemo1>"
				."<pMemo2><![CDATA[".$IpsSubject['pMemo2']."]]></pMemo2>"
				."<pMemo3><![CDATA[".$IpsSubject['pMemo3']."]]></pMemo3>"
				."</pReq>";
				
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\		
		return $strxml;		
	}
	

	/**
	 * 标的登记 及 流标
	 * @param int $deal_id
	 * @param int $pOperationType 标的操作类型，1：新增，2：结束 “新增”代表新增标的，“结束”代表标的正常还清、丌 需要再还款戒者标的流标等情况。标的“结束”后，投资 人投标冻结金额、担保方保证金、借款人保证金均自劢解 冻
	 * @param int $status; 0:新增; 2:流标结束
	 * @param string $status_msg 主要是status_msg=2时记录的，流标原因
	 * @param unknown_type $platformNo
	 * @param unknown_type $post_url
	 * @return string
	 */
	function DoBids($deal_id,$pOperationType,$status, $status_msg, $platformNo,$post_url,$sys='pc'){
			
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=yeepay&class_act=DoBids";//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=yeepay&class_act=DoBids";//s2s方式返回		
		
		//$requestNo = $GLOBALS['db']->getOne("select * from ".DB_PREFIX."yeepay_cp_transaction where is_callback = 1 and code = 1 and tenderOrderNo = '".$deal_id."'");
 
 		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
		if($deal['type']==3){
			$t_arr = $GLOBALS['db']->getAll("select ycp.*,dorder.type as order_type,dorder.progress as order_progress,dorder.deal_id,dorder.user_id from ".DB_PREFIX."yeepay_cp_transaction as ycp left join ".DB_PREFIX."deal_order as dorder on dorder.requestNo=ycp.requestNo where ycp.is_callback = 1 and ycp.tenderId = ".$deal_id." and ycp.bizType = 'TENDER' and   ycp.is_complete_transaction = 0");
		}else{
			$t_arr = $GLOBALS['db']->getAll("select ycp.*,dorder.type as order_type,dorder.progress as order_progress,dorder.deal_id,dorder.user_id from ".DB_PREFIX."yeepay_cp_transaction as ycp left join ".DB_PREFIX."deal_order as dorder on dorder.requestNo=ycp.requestNo where ycp.is_callback = 1 and ycp.tenderId = ".$deal_id." and ycp.bizType = 'TENDER' and   ycp.is_complete_transaction = 0");
		}
  		$err_count = 0;
		foreach($t_arr as $k => $v)
		{
			$data = array();
			$data['requestNo'] = $v["requestNo"];//请求流水号
			$data['platformNo'] = $platformNo;// 商户编号
			$data['mode'] = "CANCEL";
			
			/* 请求参数 */  
			$req = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
			."<request platformNo=\"".$platformNo."\">"
			."<requestNo>".$data['requestNo']."</requestNo>"
			."<mode>".$data['mode']."</mode>"
			."<notifyUrl><![CDATA[" .$pS2SUrl ."]]></notifyUrl>"
			."</request>";
			/* 签名数据 */
			$sign = cfca($req);
			
			$yeepay_log = array();
			$yeepay_log['code'] = 'COMPLETE_TRANSACTION';
			$yeepay_log['create_date'] = to_date(NOW_TIME,'Y-m-d H:i:s');
			$yeepay_log['strxml'] = $req;
			$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
			//$id = $GLOBALS['db']->insert_id();
			
			/* 调用账户查询服务 */
			$service = "COMPLETE_TRANSACTION";
			$ch = curl_init($post_url."/bhaexter/bhaController");
			curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_SSL_VERIFYPEER=>0,
			CURLOPT_SSL_VERIFYHOST=>0,
			CURLOPT_POSTFIELDS => 'service=' . $service . '&req=' . rawurlencode($req) . "&sign=" . rawurlencode($sign)
			));
			$resultStr = curl_exec($ch);
			
			if (empty($resultStr)){
				$err_count ++ ;
			}else{
					require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
					$str3ParaInfo = @XML_unserialize($resultStr);
 					$str3Req = $str3ParaInfo['response'];
 					
 					$result['pErrCode'] = $str3Req["code"];
					$result['pErrMsg'] = $str3Req["description"];
					 
 					if($str3Req["code"] == 1)
					{
						$requestNo = $v["requestNo"];
						$t_data = array();
						$t_data["is_complete_transaction"] = 2;
						$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$t_data,'UPDATE'," requestNo = '".$requestNo."'");
 						$deal_load = array();
						$deal_load['is_complete_transaction'] = 2;//2#退款成功
						$deal_load['is_refund'] = 1;
 						 
						$where = " requestNo = ".$requestNo;
						$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$deal_load,'UPDATE',$where);
						 
						if($GLOBALS['db']->affected_rows()){
							deal_order_progress($v['deal_id'],$v['user_id'],4);
						}
						
					
					}
			}
		}	
		//showIpsInfo('同步成功',"");
	}
	function DoBidsCallBack($str3Req)
	{
		if($str3Req["code"] == 1)
		{
			$requestNo = $str3Req['requestNo'];
			$t_data = array();
			$t_data["is_complete_transaction"] = 2;
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_cp_transaction",$t_data,'UPDATE'," requestNo = '".$requestNo."'");
			
			if($GLOBALS['db']->affected_rows()){
				$order=$GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order where requestNo = '".$requestNo."'");
	 			$deal_load = array();
				$deal_load['is_complete_transaction'] = 2;//2#退款成功
				$deal_load['is_refund'] = 1;
				//$deal_load['progress']=$t_r['order_type'];
				 
				$where = " requestNo = ".$requestNo;
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$deal_load,'UPDATE',$where);
				if($GLOBALS['db']->affected_rows()){
					deal_order_progress($order['deal_id'],$order['user_id'],4);
				}
			}
				
 			
		}
		return 1;
	}
	
	
?>