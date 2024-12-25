<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_doreh extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
		
	public function delete($id)
	{
		$this->load->model('m_dorehclass','dorehclass');
		
		$dorehclass = $this->db->where('dorehid',$id)->get('dorehclass')->result();
		foreach($dorehclass as $k=>$v){
			$this->dorehclass->delete($v->id);
		}
		return $this->db->where('id',$id)->delete('doreh');
	}
    public function Pre($data,$die = 1){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if($die) {
            die();
        }
    }

	public function isBought($user_id,$dorehclassid)
	{
		$dorehclassid = (int)$dorehclassid;
		$user_id = (int)$user_id;

		$this->db
		->where('ref_id',NULL)
		->where('status',NULL)
		->where('cdate < ',time()-10)
		->where('user_id',$user_id)
		->delete('classfactors');
		
		$this->db->join('ci_classfactors f','f.id=ub.factor_id','right',FALSE);
		$this->db->where('f.user_id',$user_id);
		$this->db->where('f.status',0);
		
		$this->db->where('ub.dorehclassid',$dorehclassid);
		
		return $this->db->count_all_results('user_doreh ub');
	}
	
	public function createFactor($user_id, $dorehid=NULL, $dorehclassid=NULL, $discount_id=NULL,$owner=0){
		$user_id  = (int)$user_id;
		$c_price  = 0;
		$discount = 0;
		$discountfee = 0;
		$dorehclasses    = [];

		if(is_numeric($discount_id) && $discount_id){
			$O = $this->db->select('percent,fee')->where('id',$discount_id)->get('discounts')->row();
			$discount = (float)$O->percent;
			$discountfee = (float)$O->fee;
		} else {
			$discount_id = NULL;
		}
		
		if($discount > 100) $discount = 100;
		if($discount < 0)   $discount = 0;

		if($dorehid)
		{
            $dorehclasses = $this->db->where('dorehid',$dorehid)->select('id,price,dorehid,classid')->get('dorehclass')->result();
		}
		else
		{
			$dorehclasses = $this->db->where('id',$dorehclassid)->select('id,price,dorehid,classid')->get('dorehclass')->result();
		}
		if(empty($dorehclasses))
			return [
				'done' => FALSE, 
				'msg'  => 'هیچ دوره یا کلاسی جهت خرید وجود ندارد'
			];
		
		$factor = array(
			'user_id'     => $user_id,
			'discount'    => $discountfee?$discountfee:$discount,
			'status'      => NULL,
			'discount_id' => $discount_id,
			'owner' => $owner,
			'cdate'       => time()
		);
		
		$this->db->insert('classfactors',$factor);
		
		$factor_id = $this->db->insert_id();
		$added     = 0;
		foreach($dorehclasses as $dorehclass)
		{
			if($this->isBought($user_id,$dorehclass->id))
				continue;
			
			$c_price += (int)$dorehclass->price;
			$dorehclassprice = (int)$dorehclass->price;
			$this->db->insert('classfactor_detail',array(
				'dorehclassid'   => $dorehclass->id,
				'price'   => $dorehclassprice,
				'dorehid'   => $dorehclass->dorehid,
				'class_id'   => $dorehclass->classid,
				'discount'   => ($dorehclassprice) - ($dorehclassprice*($discount/100)),
				'factor_id' => $factor_id 
			));
			$this->db->insert('user_doreh',array(
				'dorehclassid'   => $dorehclass->id,
				'user_id'   => $user_id,
				'factor_id' => $factor_id 
			));
			$added ++;
		}
		
		if($added == 0)
		{
			$this->db->where('id',$factor_id)->delete('classfactors');
			return [
				'done' => FALSE, 
				'msg'  => 'هیچ موردی جهت خرید وجود ندارد'
			];
		}
		
		$price = $c_price - $c_price*($discount/100);
		if($discountfee)
			$price = $c_price - $discountfee;
		if($price < 0)
			$price = 0;
		
		$this->db->where('id',$factor_id);
		$this->db->set('cprice',$c_price);
		$this->db->set('price',$price);
		$this->db->update('classfactors');
		
		$factor = $this->db->where('id',$factor_id)->get('classfactors',1)->row();
        if($factor) {
            $factor->section = 'doreh';
        }

		return array('done'=>TRUE, 'msg'=>'ok', 'factor'=>$factor);
	}
	public function getFactor($factor_id){
		$factor_id = str_replace("DC-","",$factor_id);
        $factor = $this->db->where('id',$factor_id)->get('classfactors')->row();
        if($factor) {
            $factor->section = 'doreh';
        }
        return $factor;
    }
	
	public function setFactorPaid($factor_id, $ref_id = NULL)
	{
		$factor_id = str_replace("DC-","",$factor_id);
		return $this->updatetFactor($factor_id,[
			'state'  => 'پرداخت موفق',
			'status' => 0,
			'ref_id' => $ref_id,
			'pdate'  => time()
		]);
	}
	
	public function updatetFactor($factor_id,$data)
	{
		$factor_id = str_replace("DC-","",$factor_id);
		return $this->db->where('id',$factor_id)->update('classfactors',$data);
	}	
	
	public function getUserBooks($user_id,$where=NULL,$limit=0,$limitstart=0)
	{
		$user_id = (int)$user_id;
		
		$this->db->select('ub.dorehclassid,ub.need_update');
		$this->db->join('ci_classfactors f','(ub.factor_id=f.id AND f.status=0)','right',FALSE);
		$this->db->where('ub.user_id',$user_id);
		$result = $this->db->get('user_doreh ub')->result();
		$count = count($result);
		if($limit || $limitstart){
			$this->db->select('ub.dorehclassid,ub.need_update');
			$this->db->join('ci_classfactors f','(ub.factor_id=f.id AND f.status=0)','right',FALSE);
			$this->db->where('ub.user_id',$user_id);
			$this->db->limit($limit,$limitstart);
			$result = $this->db->get('user_doreh ub')->result();
		}
		
		if(empty($result)) return [];
		
		$ids = [];
		$need_update = [];
		foreach($result as $row){
			$ids[] = $row->dorehclassid;
			$need_update[$row->dorehclassid] = $row->need_update;
		}

        $books = $this->post->getPosts([
			'user_id'  => $user_id,
			'type'  => 'book',
			'where' => 'p.id in ('. implode(',',$ids) .')' ,
		]);
		foreach($books as $k=>$book){
			$books[$k]->need_update = $need_update[$book->id];
		}
		if($limit || $limitstart){
			return array($count,$books);
		}
		return $books;
	}	
	//===== discounts =====/
	public function setDiscountUsed($discount_id=NULL,$factor_id=NULL)
	{
		//Alireza Balvardi	
		$discount_id = (int)$discount_id;
		$factor_id   = (int)$factor_id;

		$factor_id = str_replace("DC-","",$factor_id);
		$factor = $this->db->where('id',$factor_id)->get('factors')->row();
		$user_id      = (int)$factor->user_id;
		
		$discount = $this->db->where('id',$discount_id)->get('discounts')->row();

		$data=array('user_id'=>$user_id,'discount_id'=>$discount_id,'udate'=>time(),'factor_id'=>$factor_id);
		$this->db->insert('discount_used',$data);

		return $this->db->where('id',$discount_id)->update('discounts',[
			'factor_id' => $factor_id,
			'used'      => $discount->used+1,
			'udate'     => time()
		]);
	}
	
	public function checkDiscountCode($code=NULL, $category_id=NULL, $user_id=NULL,$bookid=NULL){
		$category_id  = (int)$category_id;
		$user_id      = (int)$user_id;
		$discount_id = NULL;
		$banTime   = 48*3600;
		$failCount = 3;
		
		$ban = $this->db->where([
			'user_id'   => $user_id,
			'event'     => 'discount_ban',
			'datestr >' => time() - $banTime,
		])->get('logs',1)->row();
		
		if(!empty($ban))
		{
			$remTime = $banTime/3600 - floor((time()-$ban->datestr)/3600);
			return "شما تا {$remTime} ساعت دیگر نمی توانید از این بخش استفاده کنید";
		}
		
		if($category_id)
			$this->db->where('category_id IN(0,'.$category_id.')');
		if($bookid && $category_id==-1)
			$this->db->where('bookid',$bookid);
		$discount = $this->db->where('code',$code)->where("(expdate > UNIX_TIMESTAMP() OR ISNULL (expdate))")->get('discounts')->row();//Alireza Balvardi

		if($code && !isset($discount->id)){
			return "کد تخفیف وارد شده معتبر نیست";
		}
		if($code && $discount->used == $discount->maxallow){
			return "سقف استفاده از کد تخفیف وارد شده تکمیل شده است";
		}
		
		if($code && $discount->category_id && $discount->category_id != $category_id){
			return "کد تخفیف وارد شده برای خرید این سطح نیست";
		}

		$discountused = $this->db
			->where('user_id',$user_id)
			->where('discount_id',$discount_id)
			->get('discount_used')->row();
		if($discountused){
			return "شما از کد تخفیف وارد شده قبلا استفاده کردید";
		}

		if($code)
			$discount_id = $discount->id;
		
		return $discount_id;
	}

}?>