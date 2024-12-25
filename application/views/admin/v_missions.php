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

$cols['از'] =
    array(
        'field_name' => 'from',
        'link' => true,
        'function'=>function($col,$row){
            return '<a href="'.site_url('user/'.$row['from_username']).'" target="_blank">'.$row['from_name'].'</a>';
        }
    );

$cols['به'] =
    array(
        'field_name' => 'to',
        'link' => true,
        'function'=>function($col,$row){
            return '<a href="'.site_url('user/'.$row['to_username']).'" target="_blank">'.$row['to_name'].'</a>';
        }
    );

$cols['انجام شده'] =
    array(
        'field_name' => 'done',
        'link' => true,
        'type' => 'bool'
    );

$cols['متن'] =
    array(
        'field_name' => 'text',
        'link' => true,
        'max' => 100
    );

$cols['انجام شده'] =
    array(
        'field_name'=>'done',
        'function'=>function($col,$row)
        {

            $id = $row['id'];
            $checked = $col == 1 ? 'checked':'';
            $col =  '<input id="cmn-tg-'.$id.'" class="cmn-toggle cmrf chk-tg-field" value="'.$id.'"
                         data-t="missions" data-f="done" type="checkbox" '.$checked.'>
                         <label for="cmn-tg-'.$id.'"></label>';
            return $col;
        },
        'td-attr'=>'align="center" style="width:50px;"'
    );

if( $this->user->can('submit_missions') )
    $cols['تایید شده'] =
        array(
            'field_name'=>'submitted',
            'function'=>function($col,$row)
            {

                $id = $row['id'];
                $checked = $col == 1 ? 'checked':'';
                $col =  '<input id="cmn-tg-'.$id.'" class="cmn-toggle cmrf chk-tg-field" value="'.$id.'"
                         data-t="missions" data-f="submitted" type="checkbox" '.$checked.'>
                         <label for="cmn-tg-'.$id.'"></label>';
                return $col;
            },
            'td-attr'=>'align="center" style="width:50px;"'
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
        $href = site_url("admin/missions/index/" . $tab);
        $class = $this->uri->segment(4) == $tab ? "active" : "";

        echo "<a href='$href' class='btn btn-primary $class'>
              <span>  " . $tab_data['name'] . " </span> &nbsp
              <span class='badge row-count row-$tab'>" . $tab_data['count'] . "</span>
              </a>";

    }

$inc->createTable($cols, $query, 'id="table" class="table light2" ', $tableName, 60);
