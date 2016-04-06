<?php 

/**
功能:
    加水印
参数:
    @param string $dstpath 目标图像
    @param string $logopath 水印图像
    @param int $x 目标x轴坐标 默认值为0
    @param int $y 目标y轴坐标 默认值为0
    @param int $tmd 水印透明度 默认值为50
返回值:
    //./Meinv114.jpg   ./s_Meinv114.jpg
    @return string $newname 加水印图像名称
*/
//echo water('./Meinv114.jpg','./baidu.gif',500,500,30);

 
function water($dstpath,$logopath,$x = 0,$y = 0,$tmd = 50,$pre = 's_'){
    //1.创建画布
    //获取目标图像信息
    $dstinfo = getimagesize($dstpath);
    //根据目标图像类型 来创建画布
    switch($dstinfo[2]){
        case 1:
            $dstimg = imagecreatefromgif($dstpath);
            $imgfunc = 'imagegif';
            break;
        case 2:
            $dstimg = imagecreatefromjpeg($dstpath);
            $imgfunc = 'imagejpeg';
            break;
        case 3:
            $dstimg = imagecreatefrompng($dstpath);
            $imgfunc = 'imagepng';
            break;
    }
     
    //获取logo的图像信息
    $srcinfo = getimagesize($logopath);
    //根据logo的类型来准备logo资源
    switch($srcinfo[2]){
        case 1:
            $srcimg = imagecreatefromgif($logopath);
            break;
        case 2:
            $srcimg = imagecreatefromjpeg($logopath);
            break;
        case 3:
            $srcimg = imagecreatefrompng($logopath);
            break;
    }
    //2.颜色
    //3.开始绘画 
    imagecopymerge($dstimg,$srcimg,$x,$y,0,0,$srcinfo[0],$srcinfo[1],$tmd);
    //4.输出header头
        //3.输出图片
   // header("Content-Type:image/jpeg");
    //imagejpeg($dstim);
    //5.保存图像
    //根据目标类型来保存图像  imagejpeg  imagepng  imagegif
    //imagejpeg($dstimg,'a.jpg');
    //获取路径中的目录部分 dirname
    //获取路径中的文件名部分 basename
    $newname = dirname($dstpath).'/'.$pre.basename($dstpath);
 
    //变量函数
    $imgfunc($dstimg,$newname);
    //6.销毁资源
    imagedestroy($dstimg);
    imagedestroy($srcimg);
    return $newname;
}

 
/*
功能:文件上传
参数：
@param string $fname 上传项的表单名称
@param array $allowtype 上传允许的MIME类型
@param int $allowsize 上传允许的文件大小
@param string $path 上传文件目录
返回值：
@return array $info
    'info' 存储的是信息 文件名称   失败原因
    'error' 上传是否成功 true成功 false失败
     
*/
function upload($fname,$allowtype = array('image/jpeg','image/png','image/gif','image/pjpeg'),$allowsize = 1048576,$path = './uploads/'){
    //定义返回值的信息
    $info = array('info' => '','error' => false);
    $file = $_FILES[$fname];
    //1.先判断错误号
    if($file['error'] > 0){
        switch($file['error']){
            case 1:
                $info['info'] = '上传大小超过了配置文件php.ini中upload_max_filesize选项的大小';
                break;
            case 2:
                $info['info'] = '超过了表单选项MAX_FILE_SIZE的大小';
                break;
            case 3:
                $info['info'] = '文件部分被上传';
                break;
            case 4:
                $info['info'] = '没有文件被上传';
                break;
            case 6:
                $info['info'] = '没有找到临时文件夹';
                break;
            case 7:
                $info['info'] = '对临时文件没有写入权限';
                break;
        }
        return $info;
    }
    //2.判断类型
    if(!in_array($file['type'],$allowtype)){
        $info['info'] = '上传类型不合法';
        return $info;
    }
 
    //3.判断大小
    if($file['size'] > $allowsize){
        $info['info'] = '请上传1M以内的图像';
        return $info;
    }
 
    //4.随机文件名称
    //获取上传文件后缀名
    $ext = pathinfo($file['name'],PATHINFO_EXTENSION);
    do{
        $newname = time().rand(1,9999).'.'.$ext;
    }while(file_exists($path.$newname));
 
    //5.进行上传  move_uploaded_file  is_uploaded_file
 
    if(is_uploaded_file($file['tmp_name'])){
        if(move_uploaded_file($file['tmp_name'],$path.$newname )){
            $info['error'] = true;
            $info['info'] = $newname;
        }else{
            $info['info'] = '文件上传失败';
        }
    }else{
        $info['info'] = '不是通过HTTP POST方式上传的';
    }
    return $info;
}
/*
功能:遍历目录查找||输出目录内文件的个数
参数：
@param string $path 遍历的文件目录
返回值：
@return array $info
    'info' 存储的是信息 文件名称   失败原因
    'error' 上传是否成功 true成功 false失败
     
*/
function bldir($dirpath){
    //打开目录
    $res = opendir($dirpath);
    //读目录  遍历目录
    $i = 0;
    while($file = readdir($res)){
        //排除两个特殊目录
        if($file == '.' || $file == '..'){
            continue;
        }
        //拼接路径
        $newpath = $dirpath.'/'.$file; //./a/3filefunc.php  ./a/b
         
        //判断是否是文件 is_file
        if(is_file($newpath)){
           // echo $file.'<br>';
            $i++;
        }
        //判断是否是目录 is_dir
        if(is_dir($newpath)){
            //递归调用
            bldir($newpath);
        }
    }
    //关闭目录
    closedir($res);
    return $i;
}
/*
功能:删除目录内文件
参数：
@param string $path 删除的文件目录
返回值：
@return array $info
    'info' 存储的是信息 文件名称   失败原因
    'error' 上传是否成功 true成功 false失败
     
*/
function tiebaDelDir($dirpath){
    //打开目录
    $res = opendir($dirpath);
    //读目录  遍历目录
  
    while($file = readdir($res)){
        //排除两个特殊目录
        if($file == '.' || $file == '..'){
            continue;
        }
        //拼接路径
        $newpath = $dirpath.'/'.$file; //./a/3filefunc.php  ./a/b
         
        //判断是否是文件 is_file
        if(is_file($newpath)){
            //echo $file.'<br>';
           @unlink($newpath);
        }
       
    }
    //关闭目录
    @closedir($res);
    //return $i;
}
 ?>