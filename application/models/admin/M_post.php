<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_post extends CI_Model
{

    public $setting = NULL;
    public $data = NULL;
    public $category_list = NULL;
    public $categories = [];

    function __construct()
    {
        parent::__construct();
        $this->setting = $this->settings->data;
    }


    public function publish($id)
    {
        $post_data = array('published' => 1, 'draft' => NULL);
        if ($this->db->where('id', $id)->update('posts', $post_data))
            return TRUE;
        return FALSE;
    }

    public function toDruft($id)
    {
        $post_data = array('published' => 0, 'draft' => $id);
        if ($this->db->where('id', $id)->update('posts', $post_data))
            return TRUE;
        return FALSE;
    }

    public function test($id)//Alireza Balvardi
    {
        $post_data = array('published' => 2, 'draft' => NULL);
        if ($this->db->where('id', $id)->update('posts', $post_data))
            return TRUE;
        return FALSE;
    }

    public function toTrashs($id)
    {
        $post_data = array('published' => 0, 'draft' => NULL);
        if ($this->db->where('id', $id)->update('posts', $post_data))
            return TRUE;
        return FALSE;
    }

    public function delete($id)
    {

        $this->db->where('book_id', $id)->delete('book_meta`');
        if ($this->db->where('id', $id)->delete('posts')) {
            return TRUE;
        }
        return FALSE;
    }

    public function addEmpty($type = "post")
{
    // گرفتن آخرین id
    $lastRow = $this->db->select_max('id')->get('posts')->row_array();
    $lastId = isset($lastRow['id']) ? (int)$lastRow['id'] : 0;
    $newId = $lastId + 1;

    $data = array(
        'id' => $newId,
        'type' => $type,
        'author' => $this->user->user_id,
        'draft' => $newId,
        'date_modified' => date("Y-m-d H:i:s"),
        'date' => date("Y-m-d H:i:s")
    );

    $this->db->insert('posts', $data);

    return $newId;
}


    public function deleteEmpty()
    {
        $this->db
            ->where('title', NULL)
            ->where('content', NULL)
            ->where('excerpt', NULL)
            ->where('category', NULL)
            ->where('tags', NULL)
            ->where('thumb', NULL)
            ->where('published', 0)
            ->where('date_modified <=', date('Y-m-d H:i:s', strtotime('-1 minutes')))
            ->delete('posts');
    }

    public function savePost($post_id = NULL, $post_data = NULL, $post_meta = NULL, $action = "publish", $post_nashr = NULL)
    {
        if ($post_id && $post_data) {
            $draft_exists = $this->db->select('id')->where('draft', $post_id)->get('posts', 1)->row();

            if ($action == "draft") {
                $post_data['draft'] = $post_id;
                $post_data['published'] = 0;

                if (!empty($draft_exists)) {
                    $post_id = $draft_exists->id;
                } else {
                    $post_id = $this->addEmpty($post_data['type']);
                }
            } elseif ($action == "publish") {
                $post_data['published'] = 1;

                $this->db->where('draft', $post_id)
                    ->where('published', 0)
                    ->where('id !=', $post_id)
                    ->delete('posts');
                $post_data['draft'] = NULL;
            } elseif ($action == "test")//Alireza Balvardi
            {
                $post_data['published'] = 2;

                $this->db->where('draft', $post_id)
                    ->where('published', 0)
                    ->where('id !=', $post_id)
                    ->delete('posts');
                $post_data['draft'] = NULL;
            }

            if (isset($post_data['content'])) {
                $string = stripHTMLtags($post_data['content']);
                $post_data['content_string'] = trim($string) != '' ? $string : NULL;
            }

            $this->db->where('id', $post_id)->update('posts', $post_data);

            if ($post_meta && is_array($post_meta)) {
                foreach ($post_meta as $meta_key => $meta_value) {
                    if ($action == "publish" && !empty($draft_exists)) {
                        $this->deletePostMeta($draft_exists->id, $meta_key);
                    }
                    $this->updatePostMeta($post_id, $meta_key, $meta_value);
                }
            }
            if ($post_nashr && is_array($post_nashr)) {
                foreach ($post_nashr as $nashr_key => $nashr_value) {
                    if ($action == "publish" && !empty($draft_exists)) {
                        $this->deletePostNashr($draft_exists->id, $nashr_key);
                    }
                    $this->updatePostNashr($post_id, $nashr_key, $nashr_value);
                }
            }

            $count = (int)$this->db->where('book_id', $post_id)->count_all_results('book_meta');
            $Xdata = array("part_count" => $count);
            $this->db->where('id', $post_id)->update('posts', $Xdata);

            $count = (int)$this->db->where('book_id', $post_id)->where('description IS NOT NULL')->count_all_results('book_meta');
            $Xdata = array("has_description" => $count);
            $this->db->where('id', $post_id)->update('posts', $Xdata);

            $count = (int)$this->db->where('book_id', $post_id)->where('sound IS NOT NULL')->count_all_results('book_meta');
            $Xdata = array("has_sound" => $count);
            $this->db->where('id', $post_id)->update('posts', $Xdata);

            $count = (int)$this->db->where('book_id', $post_id)->where('video IS NOT NULL')->count_all_results('book_meta');
            $Xdata = array("has_video" => $count > 0);
            $this->db->where('id', $post_id)->update('posts', $Xdata);

            $count = (int)$this->db->where('book_id', $post_id)->where('image IS NOT NULL')->count_all_results('book_meta');
            $Xdata = array("has_image" => $count);
            $this->db->where('id', $post_id)->update('posts', $Xdata);

            $O = $this->db->select('COUNT(id) C')->where('book_id', $post_id)->get('user_books')->row();
            $count = $O->C;
            $Xdata = array("has_download" => $count);
            $this->db->where('id', $post_id)->update('posts', $Xdata);

            $O = $this->db->select('book_id,SUM(IF( `text` IS NULL ,0,LENGTH(`text`)))	+
				SUM(IF( `description` IS NULL ,0,LENGTH(`description`))) +	
				SUM(IF( `sound` IS NULL ,0,LENGTH(`sound`)))+
				SUM(IF( `video` IS NULL ,0,LENGTH(`video`))) +	
				SUM(IF( `image` IS NULL ,0,LENGTH(`image`))) AS C')->where('book_id', $post_id)->get('book_meta')->row();

            $count = $O->C;
            $Xdata = array("size" => $count);
            $this->db->where('id', $post_id)->update('posts', $Xdata);
        }

    }

    public function getThumbArray($thumb)
    {
        $data = array(
            150 => thumb($thumb, '150'),
            300 => thumb($thumb, '300'),
            600 => thumb($thumb, '600'),
            'b' => $thumb
        );
        return $data;
    }

    public function postThumb($post_id = NULL, $op = "base", $field = "thumb")
    {
        if ($post_id) {
            if ($op == "base") {
                $thumb = $this->db->select($field)->where('id', $post_id)->get('posts')->row();
                if (isset($thumb->$field) && file_exists($thumb->$field)) {
                    $thumb = $thumb->$field;
                } else {
                    if (isset($this->setting["default_post_$field"]) && file_exists($this->setting["default_post_$field"])) {
                        $thumb = $this->setting["default_post_$field"];
                    } else {
                        $thumb = "style/images/default/post-$field.png";
                    }
                }
                return $this->getThumbArray($thumb);
            } elseif ($op == "all") {
                $data = array('base' => $this->postThumb($post_id, "base", $field));
                $thumbs = $this->getPostMeta($post_id, "post_$field");
                if ($thumbs) {
                    foreach ($thumbs as $k => $val) {
                        $thumb = $val->meta_value;

                        if ($this->tools->isJson($thumb)) {
                            $decode = $this->tools->jsonDecode($thumb);
                            foreach ($decode as $decodeK => $decodeV) {
                                $data['other'][] = $this->getThumbArray($decodeV);
                            }
                        } else
                            $data['other'][] = $this->getThumbArray($thumb);
                    }
                }
                return $data;
            }
        }
        return NULL;
    }

    /************************ POSTS HELPER ******************************/

    public function thumb($thumb = NULL, $size = 600)
    {
        if (trim($thumb) == '')
            $thumb = $this->setting['default_post_thumb'];


        if ($size != 'lg')
            $thumb = thumb($thumb, $size);

        if (!file_exists($thumb)) {
            $thumb = $this->setting['default_post_thumb'];
            if ($size != 'lg')
                $thumb = thumb($thumb, $size);
        }
        log_message('error', 'thumb:' . $thumb);
        return $thumb;

    }

    public function link($post = NULL, $id = NULL)
    {
        if ($id) {
            $post = $this->db->select('id,title')->where('id', $id)->get('posts')->row();
        }

        if ($post) {
            return site_url($post->id) . "/" . STU($post->title);
        }
        return NULL;
    }

    public function excerpt($post = NULL, $id = NULL)
    {
        if ($id) {
            $post = $this->db->select('excerpt,content')->where('id', $id)->get('posts')->row();
        }

        if ($post) {
            if (trim($post->excerpt) !== "") {
                return $post->excerpt;
            } else {
                $split = '<div class="AddMore" contenteditable="false" title="ادامه مطالب">ادامه مطالب</div>';
                $content = $post->content;
                $content = preg_replace("/<img[^>]+\>/i", " ", $content);
                $content = preg_replace("/<\/img[^>]+\>/i", " ", $content);

                $contentArray = explode($split, $content);
                if (count($contentArray) > 1) {
                    $content = $contentArray[0];
                } elseif (mb_strlen($content) > 2000) {
                    $content = mb_substr($content, 0, 2000) . " ...";
                }
                $content = closeTags($content);

                return $content;
            }
        }
        return NULL;
    }

    public function content($post = NULL, $id = NULL)
    {
        if ($id) {
            $post = $this->db->select('content')->where('id', $id)->get('posts')->row();
        }
        if ($post) {
            $content = $post->content;
            $split = '<div class="AddMore" contenteditable="false" title="ادامه مطالب">ادامه مطالب</div>';
            $content = str_replace($split, " ", $content);
            return $content;
        }
        return NULL;
    }

    public function LoadDataTableSelect($datatype, $field1 = "id", $field2 = "title")
    {
        
        if (in_array($datatype, ['publisher', 'writer', 'translator'])) {
            $O = $this->db->select("$field1, $field2")
                ->get($datatype)
                ->result();
            $data = array();
            foreach ($O as $k => $v) {
                $data[$v->{$field1}] = $v->{$field2};
            }
            return $data;
        }

       
        $suppliertype = $this->db->where("t.datatype = '$datatype'")->get('suppliertype t')->result();
        $data = array(0);
        foreach ($suppliertype as $k => $v) {
            $data[] = $v->id;
        }

        $O = $this->db->select("s.$field1,s.$field2")
            ->where("r.type_id IN(" . implode(',', $data) . ")")
            ->join('ci_supplierrules r', 's.id=r.sup_id', 'inner', FALSE)
            ->get('supplier s')->result();
        $data = array();
        foreach ($O as $k => $v) {
            $data[$v->{$field1}] = $v->{$field2};
        }
        return $data;
    }//Alireza Balvardi

    /**************************** POST NASHR ********************************/
    public function addPostNashr($post_id = NULL, $nashr_key = NULL, $nashr_value = NULL)
    {
        if ($post_id && $nashr_key !== NULL && $nashr_value != NULL) {
            if (is_array($nashr_value)) $nashr_value = json_encode($nashr_value);

            $data = array('post_id' => $post_id, 'nashr_key' => $nashr_key, 'nashr_value' => $nashr_value);

            if ($this->db->insert('post_nashr', $data))
                return TRUE;
        }
        return FALSE;
    }

    public function updatePostNashr($post_id = NULL, $nashr_key = NULL, $nashr_value = NULL)
    {
        if ($post_id && $nashr_key !== NULL) {
            $exists = $this->db->where('post_id', $post_id)->where('nashr_key', $nashr_key)->count_all_results('post_nashr');

            if ($exists) {
                if (is_array($nashr_value))
                    $nashr_value = json_encode($nashr_value);

                $data = array('nashr_value' => $nashr_value);

                $this->db->where('post_id', $post_id)->where('nashr_key', $nashr_key);
                if ($this->db->update('post_nashr', $data))
                    return TRUE;
            } else {
                return $this->addPostNashr($post_id, $nashr_key, $nashr_value);
            }
        }
        return FALSE;
    }

    public function getPostNashr($post_id = NULL, $nashr_key = NULL)
    {
        if ($post_id != NULL && $nashr_key != NULL) {
            /*return $this->db->select()
                        ->where('post_id',$post_id)
                        ->where('nashr_key',$nashr_key)
                        ->get('post_nashr')->result();*/
            $nashr = $this->db->select('nashr_value')
                ->where('post_id', $post_id)
                ->where('nashr_key', $nashr_key)
                ->get('post_nashr')->result();
            return $nashr;

        } elseif ($post_id != NULL) {
            return $this->db->where('post_id', $post_id)->get('post_nashr')->result();
        }
        return NULL;
    }

    public function deletePostNashr($post_id = NULL, $nashr_key = NULL)
    {

        if ($post_id != NULL && $nashr_key != NULL) {
            return $this->db
                ->where('post_id', $post_id)
                ->where('nashr_key', $nashr_key)
                ->delete('post_nashr');
        } elseif ($post_id != NULL) {
            return $this->db->where('post_id', $post_id)->delete('post_nashr');
        }
        return NULL;
    }

    /**************************** POST META ********************************/
    public function addPostMeta($post_id = NULL, $meta_key = NULL, $meta_value = NULL)
    {
        if ($post_id && $meta_key !== NULL && $meta_value != NULL) {
            if (is_array($meta_value)) $meta_value = json_encode($meta_value);

            $data = array('post_id' => $post_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value);

            if ($this->db->insert('post_meta', $data))
                return TRUE;
        }
        return FALSE;
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

    public function updatePostMeta($post_id = NULL, $meta_key = NULL, $meta_value = NULL)
    {
        if ($post_id && $meta_key !== NULL) {
            $exists = $this->db->where('post_id', $post_id)->where('meta_key', $meta_key)->count_all_results('post_meta');

            if ($exists) {
                if (is_array($meta_value))
                    $meta_value = json_encode($meta_value);

                $data = array('meta_value' => $meta_value);

                $this->db->where('post_id', $post_id)->where('meta_key', $meta_key);
                if ($this->db->update('post_meta', $data)) {
                    return TRUE;
                }
            } else {
                return $this->addPostMeta($post_id, $meta_key, $meta_value);
            }
        }
        return FALSE;
    }

    public function getPostMeta($post_id = NULL, $meta_key = NULL)
    {
        if ($post_id != NULL && $meta_key != NULL) {
            /*return $this->db->select()
                        ->where('post_id',$post_id)
                        ->where('meta_key',$meta_key)
                        ->get('post_meta')->result();*/
            $meta = $this->db->select('meta_value')
                ->where('post_id', $post_id)
                ->where('meta_key', $meta_key)
                ->get('post_meta')->result();
            return $meta;

        } elseif ($post_id != NULL) {
            return $this->db->where('post_id', $post_id)->get('post_meta')->result();
        }
        return NULL;
    }

    public function deletePostMeta($post_id = NULL, $meta_key = NULL)
    {

        if ($post_id != NULL && $meta_key != NULL) {
            return $this->db
                ->where('post_id', $post_id)
                ->where('meta_key', $meta_key)
                ->delete('post_meta');
        } elseif ($post_id != NULL) {
            return $this->db->where('post_id', $post_id)->delete('post_meta');
        }
        return NULL;
    }

    public function commentsCount($id)
    {
        $where = array('table' => 'posts', 'row_id' => $id, 'submitted' => 1);
        return $this->db->where($where)->count_all_results('comments');

    }

    //=================== category list =======================/

    public function getCategoryArray($parent = 0, $post_type = 'book')
    {
        $this->db->select('c.id,c.parent,c.name,c.position,c.pic,c.icon');

        if ($post_type = 'book')
            $this->db->select('c.description as discount');
        else
            $this->db->select('c.description');

        $this->db->select('(SELECT COUNT(id) FROM ci_category ch WHERE ch.parent=c.id) AS children_length');
        $this->db->select("(SELECT COUNT(id) FROM ci_posts p WHERE p.published=1 AND p.category = c.id) as post_count");
        $this->db->where('c.type', $post_type);
        $this->db->where('c.parent', (int)$parent);
        $this->db->order_by('position', 'asc');

        $categories = $this->db->get('category c')->result();

        /*$query =
            "SELECT `c`.*, 
            (SELECT COUNT(id) FROM `ci_category` `ch` WHERE `ch`.`parent`=`c`.`id`) AS children_length, 
            (
                SELECT COUNT(id) FROM ci_posts WHERE published=1 AND (
                    category LIKE CONCAT('' , c.id , ',%')   OR
                    category LIKE CONCAT('%,' , c.id , ',%') OR 
                    category LIKE CONCAT('%,' , c.id , '')   OR 
                    category = c.id
                )
            ) as post_count
            FROM `ci_category` `c`
            WHERE `c`.`type` = '{$post_type}' AND `c`.`parent` = {$parent}
            ORDER BY `c`.`position` ASC";
        $categories = $this->db->query($query)->result();*/

        if (!empty($categories))
            foreach ($categories as $k => $category) {
                $pic = $category->pic == '' ? $this->setting['default_category_pic'] : $category->pic;
                $categories[$k]->pic = base_url() . $pic;

                if ($category->children_length > 0)
                    $categories[$k]->children = $this->getCategoryArray($category->id, $post_type);
            }
        return $categories;
    }
    
    public function getCategoryArrayWithLimit($parent = 0, $post_type = 'book', $subcategories_limit = 1)
    {
        $this->db->select('c.id,c.parent,c.name,c.position,c.pic,c.icon');

        if ($post_type == 'book') {
            $this->db->select('c.description as discount');
        } else {
            $this->db->select('c.description');
        }
    
        $this->db->select('(SELECT COUNT(id) FROM ci_category ch WHERE ch.parent=c.id) AS children_length');
        $this->db->select("(SELECT COUNT(id) FROM ci_posts p WHERE p.published=1 AND p.category = c.id) as post_count");
        $this->db->where('c.type', $post_type);
        $this->db->where('c.parent', (int)$parent);
        $this->db->order_by('position', 'asc');
    
        $categories = $this->db->get('category c')->result();
    
        if (!empty($categories)) {
            foreach ($categories as $k => $category) {
                $pic = $category->pic == '' ? $this->setting['default_category_pic'] : $category->pic;
                $categories[$k]->pic = base_url() . $pic;
    
                if ($category->children_length > 0) {
                    $this->db->select('id, parent, name, position, pic, icon, description');
                    $this->db->where('type', $post_type);
                    $this->db->where('parent', (int)$category->id);
                    $this->db->order_by('position', 'asc');
    
                    if ($subcategories_limit !== null && is_int($subcategories_limit)) {
                        $this->db->limit($subcategories_limit);
                    }
    
                    $subcategories = $this->db->get('category')->result();
    
                    foreach ($subcategories as $sub_key => $subcategory) {
                        $subcategories[$sub_key]->pic = $subcategory->pic == '' ? $this->setting['default_category_pic'] : base_url() . $subcategory->pic;
    
                        if ($subcategory->children_length > 0) {
                            $subcategories[$sub_key]->children = $this->getCategoryArrayWithLimit($subcategory->id, $post_type, $subcategories_limit);
                        }
                    }
    
                    $categories[$k]->children = $subcategories;
                }
            }
        }
        
        return $categories;
    }


    public function setCategoryPostsCount($categories)
    {
        foreach ($categories as $k => $c) {
            $count = 0;
            if (isset($c->children_length) && $c->children_length > 0)
                $count = $this->_getCategoryPostsCount($c->children);

            $count += (int)$c->post_count;
            $categories[$k]->post_count = $count;
        }
        return $categories;
    }

    private function _getCategoryPostsCount($categories)
    {
        $count = 0;
        foreach ($categories as $k => $c) {
            if (isset($c->children_length) && $c->children_length > 0)
                $count = $this->_getCategoryPostsCount($c->children);
            //else
            $count += (int)$c->post_count;
        }
        return $count;
    }

    public function postCatLinks($category = NULL)
    {
        if ($category) {
            $cats = $this->db->select('id,name,icon')
                ->where_in('id', explode(',', $category))
                ->get('category', 5)
                ->result();

            if ($cats) {
                $result = "<ul class=\"inline-list\">";

                foreach ($cats as $key => $cat) {
                    if ($key > 4) continue;
                    $url = site_url('category') . "/$cat->id/$cat->name";
                    $result .= "<li><a href=\"$url\" title=\"" . html_escape($cat->name) . "\"><i class=\"fa fa-$cat->icon\"></i><span>$cat->name</span></a></li>";
                }

                $result .= "</ul>";
                return $result;
            }
        }
    }

    public function getCateoryList($post_type = "post", $parent = 0, $selectable = TRUE, $post_cat = NULL, $pic = FALSE, $sample = NULL, $start = "<ul>", $end = "</ul>")
    {
        $result = "";
        if ($this->category_list === NULL) {
            $this->category_list = $this->db->where('type', $post_type)->order_by('position', 'asc')->get('category')->result();
        }

        $category = $this->searchCateoryList($post_type, $parent);
        if (!empty($category)) {
            $result .= $start;
            foreach ($category as $cat) {
                $id = $cat['id'];
                $name = $cat['name'];
                $checked = "";

                if ($cat['pic'] != NULL && file_exists($cat['pic']))
                    $cpic = $cat['pic'];
                else
                    $cpic = $this->setting['default_category_pic'];

                $cpic150 = thumb($cpic, '150');
                $cpic300 = thumb($cpic, '300');
                $cpic600 = thumb($cpic, '600');

                if ($sample) {
                    $str_search = array(
                        '[ID]',
                        '[NAME]',
                        '[DES]',
                        '[PIC]',
                        '[PIC-150]',
                        '[PIC-300]',
                        '[PIC-600]',
                        '[PARENT]',
                        '[TYPE]',
                        '[POS]',
                        '[ICON]',
                        '[MEMBERSHIP1]',
                        '[DISCOUNTMEMBERSHIP1]',
                        '[MEMBERSHIP3]',
                        '[DISCOUNTMEMBERSHIP3]',
                        '[MEMBERSHIP6]',
                        '[DISCOUNTMEMBERSHIP6]',
                        '[MEMBERSHIP12]',
                        '[DISCOUNTMEMBERSHIP12]',
                    );

                    $str_replace = array(
                        $cat['id'],
                        $cat['name'],
                        $cat['des'],
                        $cpic,
                        $cpic150,
                        $cpic300,
                        $cpic600,
                        $parent,
                        $post_type,
                        $cat['pos'],
                        $cat['icon'],
                        $cat['membership1'],
                        $cat['discountmembership1'],
                        $cat['membership3'],
                        $cat['discountmembership3'],
                        $cat['membership6'],
                        $cat['discountmembership6'],
                        $cat['membership12'],
                        $cat['discountmembership12'],
                    );

                    $_sample = str_replace($str_search, $str_replace, $sample);

                    $_sample =
                        str_replace(
                            '[SUB-MENU]',
                            $this->getCateoryList($post_type, $cat['id'], 0, NULL, FALSE, $sample),
                            $_sample
                        );

                    $result .= $_sample;
                } else {
                    if ($post_cat) {
                        $post_cat_ar = explode(',', $post_cat);
                        if (is_array($post_cat_ar) && in_array($id, $post_cat_ar))
                            $checked = 'checked';
                    }
                    $result .= "<li item-id=$id parent=$parent name='$name'>";

                    $selectable && $result .= "<label>";
                    $selectable && $result .= "<input type=checkbox value=$id name=category[] $checked>";

                    if ($pic) {
                        $result .= "<span class=category-list-img >";
                        $result .= "<img src='$cpic150'>";
                        $result .= "</span>";
                    }

                    $result .= $name;

                    $selectable && $result .= "</label>";

                    $result .= $this->getCateoryList($post_type, $cat['id'], $selectable, $post_cat, $pic);

                    $result .= "</li>";
                }
            }
            $result .= $end;
            return $result;
        }
        return NULL;
    }

    //=================== category select menu =======================/
    public function getCategorySelectMenu($post_type = "post", $parent = 0)
    {
        $result = "";
        if ($this->category_list === NULL) {
            $this->category_list = $this->db->where('type', $post_type)->order_by('position', 'asc')->get('category')->result();
        }

        $category = $this->searchCateoryList($post_type, $parent);
        if (!empty($category)) {
            foreach ($category as $cat) {
                $id = $cat['id'];
                $name = $cat['name'];
                $pos = $cat['pos'];

                $result .= "<option item-id=$id parent=$parent name='$name' pos='$pos' value=$id >$name</option>";
                $result .= $this->getCategorySelectMenu($post_type, $id);
            }
            return $result;
        }
        return NULL;
    }

    public function searchCateoryList($post_type = "post", $parent = 0)
    {

        if ($this->category_list !== NULL) {
            $return = array();
            foreach ($this->category_list as $key => $cat) {
                if ($cat->type == $post_type && $cat->parent == $parent)
                    $return[] = array(
                        'id' => $cat->id,
                        'pos' => $cat->position,
                        'name' => $cat->name,
                        'pic' => $cat->pic,
                        'des' => $cat->description,
                        'icon' => $cat->icon,
                        'membership1' => $cat->membership1,
                        'discountmembership1' => $cat->discountmembership1,
                        'membership3' => $cat->membership3,
                        'discountmembership3' => $cat->discountmembership3,
                        'membership6' => $cat->membership6,
                        'discountmembership6' => $cat->discountmembership6,
                        'membership12' => $cat->membership12,
                        'discountmembership12' => $cat->discountmembership12
                    );
            }
            if (count($return) > 0) return $return;
        }

        return NULL;
    }

    public function addCategory($data = NULL)
    {
        if ($data) {
            unset($data['id']);// = $this->db->last_id('category');

            if (!isset($data['pic'])) $data['pic'] = NULL;

            if ($this->db->insert('category', $data)) {
                $insert_id = $this->db->insert_id();
                $return = $this->db->where('id', $insert_id)->get('category')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');

                return $return;
            }
        }
        return FALSE;
    }

    public function updateCategory($data = NULL)
    {
        if ($data && isset($data['id'])) {
            $id = $data['id'];
            if (!isset($data['pic'])) $data['pic'] = NULL;
            if ($this->db->where('id', $id)->update('category', $data)) {

                $return = $this->db->where('id', $id)->get('category')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');
                return $return;
            }
        }
        return FALSE;
    }

    public function deleteCategory($id = NULL)
    {
        if ($id) {
            $sub = $this->db->where('parent', $id)->get('category')->result();
            if ($sub) {
                foreach ($sub as $row)
                    $this->deleteCategory($row->id);
            }
            if ($this->db->where('id', $id)->delete('category'))
                return TRUE;
        }
        return FALSE;
    }

    public function get($type = 'post', $select = '*', $where = NULL, $order = NULL, $from = 0, $limit = 10)
    {
        $this->db->select($select);

        if ($type)
            $this->db->where('type', $type);

        $this->db->where('published', 1);

        if ($where)
            $this->db->where($where);

        if ($order)
            $this->db->order_by($order);

        $this->db->order_by('position', 'desc');
        $this->db->order_by('date_modified', 'desc');

        $this->db->limit($from, $limit);

        return $this->db->get('posts')->result();
    }

    public function getPostLink($id, $title = NULL)
    {

        if ($title == NULL)
            $title = $this->db->get_field('title', 'posts', $id);

        return site_url() . $id . "/" . STU($title);
    }

    public function LoadSubCategories($id)
    {
        $this->db->select("*");
        $this->db->where("parent", $id);
        $this->db->where("type", "book");
        $this->db->order_by("position");
        $O = $this->db->get("category")->result();
        $cats = [];
        foreach ($O as $k => $v) {
            $cats[$v->id] = $v->id;
            $subcats = $this->LoadSubCategories($v->id);
            if (count($subcats))
                $cats = array_merge($cats, $subcats);
        }
        return $cats;
    }

    public function ExtQuery($table, $select, $where, $group_by)
    {
        $this->load->database();
        $exdb = $this->db;
        if ($group_by) {
            $exdb->group_by($group_by);
        }
        $view_count = $exdb->select($select)->where($where)->get($table)->result();
        return $view_count;
    }

    public function getPosts0(array $options)
    {
        $output = (int)$this->input->post("output");
        $options = array_merge(array(
            'type' => 'post',
            'views' => TRUE,
            'comments' => TRUE,
            'likes' => TRUE,
            'rating' => TRUE
        ), $options);


        global $POST_TYPES;

        $post_type = @$POST_TYPES[$options['type']];
        $user_id = $options["user_id"];
        $view_count = [];
        $comment_count = [];
        $like_count = [];
        $like = [];
        $rate_count = [];
        $rating_sum = [];
        $rating = [];
        $my_rate = [];
        $meta_keys = [];
        $pages = [];
        $sample_questions = [];
        $attachments = [];

        if (!$output && $options['views'] === TRUE) {
            $where = ['table' => 'posts'];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_logs', 'row_id AS id,COUNT(row_id) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $view_count[$row->id] = $row->C;
            }
        }

        if (!$output && $options['comments'] === TRUE) {
            $where = ['table' => 'posts'];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_comments', 'row_id AS id,COUNT(row_id) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $comment_count[$row->id] = $row->C;
            }
        }

        if (!$output && $options['likes'] === TRUE) {
            $where = ['table' => 'posts'];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,COUNT(row_id) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $like_count[$row->id] = $row->C;
            }
        }

        if (!$output && $options['likes'] === TRUE) {
            $where = ['table' => 'posts', 'rating' => 0, 'user_id' => $user_id];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_rates', "row_id AS id,IF(COUNT(*) > 0, 'true', 'false') AS C", $where, 'row_id');
            foreach ($rows as $row) {
                $like[$row->id] = $row->C;
            }
        }

        if (!$output && $options['comments'] === TRUE) {
            $where = ['table' => 'posts', 'rating > ' => 0];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,COUNT(rating) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $rate_count[$row->id] = $row->C;
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,SUM(rating) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $rating_sum[$row->id] = $row->C;
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,ROUND((SUM(rating)/COUNT(rating)),1) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $rating[$row->id] = $row->C;
            }
            unset($where['rating > ']);
            $where['user_id'] = $user_id;
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,rating AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $my_rate[$row->id] = $row->C;
            }
        }

        if (!$output && isset($post_type['meta'])) {
            foreach ($post_type['meta'] as $meta_key => $field) {
                $where = ['meta_key' => $meta_key];
                if (isset($options['id'])) {
                    $where['post_id'] = $options['id'];
                }
                $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
                foreach ($rows as $row) {
                    $meta_keys[$row->id][$meta_key] = $row->C;
                }

            }
        }

        if ($options['type'] == 'book') {

            $where = ['meta_key' => 'pages'];
            if (isset($options['id'])) {
                $where['post_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
            foreach ($rows as $row) {
                $pages[$row->id] = $row->C;
            }

            $where = ['meta_key' => 'dl_book'];
            if (isset($options['id'])) {
                $where['post_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
            foreach ($rows as $row) {
                $sample_questions[$row->id] = $row->C;
            }
        }

        if (!$output && in_array('dl_box', $post_type['support'])) {
            $where = ['meta_key' => 'dl_box'];
            if (isset($options['id'])) {
                $where['post_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
            foreach ($rows as $row) {
                $attachments[$row->id] = $row->C;
            }
        }

        $db = $this->db;
        if (isset($options['select'])) {
            if (!is_array($options['select'])) {
                $options['select'] = array($options['select']);
            }
            foreach ($options['select'] as $select) {
                $db->select($select);
            }
        } else {
            $select = "p.*,0 AS need_update";

            if (in_array('thumb', $post_type['support']))
                $select .= ", p.thumb as cover";

            if (in_array('excerpt', $post_type['support']))
                $select .= ", p.excerpt";

            if (in_array('category', $post_type['support']))
                $select .= ", p.category, c.name as category_name,s.id as sath,s.name as sath_name,c.id as payeh,c.name as payeh_name";

            $select .= ",date_modified ,p.date";

            $db->select($select);
        }

        $db->join("ci_category c", "c.id=p.category", "left", FALSE);
        $db->join("ci_category s", "s.id=c.parent", "left", FALSE);

        if (!isset($options['published'])) {
            $db->where("p.published", 1);
        }

        $db->where("p.type", $options['type']);

        if (isset($options['where'])) {
            $where = $options['where'];
            if (is_array($where)) {
                foreach ($where as $wk => $wv) {
                    if (is_numeric($wk)) {
                        $db->where($wv);
                    } else {
                        $db->where($wk, $wv);
                    }
                }
            } else
                $db->where($where);
        }

        if (isset($options['category'])) {
            if (is_array($options['category']))
                $db->where_in("p.category", $options['category']);
            else
                $db->where("p.category", $options['category']);
        }

        if (isset($options['has_membership'])) {
            $db->where("p.has_membership", $options['has_membership']);
        }

        if (isset($options['order'])) {
            if (!is_array($options['order']))
                $options['order'] = array($options['order']);

            foreach ($options['order'] as $order) {
                $db->order_by($order);
            }
        }

        if (isset($options['limit']) && isset($options['min'])) {
            $db->limit($options['limit'], $options['min']);
        } elseif (isset($options['limit'])) {
            $db->limit($options['limit']);
        }

        if (isset($options['id'])) {
            $db->where_in('p.id', $options['id']);
        }
        $posts = $db->get('posts p')->result();

        foreach ($posts as $k => $p) {
            $posts[$k]->date_str = strtotime($p->date);
            $posts[$k]->date_fa = jdate($this->setting['date_format'], $p->date_str, "", "", "en");
            $posts[$k]->time_fa = jdate($this->setting['time_format'], $p->date_str, "", "", "en");
            if (isset($meta_keys[$p->id])) {
                foreach ($meta_keys[$p->id] as $meta_key => $meta_value) {
                    $posts[$k]->$meta_key = $meta_value;
                }
            }
            if (in_array('thumb', $post_type['support'])) {
                $posts[$k]->cover = $p->cover == '' ? NULL : base_url() . $p->cover;
            }
            $posts[$k]->rating_sum = @$rating_sum[$p->id];
            $posts[$k]->my_rate = @$my_rate[$p->id];
            $posts[$k]->view_count = @$view_count[$p->id];
            $posts[$k]->comment_count = @$comment_count[$p->id];
            $posts[$k]->like_count = @$like_count[$p->id];
            $posts[$k]->like = @$like[$p->id];
            $posts[$k]->rate_count = @$rate_count[$p->id];
            $posts[$k]->rating = @$rating[$p->id];
            if (!$output && in_array('dl_box', $post_type['support'])) {
                $p->attachments = isset($attachments[$p->id]) ? $attachments[$p->id] : "";
                $p->attachments = $this->tools->jsonDecode($p->attachments);
                if (is_array($p->attachments) or is_object($p->attachments)) {
                    foreach ($p->attachments as $ak => $attachment) {
                        $p->attachments[$ak]['file'] = base_url() . $attachment['file'];
                        $p->attachments[$ak]['path'] = $attachment['file'];
                    }
                }
                $posts[$k]->attachments = $p->attachments;
            }

            if (!$output && $options['type'] == 'book') {
                $p->sample_questions = "";
                if (isset($sample_questions[$p->id])) {
                    $p->sample_questions = $sample_questions[$p->id];
                }
                $p->sample_questions = $this->tools->jsonDecode($p->sample_questions);
                if (is_array($p->sample_questions) or is_object($p->sample_questions)) {
                    foreach ($p->sample_questions as $ak => $attachment) {
                        //$p->sample_questions[$ak]['file'] = md5($attachment['file']) . sha1($attachment['file']);
                        $p->sample_questions[$ak]['path'] = $attachment['file'];
                    }
                }
                $posts[$k]->sample_questions = $p->sample_questions;


                $pages = isset($pages[$p->id]) ? explode(",", $pages[$p->id]) : [];
                $pCount = count($pages);
                $pgs = [];

                if ($pCount > 0) {
                    $parts = $this->db->select('id')->where('book_id', (int)$p->id)->order_by('order', 'asc')->get('book_meta')->result();

                    foreach ($parts as $i => $part) {
                        foreach ($pages as $j => $pg) {
                            if ($j == 0 && $i <= $pg) $pgs[$j + 1][] = (int)$part->id;
                            if ($j > 0 && $i > $pages[$j - 1] && $i <= $pg) $pgs[$j + 1][] = (int)$part->id;
                        }
                    }
                }

                $posts[$k]->pagecount = $pCount;
                $posts[$k]->pages = [
                    'offset' => $p->pages,
                    'array' => $pgs
                ];
            }

        }
        foreach ($posts as $k => $v) {
            if ($v->cover) {
                $posts[$k]->cover300 = thumb($v->cover, 300);
            }
            $posts[$k]->has_description = intval($v->has_description) ? "true" : "false";
            $posts[$k]->has_sound = intval($v->has_sound) ? "true" : "false";
            $posts[$k]->has_video = intval($v->has_video) ? "1" : "0";
            $posts[$k]->has_image = intval($v->has_image) ? "1" : "0";
            $posts[$k]->has_test = intval($v->has_test) ? "1" : "0";
            $posts[$k]->has_tashrihi = intval($v->has_tashrihi) ? "1" : "0";
            $posts[$k]->has_download = intval($v->has_download) ? "1" : "0";
            $posts[$k]->need_update = intval($v->need_update) ? "1" : "0";
        }
        return $posts;
    }
    public function getPosts(array $options)
    {
        $output = (int)$this->input->post("output");
        $options = array_merge(array(
            'type' => 'post',
            'views' => TRUE,
            'comments' => TRUE,
            'likes' => TRUE,
            'rating' => TRUE
        ), $options);


        global $POST_TYPES;

        $post_type = @$POST_TYPES[$options['type']];
        $user_id = $options["user_id"];
        $view_count = [];
        $comment_count = [];
        $like_count = [];
        $like = [];
        $rate_count = [];
        $rating_sum = [];
        $rating = [];
        $my_rate = [];
        $meta_keys = [];
        $pages = [];
        $sample_questions = [];
        $attachments = [];

        if (!$output && $options['views'] === TRUE) {
            $where = ['table' => 'posts'];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_logs', 'row_id AS id,COUNT(row_id) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $view_count[$row->id] = $row->C;
            }
        }

        if (!$output && $options['comments'] === TRUE) {
            $where = ['table' => 'posts'];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_comments', 'row_id AS id,COUNT(row_id) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $comment_count[$row->id] = $row->C;
            }
        }

        if (!$output && $options['likes'] === TRUE) {
            $where = ['table' => 'posts'];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,COUNT(row_id) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $like_count[$row->id] = $row->C;
            }
        }

        if (!$output && $options['likes'] === TRUE) {
            $where = ['table' => 'posts', 'rating' => 0, 'user_id' => $user_id];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_rates', "row_id AS id,IF(COUNT(*) > 0, 'true', 'false') AS C", $where, 'row_id');
            foreach ($rows as $row) {
                $like[$row->id] = $row->C;
            }
        }

        if (!$output && $options['comments'] === TRUE) {
            $where = ['table' => 'posts', 'rating > ' => 0];
            if (isset($options['id'])) {
                $where['row_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,COUNT(rating) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $rate_count[$row->id] = $row->C;
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,SUM(rating) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $rating_sum[$row->id] = $row->C;
            }
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,ROUND((SUM(rating)/COUNT(rating)),1) AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $rating[$row->id] = $row->C;
            }
            unset($where['rating > ']);
            $where['user_id'] = $user_id;
            $rows = $this->ExtQuery('ci_rates', 'row_id AS id,rating AS C', $where, 'row_id');
            foreach ($rows as $row) {
                $my_rate[$row->id] = $row->C;
            }
        }

        if (!$output && isset($post_type['meta'])) {
            foreach ($post_type['meta'] as $meta_key => $field) {
                $where = ['meta_key' => $meta_key];
                if (isset($options['id'])) {
                    $where['post_id'] = $options['id'];
                }
                $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
                foreach ($rows as $row) {
                    $meta_keys[$row->id][$meta_key] = $row->C;
                }

            }
        }

        if ($options['type'] == 'book') {

            $where = ['meta_key' => 'pages'];
            if (isset($options['id'])) {
                $where['post_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
            foreach ($rows as $row) {
                $pages[$row->id] = $row->C;
            }

            $where = ['meta_key' => 'dl_book'];
            if (isset($options['id'])) {
                $where['post_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
            foreach ($rows as $row) {
                $sample_questions[$row->id] = $row->C;
            }
        }

        if (!$output && in_array('dl_box', $post_type['support'])) {
            $where = ['meta_key' => 'dl_box'];
            if (isset($options['id'])) {
                $where['post_id'] = $options['id'];
            }
            $rows = $this->ExtQuery('ci_post_meta', 'post_id AS id,meta_value AS C', $where, null);
            foreach ($rows as $row) {
                $attachments[$row->id] = $row->C;
            }
        }

        $db = $this->db;
        if (isset($options['select'])) {
            if (!is_array($options['select'])) {
                $options['select'] = array($options['select']);
            }
            foreach ($options['select'] as $select) {
                $db->select($select);
            }
        } else {
            $select = "p.*,0 AS need_update";

            if (in_array('thumb', $post_type['support']))
                $select .= ", p.thumb as cover";

            if (in_array('excerpt', $post_type['support']))
                $select .= ", p.excerpt";

            if (in_array('category', $post_type['support']))
                $select .= ", p.category, c.name as category_name,s.id as sath,s.name as sath_name,c.id as payeh,c.name as payeh_name";

            $select .= ",date_modified ,p.date";

            $db->select($select);
        }

        $db->join("ci_category c", "c.id=p.category", "left", FALSE);
        $db->join("ci_category s", "s.id=c.parent", "left", FALSE);

        if (!isset($options['published'])) {
            $db->where("p.published", 1);
        }

        $db->where("p.type", $options['type']);

        if (isset($options['where'])) {
            $where = $options['where'];
            if (is_array($where)) {
                foreach ($where as $wk => $wv) {
                    if (is_numeric($wk)) {
                        $db->where($wv);
                    } else {
                        $db->where($wk, $wv);
                    }
                }
            } else
                $db->where($where);
        }

        if (isset($options['category'])) {
            if (is_array($options['category']))
                $db->where_in("p.category", $options['category']);
            else
                $db->where("p.category", $options['category']);
        }

        if (isset($options['has_membership'])) {
            $db->where("p.has_membership", $options['has_membership']);
        }

        if (isset($options['order'])) {
            if (!is_array($options['order']))
                $options['order'] = array($options['order']);

            foreach ($options['order'] as $order) {
                $db->order_by($order);
            }
        }

        if (isset($options['limit']) && isset($options['min'])) {
            $db->limit($options['limit'], $options['min']);
        } elseif (isset($options['limit'])) {
            $db->limit($options['limit']);
        }

        if (isset($options['id'])) {
            $db->where_in('p.id', $options['id']);
        }
        $posts = $db->get('posts p')->result();

        foreach ($posts as $k => $p) {
            $posts[$k]->date_str = strtotime($p->date);
            $posts[$k]->date_fa = jdate($this->setting['date_format'], $p->date_str, "", "", "en");
            $posts[$k]->time_fa = jdate($this->setting['time_format'], $p->date_str, "", "", "en");
            if (isset($meta_keys[$p->id])) {
                foreach ($meta_keys[$p->id] as $meta_key => $meta_value) {
                    $posts[$k]->$meta_key = $meta_value;
                }
            }
            if (in_array('thumb', $post_type['support'])) {
                $posts[$k]->cover = $p->cover == '' ? NULL : base_url() . $p->cover;
            }
            $posts[$k]->rating_sum = @$rating_sum[$p->id];
            $posts[$k]->my_rate = @$my_rate[$p->id];
            $posts[$k]->view_count = @$view_count[$p->id];
            $posts[$k]->comment_count = @$comment_count[$p->id];
            $posts[$k]->like_count = @$like_count[$p->id];
            $posts[$k]->like = @$like[$p->id];
            $posts[$k]->rate_count = @$rate_count[$p->id];
            $posts[$k]->rating = @$rating[$p->id];
            if (!$output && in_array('dl_box', $post_type['support'])) {
                $p->attachments = isset($attachments[$p->id]) ? $attachments[$p->id] : "";
                $p->attachments = $this->tools->jsonDecode($p->attachments);
                if (is_array($p->attachments) or is_object($p->attachments)) {
                    foreach ($p->attachments as $ak => $attachment) {
                        $p->attachments[$ak]['file'] = base_url() . $attachment['file'];
                        $p->attachments[$ak]['path'] = $attachment['file'];
                    }
                }
                $posts[$k]->attachments = $p->attachments;
            }

            if (!$output && $options['type'] == 'book') {
                $p->sample_questions = "";
                if (isset($sample_questions[$p->id])) {
                    $p->sample_questions = $sample_questions[$p->id];
                }
                $p->sample_questions = $this->tools->jsonDecode($p->sample_questions);
                if (is_array($p->sample_questions) or is_object($p->sample_questions)) {
                    foreach ($p->sample_questions as $ak => $attachment) {
                        //$p->sample_questions[$ak]['file'] = md5($attachment['file']) . sha1($attachment['file']);
                        $p->sample_questions[$ak]['path'] = $attachment['file'];
                    }
                }
                $posts[$k]->sample_questions = $p->sample_questions;


                $pages = isset($pages[$p->id]) ? explode(",", $pages[$p->id]) : [];
                $pCount = count($pages);
                $pgs = [];

                if ($pCount > 0) {
                    $parts = $this->db->select('id')->where('book_id', (int)$p->id)->order_by('order', 'asc')->get('book_meta')->result();

                    foreach ($parts as $i => $part) {
                        foreach ($pages as $j => $pg) {
                            if ($j == 0 && $i <= $pg) $pgs[$j + 1][] = (int)$part->id;
                            if ($j > 0 && $i > $pages[$j - 1] && $i <= $pg) $pgs[$j + 1][] = (int)$part->id;
                        }
                    }
                }

                $posts[$k]->pagecount = $pCount;
                $posts[$k]->pages = [
                    'offset' => implode(",",$pages),
                    'array' => $pgs
                ];
            }

        }
        foreach ($posts as $k => $v) {
            if ($v->cover) {
                $posts[$k]->cover300 = thumb($v->cover, 300);
            }
            $posts[$k]->has_description = intval($v->has_description) ? "true" : "false";
            $posts[$k]->has_sound = intval($v->has_sound) ? "true" : "false";
            $posts[$k]->has_video = intval($v->has_video) ? "1" : "0";
            $posts[$k]->has_image = intval($v->has_image) ? "1" : "0";
            $posts[$k]->has_test = intval($v->has_test) ? "1" : "0";
            $posts[$k]->has_tashrihi = intval($v->has_tashrihi) ? "1" : "0";
            $posts[$k]->has_download = intval($v->has_download) ? "1" : "0";
            $posts[$k]->need_update = intval($v->need_update) ? "1" : "0";
        }
        return $posts;
    }

    //=========== BOOK ======================//

    //==== parts ===
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
            unset($partData['book_id']);
            $this->db->where('id', (int)$data['id'])->update('book_meta', $partData);
        } else {
            // Check for existing part with same book_id and order
            $existing = $this->db->select('id')->where('book_id', (int)$bookId)->where('order', (int)$data['order'])->get('book_meta')->row();
            if ($existing) {
                unset($partData['book_id']);
                $this->db->where('id', $existing->id)->update('book_meta', $partData);
            } else {
                $this->db->insert('book_meta', $partData);
            }
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

        return $result;
    }

    public function getBookPartsById($id)
    {
        $id = (int)$id;

        $this->db->select("id, order, text, description, index");
        $this->db->select("IF(sound IS NOT NULL , 'true', 'false') as has_sound");
        $this->db->select("IF(video IS NOT NULL , 'true', 'false') as has_video");
        $this->db->select("IF(image IS NOT NULL , 'true', 'false') as has_image");
        $this->db->select("IF(description IS NOT NULL , 'true', 'false') as has_description");
        $this->db->where('book_id', $id);
        $this->db->order_by('order', 'asc');
        return $this->db->get('book_meta')->result();
    }

    public function getBookTests($id)
    {
        $this->db->select('id,question,true_answer,answer_1,answer_2,answer_3,answer_4');
        return $this->db->where('book_id', (int)$id)->order_by('id', 'asc')->get('tests')->result();
    }
}