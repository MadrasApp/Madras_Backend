<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->helper('eitaa_helper');

        $error[-1] = 'خطا در پردازش اطلاعات ارسالی';
        $error[-3] = 'ورودیها حاوی کارکترهای غیرمجاز میباشند';
        $error[-4] = 'کلمه عبور یا کد فروشنده اشتباه است';
        $error[-6] = 'سند قبلا برگشت کامل یافته است';
        $error[-7] = 'رسید دیجیتالی تهی است';
        $error[-8] = 'طول ورودیها بیشتر از حد مجاز است';
        $error[-9] = 'وجود کارکترهای غیرمجاز در مبلغ برگشتی';
        $error[-10] = 'رسید دیجیتالی به صورت Base64 نیست';
        $error[-11] = 'طول ورودیها کمتر از حد مجاز است';
        $error[-12] = 'مبلغ برگشتی منفی است';
        $error[-13] = 'مبلغ برگشتی برای برگشت جزئی بیش از مبلغ برگشت نخورده ی رسید دیجیتالی است';
        $error[-14] = 'چنین تراکنشی تعریف نشده است';
        $error[-15] = 'مبلغ برگشتی به صورت اعشاری داده شده است';
        $error[-16] = 'خطای داخلی سیستم';
        $error[-17] = 'برگشت زدن جزیی تراکنش مجاز نمی باشد';
        $error[-18] = 'IP Address فروشنده نا معتبر است';

        $this->error = $error;
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

    /*==================================
                PAYMENT
    ===================================*/
    function paybook($id = null)
    {
        try {

            $id = (int)$id;
            $this->_paybook($id);

        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['error_code'] = $e->getCode();
            $data['config'] = $this->settings->data;
            $this->load->view('client/v_header', $data);
            $this->load->view('client/v_error', $data);
            $this->load->view('client/v_footer', $data);
        }
    }
    
    private function _paybook($id)
    {
        $this->load->model('m_book', 'book');
        $factor = $this->book->getFactor($id);

        if (!isset($factor->id))
            throw new Exception("شماره سفارش اشتباه است", 1);

        if ($factor->status != '')
            throw new Exception("تراکنش مربوط به این سفارش قبلا صورت گرفته است", 1);

        $this->book->updatetFactor($id, ['state' => 'انتقال به بانک']);

        $data['config'] = $this->settings->data;
        $data['factor'] = $factor;

        $this->load->view('client/v_header', $data);
        $this->load->view('client/v_payment_form', $data);
        $this->load->view('client/v_footer', $data);
    }
    
    /*==================================
                PAYMENT CLASS DOREH
    ===================================*/
    function paydorehclass($id = null)
    {
        try {

            $id = (int)$id;
            $this->_paydorehclass($id);

        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['error_code'] = $e->getCode();
            $data['config'] = $this->settings->data;
            $this->load->view('client/v_header', $data);
            $this->load->view('client/v_error', $data);
            $this->load->view('client/v_footer', $data);
        }
    }

    private function _paydorehclass($id)
    {
        $this->load->model('m_doreh', 'doreh');
        $factor = $this->doreh->getFactor($id);

        if (!isset($factor->id))
            throw new Exception("شماره سفارش اشتباه است", 1);

        if ($factor->status != '')
            throw new Exception("تراکنش مربوط به این سفارش قبلا صورت گرفته است", 1);

        $this->doreh->updatetFactor($id, ['state' => 'انتقال به بانک']);

        $factor->id = "DC-$factor->id";

        $data['config'] = $this->settings->data;
        $data['factor'] = $factor;

        $this->load->view('client/v_header', $data);
        $this->load->view('client/v_payment_form', $data);
        $this->load->view('client/v_footer', $data);
    }

    /*==================================
                PAYMENT CLASS Membership
    ===================================*/
    function paymembership($id = null)
    {
        try {

            $id = (int)$id;
            $this->_paymembership($id);

        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['error_code'] = $e->getCode();
            $data['config'] = $this->settings->data;
            $this->load->view('client/v_header', $data);
            $this->load->view('client/v_error', $data);
            $this->load->view('client/v_footer', $data);
        }
    }

    private function _paymembership($id)
    {
        $this->load->model('m_membership', 'membership');
        $factor = $this->membership->getFactor($id);

        if (!isset($factor->id)) {
            throw new Exception("شماره سفارش اشتباه است", 1);
        }

        if ($factor->status != '') {
            throw new Exception("تراکنش مربوط به این سفارش قبلا صورت گرفته است", 1);
        }

        $this->membership->updatetFactor($id, ['state' => 'انتقال به بانک']);

        $factor->id = "DC-$factor->id";

        $data['config'] = $this->settings->data;
        $data['factor'] = $factor;

        $this->load->view('client/v_header', $data);
        $this->load->view('client/v_payment_form', $data);
        $this->load->view('client/v_footer', $data);
    }


    /*==================================
                PAYMENT CLASS PAYCATEGORY
    ===================================*/
    function paycategory($id = null)
    {
        try {

            $id = (int)$id;
            $this->_paycategory($id);

        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['error_code'] = $e->getCode();
            $data['config'] = $this->settings->data;
            $this->load->view('client/v_header', $data);
            $this->load->view('client/v_error', $data);
            $this->load->view('client/v_footer', $data);
        }
    }

    private function _paycategory($id)
    {
        $this->load->model('m_category', 'category');
        $factor = $this->category->getFactor($id);

        if (!isset($factor->id)) {
            throw new Exception("شماره سفارش اشتباه است", 1);
        }

        if ($factor->status != '') {
            throw new Exception("تراکنش مربوط به این سفارش قبلا صورت گرفته است", 1);
        }

        $this->category->updatetFactor($id, ['state' => 'انتقال به بانک']);
        $factor->id = "DC-$factor->id";

        $data['config'] = $this->settings->data;
        $data['factor'] = $factor;

        $this->load->view('client/v_header', $data);
        $this->load->view('client/v_payment_form', $data);
        $this->load->view('client/v_footer', $data);
    }

    function payclassonline($id = null)
    {
        try {

            $id = (int)$id;
            $this->_payclassonline($id);

        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['error_code'] = $e->getCode();
            $data['config'] = $this->settings->data;
            $this->load->view('client/v_header', $data);
            $this->load->view('client/v_error', $data);
            $this->load->view('client/v_footer', $data);
        }
    }

    private function _payclassonline($id)
    {
        $this->load->model('m_classonline', 'classonline');
        $factor = $this->classonline->getFactor($id);

        if (!isset($factor->id)) {
            throw new Exception("شماره سفارش اشتباه است", 1);
        }

        if ($factor->status != '') {
            throw new Exception("تراکنش مربوط به این سفارش قبلا صورت گرفته است", 1);
        }

        $this->classonline->updatetFactor($id, ['state' => 'انتقال به بانک']);
        $factor->id = "DC-$factor->id";

        $data['config'] = $this->settings->data;
        $data['factor'] = $factor;

        $this->load->view('client/v_header', $data);
        $this->load->view('client/v_payment_form', $data);
        $this->load->view('client/v_footer', $data);
    }

    /*==================================
                VERIFY
    ===================================*/
    function verify()
    {
        $section = array_pop($this->uri->segments);
        try {
            $this->_verify($section);
        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['error_code'] = $e->getCode();
            $data['config'] = $this->settings->data;

            $id = $this->input->post('ResNum');
            $ref_id = $this->input->post('RefNum');

            if ($id) {
                switch ($section) {
                    case 'book':
                        $this->load->model('m_book', 'book');
                        $factor = $this->book->getFactor($id);
                        if ($factor->status == '') {
                            $this->book->updatetFactor($factor->id, [
                                'state' => 'پرداخت نا موفق : ' . $data['error'],
                                'status' => NULL,
                                'ref_id' => $ref_id,
                                'pdate' => time()
                            ]);
                            $this->db->where('status', NULL);
                            $this->db->where('id', $factor->id);
                            $this->db->delete('factors');
                            $this->db->where('factor_id', $factor->id);
                            $this->db->delete('factor_detail');
                            $this->db->where('factor_id', $factor->id);
                            $this->db->delete('user_books');
                        }
                        break;
                    case 'doreh':
                        $this->load->model('m_doreh', 'doreh');
                        $factor = $this->doreh->getFactor($id);
                        if ($factor && $factor->status == '') {
                            $this->doreh->updatetFactor($factor->id, [
                                'state' => 'پرداخت نا موفق : ' . $data['error'],
                                'status' => NULL,
                                'ref_id' => $ref_id,
                                'pdate' => time()
                            ]);
                            $this->db->where('status', NULL);
                            $this->db->where('id', $factor->id);
                            $this->db->delete('classfactors');
                            $this->db->where('factor_id', $factor->id);
                            $this->db->delete('classfactor_detail');
                            $this->db->where('factor_id', $factor->id);
                            $this->db->delete('user_doreh');
                        }
                        break;
                    case 'membership':
                        $this->load->model('m_membership', 'membership');
                        $factor = $this->membership->getFactor($id);
                        if ($factor->status == '') {
                            $this->membership->updatetFactor($factor->id, [
                                'state' => 'پرداخت نا موفق : ' . $data['error'],
                                'status' => NULL,
                                'ref_id' => $ref_id,
                                'pdate' => time()
                            ]);
                        }
                        break;
                    case 'classonline':
                        $this->load->model('m_classonline', 'classonline');
                        $factor = $this->classonline->getFactor($id);
                        if ($factor->status == '') {
                            $this->classonline->updatetFactor($factor->id, [
                                'state' => 'پرداخت نا موفق : ' . $data['error'],
                                'status' => NULL,
                                'ref_id' => $ref_id,
                                'pdate' => time()
                            ]);
                        }
                        break;
                }
            }

            $this->load->view('client/v_header', $data);
            $this->load->view('client/v_error', $data);
            $this->load->view('client/v_footer', $data);
        }
    }

    private function _verify($section)
    {
        $data = [];
        $ResNum = $this->input->post('ResNum');
        $RefNum = $this->input->post('RefNum');
        $input = $this->input->post();
        $config = $this->settings->data;
        $online = isset($config["online"]) ? $config["online"] : false;
        if($online){
            $state = $this->input->post('State');
            if ($state == '') {
                throw new Exception("اطلاعات ورودی اشتباه است", 1);
            }
            if ($state != 'OK') {
                throw new Exception($state, 1);
            }

            $client = new SoapClient('https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL', array("stream_context" => stream_context_create(
                array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    )
                )
            )));
            $result = $client->VerifyTransaction($RefNum, $config['saman_id']);

            if ($result <= 0) {
                throw new Exception($this->error[$result], 1);
            }
        } else {
            $RefNum = time();
            $factor_id = str_replace("DC-","",$ResNum);
            $factor = $this->db->select('*')->where('id',$factor_id)->get('factors')->result();
            $result = $factor[0]->price*10;
        }
        /*
        */
        switch ($section) {
            case 'book':
                $this->load->model('m_book', 'book');
                $destFactor = $this->book;
                break;
            case 'doreh':
                $this->load->model('m_doreh', 'doreh');
                $destFactor = $this->doreh;
                break;
            case 'membership':
                $this->load->model('m_membership', 'membership');
                $destFactor = $this->membership;
                break;
            case 'classonline':
                $this->load->model('m_classonline', 'classonline');
                $destFactor = $this->classonline;
                break;
            case 'category':
                $this->load->model('m_category', 'category');
                $destFactor = $this->category;
                break;
            default:
                $data["status"] = 0;
        }
        $factor = $destFactor->getFactor($ResNum,$data);
        if (!isset($factor->id)) {
            // return amount to customer ...
            $client->reverseTransaction($RefNum, $config['saman_id'], $config['saman_username'], $config['saman_password']);

            throw new Exception("سفارش مربوط به این تراکنش پیدا نشد.<br>در نتیجه این تراکنش برگشت خورده و به حساب پرداخت کننده باز خواهد گشت.", 1);
        }

        if ($this->db->where('ref_id', $RefNum)->count_all_results('factors') > 0) {
            throw new Exception("رسید دیجیتالی این تراکنش قبلا برای سفارش دیگری استفاده شده است و دیگر نمیتوان دوباره از آن استفاده کرد.", 1);
        }

        if ($factor->price * 10 != $result) {
            $client->reverseTransaction($RefNum, $config['saman_id'], $config['saman_username'], $config['saman_password']);
            throw new Exception("مبلغ پرداخت شده با مبلغ سفارش برابر نیست.<br>در نتیجه این تراکنش برگشت خورده و به حساب پرداخت کننده باز خواهد گشت.", 1);
        }

        if ($factor->status != '') {
            throw new Exception("تراکنش مربوط به این سفارش قبلا صورت گرفته است", 1);
        }

        $destFactor->updatetFactor($factor->id, ['paid' => $result / 10]);

        $destFactor->setFactorPaid($factor->id, $RefNum);

        if ($factor->discount_id != '') {
            $discountCode = @$this->db->where('id', $factor->discount_id)->get('discounts')->row()->code;
            $discount_ids = explode(",",$factor->discount_id);
            foreach ($discount_ids as $discount_id) {
                $destFactor->setDiscountUsed($discount_id, $factor->id);
            }

            $destFactor->updatetFactor($factor->id, [
                'state' => "پرداخت موفق، استفاده از کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)"
            ]);
        }

        $user_id = $factor->user_id;
        $user = $this->db->select('username')->where('id', $user_id)->get('users')->row();
        $user_meta = $this->db->select('meta_value')
                            ->where('meta_name', 'eitaa_id')
                            ->where('user_id', $user_id)
                            ->get('user_meta')
                            ->row();

        $eitaa_id = $user_meta ? $user_meta->meta_value : null;
        $username = $user ? $user->username : 'کاربر';

        //Get Book Name if Section is 'book'
        $book_name = null;
        if ($section == 'book') {
            $book_data = $this->db->select('b.name')
                                ->from('factor_detail fd')
                                ->join('books b', 'fd.book_id = b.id', 'left')
                                ->where('fd.factor_id', $factor->id)
                                ->get()
                                ->row();
            if ($book_data) {
                $book_name = $book_data->name;
            }
        }

        if ($eitaa_id) {
            if (!empty($username)) {
                $text = "📢 *{$username} عزیز، پرداخت شما با موفقیت انجام شد!* 🎉\n\n";
            } else {
                $text = "📢 *کاربر عزیز، پرداخت شما با موفقیت انجام شد!* 🎉\n\n";
            }
        
            if ($book_name) {
                $text .= "📖 *کتاب خریداری شده:* {$book_name}\n\n";
            }
        
            $text .= "🧾 *اطلاعات فاکتور:*\n";
            $text .= "🆔 *شماره فاکتور:* {$factor->id}\n";
            $text .= "💰 *مبلغ پرداخت شده:* " . number_format($factor->price) . " تومان\n";
            $text .= "💳 *قیمت بدون تخفیف:* " . number_format($factor->cprice) . " تومان\n";
            $text .= "🗓️ *تاریخ پرداخت:* " . jdate('d F y - H:i') . "\n";
            $text .= "📅 *تاریخ ایجاد سفارش:* " . jdate('d F y - H:i', $factor->cdate) . "\n\n";
            
            $text .= "🙏 *از خرید شما متشکریم! امیدواریم که تجربه‌ی فوق‌العاده‌ای داشته باشید.* 🌟";
        
            send_eitaa_message($eitaa_id, $text);
        }
        

        $data['config'] = $config;
        $data['factor'] = $factor;

        $this->load->view('client/v_header', $data);
        $this->load->view('client/v_payment_result', $data);
        $this->load->view('client/v_footer', $data);
    }

    function go_to_app()
    {
        exit('---');
    }
}
