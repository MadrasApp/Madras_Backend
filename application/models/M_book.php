<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_book extends CI_Model
{

    public $setting;
    public $data;

    function __construct()
    {
        parent::__construct();
        $this->setting = $this->settings->data;
    }

    public function isBought($user_id, $book_id)
    {
        $book_id = (int)$book_id;
        $user_id = (int)$user_id;

        $this->db
            ->where('ref_id', NULL)
            ->where('status', NULL)
            ->where('cdate < ', time() - 10)
            ->where('user_id', $user_id)
            ->delete('factors');

        $this->db->join('ci_factors f', 'f.id=ub.factor_id', 'right', FALSE);
        $this->db->where('f.user_id', $user_id);
        $this->db->where('f.status', 0);

        $this->db->where('ub.book_id', $book_id);
        $this->db->where('(ISNULL(ub.expiremembership) OR (NOT ISNULL(ub.expiremembership) AND ub.expiremembership > CURDATE()))');
        $result = $this->db->count_all_results('user_books ub');
        return $result;
    }

    public function createFactor($user_id, $book_id = NULL, $category_id = NULL, $discount_id = NULL, $owner = 0)
    {
        $user_id = (int)$user_id;
        $c_price = 0;
        $discount = 0;
        $discountfee = 0;
        $books = [];

        if (is_numeric($discount_id) && $discount_id) {
            $O = $this->db->select('percent,fee')->where('id', $discount_id)->get('discounts')->row();
            $discount = (float)$O->percent;
            $discountfee = (float)$O->fee;
        } else {
            $discount_id = NULL;
        }

        if ($discount > 100) $discount = 100;
        if ($discount < 0) $discount = 0;

        if ($category_id) {
            /*$category   = $this->db->where('id'     ,(int)$category_id)->get('category')->row();
            $categories = $this->db->where('parent' ,(int)$category_id)->get('category')->result();

            $category_ids = array();

            foreach($categories as $cat)
                $category_ids[] = $cat->id;
            */
            $category = $this->db->where('id', $category_id)->get('category', 1)->row();
            $discount = $discount ? $discount : (int)$category->description;

            if ($category->parent == 0)
                $where = ["p.category IN (SELECT `id` FROM `ci_category` WHERE `parent`={$category_id} AND `type`='book')"];
            else
                $where = ['p.category' => $category_id];


            $books = $this->post->getPosts([
                'user_id'  => $user_id,
                'type' => 'book',
                'where' => $where,
            ]);
        } else {
            $books = $this->post->getPosts([
                'user_id'  => $user_id,
                'type' => 'book',
                'where' => ['p.id' => $book_id],
                'limit' => 1
            ]);
        }
        if (empty($books))
            return [
                'done' => FALSE,
                'msg' => 'هیچ کتابی جهت خرید وجود ندارد'
            ];

        $factor = array(
            'user_id' => $user_id,
            'discount' => $discountfee ? $discountfee : $discount,
            'status' => NULL,
            'discount_id' => $discount_id,
            'owner' => $owner,
            'section' => 'book',
            'cdate' => time()
        );

        $this->db->insert('factors', $factor);

        $factor_id = $this->db->insert_id();
        $added = 0;
        foreach ($books as $book) {
            if ($this->isBought($user_id, $book->id))
                continue;

            $c_price += (int)$book->price;
            $bookprice = (int)$book->price;
            $this->db->insert('factor_detail', array(
                'book_id' => $book->id,
                'price' => $bookprice,
                'discount' => ($bookprice) - ($bookprice * ($discount / 100)),
                'factor_id' => $factor_id
            ));
            $this->db->insert('user_books', array(
                'book_id' => $book->id,
                'user_id' => $user_id,
                'factor_id' => $factor_id
            ));
            $added++;
        }

        if ($added == 0) {
            $this->db->where('id', $factor_id)->delete('factors');
            return [
                'done' => FALSE,
                'msg' => 'هیچ کتابی جهت خرید وجود ندارد'
            ];
        }

        $price = $c_price - $c_price * ($discount / 100);
        if ($discountfee)
            $price = $c_price - $discountfee;
        if ($price < 0)
            $price = 0;

        $this->db->where('id', $factor_id);
        $this->db->set('cprice', $c_price);
        $this->db->set('price', $price);
        $this->db->update('factors');

        $factor = $this->db->where('id', $factor_id)->get('factors', 1)->row();
        if($factor) {
            $factor->section = 'book';
        }

        return array('done' => TRUE, 'msg' => 'ok', 'factor' => $factor);
    }

    public function getFactor($factor_id)
    {
        $factor = $this->db->where('id', $factor_id)->get('factors')->row();
        return $factor;
    }

    public function setFactorPaid($factor_id, $ref_id = NULL)
    {
        return $this->updatetFactor($factor_id, [
            'state' => 'پرداخت موفق',
            'status' => 0,
            'ref_id' => $ref_id,
            'pdate' => time()
        ]);
    }

    public function updatetFactor($factor_id, $data)
    {
        return $this->db->where('id', $factor_id)->update('factors', $data);
    }

    public function getBookPrice($ids)
    {
        if (!is_array($ids))
            $ids = array((int)$ids);

        $this->db->select('SUM(m.meta_value) AS price');

        $this->db->where_in("b.id", $ids);
        $this->db->where("b.type", "book");
        $this->db->where("b.published", 1);

        $this->db->join("ci_post_meta m", "(m.post_id=b.id AND m.meta_key='price')", "inner", FALSE);

        $price =  (int)$this->db->get('posts b')->row()->price;
        return $price;
    }

    public function Pre($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();
    }

    public function getCategoryPrice($id, $user_id = NULL, $discount_id = NULL)
    {
        $discount = 0;
        $discountfee = 0;
        $category = $this->db->where('id', $id)->get('category', 1)->row();

        if ($category->parent != 0) {
            $discount = (int)$category->description;
        }
        if ($discount_id) {
            $finaldiscounts = $this->db->select('percent,fee')->where('id', (int)$discount_id)->get('discounts', 1)->row();
            $discount = $finaldiscounts->percent;
            $discountfee = $finaldiscounts->fee;
        }

        if ($discount > 100) $discount = 100;
        if ($discount < 0) $discount = 0;

        $this->db->select('SUM(m.meta_value) AS price');

        if ($category->parent == 0) {
            $this->db->where("b.category IN (SELECT `id` FROM `ci_category` WHERE `parent`={$id} AND `type`='book')");
        } else {
            $this->db->where("b.category", $id);
        }

        $this->db->where("b.type", "book");
        $this->db->where("b.published", 1);

        $this->db->join("ci_post_meta m", "(m.post_id=b.id AND m.meta_key='price')", "inner", FALSE);

        $price = (int)$this->db->get('posts b')->row()->price;

        $final_price = $price - $price * ($discount / 100);
        if ($discountfee) {
            $final_price = $price - $discountfee;
        }
        if ($discountfee > $price) {
            $discountfee = $price;
        }
        if ($final_price < 0) {
            $final_price = 0;
        }
        $result = [
            'price' => $price,
            'discount' => $discountfee ? $discountfee : $discount,
            'final_price' => $final_price
        ];

        //$user_id = 10;

        if ($user_id) {
            $myPrice = 0;
            if ($category->parent == 0) {
                $this->db->select("id");
                $this->db->where('parent', $id);
                $this->db->where('type', 'book');
                $category = $this->db->get('category')->result();
                foreach ($category as $key=>$value){
                    $category[$key] = $value->id;
                }
                if(!count($category)) {
                    $category[] = -1000;
                }
                $category = implode(",",$category);

                $this->db->select("id,price");
                $this->db->where("category IN($category)");
                $this->db->where('type', 'book');
                $this->db->where('published', 1);
                $books = $this->db->get('posts')->result();
            } else {
                $this->db->select("id,price");
                $this->db->where("category IN($id)");
                $this->db->where('type', 'book');
                $this->db->where('published', 1);
                $books = $this->db->get('posts')->result();
            }

            foreach ($books as $book) {
                if ($this->isBought($user_id, $book->id))
                    continue;
                if ($discountfee)
                    $myPrice += (int)$book->price - $discountfee;
                elseif ($discount)
                    $myPrice += (int)$book->price - (int)$book->price * ($discount / 100);
                else
                    $myPrice += (int)$book->price;
            }
            if ($myPrice < 0) {
                $myPrice = 0;
            }
            $result['my_price'] = $myPrice;
        }
        return $result;
    }

    public function getUserBooks($user_id, $where = NULL, $limit = 0, $limitstart = 0)
    {
        $user_id = (int)$user_id;

        $this->db->select('ub.book_id,ub.need_update');
        $this->db->join('ci_factors f', '(ub.factor_id=f.id AND f.status=0)', 'right', FALSE);
        $this->db->where('ub.user_id', $user_id);
        $result = $this->db->get('user_books ub')->result();
        $count = count($result);
        if ($limit || $limitstart) {
            $this->db->select('ub.book_id,ub.need_update');
            $this->db->join('ci_factors f', '(ub.factor_id=f.id AND f.status=0)', 'right', FALSE);
            $this->db->where('ub.user_id', $user_id);
            $this->db->limit($limit, $limitstart);
            $result = $this->db->get('user_books ub')->result();
        }

        if (empty($result)) return [];

        $ids = [];
        $need_update = [];
        foreach ($result as $row) {
            $ids[] = $row->book_id;
            $need_update[$row->book_id] = $row->need_update;
        }

        $books = $this->post->getPosts([
            'user_id'  => $user_id,
            'type' => 'book',
            'where' => 'p.id in (' . implode(',', $ids) . ')',
        ]);
        foreach ($books as $k => $book) {
            $books[$k]->need_update = $need_update[$book->id];
        }
        if ($limit || $limitstart) {
            return array($count, $books);
        }
        return $books;
    }


    public function getUserNotes($user_id)
    {
        $user_id = (int)$user_id;

        $this->db->select("n.id        as not_id");
        $this->db->select("n.part_id   as text_id");
        $this->db->select("n.text      as not_text");
        $this->db->select("n.user_text as not_text_user");
        $this->db->select("n.start     as notstart");
        $this->db->select("n.title");
        $this->db->select("n.end");
        $this->db->select("n.sharh");

        $this->db->select("(SELECT book_id FROM ci_book_meta WHERE id=n.part_id) AS bookid");

        $this->db->where('n.user_id', $user_id);

        return $this->db->get('notes n')->result();
    }

    public function getUserHighlights($user_id)
    {
        $user_id = (int)$user_id;

        $this->db->select("h.id      as highlight_id");
        $this->db->select("h.part_id as text_id");
        $this->db->select("h.title   as highlight_title");
        $this->db->select("h.text    as highlight_text");
        $this->db->select("h.description   as highlight_description");
        $this->db->select("h.color   as highlight_color");
        $this->db->select("h.start   as highlight_start");
        $this->db->select("h.end     as highlight_end");
        $this->db->select("h.sharh");


        $this->db->select("(SELECT book_id FROM ci_book_meta WHERE id=h.part_id) AS bookid");

        $this->db->where('h.user_id', $user_id);
        $result = $this->db->get('highlights h')->result();
        foreach ($result as $k => $v) {
            $tags = $this->getHighlightTag($v->highlight_id);
            $result[$k]->tag = $tags;
        }
        return $result;
    }

    public function getHighlightTag($highlight_id)
    {
        $this->db->select("h.id      as hightag_id");
        $this->db->select("h.title   as hightag_title");
        $this->db->select("h.public");
        $this->db->where('h.hid', $highlight_id);

        return $this->db->get('hightag h')->result();
    }

    public function getUserFavSounds($user_id)
    {
        $user_id = (int)$user_id;

        $this->db->select("s.id");
        $this->db->select("s.part_id AS text_id");
        $this->db->select("p.book_id AS bookid");
        $this->db->select("p.sound");

        $this->db->join('ci_book_meta p', 'p.id=s.part_id', 'left', FALSE);

        $this->db->where('s.user_id', $user_id);

        return $this->db->get('fav_sounds s')->result();
    }

    public function getUserFavImages($user_id)
    {
        $user_id = (int)$user_id;

        $this->db->select("s.id");
        $this->db->select("s.part_id AS text_id");
        $this->db->select("p.book_id AS bookid");
        $this->db->select("p.image");

        $this->db->join('ci_book_meta p', 'p.id=s.part_id', 'left', FALSE);

        $this->db->where('s.user_id', $user_id);

        return $this->db->get('fav_images s')->result();
    }


    public function addBookPart($bookId, $data)
    {
        $partData = array(
            'book_id' => (int)$bookId,
            'order' => (int)$data['order'],
            'text' => $data['text'],
            'description' => trim($data['description']) == '' ? NULL : $data['description'],
            'index' => (int)$data['index'] == 0 ? NULL : (int)$data['index'],
            'sound' => trim($data['file']) == '' ? NULL : $data['file'],
            'video' => trim($data['video']) == '' ? NULL : $data['video'],
            'image' => trim($data['image']) == '' ? NULL : $data['image'],
        );

        if (isset($data['id']) && (int)$data['id']) {
            $id = (int)$bookId;
            unset($partData['book_id']);
            $this->db->where('id', (int)$data['id'])->update('book_meta', $partData);
        } else {
            $this->db->insert('book_meta', $partData);
            $id = $this->db->insert_id();
        }
        $count = $this->db->where('book_id', $id)->count_all_results('book_meta');
        $Xdata = array("part_count" => $count);
        $this->db->where('id', $id)->update('posts', $Xdata);

        $count = $this->db->where('book_id', $id)->where('description IS NOT NULL')->count_all_results('book_meta');
        $count = $count ? 1 : 0;
        $Xdata = array("has_description" => $count);
        $this->db->where('id', $id)->update('posts', $Xdata);

        $count = $partData['sound'] ? 1 : 0;
        $Xdata = array("has_sound" => $count);
        $this->db->where('id', $id)->update('posts', $Xdata);

        $count = intval($partData['video']) > 0;
        $Xdata = array("has_video" => $count);
        $this->db->where('id', $id)->update('posts', $Xdata);

        $count = $partData['image'] ? 1 : 0;
        $Xdata = array("has_image" => $count);
        $this->db->where('id', $id)->update('posts', $Xdata);

        $O = $this->db->select('book_id,SUM(IF( `text` IS NULL ,0,LENGTH(`text`)))	+
			SUM(IF( `description` IS NULL ,0,LENGTH(`description`))) +	
			SUM(IF( `sound` IS NULL ,0,LENGTH(`sound`)))+
			SUM(IF( `video` IS NULL ,0,LENGTH(`video`))) +	
			SUM(IF( `image` IS NULL ,0,LENGTH(`image`))) AS C')->where('book_id', $id)->get('book_meta')->row();

        $count = $O->C;
        $Xdata = array("size" => $count);
        $this->db->where('id', $id)->update('posts', $Xdata);

        $O = $this->db->select('COUNT(id) C')->where('book_id', $id)->get('user_books')->row();
        $count = $O->C;
        $Xdata = array("has_download" => $count);
        $this->db->where('id', $id)->update('posts', $Xdata);

        return TRUE;
    }

    public function getChildren($id, &$items, &$ids)
    {
        $children = $this->db->where('parent', $id)->order_by('position', 'asc')->get('group')->result();
        foreach ($children as $item) {
            $items[$item->id] = $item;
            $ids[$item->id] = $item->id;
            $this->getChildren($item->id, $items, $ids);
        }
    }

    public function getBookIndexesById($id)
    {
        $id = (int)$id;

        $result = [];

        $this->db->select('p.id, p.index, g.parent');
        $this->db->join('ci_group g', "g.id=p.index", "left", FALSE);
        $index = $this->db->where('p.book_id', $id)->where('p.index IS NOT NULL')->get('book_meta p', 1)->row();

        if ($index && isset($index->parent)) {
            $this->db->select('g.name, g.id, p.id AS part_id');
            $this->db->join('ci_book_meta p', "(p.index=g.id AND p.book_id={$id})", 'left', FALSE);
            $this->db->where('g.parent', $index->parent);
            $this->db->order_by('g.position', 'asc');
            $result = $this->db->get('group g')->result();
        }
        if (1) {
            $items = array();
            $ids = array(0);
            $this->getChildren($index->parent, $items, $ids);

            $this->db->select('p.id,p.index');
            $this->db->where('p.index IN(' . implode(",", $ids) . ')');
            $resultx = $this->db->get('ci_book_meta p')->result();
            $export = array();
            foreach ($items as $k => $v) {
                $export[$k] = new stdClass;
                $export[$k]->id = $v->id;
                $export[$k]->part_id = 0;
                $export[$k]->name = $v->name;
            }
            foreach ($resultx as $k => $v) {
                $export[$v->index]->part_id = $v->id;
            }
            $result = array_values($export);
        }

        return $result;
    }

    public function getBookPartsById($id)
    {
        $id = (int)$id;

        $this->db->select("id, order, text, description, index,image,video,sound");
        $this->db->select("IF(sound IS NOT NULL , 'true', 'false') as has_sound");
        $this->db->select("IF(video IS NOT NULL , 'true', 'false') as has_video");
        $this->db->select("IF(image IS NOT NULL , 'true', 'false') as has_image");
        $this->db->select("IF(description IS NOT NULL , 'true', 'false') as has_description");
        $this->db->where('book_id', $id);
        $this->db->order_by('order', 'asc');
        return $this->db->get('book_meta')->result();
    }

    //=== tests ===//
    public function addBookTest($bookId, $data)
    {
        $testData = array(
            'book_id' => (int)$bookId,
            'term' => (int)@$data['term'],//Alireza Balvardi
            'page' => (int)@$data['page'],//Alireza Balvardi
            'category' => $data['category'],
            'question' => $data['question'],
            'testnumber' => $data['testnumber'],
            'true_answer' => (int)$data['answer'],
            'answer_1' => $data['answer_1'],
            'answer_2' => $data['answer_2'],
            'answer_3' => $data['answer_3'],
            'answer_4' => $data['answer_4'],
        );

        if (isset($data['id']) && (int)$data['id']) {
            unset($testData['book_id']);
            $this->db->where('id', (int)$data['id'])->update('tests', $testData);
        } else {
            $this->db->insert('tests', $testData);
        }
    }

    public function addBookTashrihi($bookId, $data)
    {
        $tashrihiData = array(
            'book_id' => (int)$bookId,
            'term' => (int)@$data['term'],//Alireza Balvardi
            'page' => (int)@$data['page'],//Alireza Balvardi
            'barom' => (float)@$data['barom'],//Alireza Balvardi
            'category' => $data['category'],
            'testnumber' => $data['testnumber'],
            'question' => $data['question'],
            'answer' => $data['answer'],
        );

        if (isset($data['id']) && (int)$data['id']) {
            unset($tashrihiData['book_id']);
            $this->db->where('id', (int)$data['id'])->update('tashrihi', $tashrihiData);
        } else {
            $this->db->insert('tashrihi', $tashrihiData);
        }
    }

//Alireza Balvardi
    public function getBookTests($id)
    {
        $this->db->select('id,category,question,true_answer,answer_1,answer_2,answer_3,answer_4,term');
        return $this->db->where('book_id', (int)$id)->order_by('category', 'asc')->order_by('id', 'asc')->get('tests')->result();
    }

    //===== discounts =====/
    public function setDiscountUsed($discount_id, $factor_id)
    {
        //Alireza Balvardi
        $discount_id = (int)$discount_id;
        $factor_id = (int)$factor_id;

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

    public function checkDiscountCode($code = NULL, $category_id = NULL, $user_id = NULL, $bookid = NULL)
    {
        $category_id = (int)$category_id;
        $user_id = (int)$user_id;
        $discount_id = NULL;
        $banTime = 48 * 3600;
        $failCount = 3;

        $ban = $this->db->where([
            'user_id' => $user_id,
            'event' => 'discount_ban',
            'datestr >' => time() - $banTime,
        ])->get('logs', 1)->row();

        if (!empty($ban)) {
            $remTime = $banTime / 3600 - floor((time() - $ban->datestr) / 3600);
            return "شما تا {$remTime} ساعت دیگر نمی توانید از این بخش استفاده کنید";
        }

        $isFirstLevel = in_array($category_id, array(-1, -2)) ? $category_id : $this->db->where('id', $category_id)->count_all_results('category');

        if (!$isFirstLevel)
            return "شماره سطح صحیح نیست";
        /*
                if($code == '')
                    return "کد تخفیف را ارسال کنید";
        */
        if ($category_id)
            $this->db->where('category_id IN(0,' . $category_id . ')');
        if ($bookid && $category_id == -1)
            $this->db->where('bookid', $bookid);
        $discount = $this->db->where('code', $code)->where("(expdate > UNIX_TIMESTAMP() OR ISNULL (expdate))")->get('discounts')->row();//Alireza Balvardi

        if ($code && !isset($discount->id)) {
            return "کد تخفیف وارد شده معتبر نیست";
        }
        if ($code && $discount->used == $discount->maxallow) {
            return "سقف استفاده از کد تخفیف وارد شده تکمیل شده است";
        }

        /*
        if(!isset($discount->id) OR $discount->used == 1)
        {
            $this->db->insert('logs',[
                'user_id' => $user_id,
                'event'   => 'discount_fail',
                'datestr' => time(),
            ]);

            $fails = $this->db->where([
                'user_id'   => $user_id,
                'event'     => 'discount_fail',
                'datestr >' => time() - $banTime,
            ])->count_all_results('logs');

            if($fails >= $failCount)
                $this->db->insert('logs',[
                    'user_id' => $user_id,
                    'event'   => 'discount_ban',
                    'datestr' => time(),
                ]);

            return "کد تخفیف وارد شده معتبر نیست";
        }
        */

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

        if ($code)
            $discount_id = $discount->id;

        return $discount_id;
    }


}//=== end media model
?>