<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_Classonline extends CI_Model {

	public $setting = NULL;
	public $data = NULL;

	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;
	}


	public function delete($id)
	{
		return $this->db->where('id',$id)->delete('classonline');
	}

	public function addClassonline_Data($tid,$data_type)
	{
		$this->db->where('cid',$tid)->delete('classonline_data');
		foreach($data_type as $k=>$v){
            switch ($k){
                case 'book':
                    foreach($v as $k1=>$v1){
                        $data = array();
                        $data['cid'] = $tid;
                        $data['data_type'] = $k;
                        $data['data_id'] = $v1;
                        $data['startpage'] = $data_type['startpage'][$v1];
                        $data['endpage'] = $data_type['endpage'][$v1];
                        $this->db->insert('classonline_data',$data);
                    }
                    break;
                case 'hamniaz':
                    foreach($v as $k1=>$v1){
                        $data = array();
                        $data['cid'] = $tid;
                        $data['data_type'] = $k;
                        $data['data_id'] = $v1;
                        $data['startpage'] = 0;
                        $data['endpage'] = 0;
                        $this->db->insert('classonline_data',$data);
                    }
                    break;
                case 'dayofweek':
                    foreach($v as $k1=>$v1){
                        $data = array();
                        $data['cid'] = $tid;
                        $data['data_type'] = $k;
                        $data['data_id'] = $k1;
                        $data['dayofweek'] = $data_type['dayofweek'][$k1];
                        $data['starttime'] = $data_type['starttime'][$k1];
                        $data['endtime'] = $data_type['endtime'][$k1];
                        $this->db->insert('classonline_data',$data);
                    }
                    break;
            }
        }
    }
    public function isBought($user_id, $classonline_id)
    {
        $classonline_id = (int)$classonline_id;
        $user_id = (int)$user_id;

        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->where('classonline_id', $classonline_id);
        //$this->db->where('enddate >= CURDATE()');

        $result = $this->db->count_all_results('user_classonline');
        return $result;
    }

    public function checkDiscountCode($code = NULL, $classonline_id = NULL, $user_id = NULL)
    {
        $classonline_id = (int)$classonline_id;
        $user_id = (int)$user_id;
        $discount_id = NULL;
        $banTime = 48 * 3600;

        $ban = $this->db->where([
            'user_id' => $user_id,
            'event' => 'discount_ban',
            'datestr >' => time() - $banTime,
        ])->get('logs', 1)->row();

        if (!empty($ban)) {
            $remTime = $banTime / 3600 - floor((time() - $ban->datestr) / 3600);
            return "شما تا {$remTime} ساعت دیگر نمی توانید از این بخش استفاده کنید";
        }

        if ($classonline_id) {
            $this->db->where('category_id IN(0,' . $classonline_id . ')');
        }
        $discount = $this->db->where('code', $code)->where("(expdate > UNIX_TIMESTAMP() OR ISNULL (expdate))")->get('discounts')->row();//Alireza Balvardi

        if ($code && !isset($discount->id)) {
            return "کد تخفیف وارد شده معتبر نیست";
        }
        if ($code && $discount->used == $discount->maxallow) {
            return "سقف استفاده از کد تخفیف وارد شده تکمیل شده است";
        }

        if ($code && $discount->classonline_id && $discount->classonline_id != $classonline_id) {
            return "کد تخفیف وارد شده معتبر نیست";
        }

        $discountused = $this->db
            ->where('user_id', $user_id)
            ->where('discount_id', $discount_id)
            ->get('discount_used')->row();
        if ($discountused) {
            return "شما از کد تخفیف وارد شده قبلا استفاده کردید";
        }

        if ($code) {
            $discount_id = $discount->id;
        }
        return $discount_id;
    }

    public function createFactor($user_id, $classonline_id, $discount_id = NULL)
    {
        $user_id = (int)$user_id;
        $discount = 0;
        $discountfee = 0;

        if (is_numeric($discount_id) && $discount_id) {
            $O = $this->db->select('percent,fee')->where('id', $discount_id)->get('discounts')->row();
            $discount = (float)$O->percent;
            $discountfee = (float)$O->fee;
        } else {
            $discount_id = NULL;
        }

        if ($discount > 100) $discount = 100;
        if ($discount < 0) $discount = 0;

        $classonline = $this->db
            ->select('id,price,discount')
            ->where('id', $classonline_id)
            ->get('classonline')
            ->row();

         $classaccount = $this->db
            ->select('id')
            ->where('classonline_id', $classonline_id)
            ->where('user_id', 0)
            ->get('classaccount')
            ->row();

        if (empty($classonline) || empty($classaccount)) {
            return [
                'done' => FALSE,
                'msg' => 'هیچ اشتراکی جهت خرید وجود ندارد'
            ];
        }
        $c_price = $classonline->price - ($classonline->price * floatval($classonline->discount)/100);
        $d_price = $c_price;
        if($discount) {
            $d_price = $c_price - $c_price * ($discount / 100);
        } elseif ($discountfee) {
            $d_price = $c_price - $discountfee;
        }
        if ($d_price < 0) {
            $d_price = 0;
        }

        $factor = array(
            'user_id' => $user_id,
            'status' => NULL,
            'discount_id' => $discount_id,
            'cprice' => $c_price,
            'price' => $d_price,
            'discount' => $c_price - $d_price,
            'owner' => 0,
            'section' => 'classonline',
            'data_id' => $classonline_id,
            'cdate' => time()
        );

        $this->db->insert('factors', $factor);

        $factor_id = $this->db->insert_id();



        $this->db->where('id', $factor_id);
        $factor = $this->db->where('id', $factor_id)->get('factors', 1)->row();

        return array('done' => TRUE, 'msg' => 'ok', 'factor' => $factor);
    }

    public function getFactor($factor_id)
    {
        $factor_id = str_replace("DC-", "", $factor_id);
        return $this->db->where('id', $factor_id)->get('factors')->row();
    }

    public function setFactorPaid($factor_id, $ref_id = NULL)
    {
        $factor_id = str_replace("DC-", "", $factor_id);
        return $this->updatetFactor($factor_id, [
            'state' => 'پرداخت موفق',
            'status' => 0,
            'ref_id' => $ref_id,
            'pdate' => time()
        ]);
    }

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

    public function updatetFactor($factor_id, $data)
    {
        $factor_id = str_replace("DC-", "", $factor_id);
        if(isset($data['status']) && is_numeric($data['status']) && $data['status'] == 0){
            $factor = $this->db->where('id', $factor_id)->get('factors', 1)->row();
            $classonlinedata = array(
                'factor_id' => $factor_id,
                'user_id' => $factor->user_id,
                'classonline_id' => $factor->data_id,
            );
            $this->db->insert('user_classonline', $classonlinedata);
            $this->db->where("user_id",0)->where("classonline_id",$factor->data_id)->limit('1')->update('classaccount',["user_id"=>$factor->user_id,"factor_id"=>$factor_id]);
        }
        return $this->db->where('id', $factor_id)->update('factors', $data);
    }

    public function Pre($data, $die = 1)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if ($die) {
            die();
        }
    }
}