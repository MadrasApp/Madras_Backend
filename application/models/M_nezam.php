<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_nezam extends CI_Model
{

    public $setting = NULL;
    public $data = NULL;
    public $nezam_list = NULL;

    function __construct()
    {
        parent::__construct();
        $this->setting = $this->settings->data;
    }


    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('nezam');
    }

    public function getNezamList($owner, $parent = 0, $selectable = TRUE, $post_cat = NULL, $pic = FALSE, $sample = NULL, $start = "<ul>", $end = "</ul>")
    {
        $result = "";
        if ($this->nezam_list === NULL) {
            if ($owner) {
                $this->nezam_list = $this->db->where('user_id', $owner)->order_by('position', 'asc')->get('nezam')->result();
            } else {
                $this->nezam_list = $this->db->order_by('position', 'asc')->get('nezam')->result();
            }
        }

        $nezam = $this->searchNezamList($parent);
        if (!empty($nezam)) {
            $result .= $start;
            foreach ($nezam as $cat) {
                $publishedval = $cat['published'];
                $published = $publishedval ? 'check green' : 'close red';
                $specialval = $cat['special'];
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
                            $this->getNezamList($owner, $cat['id'], 0, NULL, FALSE, $sample),
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
                    $selectable && $result .= "<input type=radio value=$id name=nezam[] $checked>";

                    if ($pic) {
                        $result .= "<span class=nezam-list-img >";
                        $result .= "<img src='$cpic150'>";
                        $result .= "</span>";
                    }

                    $result .= $name;

                    $selectable && $result .= "</label>";

                    $result .= $this->getNezamList($owner, $cat['id'], $selectable, $post_cat, $pic);

                    $result .= "</li>";
                }
            }
            $result .= $end;
            return $result;
        }
        return NULL;
    }

    public function getNezamSelectMenu($owner, $parent = 0)
    {
        $result = "";
        if ($this->nezam_list === NULL) {
            if ($owner) {
                $this->nezam_list = $this->db->where('user_id', $owner)->order_by('position', 'asc')->get('nezam')->result();
            } else {
                $this->nezam_list = $this->db->order_by('position', 'asc')->get('nezam')->result();
            }
        }

        $nezam = $this->searchNezamList($parent);
        if (!empty($nezam)) {
            foreach ($nezam as $cat) {
                $id = $cat['id'];
                $name = $cat['name'];
                $pos = $cat['pos'];

                $result .= "<option item-id=$id parent=$parent name='$name' pos='$pos' value=$id >$name</option>";
                $result .= $this->getNezamSelectMenu($owner, $id);
            }
            return $result;
        }
        return NULL;
    }

    public function searchNezamList($parent = 0)
    {

        if ($this->nezam_list !== NULL) {
            $return = array();
            foreach ($this->nezam_list as $key => $cat) {
                if ($cat->parent == $parent)
                    $return[] = array(
                        'id' => $cat->id,
                        'pos' => $cat->position,
                        'published' => $cat->published,
                        'special' => $cat->special,
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

    public function addNezam($data = NULL)
    {
        if ($data) {
            unset($data['id']);// = $this->db->last_id('nezam');

            if (!isset($data['pic'])) $data['pic'] = NULL;

            if ($this->db->insert('nezam', $data)) {
                $insert_id = $this->db->insert_id();
                $return = $this->db->where('id', $insert_id)->get('nezam')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');

                return $return;
            }
        }
        return FALSE;
    }

    public function updateNezam($data = NULL)
    {
        if ($data && isset($data['id'])) {
            $id = $data['id'];
            if (!isset($data['pic'])) $data['pic'] = NULL;
            if ($this->db->where('id', $id)->update('nezam', $data)) {

                $return = $this->db->where('id', $id)->get('nezam')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');
                return $return;
            }
        }
        return FALSE;
    }

    public function deleteNezam($id = NULL)
    {
        if ($id) {
            $sub = $this->db->where('parent', $id)->get('nezam')->result();
            if ($sub) {
                foreach ($sub as $row)
                    $this->deleteNezam($row->id);
            }
            if ($this->db->where('id', $id)->delete('nezam'))
                return TRUE;
        }
        return FALSE;
    }

}

?>