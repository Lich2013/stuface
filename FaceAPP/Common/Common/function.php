<?php
	function doupload(){
		$upload = new \Think\Upload();						// 实例化上传类
    	$upload->maxSize = 3145728;							// 设置附件上传大小
    	$upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
    	$upload->rootPath = './Public/upimage/'; 			// 设置附件上传根目录
    	$upload->autoSub = false;
        $upload->savePath = ''; 		
        if(I('session.uid')!=null){					        //学生上传单张照片
    	    $upload->saveName = I('session.uid');              // 设置上传文件名
            $info = $upload->uploadOne($_FILES['photo']);		//执行上传方法
    	    if(!$info) {                                       // 上传错误提示错误信息
                $this->error($upload->getError());
            }else{                                              // 上传成功 获取上传文件信息
                return './Public/upimage/'.$info['savename'];
            }
        }else{                                                  //后台管理员批量上传照片
            $info = $upload->upload($_FILES['photo']);      
            if(!$info) {                                       // 上传错误提示错误信息
                $this->error($upload->getError());
            }else{                                              // 上传成功 获取上传文件信息
                return $info;
            } 
        }
        
    }

    function dothumb($picpath){
    	$arr = explode('/', $picpath);
    	$filename = array_pop($arr);
        $big_name = get_big_name($filename);
        $imginfo = getImageSize($picpath);
        $imgw = $imginfo [0];     
        $imgh = $imginfo [1];
    	$image = new \Think\Image();
        $image->open($picpath);
        $image->save('./Public/allimage/'.$big_name);
        $image->thumb(300, 300,\Think\Image::IMAGE_THUMB_SCALE)->save('./Public/allimage/'.$filename);
        unlink($picpath);
	    return $filename;
    }

    function savepic($filename){
        $picpath = './Public/upimage/'.$filename;
        $image = new \Think\Image();
        $image->open('./Public/upimage/'.$filename);
        $image->save('./Public/allimage/'.$filename);
        unlink($filename);
       
    }

    function get_big_name($filename){
        $arr = explode('.', $filename);
        $big_name = $arr[0].'_big.'.$arr[1];
        return $big_name; 
    }

    function get_uid($filename){
        $arr = explode('.', $filename);
        $uid = $arr[0];
        return $uid;
    }

    function log_result(){
        $url = "http://hongyan.cqupt.edu.cn/RedCenter/Api/Handle/login";
        $post_data = array(
            'user' => I('post.username'),
            'password' => I('post.password'),
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode ($output,true);
        return $data;
    }
  