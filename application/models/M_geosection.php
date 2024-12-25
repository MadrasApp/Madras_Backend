<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_geosection extends CI_Model
{

    public $setting = NULL;
    public $data = NULL;
    public $geosection_list = NULL;

    function __construct()
    {
        parent::__construct();
        $this->setting = $this->settings->data;
    }


    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('geosection');
    }

    public function getGeosectionList($owner, $parent = 0, $selectable = TRUE, $post_cat = NULL, $pic = FALSE, $sample = NULL, $start = "<ul>", $end = "</ul>")
    {
        $result = "";
        if ($this->geosection_list === NULL) {
            if ($owner) {
                $this->geosection_list = $this->db->where('user_id', $owner)->order_by('position', 'asc')->get('geosection')->result();
            } else {
                $this->geosection_list = $this->db->order_by('position', 'asc')->get('geosection')->result();
            }
        }

        $geosection = $this->searchGeosectionList($parent);
        if (!empty($geosection)) {
            $result .= $start;
            foreach ($geosection as $cat) {
                $publishedval = $cat['published'];
                $published = $publishedval ? 'check green' : 'close red';
                $specialval = $cat['special'];
                $special = $specialval ? 'star gold' : 'star-o';
                $id = $cat['id'];
                $name = $cat['name'];
                $gtidval = $cat['gtid'];
                $this->db->select("d.title");
                $this->db->where("d.id IN($gtidval)");
                $O = $this->db->get('geotype d')->first_row();
                $gtid = $O?$O->title:"";
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
                        '[PARENT]', '[POS]', '[ICON]', '[SPECIAL]', '[SPECIALVAL]', '[PUBLISHED]', '[PUBLISHEDVAL]', '[GTID]', '[GTIDVAL]');

                    $str_replace = array($cat['id'], $cat['name'], $cat['des'], $cpic, $cpic150, $cpic300, $cpic600,
                        $parent, $cat['pos'], $cat['icon'], $special, $specialval, $published, $publishedval, $gtid, $gtidval);

                    $_sample = str_replace($str_search, $str_replace, $sample);

                    $_sample =
                        str_replace(
                            '[SUB-MENU]',
                            $this->getGeosectionList($owner, $cat['id'], 0, NULL, FALSE, $sample),
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
                    $selectable && $result .= "<input type=radio value=$id name=geosection[] $checked>";

                    if ($pic) {
                        $result .= "<span class=geosection-list-img >";
                        $result .= "<img src='$cpic150'>";
                        $result .= "</span>";
                    }

                    $result .= $name;

                    $selectable && $result .= "</label>";

                    $result .= $this->getGeosectionList($owner, $cat['id'], $selectable, $post_cat, $pic);

                    $result .= "</li>";
                }
            }
            $result .= $end;
            return $result;
        }
        return NULL;
    }

    public function getGeosectionSelectMenu($owner, $parent = 0)
    {
        $result = "";
        if ($this->geosection_list === NULL) {
            if ($owner) {
                $this->geosection_list = $this->db->where('user_id', $owner)->order_by('position', 'asc')->get('geosection')->result();
            } else {
                $this->geosection_list = $this->db->order_by('position', 'asc')->get('geosection')->result();
            }
        }

        $geosection = $this->searchGeosectionList($parent);
        if (!empty($geosection)) {
            foreach ($geosection as $cat) {
                $id = $cat['id'];
                $name = $cat['name'];
                $pos = $cat['pos'];

                $result .= "<option item-id=$id parent=$parent name='$name' pos='$pos' value=$id >$name</option>";
                $result .= $this->getGeosectionSelectMenu($owner, $id);
            }
            return $result;
        }
        return NULL;
    }

    public function searchGeosectionList($parent = 0)
    {

        if ($this->geosection_list !== NULL) {
            $return = array();
            foreach ($this->geosection_list as $key => $cat) {
                if ($cat->parent == $parent)
                    $return[] = array(
                        'id' => $cat->id,
                        'pos' => $cat->position,
                        'gtid' => $cat->gtid,
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

    public function addGeosection($data = NULL)
    {
        if ($data) {
            unset($data['id']);// = $this->db->last_id('geosection');

            if (!isset($data['pic'])) $data['pic'] = NULL;

            if ($this->db->insert('geosection', $data)) {
                $insert_id = $this->db->insert_id();
                $return = $this->db->where('id', $insert_id)->get('geosection')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');

                return $return;
            }
        }
        return FALSE;
    }

    public function updateGeosection($data = NULL)
    {
        if ($data && isset($data['id'])) {
            $id = $data['id'];
            if (!isset($data['pic'])) $data['pic'] = NULL;
            if ($this->db->where('id', $id)->update('geosection', $data)) {

                $return = $this->db->where('id', $id)->get('geosection')->row();

                if (!isset($return->pic) || trim($return->pic) == "")
                    $return->pic = $this->setting['default_category_pic'];

                $return->pic150 = thumb($return->pic, '150');
                $return->pic300 = thumb($return->pic, '300');
                return $return;
            }
        }
        return FALSE;
    }

    public function deleteGeosection($id = NULL)
    {
        if ($id) {
            $sub = $this->db->where('parent', $id)->get('geosection')->result();
            if ($sub) {
                foreach ($sub as $row)
                    $this->deleteGeosection($row->id);
            }
            if ($this->db->where('id', $id)->delete('geosection'))
                return TRUE;
        }
        return FALSE;
    }

}

?>