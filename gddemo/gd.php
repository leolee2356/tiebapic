<?php
//创建空白画布
//查看图片数量

function tiebaGd($title){
	//进行
	$dir='./pic';
	$num = bldir($dir);
	$height= ceil($num/5)*110;
	$width=110*5;
	$dstim = imagecreatetruecolor($width,$height); //创建一个真彩的画布
//进行两层循环解决图片展示
	static $i = 0;
	for ($z=0; $z <ceil($num/5) ; $z++) { 

			for ($j=0; $j <5 ; $j++) { 
				 # 于已知图片的创建
				if ($i<$num ) {
					//中间是类型判断png,gif还是jpg可读性太差,不要骂我,后面补的坑

					if(@getimagesize("./pic/".$i.".jpg")[2]==3){
						rename("./pic/".$i.".jpg","./pic/".$i.".png" );
						@$dst[$i] = imagecreatefrompng("./pic/".$i.".png");
					}else{
						$dst[$i] =   file_exists("./pic/".$i.".jpg") ? imagecreatefromjpeg("./pic/".$i.".jpg") : imagecreatefromgif("./pic/".$i.".gif");
					}
				$x=$j*110;
				$y=$z*110;
				imagecopymerge($dstim,$dst[$i],$x,$y,0,0,110,110,100);
				$i++;
				}else{
					break;
				}		
			}
		
	}
	//3.输出图片
//header("Content-Type:image/jpeg");
//imagejpeg($dstim);
//5保存图像
	$title=urlencode($title);
	imagejpeg($dstim,"./tiebapic/{$title}.jpg");

}


