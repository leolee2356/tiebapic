
<?php
/**
 * 慕课网视频教学
 * 代码实例-PHP-cURL实战
 * 实例描述：在网络上下载一个网页并把内容中的“百度”替换为“屌丝”之后输出
 */
$curlobj = curl_init();			// 初始化
curl_setopt($curlobj, CURLOPT_URL, "http://www.baidu.com");		// 设置访问网页的URL
curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, true);			// 执行之后不直接打印出来
$output=curl_exec($curlobj);	// 执行
curl_close($curlobj);			// 关闭cURL
echo str_replace("百度","屌丝",$output);
?>