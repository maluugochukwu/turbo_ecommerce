<?php

class Blog extends dbobject
{
   public function blogList($data)
    {
		$table_name    = "blog";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'title', 'dt' => 1 ),
			array( 'db' => 'picture', 'dt' => 2, 'formatter' => function( $d,$row ) {
                $id = $row['id'];
                return '<img class="img-thumbnail" src="'.$d.'" width="50" height="50" /><span class="badge badge-success" style="display:block;cursor:pointer" onclick="getModal(\'setup/blog_image.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Image</span>';
            }  ),
			array( 'db' => 'category_id',  'dt' => 3, 'formatter' => function( $d,$row ) {
                return $this->getitemlabel("blog_category","id",$d,"name");
            } ),
            array( 'db' => 'author', 'dt' => 4),
            array( 'db' => 'id', 'dt' => 5, 'formatter' => function( $d,$row ) {
                $status = $row['status'];
                $text = ($status == 1)?"Disable":"Enable";
                $comment_count = count($this->db_query("SELECT id FROM blog_comment WHERE blog_id = '$d'"));
                $feature_state = $row['is_featured'];
                $feature_text = ($row['is_featured'] == 1)?"Unset Feature":"Set as feature";
                $feature_style = ($row['is_featured'] == 1)?"btn btn-danger":"btn btn-success";
                $style = ($status == 1)?"btn-danger":"btn-success";
						return '<button class="btn btn-warning"  onclick="getModal(\'setup/blog_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit</button> | <button class="btn '.$style.'"  onclick="disableBlog(\''.$d.'\',\''.$status.'\')"  href="javascript:void(0)" >'.$text.'</button> | <button class="btn btn-info"  onclick="getpage(\'blog_comment.php?id='.$d.'\',\'page\')"  href="javascript:void(0)" >View ('.$comment_count.') Comment </button> | <button class="btn btn-danger" onclick="deleteBlog(\''.$d.'\')"   ><i class="fa fa-trash"></i> Delete </button> | <button class="btn btn-primary"  onclick="getModal(\'setup/blog_tag.php?blog_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Create Tags</button> | <button class="'.$feature_style.'"  onclick="setAsFeature(\''.$feature_state.'\',\''.$d.'\')"  >'.$feature_text.'</button>';
					} ),
			array( 'db' => 'created',     'dt' => 6, 'formatter' => function( $d,$row ) {
						return $d;
					}
                ),
                array( 'db' => 'status', 'dt' => -1),
                array( 'db' => 'is_featured', 'dt' => -1)
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }

    public function setAsFeature($data)
    {
        $feature_state = ($data['state'] == '1')?0:1;
        $blog_id = $data['id'];
        $sql = "UPDATE blog SET is_featured = '$feature_state' WHERE id = '$blog_id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(["response_code"=>0,"response_message"=>"Updated successfully"]);
        }
        else
        {
            return json_encode(["response_code"=>63,"response_message"=>"Could not Updated blog"]);
        }
    }
    public function deleteTag($data)
    {
        $tag_name = $data['tag'];
        $blog_id = $data['blog_id'];
        $sql = "DELETE FROM blog_tag WHERE tag = '$tag_name' AND blog_id = '$blog_id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            $html =$this->getBlogTagList($blog_id);
            return json_encode(["response_code"=>0,"response_message"=>"Deleted successfully","data"=>$html]);
        }
        else
        {
            return json_encode(["response_code"=>63,"response_message"=>"Could not delete tag"]);
        }
    }
    public function getBlogTagList($blog_id)
    {
        $html = "";
        $counter = 0;
        $result = $this->db_query("SELECT * FROM blog_tag WHERE blog_id = '$blog_id'");
        foreach ($result as $row) {
            $counter++;
            $html .= "<tr><td>".$counter."</td><td>".$row['tag']."</td><td>". date("jS M h:i:s",strtotime($row['created']))."</td><td><button onclick='deleteTag(\"$blog_id\",\"$row[tag]\")' class='btn btn-danger'>Delete</button></td></tr>";
        }
        return $html;
    }
    public function commentHasChildren($data)
    {
        $blog_id = $data["blog_id"];
        $parent_id = $data["parent_id"];
        $sql2 = "SELECT count(id) as counter FROM blog_comment WHERE blog_id = '$blog_id' AND parent_id = '$parent_id' AND status = '1'";
        $result2 = $this->db_query($sql2);
        return $result2[0]['counter'];
    }
    public function blogComment($data)
    {
        $id = $data["blog_id"];
        $parent_id = $data["parent_id"];
        $filter = ($parent_id == "")?"parent_id IS NULL":"parent_id = '$parent_id'";
        $sql = "SELECT * FROM blog_comment WHERE blog_id = '$id' AND $filter AND status = '1'";
        $result = $this->db_query($sql);
        $html = '';
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $counter = $this->commentHasChildren(['parent_id'=>$row['id'],'blog_id'=>$id]);
                $reply_text = "";
                $style = "";
                if($counter > 1)
                {
                    $reply_text = "view $counter replies";
                }elseif($counter == 1)
                {
                    $reply_text = "view reply";
                }else
                {
                    $style = "display:none;";
                }
                $children = ($counter > 0)?"<a title='$reply_text' onclick='showReply(this)' style='$style cursor:pointer;color:#ffdc71'>$reply_text</a><ul style='display:none' class='children'>".$this->blogComment(['parent_id'=>$row['id'],'blog_id'=>$id])."</ul>":"";
                $html = $html.'<li class="comment">
                                    
                                    <div class="comment-body">
                                        <h3>'.$row['full_name'].'</h3>
                                        <div class="meta">'.date("M d, Y -- h:iA",strtotime($row['created'])).'</div>
                                        '.$row['content'].'<br/>
                                        <a onclick="showComment(this)"  href="javascript:void(0)" class="reply">Reply</a>
                                        <form onsubmit="return false" class="contactForm form" style="display:none">
                                            <input type="hidden" value="reply_comment" name="op" />
                                            <input type="hidden" value="'.$id.'" name="blog_id" />
                                            <input type="hidden" value="'.$row['id'].'" name="parent_id" />
                                            <div class="row" >
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label" for="name">Full Name</label>
                                                        <input type="text" class="form-control" autocomplete="off" name="full_name" id="name" placeholder="Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="label" for="email">Email Address</label>
                                                        <input type="email" class="form-control" autocomplete="off" name="email" id="email" placeholder="Email">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="label" for="#">Comment</label>
                                                        <textarea name="content" autocomplete = "off" class="form-control" id="message" cols="30" rows="4" placeholder="Comment"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="submit" onclick="submitReply(this)" value="Reply Comment" class="btn btn-primary">
                                                        <div class="submitting"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    '.$children.'</div>
                                </li>';
            }
            return $html;
        }else
        {
            return "sddfdf";
        }
    }
    public function saveTag($data)
    {
        $blog_id = $data['blog_id'];
        $data['created'] = date("Y-m-d h:i:s");
        if(!empty($data['tag']))
        {
            $count = $this->doInsert("blog_tag",$data,["op"]);
            if($count > 0)
            {
                $html = $this->getBlogTagList($blog_id);
                return json_encode(["response_code"=>0,"response_message"=>"Tag saved successfully","data"=>$html]);
            }else
            {
                return json_encode(["response_code"=>819,"response_message"=>"Could not save tag"]);
            }
        }
        else
        {
            return json_encode(["response_code"=>89,"response_message"=>"Tag field is required"]);
        }
    }
    public function searchResult($data)
    {
        $term   = $data['term'];
        $sql    = "SELECT blog_id AS b_id,tag FROM blog_tag WHERE tag LIKE '%$term%' GROUP BY blog_id";
        $result = $this->db_query($sql);
        $count  = count($result);
        $output = [];
        if($count > 0)
        {
            foreach ($result as $row) 
            {
                $details = $this->getItemLabelArr("blog",["id"],[$row['b_id']],["title","url"]);
                $output[] =  ["tags"=>$row['tag'],"title"=>$details[0]['title'],"url"=>$details[0]['url']];
            }
            return json_encode($output);
        }
        else
        {
            return json_encode($output);
        }
    }
   public function commentList($data)
    {
		$table_name    = "blog_comment";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'username', 'dt' => 1 ),
			array( 'db' => 'full_name', 'dt' => 2),
			array( 'db' => 'content',  'dt' => 3),
            array( 'db' => 'id', 'dt' => 4, 'formatter' => function( $d,$row ) {
                $status = $row['status'];
                $bg = ($status == 1)?"btn-danger":"btn-success";
                $text = ($status == 1)?"Hide Comment":"Show Comment";
                return '<button class="btn '.$bg.'"  onclick="commentStatus(\''.$d.'\',\''.$status.'\')"  href="javascript:void(0)" >'.$text.'</button>';
            }),
			array( 'db' => 'created',     'dt' => 5, 'formatter' => function( $d,$row ) {
						return date('jS F, Y h:iA',strtotime($d));
					}
                ),
                array( 'db' => 'status', 'dt' => -1)
			);
		$filter = "";
        $blog_id = $data['blog_id'];
		$filter = " AND blog_id='$blog_id'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function disableBlog($data)
    {
        $state = ($data['state'] == 1)?0:1;
        $id = $data['id'];
        $sql = "UPDATE blog SET status = '$state' WHERE id = '$id' LIMIT 1";
        $this->db_query($sql,false);
        return json_encode(array('response_code'=>'0','response_message'=>'Status has been changed')); 
    }
    public function commentStatus($data)
    {
        $state = ($data['state'] == 1)?0:1;
        $id = $data['comment_id'];
        $sql = "UPDATE blog_comment SET status = '$state' WHERE id = '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(array('response_code'=>'0','response_message'=>'Status has been changed'));
        }else
        {
            return json_encode(array('response_code'=>'0','response_message'=>'Status could not be changed'));
        }
         
    }
    public function blogCategoryList($data)
    {
		$table_name    = "blog_category";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'id', 'dt' => 2, 'formatter' => function( $d,$row ) {
                return '<button class="btn btn-info"  onclick="getModal(\'setup/blog_category_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary"><i class="fa fa-edit"></i> Edit</button> ';
            } ),
			array( 'db' => 'created',  'dt' => 3 )
			
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function saveBlog($data)
    {
        $validation = $this->validate($data,array('title'=>'required',"category_id"=>"required"),array('category_id'=>'Category'));
        if(!$validation['error'])
        {
            
            
            $data['is_approved']       = "1";
            $data['allow_comment']     = (isset($data['allow_comment']))?"1":"0";
            $data['auto_show_comment'] = (isset($data['auto_show_comment']))?"1":"0";
            $data['body']              = str_replace("nbsp;"," ",$data['body']);
            file_put_contents("blog_content.txt",$data['body']);
            if($data['operation'] == "new")
            {
                $data['author'] = $_SESSION['username_sess'];
                $data['created'] = date('Y-m-d h:i:s');
                $data['url'] = $this->generateUrl($data['title']);
                $count = $this->doInsert('blog',$data,array('op','operation','id','_files'));
                if($count > 0)
                {
                    $insert_id = $this->getInsert_id($this->myconn);
                    $file_data = $data['_files'];
                    // $path = './uploads/';
                    $ff   = $this->saveMerchantImage($file_data,"uploads/","");
                    $ff   = json_decode($ff,true);
//                    var_dump($ff);
                    if($ff['response_code'] == "0")
                    {
                        $full_path = $ff['data'];
                        $sql       = "UPDATE blog SET picture = '$full_path' WHERE id = '$insert_id' LIMIT 1";
                        $count = $this->db_query($sql,false);
                        return ($count == 1)?json_encode(array('response_code'=>'0','response_message'=>'Successful','data'=>array('product_id'=>$insert_id))):json_encode(array('response_code'=>'471','response_message'=>'Failed to update product image'));
                    }
                    else
                    {
                        return json_encode(array('response_code'=>'71','response_mesage'=>'Failed to upload blog image'));
                    }
                }else
                {
                    return json_encode(array('response_code'=>'291','response_message'=>'Blog Could not be Created'));
                }
            }else
            {
                $data['is_modified'] = "1";
                $data['modified_date'] = date('Y-m-d h:i:s');;
                $count = $this->doUpdate('blog',$data,['op','operation','_files'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Blog Update Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Blog Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function deleteBlog($data)
    {
        $id = $data['id'];
        $sql = "DELETE FROM blog WHERE id= '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(['response_code'=>0,'response_message'=>'Deleted successfully']);
        }else
        {
            return json_encode(['response_code'=>77,'response_message'=>'Could not delete blog']);
        }
    }
    public function updateDressImage($data)
    {
    //   var_dump($data);
        $file_data        = $data['_files'];
//        $merchant_id = $_SESSION['merchant_sess_id'];
        $path        = 'uploads/';
//        foreach($data['_files'] as $file_data)
        $image_id    = rand(1111,999999999).date('his');
        
        $ff = $this->saveMerchantImage($file_data,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $dress_id = $data['id'];
            $sql       = "UPDATE blog SET picture = '$full_path' WHERE id = '$dress_id' LIMIT 1";
            $count     = $this->db_query($sql,false);
            // unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function saveBlogCategory($data)
    {
        $validation = $this->validate($data,["name"=>"required"]);
        if(!$validation['error'])
        {
            $data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == "new")
            {
                    $count = $this->doInsert("blog_category",$data,['op','operation','id']);
                    if($count > 0)
                    {
                        return json_encode(array('response_code'=>0,'response_message'=>'Category Created Successfully'));
                    }else
                    {
                        return json_encode(array('response_code'=>996,'response_message'=>'Category could not be created'));
                    }
            }else
            {
                $count = $this->doUpdate("blog_category",$data,['op','operation','id'],["id"=>$data['id']]);
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Category updated Successfully'));
                }else
                {
                    return json_encode(array('response_code'=>996,'response_message'=>'Category could not be updated'));
                }
            }
        }else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function saveMerchantImage($data,$path,$image_id="")
    {
        $_FILES = $data;
//        var_dump($_FILES);
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Invalid parameter.'));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return json_encode(array('response_code'=>'74','response_mesage'=>'No file sent.'));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.'));
            default:
                return json_encode(array('response_code'=>'74','response_mesage'=>'Unknown errors.'));
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.'));
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
    //    $finfo = new finfo(FILEINFO_MIME_TYPE);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            finfo_file($finfo,$_FILES['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                // 'jpeg' => 'image/jpeg',
                'png' => 'image/png'
            ),
            true
        )) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Invalid file format.'));
        }

            // You should name it uniquely.
            // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from its binary data.
            $email = ($image_id == "")?date('mdhis'):$image_id;

            //@@@@@@@@@@@@@@@@@@@@@@@
            
            if (!move_uploaded_file($_FILES['upfile']['tmp_name'],sprintf($path.'%s.%s',$email,$ext))) {
                return json_encode(array('response_code'=>'50','response_mesage'=>'Failed to move uploaded file.'));
            }
            $full_path = $path.$email.'.'.$ext;
            $myImage = new SimpleImage();
            $myImage->load($full_path);
            $myImage->resize(860,700);
            $myImage->save($full_path);
            unlink($_FILES['upfile']['tmp_name']);
            return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
                
            
        
    }
    public function generateUrl($title)
    {
        $url = strtolower(trim($title));
        $url = preg_replace('/[\@\.\;\'\"]+/', '', $url);
        $url = str_replace(" ","-",$url);
        return $url;
    }
    public function getNextRoleId()
    {
        $sql    = "select CONCAT('00',max(role_id) +1) as rolee FROM role";
        $result = $this->db_query($sql);
        return $result[0]['rolee'];
        
    }
}