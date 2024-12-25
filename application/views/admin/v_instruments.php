<?php
/**
 * Created by Talkhabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 12:05 PM
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->helper('inc');

$inc = new inc;

$cols['نام'] =
    array(
        'field_name' => 'name',
        'link' => true,
        'html' => '<div class="wb" style="font-size:13px"><a href="'.site_url('tools/view/[ID]').'" target="_blank">[FLD]</a></div>',
        'max' => 50
    );


if( $this->user->can('submit_tools') )
    $cols['تایید شده'] =
        array(
            'field_name'=>'submitted',
            'function'=>function($col,$row)
            {

                $id = $row['id'];
                $checked = $col == 1 ? 'checked':'';
                $col =  '<input id="cmn-tg-'.$id.'" class="cmn-toggle cmrf chk-tg-field" value="'.$id.'"
                         data-t="instruments" data-f="submitted" type="checkbox" '.$checked.'>
                         <label for="cmn-tg-'.$id.'"></label>';
                return $col;
            },
            'td-attr'=>'align="center" style="width:70px;"'
        );
else
    $cols['تایید شده'] =
        array(
            'field_name'=>'submitted',
            'function'=>function($col,$row)
            {

                $submitted =
                    $row['submitted'] == 1 ?
                        '<i class="fa fa-check-circle fa-lg text-success" title="تایید شده"></i>' :
                        '<i class="fa fa-times-circle fa-lg text-warning" title="تایید نشده"></i>';
                return $submitted;
            },
            'td-attr'=>'align="center" style="width:70px;"'
        );


$cols['تاریخ'] =
    array(
        'field_name' => 'date',
        'link' => true,
        'type' => 'date',
        'th-attr' => 'style="width:150px"'
    );

if (isset($options) && count($options) > 0)
    $cols['  '] = array('field_name' => 'id', 'type' => 'op', 'items' => $options, 'td-attr' => 'align="center" width="30px" style="padding:0;width:30px;"');

echo $searchHtml;

if (isset($_tabs))
    foreach ($_tabs as $tab => $tab_data) {
        $href = site_url("admin/tools/index/" . $tab);
        $class = $this->uri->segment(4) == $tab ? "active" : "";

        echo "<a href='$href' class='btn btn-primary $class'>
              <span>  " . $tab_data['name'] . " </span> &nbsp
              <span class='badge row-count row-$tab'>" . $tab_data['count'] . "</span>
              </a>";

    }

$inc->createTable($cols, $query, 'id="table" class="table light2" ', $tableName, 60);
