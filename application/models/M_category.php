<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_category extends CI_Model
{

    public $data = NULL;

    function __construct()
    {
        parent::__construct();
    }

    public function isBought($user_id, $cat_id, $membership_ids)
    {
        $count = 0;
        foreach ($membership_ids as $key=>$membership_id) {
            $this->db->select('*');
            $this->db->where('user_id', $user_id);
            $this->db->where('cat_id', $cat_id[$key]);
            //$this->db->where('membership_id', $membership_id);
            $this->db->where('enddate >= DATE_ADD(CURDATE(),INTERVAL ' . $membership_id . ' MONTH)');
            $result = $this->db->get('user_catmembership')->row();
            if(is_object($result)) {
                $count++;
            }
        }
        return $count == count($cat_id);
    }
    // public function isBought($user_id, $cat_ids)
    // {
    //     $boughtItems = [];
    //     foreach ($cat_ids as $cat_id) {
    //         $this->db->select('*');
    //         $this->db->where('user_id', $user_id);
    //         $this->db->where('cat_id', $cat_id);
    //         $this->db->where('enddate >= CURDATE()');  // Ensure the membership is active
    //         $result = $this->db->get('user_catmembership')->row();
            
    //         if (is_object($result)) {
    //             $boughtItems[] = [
    //                 'id' => $result->id,
    //                 'cat_id' => $result->cat_id,
    //                 'enddate' => $result->enddate
    //             ];
    //         }
    //     }
    //     return $boughtItems; // Return all bought items (existing active memberships)
    // }


    // public function checkDiscountCode($code, $startcode, $plan_id,$category_id, $user_id)
    // {
    //     $plan_id = (array)$plan_id;
    //     $user_id = (int)$user_id;
    //     $discount_id = NULL;
    //     $banTime = 48 * 3600;

    //     $ban = $this->db->where([
    //         'user_id' => $user_id,
    //         'event' => 'discount_ban',
    //         'datestr >' => time() - $banTime,
    //     ])->get('logs', 1)->row();

    //     if (!empty($ban)) {
    //         $remTime = $banTime / 3600 - floor((time() - $ban->datestr) / 3600);
    //         return "شما تا {$remTime} ساعت دیگر نمی توانید از این بخش استفاده کنید";
    //     }

    //     if ($plan_id) {
    //         foreach ($plan_id as $key => $value) {
    //             $bookid = $category_id[$key];
    //             $plan_id[$key] = "$startcode$value@$bookid";
    //         }
    //         $this->db->where_in("CONCAT(category_id,'@',bookid)", $plan_id);
    //     }
    //     $discounts = $this->db->where('code', $code)->where("(expdate > UNIX_TIMESTAMP() OR ISNULL (expdate))")->get('discounts')->result();//Alireza Balvardi
    //     if ($code && !count($discounts)) {
    //         return "کد تخفیف وارد شده معتبر نیست";
    //     }
    //     $discount_ids = [];
    //     foreach ($discounts as $key=>$discount) {
    //         if ($code && $discount->used == $discount->maxallow) {
    //             $discount_ids["used"][$discount->category_id] = "سقف استفاده از کد تخفیف وارد شده تکمیل شده است";
    //         }

    //         if ($code && $discount->category_id && !in_array($discount->category_id."@".$discount->bookid,$plan_id)) {
    //             $discount_ids["notallowed"][$discount->category_id] = "کد تخفیف وارد شده برای خرید این سطح نیست";
    //         }

    //         $discountused = $this->db
    //             ->where('user_id', $user_id)
    //             ->where('discount_id', $discount_id)
    //             ->get('discount_used')->row();
    //         if ($discountused) {
    //             $discount_ids["usedbefore"][$discount->id] = "شما از کد تخفیف وارد شده قبلا استفاده کردید";
    //         }

    //         if ($code && $discount->category_id && in_array($discount->category_id."@".$discount->bookid,$plan_id)) {
    //             $discount_ids["allowed"][$discount->bookid] = $discount;
    //         }
    //     }
    //     return $discount_ids;
    // }
    
    public function checkDiscountCode($code, $startcode, $plan_id, $category_id, $user_id)
    {
        $plan_id = (array)$plan_id;
        $user_id = (int)$user_id;
        $discount_ids = []; // Array to hold all discount validation messages
        $banTime = 48 * 3600; // 48 hours in seconds
    
        // Check if the user is banned from using discounts
        $ban = $this->db->where([
            'user_id' => $user_id,
            'event' => 'discount_ban',
            'datestr >' => time() - $banTime,
        ])->get('logs', 1)->row();
    
        if (!empty($ban)) {
            $remTime = $banTime / 3600 - floor((time() - $ban->datestr) / 3600);
            return "شما تا {$remTime} ساعت دیگر نمی توانید از این بخش استفاده کنید";
        }
    
        // Validate plan_id and category_id
        if ($plan_id) {
            foreach ($plan_id as $key => $value) {
                $bookid = $category_id[$key];
                $plan_id[$key] = "$startcode$value@$bookid";
            }
            $this->db->where_in("CONCAT(category_id,'@',bookid)", $plan_id);
        }
    
        // Fetch discounts matching the code
        $discounts = $this->db
            ->where('code', $code)
            ->where("(expdate > UNIX_TIMESTAMP() OR ISNULL(expdate))") // Check expiration
            ->get('discounts')
            ->result();
    
        // Check if the discount code exists and is valid
        if ($code && !count($discounts)) {
            return "کد تخفیف وارد شده معتبر نیست";
        }
    
        // Process each discount
        foreach ($discounts as $discount) {
            // Check if the discount has reached its usage limit
            if ($discount->used >= $discount->maxallow) {
                $discount_ids["used"][$discount->category_id] = "سقف استفاده از کد تخفیف وارد شده تکمیل شده است";
                continue; // Skip to the next discount
            }
    
            // Check if the discount is applicable to the plan and category
            if ($discount->category_id && !in_array($discount->category_id . "@" . $discount->bookid, $plan_id)) {
                $discount_ids["notallowed"][$discount->category_id] = "کد تخفیف وارد شده برای خرید این سطح نیست";
                continue; // Skip to the next discount
            }
    
            // Check if the user has already used this discount
            $discount_used = $this->db
                ->where('user_id', $user_id)
                ->where('discount_id', $discount->id) // Use $discount->id instead of $discount_id
                ->get('discount_used')
                ->row();
    
            if ($discount_used) {
                $discount_ids["usedbefore"][$discount->id] = "شما از کد تخفیف وارد شده قبلا استفاده کردید";
                continue; // Skip to the next discount
            }
    
            // Add the discount to the allowed list
            $discount_ids["allowed"][$discount->bookid] = $discount;
        }
    
        // Return the first allowed discount, or all validation messages if no valid discount
        if (!empty($discount_ids["allowed"])) {
            return $discount_ids["allowed"]; // Return allowed discounts
        }
    
        return $discount_ids; // Return validation messages
    }
 


    public function createFactor($user_id, $category_ids, $plan_ids, $discount_ids = [])
    {
        $user_id = (int)$user_id;
        $category_ids = (array)$category_ids;
        $useddiscounts = [];
        $discounts = [];
        $discountsfee = [];
        $plans = [];
        $categories = [];
        if (count($discount_ids)) {
            foreach ($discount_ids as $category_id=>$discount_id) {
                $useddiscounts[] = $discount_id->id;
                $discounts[$category_id] = (float)$discount_id->percent;
                $discountsfee[$category_id] = (float)$discount_id->fee;
            }
        }
        $plan_ids = (array)$plan_ids;

        foreach ($plan_ids as $key=>$plan_id) {
            $O = $this->db->where_in('id', $category_ids[$key])->select("id,membership$plan_id AS price,discountmembership$plan_id AS discount")->get('category')->row();
            if($O){
                $categories[$category_ids[$key]] = $O->price - ($O->price * floatval($O->discount)/100);
                $plans[$category_ids[$key]] = $plan_id;
            }
        }
        if (!count($categories)) {
            return [
                'done' => FALSE,
                'msg' => 'هیچ اشتراکی جهت خرید وجود ندارد'
            ];
        }
        $c_price = [];
        $section = [];

        foreach ($categories as $key=>$price) {
            $c_price[$key] = $price;
            $d_price[$key] = $price;
            if (isset($discounts[$key])) {
                $d_price[$key] = intval($c_price[$key] - $c_price[$key] * ($discounts[$key] / 100));
            } elseif (isset($discountsfee[$key])) {
                $d_price[$key] = intval($c_price[$key] - $discountsfee[$key]);
            }
            if ($d_price[$key] < 0) {
                $d_price[$key] = 0;
            }
            $section[] = "$key.$plans[$key]";
        }

        $factor = array(
            'user_id' => $user_id,
            //'status' => NULL,
            'discount_id' => implode(",",$useddiscounts),
            'cprice' => array_sum($c_price),
            'price' => array_sum($d_price),
            'discount' => array_sum($c_price) - array_sum($d_price),
            'owner' => 0,
            'section' => 'category',
            'data_id' => implode(",", $section),
            'cdate' => time()
        );

        $this->db->insert('factors', $factor);

        $factor_id = $this->db->insert_id();


        $this->db->where('id', $factor_id);
        $factor = $this->db->where('id', $factor_id)->get('factors', 1)->row();
        $factor->discount_details = $discounts;
        return array('done' => TRUE, 'msg' => 'ok', 'factor' => $factor);
    }
    
//     public function createFactor($user_id, $category_ids, $plan_ids, $discount_ids = [])
// {
//     $user_id = (int)$user_id;
//     $category_ids = (array)$category_ids;
//     $useddiscounts = [];
//     $discounts = [];
//     $discountsfee = [];
//     $plans = [];
//     $categories = [];

//     // Fetch existing active memberships for this user
//     $existingMemberships = $this->db
//         ->select('cat_id, membership_id, enddate')
//         ->where('user_id', $user_id)
//         ->where_in('cat_id', $category_ids)
//         ->where('enddate >= CURDATE()') // Ensure only active memberships
//         ->get('user_catmembership')
//         ->result_array();

//     $ownedMemberships = [];
//     foreach ($existingMemberships as $membership) {
//         $ownedMemberships[$membership['cat_id']] = $membership['membership_id'];
//     }

//     $newCategoryIds = [];
//     foreach ($category_ids as $index => $cat_id) {
//         $plan_id = $plan_ids[$index];

//         // Allow purchase if the category is not owned, or if it's owned but with a different plan
//         if (!isset($ownedMemberships[$cat_id]) || $ownedMemberships[$cat_id] != $plan_id) {
//             $newCategoryIds[] = $cat_id;
//         }
//     }

//     if (count($discount_ids)) {
//         foreach ($discount_ids as $category_id => $discount_id) {
//             $useddiscounts[] = $discount_id->id;
//             $discounts[$category_id] = (float)$discount_id->percent;
//             $discountsfee[$category_id] = (float)$discount_id->fee;
//         }
//     }
//     $plan_ids = (array)$plan_ids;

//     foreach ($plan_ids as $key => $plan_id) {
//         if (in_array($category_ids[$key], $newCategoryIds)) { // Process only new or different-plan categories
//             $O = $this->db
//                 ->where('id', $category_ids[$key])
//                 ->select("id, membership$plan_id AS price, discountmembership$plan_id AS discount")
//                 ->get('category')
//                 ->row();

//             if ($O) {
//                 $categories[$category_ids[$key]] = $O->price - ($O->price * floatval($O->discount) / 100);
//                 $plans[$category_ids[$key]] = $plan_id;
//             }
//         }
//     }

//     if (!count($categories)) {
//         return [
//             'done' => FALSE,
//             'msg' => 'هیچ اشتراکی جهت خرید وجود ندارد'
//         ];
//     }

//     $c_price = [];
//     $section = [];

//     foreach ($categories as $key => $price) {
//         $c_price[$key] = $price;
//         $d_price[$key] = $price;
//         if (isset($discounts[$key])) {
//             $d_price[$key] = intval($c_price[$key] - $c_price[$key] * ($discounts[$key] / 100));
//         } elseif (isset($discountsfee[$key])) {
//             $d_price[$key] = intval($c_price[$key] - $discountsfee[$key]);
//         }
//         if ($d_price[$key] < 0) {
//             $d_price[$key] = 0;
//         }
//         $section[] = "$key.$plans[$key]";
//     }

//     $factor = array(
//         'user_id' => $user_id,
//         'discount_id' => implode(",", $useddiscounts),
//         'cprice' => array_sum($c_price),
//         'price' => array_sum($d_price),
//         'discount' => array_sum($c_price) - array_sum($d_price),
//         'owner' => 0,
//         'section' => 'category',
//         'data_id' => implode(",", $section),
//         'cdate' => time()
//     );

//     $this->db->insert('factors', $factor);

//     $factor_id = $this->db->insert_id();

//     $factor = $this->db->where('id', $factor_id)->get('factors', 1)->row();
//     $factor->discount_details = $discounts;

//     return array('done' => TRUE, 'msg' => 'ok', 'factor' => $factor);
// }



    public function getFactor($factor_id)
    {
        $factor_id = str_replace("DC-", "", $factor_id);
        return $this->db->where('id', $factor_id)->get('factors')->row();
    }

    public function updatetFactor($factor_id, $data)
    {
        $factor_id = str_replace("DC-", "", $factor_id);
        $factor = $this->db->where('id', $factor_id)->get('factors', 1)->row();
        if (isset($data['status']) && is_numeric($data['status']) && $data['status'] == 0) {
            $data_ids = explode(",",$factor->data_id);
            foreach ($data_ids as $data_id){
                list($category_id, $plan_id) = explode(".", $data_id);
                $enddate = date('Y-m-d', strtotime('+' . $plan_id . ' month'));
                $membershipdata = array(
                    'factor_id' => $factor_id,
                    'user_id' => $factor->user_id,
                    'cat_id' => $category_id,
                    'membership_id' => $plan_id,
                    'startdate' => date('Y-m-d'),
                    'enddate' => $enddate,
                );
                $category_ids = [$category_id];
                $this->db->insert('user_catmembership', $membershipdata);
                $categories = $this->db->where('parent', $category_id)->get('category')->result();
                foreach ($categories as $category) {
                    $category_ids[] = $category->id;
                    $subcategories = $this->db->where('parent', $category->id)->get('category')->result();
                    if (count($subcategories)) {
                        foreach ($subcategories as $subcategory) {
                            $category_ids[] = $subcategory->id;
                        }
                    }
                }
                $books = $this->db->select('id')->where('category IN(' . implode(",", $category_ids) . ")")->get('posts')->result();
                $book_ids = [];
                foreach ($books as $book) {
                    $book_ids[] = $book->id;
                    $this->db->insert('user_books', array(
                        'book_id' => $book->id,
                        'user_id' => $factor->user_id,
                        'factor_id' => $factor_id,
                        'expiremembership' => $enddate
                    ));
                }
                if (count($book_ids)) {
                    $this->db->set("has_bought", "has_bought+1", false);
                    $this->db->where_in('id', implode(",", $book_ids), false)->update('posts');
                }
            }
        }
        $this->db->where('id', $factor_id)->update('factors', $data);
    }
    public function setFactorPaid($factor_id, $ref_id = NULL)
    {
        $this->updatetFactor($factor_id, [
            'state' => 'پرداخت موفق',
            'status' => 0,
            'ref_id' => $ref_id,
            'pdate' => time()
        ]);
    }

    public function setDiscountUsed($discount_id, $factor_id)
    {
        //Alireza Balvardi
        $discount_id = (int)$discount_id;
        $factor_id = (int)$factor_id;
        if(!$discount_id){
            return ;
        }
        $factor = $this->db->where('id', $factor_id)->get('factors')->row();
        $user_id = (int)$factor->user_id;

        $discount = $this->db->where('id', $discount_id)->get('discounts')->row();

        $data = array('user_id' => $user_id, 'discount_id' => $discount_id, 'udate' => time(), 'factor_id' => $factor_id);
        $this->db->insert('discount_used', $data);
        return $this->db->where('id', $discount_id)->update('discounts', [
            'factor_id' => $factor_id,
            'used' => $discount->used + 1,
            'udate' => time()
        ]);
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