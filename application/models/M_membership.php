<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_membership extends CI_Model
{

    public $setting = NULL;
    public $data = NULL;

    function __construct()
    {
        parent::__construct();
        $this->setting = $this->settings->data;
    }


    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('membership');
    }

    public function isBought($user_id, $membership_id)
    {
        $membership_id = (int)$membership_id;
        $user_id = (int)$user_id;

        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->where('membership_id', $membership_id);
        $this->db->where('enddate >= CURDATE()');

        return $this->db->count_all_results('user_membership');
    }

    public function checkDiscountCode($code = NULL, $category_id = NULL, $user_id = NULL)
    {
        $category_id = (int)$category_id;
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

        if ($category_id) {
            $this->db->where('category_id IN(0,' . $category_id . ')');
        }
        $discount = $this->db->where('code', $code)->where("(expdate > UNIX_TIMESTAMP() OR ISNULL (expdate))")->get('discounts')->row();//Alireza Balvardi

        if ($code && !isset($discount->id)) {
            return "کد تخفیف وارد شده معتبر نیست";
        }
        if ($code && $discount->used == $discount->maxallow) {
            return "سقف استفاده از کد تخفیف وارد شده تکمیل شده است";
        }

        if ($code && $discount->category_id && $discount->category_id != $category_id) {
            return "کد تخفیف وارد شده برای خرید این سطح نیست";
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

    public function createFactor($user_id, $membership_id, $discount_id = NULL)
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

        $membership = $this->db->where('id', $membership_id)->select('id,price')->get('membership')->row();
        if (empty($membership)) {
            return [
                'done' => FALSE,
                'msg' => 'هیچ اشتراکی جهت خرید وجود ندارد'
            ];
        }
        $c_price = $membership->price;
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
            'section' => 'membership',
            'data_id' => $membership_id,
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

    public function updatetFactor($factor_id, $data)
    {
        $factor_id = str_replace("DC-", "", $factor_id);
        if(isset($data['status']) && is_numeric($data['status']) && $data['status'] == 0){
            $factor = $this->db->where('id', $factor_id)->get('factors', 1)->row();
            $membership = $this->db->where('id', $factor->data_id)->get('membership', 1)->row();
            $enddate = date('Y-m-d', strtotime('+'.$membership->allowmonths.' month'));
            $membershipdata = array(
                'factor_id' => $factor_id,
                'user_id' => $factor->user_id,
                'membership_id' => $factor->data_id,
                'startdate' => date('Y-m-d'),
                'enddate' => $enddate,
            );

            $this->db->insert('user_membership', $membershipdata);
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