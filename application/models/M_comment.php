<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_comment extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
	public function unsubmit($id)
	{
		$data = array('submitted'=>0);
		return $this->db->where('id',$id)->update('comments',$data);
	}
		
	public function submit($id)
	{
		$data = array('submitted'=>1);
		return $this->db->where('id',$id)->update('comments',$data);
	}
			
	public function delete($id)
	{
        $childs =  $this->db->select('id')->where('parent',$id)->get('comments')->result();

        if(!empty($childs))
        {
            foreach ($childs as $item)
                $this->delete($item->id);
        }
		return $this->db->where('id',$id)->delete('comments');
	}

	
	/*====================================================
		CLIENT SIDE		
	====================================================*/	
	public function add($data)
	{
		if( ! isset($data['table'],$data['row_id'],$data['parent'],$data['text']) )
		return FALSE;
		
		if( ! isset( $this->user->data->id ) && ! isset($data['name'],$data['email']) )
		return FALSE;
		
		$userid = isset( $this->user->data->id ) ? $this->user->data->id:0;
		$name   = isset( $this->user->data->id ) ? $this->user->data->displayname:$data['name'];
        $email  = isset( $this->user->data->id ) ? $this->user->data->email:$data['email'];

		$data = array(
			'table'     => $data['table'],
			'row_id'    => $data['row_id'],
			'submitted' => $this->settings->data['auto_submit_comment'],
			'user_id'   => $userid,
			'name'      => $name,
            'email'     => $email,
			'text'      => $data['text'],
			'ip'        => $this->input->ip_address(),
			'date'      => date('Y-m-d H:i:s'),
			'parent'    => $data['parent'],
		);
		
		if( $this->db->insert('comments',$data) )
		return $this->db->insert_id();
		return  NULL;
	}	 	 	 	 	 	 	 	 	 
	
	public function get($table,$row_id,$from=0,$limit=20)
	{
		$where = array('table'=>$table,'row_id'=>$row_id,'submitted'=>1);
		return $this->db->where($where)->get('comments',$from,$limit)->result();	
	}
	
	public function getPrimary($table,$row_id,$from=0,$total=20)
	{
		$where = array('table'=>$table,'row_id'=>$row_id,'parent'=>0,'submitted'=>1);
		return $this->db->where($where)->get('comments',$from,$total)->result();
		
	}	
	
	public function getChilds($table,$row_id,$id,$from=0,$total=20)
	{
		$where = array('table'=>$table,'row_id'=>$row_id,'parent'=>$id,'submitted'=>1);
		return $this->db->where($where)->get('comments',$from,$total)->result();
		
	}
	
	public function postComments($id,$from=0,$total=20)
	{
		$where = array('table'=>$table,'parent'=>$id,'submitted'=>1);
		return $this->db->where($where)->get('comments',$from,$total)->result();
		
	}	
	
	public function postCommentsCount($id)
	{
		$where = array('table'=>'posts','row_id'=>$id,'submitted'=>1);
		return $this->db->where($where)->count_all_results('comments');	
		
	}
	
	public function selectById($id,$mod='post-comments')
	{
		if( ! $id ) return NULL;
		
		$id      = intval($id);
		$add     = $mod == 'user-reviews' ? "WHERE c.id=$id LIMIT 1":"WHERE id=$id LIMIT 1";
		$query   = $this->tools->buildQuery($mod,$add);
		$comment = $this->db->query($query)->result();	
		
		return isset( $comment[0] )	? $comment[0]:NULL;
	}	
	
	public function printPostComments($post_id,$parent=0,$table='posts',$from=0,$limit=10)
	{
		$ORDER = $parent == 0 ? 'DESC':'ASC';

        $post_id = (int)$post_id;
        $parent  = (int)$parent;
        $from    = (int)$from;
        $limit   = (int)$limit;
        $isAdmin = $this->user->can('delete_comment');

		$add = 
		"WHERE   `c`.`submitted`=1
			 AND `c`.`table`=".$this->db->escape($table)."
			 AND `c`.`row_id`=$post_id
			 AND `c`.`parent`=$parent
		ORDER BY `c`.`date` $ORDER LIMIT $from,$limit";
		
		$USER = isset( $this->user->data->id ) ? $this->user->data->id:0;

        $query = $this->tools->buildQuery($table == 'users' ?'user-reviews':'post-comments',$add);

        $comments = $this->db->query($query)->result();

        $total = $this->db->where(array(
            'submitted' => 1 ,
            'row_id'    => $post_id ,
            'parent'    => $parent ,
            'table'     => $table ,
        ))->count_all_results('comments');

        $from += $limit;

		if( $comments )
		{
			foreach( $comments as $cm ) : $hasRating = isset($cm->rating) && (int)$cm->rating; ?>
				<div class="item comment" item-id="<?php echo  $cm->id ?>">
                    <header>
                        <div class="author">
                            <?php if($cm->user_username != ''): ?>
                            <a href="<?php echo  site_url('user/'.$cm->user_username) ?>" title="<?php echo $cm->name  ?>">
                            <?php endif ?>
                                <img src="<?php echo  $this->user->getAvatarSrc(NULL,150,$cm->user_avatar) ?>" alt="<?php echo  html_escape($cm->name) ?>">
                            <?php if($cm->user_username != ''): ?>
                            </a>
                            <?php endif ?>
                        </div>
                        <div class="name wbr <?php echo  $hasRating ? 'has-rating':'' ?>">
                            <i class="wbr"><?php echo  $cm->name ?></i>
                            <?php if($hasRating):?>
                                <div class="rating-stars clearfix">
                                    <?php echo  $this->rate->ratingStars((int)$cm->rating); ?>
                                </div>
                            <?php endif ?>
                        </div>
                        <div class="cm-date">
                            <?php echo  $this->tools->Date($cm->date) ?>
                        </div>
                    </header>
                    <ul class="list-unstyled cm-options">
                        <?php $rated = $cm->is_rated ? 'on':'' ?>
                        <li class="toggle-rate <?php echo  $rated ?>" data-toggle='{"table":"comments","row":<?php echo  $cm->id ?>}'>
                            <i class="fa fa-star"></i>
                            <span class="rate-count"><?php echo  $cm->rate_count ? :'' ?></span>
                        </li>
                        <li onClick="replyComment(this)">
                            <i class="fa fa-mail-reply-all"></i>
                            <span class="hidden">پاسخ</span>
                        </li>
                        <?php if($isAdmin OR ($USER && $cm->user_id == $USER)): ?>
                            <li onClick="deleteComment(this)">
                                <i class="fa fa-trash"></i>
                            </li>
                        <?php endif  ?>
                    </ul>
                    <div class="body clearfix">
						<div class="content wbr"><?php echo  html($cm->text) ?></div>
                        <div class="reply-con"><?php $this->printPostComments($post_id,$cm->id,$table) ?></div>
                    </div>
				</div>
            <?php endforeach;

            if( $total > $from ) : ?>
                <div class="text-center load-more-comments">
                    <span class="text-muted mb-10" onclick="loadMoreComments(this,<?php echo  "$post_id,$parent,'$table',$from,$limit" ?>)">
                        <i class="fa fa-refresh"></i>
                        <span> &nbsp; نمایش نظرات بیشتر</span>
                    </span>
                </div>
            <?php endif;
		}	
	}

	public function htmlTemplate($cm)
	{
        $USER = isset( $this->user->data->id ) ? $this->user->data->id:0;
        $hasRating = isset($cm->rating) && (int)$cm->rating;

		$result =
            /** @lang text */
            '<div class="item comment" item-id="'.$cm->id.'">
            <header>
                <div class="author">
                    '.($cm->user_username ?'<a href="'.site_url('user/'.$cm->user_username).'">':'').'
                    <img src="'.$this->user->getAvatarSrc(NULL,150,$cm->user_avatar).'" alt="'.html_escape($cm->name).'">
                    '.($cm->user_username ?'</a>':'').'
                </div>
                <div class="name '.($hasRating ? 'has-rating':'').'">
                    <i>'.$cm->name.'</i>
                    '.($hasRating?'<div class="rating-stars clearfix">'.($this->rate->ratingStars((int)$cm->rating)).'</div>':'').'
                </div>
                <div class="cm-date">
                    '.$this->tools->Date($cm->date).'
                </div>
            </header>
            <ul class="list-unstyled cm-options">
                <li class="toggle-rate '.( $cm->is_rated ? 'on':'' ).'" data-toggle=\'{"table":"comments","row":'.$cm->id.'}\'>
                    <i class="fa fa-star"></i>
                    <span>'.($cm->rate_count ? :'').'</span>
                </li>
                <li onClick="replyComment(this)">
                    <i class="fa fa-mail-reply-all"></i>
                </li>
                '.($USER && $cm->user_id == $USER ? '<li onClick="deleteComment(this)"><i class="fa fa-trash"></i></li>':'').'
            </ul>
            <div class="body clearfix">
                <div class="content wbr">'.html($cm->text).'</div>
                <div class="reply-con"></div>
            </div>
        </div>';
		return $result;	
	}
}