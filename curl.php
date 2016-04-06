<?php

/*
参数:@param :获取页面使用正则分析出所需要的url
*/
function tiebaCurl($tiebaTitle){
	// 1. 初始化
	$ch = curl_init();
	// 2. 设置选项，包括URL
	curl_setopt($ch, CURLOPT_URL, "http://tieba.baidu.com/f/like/furank?kw={$tiebaTitle}&ie=utf-8#");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	// 3. 执行并获取HTML文档内容
	$output = curl_exec($ch);
	//3.5用以排错
	if ($output === FALSE) {
	echo "cURL Error: " . curl_error($ch);
	}
	//3.6 获取请求的相关信息
	curl_exec($ch);
	$info = curl_getinfo($ch);
	//echo '获取'. $info['url'] . '耗时'. $info['total_time'] . '秒';
	// 4. 释放curl句柄
	curl_close($ch);
	unset($ch);//销毁变量:马丹,影响到我后面输出了竟然日了狗了
	//5.将获取的网页进行正则匹配出需求的信息
	/*
	使用别的定界符后成功
	*/
	 preg_match_all('{\/home/main/\?.*?&fr=furank}',$output,$match);
	//进行遍历生成拥有新的数组
	foreach ($match[0] as $key => $value) 
	{
	 	# code...
		 $match[$key] = 'http://tieba.baidu.com/'.$value;
	 } 
	 //7.创建cUrl批处理句柄
	$mh = curl_multi_init();
	//6.对新生成的url进行批处理
	foreach($match as $key => $value)
	{
		//6.1遍历创建url资源
		$ch[$key] = curl_init();
		//6.2指定URL和恰当的参数
		curl_setopt($ch[$key], CURLOPT_URL, $value);
		curl_setopt($ch[$key], CURLOPT_HEADER, 0);
		curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch[$key], CURLOPT_TIMEOUT, 3);
		curl_multi_add_handle($mh,$ch[$key]);

	}



	//8.1预定义一个状态变量


	$active = null;
	//8.2 执行批处理
	do {
	    $mrc = curl_multi_exec($mh, $active);
	} while ($mrc == CURLM_CALL_MULTI_PERFORM);
	 
	 
	while ($active and $mrc == CURLM_OK) {
	    
	    if(curl_multi_select($mh) === -1){
	        usleep(100);
	    }
	    do {
	        $mrc = curl_multi_exec($mh, $active);
	    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
	 
	}
	//9 获取批处理内容

	foreach ($ch as $key => $value) {
		 $content = curl_multi_getcontent($value);
		 //对获取到的单条资源进行正则匹配
	 	  preg_match('{class="userinfo_head"><img src="http:.*?\.jpg"\/>}',$content,$matchd) ||preg_match('{class="userinfo_head"><img src="http://tb.himg.baidu.com/sys/portrait/item/.*\d?\?t=\d*}',$content,$matchs) ||preg_match('{class="userinfo_head"><img src="http://tb.himg.baidu.com/sys/portrait/item/\w*}',$content,$matchs);
		$content = $matchd ? ltrim($matchd[0],'class="userinfo_head">') : $matchs[0].'">';
	 	$content = ltrim($content,'class="userinfo_head">');
		$contents[$key] = curl_errno($value) == 0 ? $content : '';
		#移除资源句柄
		curl_multi_remove_handle($mh,$value);
	}
	//10.已经获取到相关数据,如何处理呢,

	foreach ($contents as $key => $value) {
		$value = rtrim(rtrim(ltrim($value,'<img src="'),'">'),'"/');
		 if (stripos($value,'hiphotos')) {
		 	file_put_contents('./pic/'.$key.'.gif', file_get_contents($value));
		 }else{
		 	file_put_contents('./pic/'.$key.'.jpg', file_get_contents($value));
		 }
		

	}


	//关闭各个句柄
	curl_multi_close($mh);
	
}
