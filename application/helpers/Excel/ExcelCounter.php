<?php
defined('BASEPATH') or exit('No direct script access allowed');

include_once "SimpleXLSX.php";

class ExcelCounter extends CI_Model
{

    public function Pre($data, $die = 1)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if ($die) {
            die();
        }
    }

    public function Comma($value)
    {
        if (is_numeric($value)) {
            $count = 2;
            if (str_replace(".", "", $value) == $value) {
                $count = 0;
            }
            $value = number_format($value, $count, '.', ',');
        }

        return $value;
    }
    static function LoadJsonOut($data)
    {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        die;
    }


    public function IsUsed($table, $where = array(1), $field = "COUNT(*)")
    {
        $db = $this->db;
        $where = is_array($where) ? implode(" AND ", $where) : $where;
        $db->select("$field AS C");
        $db->where($where);
        $row = $db->get($table)->result();
        $total = isset($row[0])?$row[0]->C:null;
        return $total;
    }

    public function LoadInsertData($table, $data)
    {
        $db = $this->db;
        foreach ($data as $k => $v) {
            $data[$k] = str_replace("'", "\\'", $v);
        }
        $db->insert($table, $data);
        $result = $db->insert_id();
        return $result;
    }

    public function LoadUpdateData($table, $data, $where)
    {
        $db = $this->db;
        $db->where($where);
        $db->update($table, $data);
        $result = $db->affected_rows();
        return $result;
    }

    public function Translate($key)
    {
        $key = strtolower($key);
        $Translate = [
            "classonline" => "کلاس آنلاین",
            "classaccount" => "اکانتهای کلاس آنلاین",
            "classaccount_id" => "شمارنده",
            "classaccount_classonline_id" => "شماره کلاس آنلاین",
            "classaccount_useronline" => "نام کاربری",
            "classaccount_userpass" => "رمز عیور",
            "classaccount_accessslink" => "لینک دسترسی",
        ];
        return isset($Translate[$key]) ? $Translate[$key] : null;
    }

    public function LoadColumns($table)
    {
        $db = $this->db;
        $query = "SHOW COLUMNS FROM `ci_$table`";
        $query = $db->query($query);
        $totals = $query->result_array();
        $total = [];
        $forbidden = ["hits", "language", "register_user_id", "update_user_id", "registerdate", "upddate", "created", "created_by", "image", "modified", "modified_by"];
        foreach ($totals as $key => $value) {
            $value = $value["Field"];
            if (!in_array($value, $forbidden)) {
                $text = $this->Translate($table . "_" . $value);
                if ($text) {
                    $total[$text] = $value;
                }
            }
        }

        return $total;
    }

    public function ChangeTextFarsi($str)
    {
        $from = array('“', '”', "'", 'ي', 'ك', 'هٔ', '\\\\', '\"');
        $to = array('&ldquo;', '&rdquo;', '&rdquo;', 'ی', 'ک', 'ه‌ی', ' ', '"');
        $str = str_replace($from, $to, $str);
        $from = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹");
        $to = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $str = str_replace($from, $to, $str);
        $from = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
        $to = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $str = str_replace($from, $to, $str);
        $str = str_replace('  ', ' ', $str);
        $str = trim($str);

        return $str;
    }
    static function GregorianDate($data, $mod = "/")
    {
        APPPATH . '/helpers/Excel/PersianCalendar.php';
        $date = new PersianCalendar();
        $data = str_replace("/", "-", $data);
        list($y, $m, $d) = explode("-", $data);
        $data = $date->jalali_to_gregorian($y, $m, $d, $mod);

        return $data;
    }

    public function LoadDynamicExcel($step, $table, $post)
    {
        require('includes/excel_reader2.php');
        require('includes/SpreadsheetReader.php');
        $tempid = time();
        $classcolor = ["danger", "success", "primary", "secondary"];
        $extrafields = [
        ];
        $fieldsdatas = isset($post["fieldsdata"]) ? $post["fieldsdata"] : [];
        $count = @$_SESSION["counts_data"];
        $start = @$post["start"] ?: 0;
        $limit = @$post["limit"] ?: 20;
        $destupfile = isset($_SESSION["destupfile"]) ? $_SESSION["destupfile"] : "";
        $flipped = [];
        $status = 1;
        $html = "";
        try {
            switch ($step) {
                case 1:
                    $_SESSION["DataUploadedSection"] = [];
                    $Files = $_FILES["xlsfile"];
                    if ($Files["error"]) {
                        $message = $this->Translate("FILE_ERROR_VALUE");
                        $data = ["status" => $status,"html"=>$message];
                        $this->LoadJsonOut($data);
                    }
                    $name = $Files["name"];
                    $ext = explode(".", $name);
                    $ext = $ext[count($ext) - 1];
                    $allowext = array("xls", "xlsx");
                    if (!in_array($ext, $allowext)) {
                        $message = $this->Translate("FILE_ERROR_EXT");
                        $data = ["status" => $status,"html"=>$message];
                        $this->LoadJsonOut($data);
                    }
                    $dest = APPPATH . "/cache";
                    $dest = str_replace("\\", "/", $dest);
                    $dest = str_replace("//", "/", $dest);
                    @mkdir($dest);
                    $destupfile = "$dest/$tempid.$ext";
                    move_uploaded_file($Files["tmp_name"], $destupfile);
                    $_SESSION["destupfile"] = $destupfile;
                    $_SESSION["DataUploadedRows"] = [];

                    $xlsx = SimpleXLSX::parse($destupfile);
                    $Name = $xlsx->sheetName(0);
                    $rows = $xlsx->rows();
                    $_SESSION["DataUploadedRows"] = $rows;
                    $_SESSION["DataUploadedSectionHeader"] = array_shift($_SESSION["DataUploadedRows"]);

                    $counts = count($rows);
                    $_SESSION["counts_data"] = $counts;
                    $status = $counts;
                    $counts = $this->Comma($counts);
                    $html = '<div class="text-white bg-success p-2 h2 m-3">' . sprintf("بارگذاری فایل اکسل با موفقیت انجام شد.تعداد رکورد ثبت شده مورد تایید در فایل ارسالی %s مورد می باشد", $counts) . '</div>';
                    $attr = 'class="required cotrol-select fieldsdata" required';
                    $field = array();
                    $header = $this->LoadColumns($table);
                    if (isset($extrafields[$table]) && is_array($extrafields[$table]) && count($extrafields[$table])) {
                        $header = array_merge($header, $extrafields[$table]);
                    }
                    $O = [];
                    $O[] = '<option value="">' . "یک گزینه را انخاب نمایید" . '</option>';
                    $O[] = '<option value="0">' . "هیچکدام" . '</option>';
                    foreach ($header as $kh => $vh) {
                        if ($kh) {
                            $O[] = '<option value="' . $vh . '">' . $kh . '</option>';
                            $flipped[$vh] = $kh;
                        }
                    }
                    $MaxCols = 0;
                    $rowcounter = 1;
                    $rows = array_splice($rows, 0, 51);

                    $htmltemp = '';
                    $htmltemp .= '<div class="text-center"><div class="btn">Sheet : ' . $Name . '</div></div>';
                    $htmltemp .= '<div class="scroll-x">';
                    $htmltemp .= '<table class="table table-striped border" id="excelout">';
                    foreach ($rows as $Key => $Row) {
                        if ($Key) {
                            if ($rowcounter < 50) {
                                $htmltemp .= "<tr>";
                            }
                            if (!strlen(trim($Row[0])) && !strlen(trim($Row[1])) && !strlen(trim($Row[2]))) {
                                continue;
                            }
                            if ($rowcounter < 50) {
                                $htmltemp .= "<td>" . $rowcounter . "</td>";
                            }
                            $counter = 0;
                            foreach ($Row as $k => $v) {
                                $v = $this->ChangeTextFarsi($v);
                                if ($k < $MaxCols) {
                                    if ($rowcounter < 50) {
                                        $htmltemp .= '<td><div>' . $v . '</div></td>';
                                    }
                                    $field[$rowcounter - 1][] = $v;
                                    if (strlen(trim($v))) {
                                        $counter++;
                                    }
                                }
                            }
                            if ($counter < 3) {
                                $field[$rowcounter - 1] = array_slice($field[$rowcounter - 1], 0, $counter);
                            }
                            if ($rowcounter < 50) {
                                $htmltemp .= "</tr>";
                            }

                            $rowcounter++;
                        } else {
                            $htmltemp .= "<tr>";
                            $htmltemp .= "<td class='radif'>#</td>";
                            foreach ($Row as $k => $v) {
                                $v = $this->ChangeTextFarsi($v);
                                if (count($header)) {
                                    $value = isset($flipped[$v]) ? $flipped[$v] : "";
                                    $fielsselector = '<select name="fieldsdata[]" id="fieldsdata_' . $k . '" ' . $attr . '>' . implode("\n", $O) . '</select>';
                                    $v = $v . "<br>" . $fielsselector;
                                }
                                if (strlen($v)) {
                                    $MaxCols++;
                                    $htmltemp .= '<td><div>' . $v . '</div></td>';
                                }
                            }
                            $htmltemp .= "</tr>";
                        }
                    }
                    $htmltemp .= '</table>';
                    $htmltemp .= '</div>';
                    $html .= $htmltemp;
                    break;
                case 2:
                    if (!isset($_SESSION["DataUploadedSection"]) || !count($_SESSION["DataUploadedSection"])) {
                        foreach ($fieldsdatas as $key => $value) {
                            if (!is_numeric($value["value"])) {
                                $_SESSION["DataUploadedSection"][$key] = $value["value"];
                                $_SESSION["DataUploadedSectionHeader"][$key] = $this->Translate($table."_".$value["value"]);
                            } else {
                                unset($_SESSION["DataUploadedSectionHeader"][$key]);
                            }
                        }
                    }
                    $rows = array_slice($_SESSION["DataUploadedRows"], $start, $limit);
                    $fieldsdata = $_SESSION["DataUploadedSection"];
                    $html = '';
                    $rowcounter = 1;
                    $field = [];
                    foreach ($rows as $Key => $Row) {
                        foreach ($Row as $k => $v) {
                            if (isset($fieldsdata[$k]) && !is_array($fieldsdata[$k])) {
                                $field[$rowcounter - 1][$fieldsdata[$k]] = $v;
                            }
                        }
                        $rowcounter++;
                    }
                    $outDatas = [];
                    $I = 0;
                    if (count($field)) {
                        $actions = ["بدون فعالیت", "بروزرسانی", "ثبت جدید"];
                        $J = 0;
                        $tablekeys = [
                            "classaccount"=>["classonline_id","useronline"]
                        ];
                        foreach ($field as $key => $values) {
                            $section = $this->Translate($table);
                            $f2 = array();
                            $update = 0;
                            $skubase = null;
                            $skuupdate = null;
                            $affected = [];

                            foreach ($values as $kf1 => $kv1) {
                                $value = trim(str_replace(["'", '"'], ['', ''], $kv1));
                                $value = $this->ChangeTextFarsi($value);
                                switch ($kf1) {
                                    case "classonline_id":
                                        $classonline_id = $this->IsUsed("classonline", "title='$value'", "id");
                                        if (!$classonline_id) {
                                            $classonline_id = $this->LoadInsertData("classonline", ["title" => $value, "published" => 1]);
                                        }
                                        $value = $classonline_id;
                                        break;
                                    case "price":
                                        $value = str_replace(",", "", $value);
                                        break;
                                    case "birthdate":
                                    case "sodordateshsh":
                                    case "marriagedate":
                                    case "divorcedate":
                                    case "hiringdate":
                                    case "usestartdate":
                                    case "start_date":
                                        if (intval($value)) {
                                            $value = $this->GregorianDate($value, "-");
                                        }
                                        break;
                                    case "id":
                                        $value = intval($value);
                                        $value = (int)$this->IsUsed($table, "id=$value", "id");
                                        if ($value) {
                                            $skubase = "id";
                                            $skuupdate = $value;
                                        }
                                        break;
                                }
                                if (!isset($f2[$kf1])) {
                                    $f2[$kf1] = $value;
                                }
                                $affected[] = $this->ChangeTextFarsi($kv1);
                            }
                            if(!$skubase && isset($tablekeys[$table])){
                                $allow = 1;
                                $controldatakey = [];
                                $controldatavalue = [];
                                foreach ($tablekeys[$table] as $kf1 => $kv1){
                                    if(!isset($values[$kv1])){
                                        $allow = 0;
                                    } else {
                                        $kf1 = isset(${"$kv1"})?${$kv1}:$values[$kv1];
                                        $controldatakey[$kv1] = $kv1;
                                        $controldatavalue[$kv1] = $kf1;
                                    }
                                }
                                if($allow){
                                    $skubase = 'CONCAT('.implode(",',',",$controldatakey).')';
                                    $skuupdate = implode(",",$controldatavalue);
                                }
                            }
                            if ($skubase) {
                                $update = (int)$this->IsUsed($table, "$skubase = '$skuupdate'", "id");
                            }
                            if ($update) {
                                $this->LoadUpdateData($table, $f2, "id = $update");
                                $outDatas[$J] = array_merge([1, $actions[1], $section], $affected);
                                $I++;
                            } else {
                                $result = $this->LoadInsertData($table, $f2);
                                $action = $result?2:0;
                                $outDatas[$J] = array_merge([$action, $actions[$action], $section], $affected);
                                $I++;
                            }
                            $J++;
                        }
                        if (!$I) {
                            $html .= '<div class="border text-center h3 p-3 btn-danger">' . $this->Translate("Nothing To Do !!!") . '</div>';
                            $status = 0;
                        } else {
                            $xadded = $this->Comma($I + $start);
                            $xcount = $this->Comma($count);
                            $html = '<div class="border text-center h3 p-3 btn-success">' . sprintf("  تعداد %s رکورد دریافت و با موفقیت ثبت گردید.", $xadded, $xcount) . '</div>';
                            $status = 1;
                        }
                        if(file_exists($destupfile)) {
                            unlink($destupfile);
                        }
                        $html .= '<div class="lightScroll" id="13561977">';
                        $html .= '<table class="table">';
                        $outData = ["نوع فعالیت", "بخش"];
                        $html .= '<tr>';
                        $html .= '<th class="bg-border-white">#</th></th><th class="bg-border-white">' . implode('</th><th class="bg-border-white">', $outData) . '</th><th class="bg-border-white">' . implode('</th><th class="bg-border-white">', $_SESSION["DataUploadedSectionHeader"]) . '</th>';
                        $html .= '</tr>';
                        $key = 1;
                        foreach ($outDatas as $outData) {
                            $class = array_shift($outData);
                            $action = array_shift($outData);
                            $html .= '<tr>';
                            $html .= '<td>' . ($key + $start) . '</td>';
                            $html .= '<td class="text-white bg-' . $classcolor[$class] . '">' . $action . '</td><td class="text-white bg-' . (count($outData) < 4 ? 'secondary' : 'success') . '"><div>' . implode('</div></td><td><div>', $outData) . '</div></td>';
                            $html .= '</tr>';
                            $key++;
                        }
                        $html .= '</table>';
                        $html .= '</div>';
                    }
                    break;
            }
            if ($step == 1) {
                return [$status, $html];
            }
            $percent = 100 * ($start) / $count;
            if ($percent > 100) {
                $percent = 100;
            }
            $data = ["status" => $status, "html" => $html, "count" => $count, "start" => $start + $limit, "limit" => $limit, "percent" => $percent];
            $this->LoadJsonOut($data);
        } catch (Exception $E) {
            $message = $E->getMessage();
            $data = ["status" => $status,"html"=>$message];
            $this->LoadJsonOut($data);
        }
    }
}
