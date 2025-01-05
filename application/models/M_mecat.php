<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_mecat extends CI_Model
{

    public $setting = NULL;
    public $data = NULL;
    public $mecat_list = NULL;

    function __construct()
    {
        parent::__construct();
        $this->setting = $this->settings->data;
    }


    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('mecat');
    }

    public function getMeCatList($owner, $parent = 0, $selectable = TRUE, $post_cat = NULL, $pic = FALSE, $sample = NULL, $start = "<ul>", $end = "</ul>")
    {
        $result = "";
        if ($this->mecat_list === NULL) {
            if ($owner) {
                $this->mecat_list = $this->db->where('user_id', $owner)->order_by('position', 'asc')->get('mecat')->result();
            } else {
                $this->mecat_list = $this->db->order_by('position', 'asc')->get('mecat')->result();
            }
        }

        $mecat = $this->searchMeCatList($parent);
        if (!empty($mecat)) {
            $result .= $start;
            foreach ($mecat as $cat) {
                $publishedval = isset($cat['published']) ? $cat['published'] : 0;
                $published = $publishedval ? 'check green' : 'close red';
                $specialval = isset($cat['special']) ? $cat['special'] : 0;
                $special = $specialval ? 'star gold' : 'star-o';
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
                    $str_search = array('[ID]', '[NAME]', '[DES]', '[PIC]', '[PIC-150]', '[PIC-300]', '[PIC-600]',
                        '[PARENT]', '[POS]', '[ICON]', '[SPECIAL]', '[SPECIALVAL]', '[PUBLISHED]', '[PUBLISHEDVAL]');

                    $str_replace = array($cat['id'], $cat['name'], $cat['des'], $cpic, $cpic150, $cpic300, $cpic600,
                        $parent, $cat['pos'], $cat['icon'], $special, $specialval, $published, $publishedval);

                    $_sample = str_replace($str_search, $str_replace, $sample);

                    $_sample =
                        str_replace(
                            '[SUB-MENU]',
                            $this->getMeCatList($owner, $cat['id'], 0, NULL, FALSE, $sample),
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
                    $selectable && $result .= "<input type=radio value=$id name=mecat[] $checked>";

                    if ($pic) {
                        $result .= "<span class=mecat-list-img >";
                        $result .= "<img src='$cpic150'>";
                        $result .= "</span>";
                    }

                    $result .= $name;

                    $selectable && $result .= "</label>";

                    $result .= $this->getMeCatList($owner, $cat['id'], $selectable, $post_cat, $pic);

                    $result .= "</li>";
                }
            }
            $result .= $end;
            return $result;
        }
        return NULL;
    }

    public function getMeCatSelectMenu($owner, $parent = 0)
    {
        $result = "";
        if ($this->mecat_list === NULL) {
            if ($owner) {
                $this->mecat_list = $this->db->where('user_id', $owner)->order_by('position', 'asc')->get('mecat')->result();
            } else {
                $this->mecat_list = $this->db->order_by('position', 'asc')->get('mecat')->result();
            }
        }

        $mecat = $this->searchMeCatList($parent);
        if (!empty($mecat)) {
            foreach ($mecat as $cat) {
                $id = $cat['id'];
                $name = $cat['name'];
                $pos = $cat['pos'];

                $result .= "<option item-id=$id parent=$parent name='$name' pos='$pos' value=$id >$name</option>";
                $result .= $this->getMeCatSelectMenu($owner, $id);
            }
            return $result;
        }
        return NULL;
    }

    public function searchMeCatList($parent = 0)
    {

        if ($this->mecat_list !== NULL) {
            $return = array();
            foreach ($this->mecat_list as $key => $cat) {
                if ($cat->parent == $parent)
                    $return[] = array(
                        'id' => $cat->id,
                        'pos' => $cat->position,
                        'name' => $cat->name,
                        'pic' => $cat->pic,
                        'des' => $cat->description,
                        'icon' => $cat->icon
                    );
            }
            if (count($return) > 0) return $return;
        }

        return NULL;
    }

    public function addMeCat($data = NULL)
    {
        if ($data) {
            unset($data['id']);// = $this->db->last_id('mecat');

            if (!isset($data['pic'])) $data['pic'] = NULL;

            if ($this->db->insert('mecat', $data)) {
                $insert_id = $this->db->insert_id();
                $return = $this->db->where('id', $insert_id)->get('mecat')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');

                return $return;
            }
        }
        return FALSE;
    }

    public function updateMeCat($data = NULL)
    {
        if ($data && isset($data['id'])) {
            $id = $data['id'];
            if (!isset($data['pic'])) $data['pic'] = NULL;
            if ($this->db->where('id', $id)->update('mecat', $data)) {

                $return = $this->db->where('id', $id)->get('mecat')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');
                return $return;
            }
        }
        return FALSE;
    }

    public function deleteMeCat($id = NULL)
    {
        if ($id) {
            $sub = $this->db->where('parent', $id)->get('mecat')->result();
            if ($sub) {
                foreach ($sub as $row)
                    $this->deleteMeCat($row->id);
            }
            if ($this->db->where('id', $id)->delete('mecat'))
                return TRUE;
        }
        return FALSE;
    }

}

?>