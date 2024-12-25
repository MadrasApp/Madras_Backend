<?php
/**
 * XLS parsing uses php-excel-reader from http://code.google.com/p/php-excel-reader/
 */
// Excel reader from http://code.google.com/p/php-excel-reader/
global $option, $compconfig, $app, $destupfile, $tempid;
$db = Factory::getContainer()->get('DatabaseDriver');
require('includes/excel_reader2.php');
require('includes/SpreadsheetReader.php');
$section = $app->input->getString("section");
date_default_timezone_set('UTC');

$StartMem       = memory_get_usage();
$message        = "";
$customupload = ["mybasket"];
$customfields = [
    "mybasket"=>["title","mellicode","personelcode","name","family","father","birthdate","mobile","position_id","post_id","start_date","product_id","amount"]
];
if(in_array($section,$customupload)){
    $headers = $customfields[$section];
} else {
    $headers = myerpController::LoadColumns($section);
}
if (count($headers))
{
	$O    = [];
	$O[]  = JHTML::_("select.option", "0", JText::_("JNONE"));
	$attr = 'class="required form-select" required';
	foreach ($headers as $kh => $vh)
	{
		if($vh)
		{
            $vt = str_replace('[]','',$vh);
            $vt = JText::_($section."_".$vt);
			$O[] = JHTML::_("select.option", $vh, $vt);
            $headers[$vh] = $vt;
		} else {
            unset($headers[$kh]);
        }
	}
	$flipped = array_flip($headers);
}

try
{
	$Spreadsheet = new SpreadsheetReader($destupfile);
	$BaseMem     = memory_get_usage();

	$query = "DELETE FROM `#__myerp_temp`";
	$db->setQuery($query);
	//echo "<P>".$db->replacePrefix( $query )."</P>";
	$db->execute();

    $Sheets    = $Spreadsheet->Sheets();
    $html      = '';
    $LastIndex = -1;
    $MaxCols   = 0;
    $field     = array();
    foreach ($Sheets as $Index => $Name)
    {
        $Time  = microtime(true);
        $Error = 0;
        $Spreadsheet->ChangeSheet($Index);
        $htmltemp = '';
        if ($LastIndex != $Index)
        {
            $htmltemp  = '<center><div class="btn">Sheet : ' . $Name . '</div></center>';
            $LastIndex = $Index;
        }
        $htmltemp   .= '<table class="xtable table table-striped" id="excelout" border="1">';
        $RowCounter = 0;
        foreach ($Spreadsheet as $Key => $Row)
        {
            if ($Row)
            {
                $htmltemp .= "<tr>";
                if ($RowCounter)
                {
                    $htmltemp            .= "<th>" . $Key . "</th>";
                    foreach ($Row as $k => $v)
                    {
                        $v = trim($v);
                        $v = myerpController::ChangeTextFarsi($v);
                        if ($k > 79 || (!isset($O[$k]) && !strlen($v)))
                        {
                            continue;
                        }
                        $k++;
                        $field[$Key][] = $v;
                        $htmltemp .= '<td>' . $v . '</td>';
                    }
                }
                else
                {
                    $RowCounter++;
                    $htmltemp .= "<th class='radif'>" . JText::_("RADIF") . "</th>";
                    foreach ($Row as $k => $v)
                    {
                        $v = trim($v);
                        if ($k > 79 || (!isset($O[$k]) && !strlen($v)))
                        {
                            continue;
                        }
                        $k++;
                        if (count($headers))
                        {
                            $value         = isset($flipped[$v]) ? $flipped[$v] : 0;
                            $fielsselector = JHtml::_('select.genericlist', $O, "fieldsdata[$k]", $attr, 'value', 'text', $value);
                            $v             .= "<br>".$fielsselector;
                        }
                        if (strlen($v))
                        {
                            $htmltemp .= '<td class="text-center">' . $v . '</td>';
                        }
                    }
                }
                $htmltemp .= "</tr>";
                $MaxCols  = $k;
            } else {
                $Error = 1;
            }
        }
        $htmltemp .= '</table>';
        if ($Error)
            $htmltemp = '';
        $html .= $htmltemp;
    }
    $queries = array();
    if (count($field))
    {
        foreach ($field as $k => $v)
        {
            $f1 = array();
            $f2 = array();
            foreach ($v as $k1 => $v1)
            {
                $v1 = trim($v1);
                $v1   = myerpController::ChangeTextFarsi($v1);
                $f1[] = "`field" . ($k1 + 1) . "`";
                $f2[] = $v1;
            }
            $queries[] = "INSERT IGNORE INTO `#__myerp_temp`(`tempid`," . implode(",", $f1) . ")VALUES ('$tempid','" . implode("','", $f2) . "')";
        }
    }
    echo '<input type="hidden" name="tempid" value="' . $tempid . '">';
    $I = 0;
    foreach ($queries as $query)
    {
        //echo "<P>" . $db->replacePrefix($query) . "</P>";
        $db->setQuery($query);
        if ($db->execute())
        {
            $I++;
        }
    }
    if (!$I)
    {
        $html = "<center><h1>خطا در تنظیمات عناوین یا عدم هماهنگی فایل اکسل با تنظیمات برنامه<h1></center>" .
            '<center><img width="90%" src="../components/' . $option . '/images/help/help.png" /></center>';
    }

	echo $message . $html;
	JFile::delete($destupfile);
}
catch (Exception $E)
{
	echo $E->getMessage();
}