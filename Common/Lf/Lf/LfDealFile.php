<?php
class LfDealFile{
/*
*传入文件目录，返回目录下的所有文件
*/
static function FileInDir($dir){
	if(!file_exists($dir)||!is_dir($dir)){
		return '';
	}
	$dirList=array('dirNum'=>0,'fileNum'=>0,'lists'=>'');
	$dir=opendir($dir);
	$i=0;
	while($file=readdir($dir)){
		if($file!=='.'&&$file!=='..'){
			$dirList['lists'][$i]['name']=$file;
			if(is_dir($file)){
				$dirList['lists'][$i]['isDir']=true;
				$dirList['dirNum']++;
			}else{
				$dirList['lists'][$i]['isDir']=false;
				$dirList['fileNum']++;
			}
			$i++;
		};
	};
	closedir($dir);
	return $dirList;
}
/*
*传入文件名，返回文件的后缀
*/
static function getExt($file) {
	$ext = substr(strtolower(strrchr($file, '.')),1);
	return $ext;
}
static function searchDir($path,&$data){
    if(is_dir($path)){
        $dp=dir($path);
        while($file=$dp->read()){
            if($file!='.'&& $file!='..'){
                searchDir($path.'/'.$file,$data);
            }
        }
        $dp->close();
    }
    if(is_file($path)){
        $data[]=$path;
    }
}
static function getDir($dir){
    $data=array();
    searchDir($dir,$data);
    return   $data;
}
}