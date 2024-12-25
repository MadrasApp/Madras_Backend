<?php
//$this->settings->data //Alireza Balvardi
?>
<style>
    .box {
        margin-bottom: 35px;
    }

    .picturts-form:hover {
        background-color: #eee;
    }

    p {
        margin-top: 10px;
    }
</style>

<div class="row page hash" id="home">
    <div class="col-sm-6">
        <div class="box">
            <form>
                <div class="box-title"><i class="fa fa-gear"></i> <span>تنظیمات عمومی</span></div>
                <div class="box-content">
                    <div class="clearfix">
                        <p><font color="red" size="+1">*</font> عنوان سایت <font color="#009999">( حداکثر 60 )</font>
                        </p>
                        <input type="text" name="data[title]" class="form-control" value="<?php echo @$title ?>"
                               maxlength="60"><br/>
                        <p><font color="red" size="+1">*</font> کلمات کلیدی متا <font color="#009999">( حداکثر 200
                                )</font></p>
                        <input type="text" name="data[meta_key]" class="form-control" value="<?php echo @$meta_key ?>"
                               maxlength="200"><br/>
                        <p><font color="red" size="+1">*</font> توضیحات متا <font color="#009999">( حداکثر 150 )</font>
                        </p>
                        <textarea name="data[meta_description]" class="form-control"
                                  maxlength="150"><?php echo @$meta_description ?></textarea><br/>
                        <p>شعار سایت</p>
                        <textarea name="data[slogan]" class="form-control"><?php echo @$slogan ?></textarea><br/>

                        <hr/>
                        <p>ورژن برنامه</p>
                        <input type="text" name="data[app_last_version]" class="form-control OnlyNumber en"
                               value="<?php echo @$app_last_version ?>"><br/>

                        <p>حداقل ورژن برنامه</p>
                        <input type="text" name="data[app_min_version]" class="form-control OnlyNumber en"
                               value="<?php echo @$app_min_version ?>"><br/>
                        <p>لینک برنامه</p>
                        <input type="text" name="data[app_file]" class="form-control en"
                               value="<?php echo @$app_file ?>">

                    </div>
                    <?php /* ?><hr/>
                    <div class="row">
                        <div class="col-sm-4">
                            <p>تایید نظرات</p>
                            <select name="data[auto_submit_comment]" class="form-control" data-val="<?php echo  $auto_submit_comment ?>">
                                <option value="1">خودکار</option>
                                <option value="0">به وسیله مدیر</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <p>تایید ثبت نام</p>
                            <select name="data[auto_submit_register]" class="form-control" data-val="<?php echo  $auto_submit_register ?>">
                                <option value="1">خودکار</option>
                                <option value="0">به وسیله مدیر</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <p>ثبت نظر</p>
                            <select name="data[user_can_comment]" class="form-control" data-val="<?php echo  $user_can_comment ?>">
                                <option value="1">همه</option>
                                <option value="0">فقط کاربران عضو شده</option>
                            </select>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-sm-4">
                            <p>تعداد مطالب در هر صفحه</p>
                            <select name="data[home_perpage]" class="form-control" data-val="<?php echo  $home_perpage ?>">
                                <?php for($i=10;$i<=200;$i+=10): ?>
                                    <option value="<?php echo  $i ?>"><?php echo  $i ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <p>تصویر امنیتی</p>
                            <select name="data[cap_protect]" class="form-control">
                                <option value="1">فعال</option>
                                <option value="0">غیر فعال</option>
                            </select>
                        </div>
                    </div><?php */ ?>
                    <div class="clearfix"></div>
                </div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
        </div>
        <div class="box">
            <form>
                <div class="box-title"><i class="fa fa-clock-o"></i> <span>تنظیمات زمان</span></div>
                <div class="box-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <p>فرمت تاریخ</p>
                            <?php $date_format_array = array("l J F ماه سال Y", "l J F Y", "l d F Y", "l d F y", "l Y/m/d", "J F y", "d F y", "Y/m/d") ?>
                            <select name="data[date_format]" id="d_format" class="form-control"
                                    data-val="<?php echo $date_format ?>">
                                <?php
                                $date = time();
                                foreach ($date_format_array as $date_format)
                                    echo '<option value="' . $date_format . '" label="' . jdate($date_format, $date) . '">' . jdate($date_format, $date) . '</option>';
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <p>فرمت زمان</p>
                            <?php $time_format_array = array("H:i", "h:i a", "h:i A", "ساعت H و i دقیقه و s ثانیه"); ?>
                            <select name="data[time_format]" class="form-control" data-val="<?php echo $time_format ?>">
                                <?php
                                foreach ($time_format_array as $time_format)
                                    echo '<option value="' . $time_format . '" label="' . jdate($time_format, $date) . '">' . jdate($time_format, $date) . '</option>';
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <p>منطقه زمانی</p>
                            <select name="data[time_zone]" class="form-control en" data-val="<?php echo $time_zone ?>">
                                <optgroup label="GMT-11">
                                    <option value="Pacific/Midway">GMT-11 Pacific/Midway</option>
                                    <option value="Pacific/Niue">GMT-11 Pacific/Niue</option>
                                    <option value="Pacific/Pago_Pago">GMT-11 Pacific/Pago_Pago</option>
                                    <option value="Pacific/Samoa">GMT-11 Pacific/Samoa</option>
                                    <option value="US/Samoa">GMT-11 US/Samoa</option>
                                </optgroup>
                                <optgroup label="GMT-10">
                                    <option value="America/Adak">GMT-10 America/Adak</option>
                                    <option value="America/Atka">GMT-10 America/Atka</option>
                                    <option value="HST">GMT-10 HST</option>
                                    <option value="Pacific/Honolulu">GMT-10 Pacific/Honolulu</option>
                                    <option value="Pacific/Johnston">GMT-10 Pacific/Johnston</option>
                                    <option value="Pacific/Rarotonga">GMT-10 Pacific/Rarotonga</option>
                                    <option value="Pacific/Tahiti">GMT-10 Pacific/Tahiti</option>
                                    <option value="US/Aleutian">GMT-10 US/Aleutian</option>
                                    <option value="US/Hawaii">GMT-10 US/Hawaii</option>
                                </optgroup>
                                <optgroup label="GMT-9.5">
                                    <option value="Pacific/Marquesas">GMT-9.5 Pacific/Marquesas</option>
                                </optgroup>
                                <optgroup label="GMT-9">
                                    <option value="America/Anchorage">GMT-9 America/Anchorage</option>
                                    <option value="America/Juneau">GMT-9 America/Juneau</option>
                                    <option value="America/Nome">GMT-9 America/Nome</option>
                                    <option value="America/Sitka">GMT-9 America/Sitka</option>
                                    <option value="America/Yakutat">GMT-9 America/Yakutat</option>
                                    <option value="Pacific/Gambier">GMT-9 Pacific/Gambier</option>
                                    <option value="US/Alaska">GMT-9 US/Alaska</option>
                                </optgroup>
                                <optgroup label="GMT-8">
                                    <option value="America/Dawson">GMT-8 America/Dawson</option>
                                    <option value="America/Ensenada">GMT-8 America/Ensenada</option>
                                    <option value="America/Los_Angeles">GMT-8 America/Los_Angeles</option>
                                    <option value="America/Metlakatla">GMT-8 America/Metlakatla</option>
                                    <option value="America/Santa_Isabel">GMT-8 America/Santa_Isabel</option>
                                    <option value="America/Tijuana">GMT-8 America/Tijuana</option>
                                    <option value="America/Vancouver">GMT-8 America/Vancouver</option>
                                    <option value="America/Whitehorse">GMT-8 America/Whitehorse</option>
                                    <option value="Canada/Pacific">GMT-8 Canada/Pacific</option>
                                    <option value="Canada/Yukon">GMT-8 Canada/Yukon</option>
                                    <option value="Mexico/BajaNorte">GMT-8 Mexico/BajaNorte</option>
                                    <option value="Pacific/Pitcairn">GMT-8 Pacific/Pitcairn</option>
                                    <option value="PST8PDT">GMT-8 PST8PDT</option>
                                    <option value="US/Pacific">GMT-8 US/Pacific</option>
                                    <option value="US/Pacific-New">GMT-8 US/Pacific-New</option>
                                </optgroup>
                                <optgroup label="GMT-7">
                                    <option value="America/Boise">GMT-7 America/Boise</option>
                                    <option value="America/Cambridge_Bay">GMT-7 America/Cambridge_Bay</option>
                                    <option value="America/Chihuahua">GMT-7 America/Chihuahua</option>
                                    <option value="America/Creston">GMT-7 America/Creston</option>
                                    <option value="America/Dawson_Creek">GMT-7 America/Dawson_Creek</option>
                                    <option value="America/Denver">GMT-7 America/Denver</option>
                                    <option value="America/Edmonton">GMT-7 America/Edmonton</option>
                                    <option value="America/Hermosillo">GMT-7 America/Hermosillo</option>
                                    <option value="America/Inuvik">GMT-7 America/Inuvik</option>
                                    <option value="America/Mazatlan">GMT-7 America/Mazatlan</option>
                                    <option value="America/Ojinaga">GMT-7 America/Ojinaga</option>
                                    <option value="America/Phoenix">GMT-7 America/Phoenix</option>
                                    <option value="America/Shiprock">GMT-7 America/Shiprock</option>
                                    <option value="America/Yellowknife">GMT-7 America/Yellowknife</option>
                                    <option value="Canada/Mountain">GMT-7 Canada/Mountain</option>
                                    <option value="Mexico/BajaSur">GMT-7 Mexico/BajaSur</option>
                                    <option value="MST">GMT-7 MST</option>
                                    <option value="MST7MDT">GMT-7 MST7MDT</option>
                                    <option value="Navajo">GMT-7 Navajo</option>
                                    <option value="US/Arizona">GMT-7 US/Arizona</option>
                                    <option value="US/Mountain">GMT-7 US/Mountain</option>
                                </optgroup>
                                <optgroup label="GMT-6">
                                    <option value="America/Bahia_Banderas">GMT-6 America/Bahia_Banderas</option>
                                    <option value="America/Belize">GMT-6 America/Belize</option>
                                    <option value="America/Cancun">GMT-6 America/Cancun</option>
                                    <option value="America/Chicago">GMT-6 America/Chicago</option>
                                    <option value="America/Costa_Rica">GMT-6 America/Costa_Rica</option>
                                    <option value="America/El_Salvador">GMT-6 America/El_Salvador</option>
                                    <option value="America/Guatemala">GMT-6 America/Guatemala</option>
                                    <option value="America/Indiana/Knox">GMT-6 America/Indiana/Knox</option>
                                    <option value="America/Indiana/Tell_City">GMT-6 America/Indiana/Tell_City</option>
                                    <option value="America/Knox_IN">GMT-6 America/Knox_IN</option>
                                    <option value="America/Managua">GMT-6 America/Managua</option>
                                    <option value="America/Matamoros">GMT-6 America/Matamoros</option>
                                    <option value="America/Menominee">GMT-6 America/Menominee</option>
                                    <option value="America/Merida">GMT-6 America/Merida</option>
                                    <option value="America/Mexico_City">GMT-6 America/Mexico_City</option>
                                    <option value="America/Monterrey">GMT-6 America/Monterrey</option>
                                    <option value="America/North_Dakota/Beulah">GMT-6 America/North_Dakota/Beulah
                                    </option>
                                    <option value="America/North_Dakota/Center">GMT-6 America/North_Dakota/Center
                                    </option>
                                    <option value="America/North_Dakota/New_Salem">GMT-6 America/North_Dakota/New_Salem
                                    </option>
                                    <option value="America/Rainy_River">GMT-6 America/Rainy_River</option>
                                    <option value="America/Rankin_Inlet">GMT-6 America/Rankin_Inlet</option>
                                    <option value="America/Regina">GMT-6 America/Regina</option>
                                    <option value="America/Resolute">GMT-6 America/Resolute</option>
                                    <option value="America/Swift_Current">GMT-6 America/Swift_Current</option>
                                    <option value="America/Tegucigalpa">GMT-6 America/Tegucigalpa</option>
                                    <option value="America/Winnipeg">GMT-6 America/Winnipeg</option>
                                    <option value="Canada/Central">GMT-6 Canada/Central</option>
                                    <option value="Canada/East-Saskatchewan">GMT-6 Canada/East-Saskatchewan</option>
                                    <option value="Canada/Saskatchewan">GMT-6 Canada/Saskatchewan</option>
                                    <option value="CST6CDT">GMT-6 CST6CDT</option>
                                    <option value="Mexico/General">GMT-6 Mexico/General</option>
                                    <option value="Pacific/Galapagos">GMT-6 Pacific/Galapagos</option>
                                    <option value="US/Central">GMT-6 US/Central</option>
                                    <option value="US/Indiana-Starke">GMT-6 US/Indiana-Starke</option>
                                </optgroup>
                                <optgroup label="GMT-5">
                                    <option value="America/Atikokan">GMT-5 America/Atikokan</option>
                                    <option value="America/Bogota">GMT-5 America/Bogota</option>
                                    <option value="America/Cayman">GMT-5 America/Cayman</option>
                                    <option value="America/Coral_Harbour">GMT-5 America/Coral_Harbour</option>
                                    <option value="America/Detroit">GMT-5 America/Detroit</option>
                                    <option value="America/Eirunepe">GMT-5 America/Eirunepe</option>
                                    <option value="America/Fort_Wayne">GMT-5 America/Fort_Wayne</option>
                                    <option value="America/Guayaquil">GMT-5 America/Guayaquil</option>
                                    <option value="America/Havana">GMT-5 America/Havana</option>
                                    <option value="America/Indiana/Indianapolis">GMT-5 America/Indiana/Indianapolis
                                    </option>
                                    <option value="America/Indiana/Marengo">GMT-5 America/Indiana/Marengo</option>
                                    <option value="America/Indiana/Petersburg">GMT-5 America/Indiana/Petersburg</option>
                                    <option value="America/Indiana/Vevay">GMT-5 America/Indiana/Vevay</option>
                                    <option value="America/Indiana/Vincennes">GMT-5 America/Indiana/Vincennes</option>
                                    <option value="America/Indiana/Winamac">GMT-5 America/Indiana/Winamac</option>
                                    <option value="America/Indianapolis">GMT-5 America/Indianapolis</option>
                                    <option value="America/Iqaluit">GMT-5 America/Iqaluit</option>
                                    <option value="America/Jamaica">GMT-5 America/Jamaica</option>
                                    <option value="America/Kentucky/Louisville">GMT-5 America/Kentucky/Louisville
                                    </option>
                                    <option value="America/Kentucky/Monticello">GMT-5 America/Kentucky/Monticello
                                    </option>
                                    <option value="America/Lima">GMT-5 America/Lima</option>
                                    <option value="America/Louisville">GMT-5 America/Louisville</option>
                                    <option value="America/Montreal">GMT-5 America/Montreal</option>
                                    <option value="America/Nassau">GMT-5 America/Nassau</option>
                                    <option value="America/New_York">GMT-5 America/New_York</option>
                                    <option value="America/Nipigon">GMT-5 America/Nipigon</option>
                                    <option value="America/Panama">GMT-5 America/Panama</option>
                                    <option value="America/Pangnirtung">GMT-5 America/Pangnirtung</option>
                                    <option value="America/Port-au-Prince">GMT-5 America/Port-au-Prince</option>
                                    <option value="America/Porto_Acre">GMT-5 America/Porto_Acre</option>
                                    <option value="America/Rio_Branco">GMT-5 America/Rio_Branco</option>
                                    <option value="America/Thunder_Bay">GMT-5 America/Thunder_Bay</option>
                                    <option value="America/Toronto">GMT-5 America/Toronto</option>
                                    <option value="Brazil/Acre">GMT-5 Brazil/Acre</option>
                                    <option value="Canada/Eastern">GMT-5 Canada/Eastern</option>
                                    <option value="Chile/EasterIsland">GMT-5 Chile/EasterIsland</option>
                                    <option value="Cuba">GMT-5 Cuba</option>
                                    <option value="EST">GMT-5 EST</option>
                                    <option value="EST5EDT">GMT-5 EST5EDT</option>
                                    <option value="Jamaica">GMT-5 Jamaica</option>
                                    <option value="Pacific/Easter">GMT-5 Pacific/Easter</option>
                                    <option value="US/East-Indiana">GMT-5 US/East-Indiana</option>
                                    <option value="US/Eastern">GMT-5 US/Eastern</option>
                                    <option value="US/Michigan">GMT-5 US/Michigan</option>
                                </optgroup>
                                <optgroup label="GMT-4.5">
                                    <option value="America/Caracas">GMT-4.5 America/Caracas</option>
                                </optgroup>
                                <optgroup label="GMT-4">
                                    <option value="America/Anguilla">GMT-4 America/Anguilla</option>
                                    <option value="America/Antigua">GMT-4 America/Antigua</option>
                                    <option value="America/Aruba">GMT-4 America/Aruba</option>
                                    <option value="America/Barbados">GMT-4 America/Barbados</option>
                                    <option value="America/Blanc-Sablon">GMT-4 America/Blanc-Sablon</option>
                                    <option value="America/Boa_Vista">GMT-4 America/Boa_Vista</option>
                                    <option value="America/Curacao">GMT-4 America/Curacao</option>
                                    <option value="America/Dominica">GMT-4 America/Dominica</option>
                                    <option value="America/Glace_Bay">GMT-4 America/Glace_Bay</option>
                                    <option value="America/Goose_Bay">GMT-4 America/Goose_Bay</option>
                                    <option value="America/Grand_Turk">GMT-4 America/Grand_Turk</option>
                                    <option value="America/Grenada">GMT-4 America/Grenada</option>
                                    <option value="America/Guadeloupe">GMT-4 America/Guadeloupe</option>
                                    <option value="America/Guyana">GMT-4 America/Guyana</option>
                                    <option value="America/Halifax">GMT-4 America/Halifax</option>
                                    <option value="America/Kralendijk">GMT-4 America/Kralendijk</option>
                                    <option value="America/La_Paz">GMT-4 America/La_Paz</option>
                                    <option value="America/Lower_Princes">GMT-4 America/Lower_Princes</option>
                                    <option value="America/Manaus">GMT-4 America/Manaus</option>
                                    <option value="America/Marigot">GMT-4 America/Marigot</option>
                                    <option value="America/Martinique">GMT-4 America/Martinique</option>
                                    <option value="America/Moncton">GMT-4 America/Moncton</option>
                                    <option value="America/Montserrat">GMT-4 America/Montserrat</option>
                                    <option value="America/Port_of_Spain">GMT-4 America/Port_of_Spain</option>
                                    <option value="America/Porto_Velho">GMT-4 America/Porto_Velho</option>
                                    <option value="America/Puerto_Rico">GMT-4 America/Puerto_Rico</option>
                                    <option value="America/Santo_Domingo">GMT-4 America/Santo_Domingo</option>
                                    <option value="America/St_Barthelemy">GMT-4 America/St_Barthelemy</option>
                                    <option value="America/St_Kitts">GMT-4 America/St_Kitts</option>
                                    <option value="America/St_Lucia">GMT-4 America/St_Lucia</option>
                                    <option value="America/St_Thomas">GMT-4 America/St_Thomas</option>
                                    <option value="America/St_Vincent">GMT-4 America/St_Vincent</option>
                                    <option value="America/Thule">GMT-4 America/Thule</option>
                                    <option value="America/Tortola">GMT-4 America/Tortola</option>
                                    <option value="America/Virgin">GMT-4 America/Virgin</option>
                                    <option value="Atlantic/Bermuda">GMT-4 Atlantic/Bermuda</option>
                                    <option value="Brazil/West">GMT-4 Brazil/West</option>
                                    <option value="Canada/Atlantic">GMT-4 Canada/Atlantic</option>
                                </optgroup>
                                <optgroup label="GMT-3.5">
                                    <option value="America/St_Johns">GMT-3.5 America/St_Johns</option>
                                    <option value="Canada/Newfoundland">GMT-3.5 Canada/Newfoundland</option>
                                </optgroup>
                                <optgroup label="GMT-3">
                                    <option value="America/Araguaina">GMT-3 America/Araguaina</option>
                                    <option value="America/Argentina/Buenos_Aires">GMT-3 America/Argentina/Buenos_Aires
                                    </option>
                                    <option value="America/Argentina/Catamarca">GMT-3 America/Argentina/Catamarca
                                    </option>
                                    <option value="America/Argentina/ComodRivadavia">GMT-3
                                        America/Argentina/ComodRivadavia
                                    </option>
                                    <option value="America/Argentina/Cordoba">GMT-3 America/Argentina/Cordoba</option>
                                    <option value="America/Argentina/Jujuy">GMT-3 America/Argentina/Jujuy</option>
                                    <option value="America/Argentina/La_Rioja">GMT-3 America/Argentina/La_Rioja</option>
                                    <option value="America/Argentina/Mendoza">GMT-3 America/Argentina/Mendoza</option>
                                    <option value="America/Argentina/Rio_Gallegos">GMT-3 America/Argentina/Rio_Gallegos
                                    </option>
                                    <option value="America/Argentina/Salta">GMT-3 America/Argentina/Salta</option>
                                    <option value="America/Argentina/San_Juan">GMT-3 America/Argentina/San_Juan</option>
                                    <option value="America/Argentina/San_Luis">GMT-3 America/Argentina/San_Luis</option>
                                    <option value="America/Argentina/Tucuman">GMT-3 America/Argentina/Tucuman</option>
                                    <option value="America/Argentina/Ushuaia">GMT-3 America/Argentina/Ushuaia</option>
                                    <option value="America/Asuncion">GMT-3 America/Asuncion</option>
                                    <option value="America/Bahia">GMT-3 America/Bahia</option>
                                    <option value="America/Belem">GMT-3 America/Belem</option>
                                    <option value="America/Buenos_Aires">GMT-3 America/Buenos_Aires</option>
                                    <option value="America/Campo_Grande">GMT-3 America/Campo_Grande</option>
                                    <option value="America/Catamarca">GMT-3 America/Catamarca</option>
                                    <option value="America/Cayenne">GMT-3 America/Cayenne</option>
                                    <option value="America/Cordoba">GMT-3 America/Cordoba</option>
                                    <option value="America/Cuiaba">GMT-3 America/Cuiaba</option>
                                    <option value="America/Fortaleza">GMT-3 America/Fortaleza</option>
                                    <option value="America/Godthab">GMT-3 America/Godthab</option>
                                    <option value="America/Jujuy">GMT-3 America/Jujuy</option>
                                    <option value="America/Maceio">GMT-3 America/Maceio</option>
                                    <option value="America/Mendoza">GMT-3 America/Mendoza</option>
                                    <option value="America/Miquelon">GMT-3 America/Miquelon</option>
                                    <option value="America/Paramaribo">GMT-3 America/Paramaribo</option>
                                    <option value="America/Recife">GMT-3 America/Recife</option>
                                    <option value="America/Rosario">GMT-3 America/Rosario</option>
                                    <option value="America/Santarem">GMT-3 America/Santarem</option>
                                    <option value="America/Santiago">GMT-3 America/Santiago</option>
                                    <option value="Antarctica/Palmer">GMT-3 Antarctica/Palmer</option>
                                    <option value="Antarctica/Rothera">GMT-3 Antarctica/Rothera</option>
                                    <option value="Atlantic/Stanley">GMT-3 Atlantic/Stanley</option>
                                    <option value="Chile/Continental">GMT-3 Chile/Continental</option>
                                </optgroup>
                                <optgroup label="GMT-2">
                                    <option value="America/Montevideo">GMT-2 America/Montevideo</option>
                                    <option value="America/Noronha">GMT-2 America/Noronha</option>
                                    <option value="America/Sao_Paulo">GMT-2 America/Sao_Paulo</option>
                                    <option value="Atlantic/South_Georgia">GMT-2 Atlantic/South_Georgia</option>
                                    <option value="Brazil/DeNoronha">GMT-2 Brazil/DeNoronha</option>
                                    <option value="Brazil/East">GMT-2 Brazil/East</option>
                                </optgroup>
                                <optgroup label="GMT-1">
                                    <option value="America/Scoresbysund">GMT-1 America/Scoresbysund</option>
                                    <option value="Atlantic/Azores">GMT-1 Atlantic/Azores</option>
                                    <option value="Atlantic/Cape_Verde">GMT-1 Atlantic/Cape_Verde</option>
                                </optgroup>
                                <optgroup label="GMT">
                                    <option value="GMT">GMT GMT</option>
                                    <option value="Africa/Abidjan">GMT Africa/Abidjan</option>
                                    <option value="Africa/Accra">GMT Africa/Accra</option>
                                    <option value="Africa/Bamako">GMT Africa/Bamako</option>
                                    <option value="Africa/Banjul">GMT Africa/Banjul</option>
                                    <option value="Africa/Bissau">GMT Africa/Bissau</option>
                                    <option value="Africa/Casablanca">GMT Africa/Casablanca</option>
                                    <option value="Africa/Conakry">GMT Africa/Conakry</option>
                                    <option value="Africa/Dakar">GMT Africa/Dakar</option>
                                    <option value="Africa/El_Aaiun">GMT Africa/El_Aaiun</option>
                                    <option value="Africa/Freetown">GMT Africa/Freetown</option>
                                    <option value="Africa/Lome">GMT Africa/Lome</option>
                                    <option value="Africa/Monrovia">GMT Africa/Monrovia</option>
                                    <option value="Africa/Nouakchott">GMT Africa/Nouakchott</option>
                                    <option value="Africa/Ouagadougou">GMT Africa/Ouagadougou</option>
                                    <option value="Africa/Sao_Tome">GMT Africa/Sao_Tome</option>
                                    <option value="Africa/Timbuktu">GMT Africa/Timbuktu</option>
                                    <option value="America/Danmarkshavn">GMT America/Danmarkshavn</option>
                                    <option value="Atlantic/Canary">GMT Atlantic/Canary</option>
                                    <option value="Atlantic/Faeroe">GMT Atlantic/Faeroe</option>
                                    <option value="Atlantic/Faroe">GMT Atlantic/Faroe</option>
                                    <option value="Atlantic/Madeira">GMT Atlantic/Madeira</option>
                                    <option value="Atlantic/Reykjavik">GMT Atlantic/Reykjavik</option>
                                    <option value="Atlantic/St_Helena">GMT Atlantic/St_Helena</option>
                                    <option value="Eire">GMT Eire</option>
                                    <option value="Europe/Belfast">GMT Europe/Belfast</option>
                                    <option value="Europe/Dublin">GMT Europe/Dublin</option>
                                    <option value="Europe/Guernsey">GMT Europe/Guernsey</option>
                                    <option value="Europe/Isle_of_Man">GMT Europe/Isle_of_Man</option>
                                    <option value="Europe/Jersey">GMT Europe/Jersey</option>
                                    <option value="Europe/Lisbon">GMT Europe/Lisbon</option>
                                    <option value="Europe/London">GMT Europe/London</option>
                                    <option value="Factory">GMT Factory</option>
                                    <option value="GB">GMT GB</option>
                                    <option value="GB-Eire">GMT GB-Eire</option>
                                    <option value="Greenwich">GMT Greenwich</option>
                                    <option value="Iceland">GMT Iceland</option>
                                    <option value="Portugal">GMT Portugal</option>
                                    <option value="UCT">GMT UCT</option>
                                    <option value="Universal">GMT Universal</option>
                                    <option value="UTC">GMT UTC</option>
                                    <option value="WET">GMT WET</option>
                                    <option value="Zulu">GMT Zulu</option>
                                </optgroup>
                                <optgroup label="GMT+1">
                                    <option value="Africa/Algiers">GMT+1 Africa/Algiers</option>
                                    <option value="Africa/Bangui">GMT+1 Africa/Bangui</option>
                                    <option value="Africa/Brazzaville">GMT+1 Africa/Brazzaville</option>
                                    <option value="Africa/Ceuta">GMT+1 Africa/Ceuta</option>
                                    <option value="Africa/Douala">GMT+1 Africa/Douala</option>
                                    <option value="Africa/Kinshasa">GMT+1 Africa/Kinshasa</option>
                                    <option value="Africa/Lagos">GMT+1 Africa/Lagos</option>
                                    <option value="Africa/Libreville">GMT+1 Africa/Libreville</option>
                                    <option value="Africa/Luanda">GMT+1 Africa/Luanda</option>
                                    <option value="Africa/Malabo">GMT+1 Africa/Malabo</option>
                                    <option value="Africa/Ndjamena">GMT+1 Africa/Ndjamena</option>
                                    <option value="Africa/Niamey">GMT+1 Africa/Niamey</option>
                                    <option value="Africa/Porto-Novo">GMT+1 Africa/Porto-Novo</option>
                                    <option value="Africa/Tunis">GMT+1 Africa/Tunis</option>
                                    <option value="Arctic/Longyearbyen">GMT+1 Arctic/Longyearbyen</option>
                                    <option value="Atlantic/Jan_Mayen">GMT+1 Atlantic/Jan_Mayen</option>
                                    <option value="CET">GMT+1 CET</option>
                                    <option value="Europe/Amsterdam">GMT+1 Europe/Amsterdam</option>
                                    <option value="Europe/Andorra">GMT+1 Europe/Andorra</option>
                                    <option value="Europe/Belgrade">GMT+1 Europe/Belgrade</option>
                                    <option value="Europe/Berlin">GMT+1 Europe/Berlin</option>
                                    <option value="Europe/Bratislava">GMT+1 Europe/Bratislava</option>
                                    <option value="Europe/Brussels">GMT+1 Europe/Brussels</option>
                                    <option value="Europe/Budapest">GMT+1 Europe/Budapest</option>
                                    <option value="Europe/Copenhagen">GMT+1 Europe/Copenhagen</option>
                                    <option value="Europe/Gibraltar">GMT+1 Europe/Gibraltar</option>
                                    <option value="Europe/Ljubljana">GMT+1 Europe/Ljubljana</option>
                                    <option value="Europe/Luxembourg">GMT+1 Europe/Luxembourg</option>
                                    <option value="Europe/Madrid">GMT+1 Europe/Madrid</option>
                                    <option value="Europe/Malta">GMT+1 Europe/Malta</option>
                                    <option value="Europe/Monaco">GMT+1 Europe/Monaco</option>
                                    <option value="Europe/Oslo">GMT+1 Europe/Oslo</option>
                                    <option value="Europe/Paris">GMT+1 Europe/Paris</option>
                                    <option value="Europe/Podgorica">GMT+1 Europe/Podgorica</option>
                                    <option value="Europe/Prague">GMT+1 Europe/Prague</option>
                                    <option value="Europe/Rome">GMT+1 Europe/Rome</option>
                                    <option value="Europe/San_Marino">GMT+1 Europe/San_Marino</option>
                                    <option value="Europe/Sarajevo">GMT+1 Europe/Sarajevo</option>
                                    <option value="Europe/Skopje">GMT+1 Europe/Skopje</option>
                                    <option value="Europe/Stockholm">GMT+1 Europe/Stockholm</option>
                                    <option value="Europe/Tirane">GMT+1 Europe/Tirane</option>
                                    <option value="Europe/Vaduz">GMT+1 Europe/Vaduz</option>
                                    <option value="Europe/Vatican">GMT+1 Europe/Vatican</option>
                                    <option value="Europe/Vienna">GMT+1 Europe/Vienna</option>
                                    <option value="Europe/Warsaw">GMT+1 Europe/Warsaw</option>
                                    <option value="Europe/Zagreb">GMT+1 Europe/Zagreb</option>
                                    <option value="Europe/Zurich">GMT+1 Europe/Zurich</option>
                                    <option value="MET">GMT+1 MET</option>
                                    <option value="Poland">GMT+1 Poland</option>
                                </optgroup>
                                <optgroup label="GMT+2">
                                    <option value="Africa/Blantyre">GMT+2 Africa/Blantyre</option>
                                    <option value="Africa/Bujumbura">GMT+2 Africa/Bujumbura</option>
                                    <option value="Africa/Cairo">GMT+2 Africa/Cairo</option>
                                    <option value="Africa/Gaborone">GMT+2 Africa/Gaborone</option>
                                    <option value="Africa/Harare">GMT+2 Africa/Harare</option>
                                    <option value="Africa/Johannesburg">GMT+2 Africa/Johannesburg</option>
                                    <option value="Africa/Kigali">GMT+2 Africa/Kigali</option>
                                    <option value="Africa/Lubumbashi">GMT+2 Africa/Lubumbashi</option>
                                    <option value="Africa/Lusaka">GMT+2 Africa/Lusaka</option>
                                    <option value="Africa/Maputo">GMT+2 Africa/Maputo</option>
                                    <option value="Africa/Maseru">GMT+2 Africa/Maseru</option>
                                    <option value="Africa/Mbabane">GMT+2 Africa/Mbabane</option>
                                    <option value="Africa/Tripoli">GMT+2 Africa/Tripoli</option>
                                    <option value="Africa/Windhoek">GMT+2 Africa/Windhoek</option>
                                    <option value="Asia/Amman">GMT+2 Asia/Amman</option>
                                    <option value="Asia/Beirut">GMT+2 Asia/Beirut</option>
                                    <option value="Asia/Damascus">GMT+2 Asia/Damascus</option>
                                    <option value="Asia/Gaza">GMT+2 Asia/Gaza</option>
                                    <option value="Asia/Hebron">GMT+2 Asia/Hebron</option>
                                    <option value="Asia/Istanbul">GMT+2 Asia/Istanbul</option>
                                    <option value="Asia/Jerusalem">GMT+2 Asia/Jerusalem</option>
                                    <option value="Asia/Nicosia">GMT+2 Asia/Nicosia</option>
                                    <option value="Asia/Tel_Aviv">GMT+2 Asia/Tel_Aviv</option>
                                    <option value="EET">GMT+2 EET</option>
                                    <option value="Egypt">GMT+2 Egypt</option>
                                    <option value="Europe/Athens">GMT+2 Europe/Athens</option>
                                    <option value="Europe/Bucharest">GMT+2 Europe/Bucharest</option>
                                    <option value="Europe/Chisinau">GMT+2 Europe/Chisinau</option>
                                    <option value="Europe/Helsinki">GMT+2 Europe/Helsinki</option>
                                    <option value="Europe/Istanbul">GMT+2 Europe/Istanbul</option>
                                    <option value="Europe/Kaliningrad">GMT+2 Europe/Kaliningrad</option>
                                    <option value="Europe/Kiev">GMT+2 Europe/Kiev</option>
                                    <option value="Europe/Mariehamn">GMT+2 Europe/Mariehamn</option>
                                    <option value="Europe/Nicosia">GMT+2 Europe/Nicosia</option>
                                    <option value="Europe/Riga">GMT+2 Europe/Riga</option>
                                    <option value="Europe/Sofia">GMT+2 Europe/Sofia</option>
                                    <option value="Europe/Tallinn">GMT+2 Europe/Tallinn</option>
                                    <option value="Europe/Tiraspol">GMT+2 Europe/Tiraspol</option>
                                    <option value="Europe/Uzhgorod">GMT+2 Europe/Uzhgorod</option>
                                    <option value="Europe/Vilnius">GMT+2 Europe/Vilnius</option>
                                    <option value="Europe/Zaporozhye">GMT+2 Europe/Zaporozhye</option>
                                    <option value="Israel">GMT+2 Israel</option>
                                    <option value="Libya">GMT+2 Libya</option>
                                    <option value="Turkey">GMT+2 Turkey</option>
                                </optgroup>
                                <optgroup label="GMT+3">
                                    <option value="Africa/Addis_Ababa">GMT+3 Africa/Addis_Ababa</option>
                                    <option value="Africa/Asmara">GMT+3 Africa/Asmara</option>
                                    <option value="Africa/Asmera">GMT+3 Africa/Asmera</option>
                                    <option value="Africa/Dar_es_Salaam">GMT+3 Africa/Dar_es_Salaam</option>
                                    <option value="Africa/Djibouti">GMT+3 Africa/Djibouti</option>
                                    <option value="Africa/Juba">GMT+3 Africa/Juba</option>
                                    <option value="Africa/Kampala">GMT+3 Africa/Kampala</option>
                                    <option value="Africa/Khartoum">GMT+3 Africa/Khartoum</option>
                                    <option value="Africa/Mogadishu">GMT+3 Africa/Mogadishu</option>
                                    <option value="Africa/Nairobi">GMT+3 Africa/Nairobi</option>
                                    <option value="Antarctica/Syowa">GMT+3 Antarctica/Syowa</option>
                                    <option value="Asia/Aden">GMT+3 Asia/Aden</option>
                                    <option value="Asia/Baghdad">GMT+3 Asia/Baghdad</option>
                                    <option value="Asia/Bahrain">GMT+3 Asia/Bahrain</option>
                                    <option value="Asia/Kuwait">GMT+3 Asia/Kuwait</option>
                                    <option value="Asia/Qatar">GMT+3 Asia/Qatar</option>
                                    <option value="Asia/Riyadh">GMT+3 Asia/Riyadh</option>
                                    <option value="Europe/Minsk">GMT+3 Europe/Minsk</option>
                                    <option value="Europe/Moscow">GMT+3 Europe/Moscow</option>
                                    <option value="Europe/Simferopol">GMT+3 Europe/Simferopol</option>
                                    <option value="Europe/Volgograd">GMT+3 Europe/Volgograd</option>
                                    <option value="Indian/Antananarivo">GMT+3 Indian/Antananarivo</option>
                                    <option value="Indian/Comoro">GMT+3 Indian/Comoro</option>
                                    <option value="Indian/Mayotte">GMT+3 Indian/Mayotte</option>
                                    <option value="W-SU">GMT+3 W-SU</option>
                                </optgroup>
                                <optgroup label="GMT+3.5">
                                    <option value="Asia/Tehran">GMT+3.5 تهران</option>
                                </optgroup>
                                <optgroup label="GMT+4">
                                    <option value="Asia/Baku">GMT+4 Asia/Baku</option>
                                    <option value="Asia/Dubai">GMT+4 Asia/Dubai</option>
                                    <option value="Asia/Muscat">GMT+4 Asia/Muscat</option>
                                    <option value="Asia/Tbilisi">GMT+4 Asia/Tbilisi</option>
                                    <option value="Asia/Yerevan">GMT+4 Asia/Yerevan</option>
                                    <option value="Europe/Samara">GMT+4 Europe/Samara</option>
                                    <option value="Indian/Mahe">GMT+4 Indian/Mahe</option>
                                    <option value="Indian/Mauritius">GMT+4 Indian/Mauritius</option>
                                    <option value="Indian/Reunion">GMT+4 Indian/Reunion</option>
                                </optgroup>
                                <optgroup label="GMT+4.5">
                                    <option value="Asia/Kabul">GMT+4.5 Asia/Kabul</option>
                                </optgroup>
                                <optgroup label="GMT+5">
                                    <option value="Antarctica/Mawson">GMT+5 Antarctica/Mawson</option>
                                    <option value="Asia/Aqtau">GMT+5 Asia/Aqtau</option>
                                    <option value="Asia/Aqtobe">GMT+5 Asia/Aqtobe</option>
                                    <option value="Asia/Ashgabat">GMT+5 Asia/Ashgabat</option>
                                    <option value="Asia/Ashkhabad">GMT+5 Asia/Ashkhabad</option>
                                    <option value="Asia/Dushanbe">GMT+5 Asia/Dushanbe</option>
                                    <option value="Asia/Karachi">GMT+5 Asia/Karachi</option>
                                    <option value="Asia/Oral">GMT+5 Asia/Oral</option>
                                    <option value="Asia/Samarkand">GMT+5 Asia/Samarkand</option>
                                    <option value="Asia/Tashkent">GMT+5 Asia/Tashkent</option>
                                    <option value="Asia/Yekaterinburg">GMT+5 Asia/Yekaterinburg</option>
                                    <option value="Indian/Kerguelen">GMT+5 Indian/Kerguelen</option>
                                    <option value="Indian/Maldives">GMT+5 Indian/Maldives</option>
                                </optgroup>
                                <optgroup label="GMT+5.5">
                                    <option value="Asia/Calcutta">GMT+5.5 Asia/Calcutta</option>
                                    <option value="Asia/Colombo">GMT+5.5 Asia/Colombo</option>
                                    <option value="Asia/Kolkata">GMT+5.5 Asia/Kolkata</option>
                                </optgroup>
                                <optgroup label="GMT+5.75">
                                    <option value="Asia/Kathmandu">GMT+5.75 Asia/Kathmandu</option>
                                    <option value="Asia/Katmandu">GMT+5.75 Asia/Katmandu</option>
                                </optgroup>
                                <optgroup label="GMT+6">
                                    <option value="Antarctica/Vostok">GMT+6 Antarctica/Vostok</option>
                                    <option value="Asia/Almaty">GMT+6 Asia/Almaty</option>
                                    <option value="Asia/Bishkek">GMT+6 Asia/Bishkek</option>
                                    <option value="Asia/Dacca">GMT+6 Asia/Dacca</option>
                                    <option value="Asia/Dhaka">GMT+6 Asia/Dhaka</option>
                                    <option value="Asia/Kashgar">GMT+6 Asia/Kashgar</option>
                                    <option value="Asia/Novosibirsk">GMT+6 Asia/Novosibirsk</option>
                                    <option value="Asia/Omsk">GMT+6 Asia/Omsk</option>
                                    <option value="Asia/Qyzylorda">GMT+6 Asia/Qyzylorda</option>
                                    <option value="Asia/Thimbu">GMT+6 Asia/Thimbu</option>
                                    <option value="Asia/Thimphu">GMT+6 Asia/Thimphu</option>
                                    <option value="Asia/Urumqi">GMT+6 Asia/Urumqi</option>
                                    <option value="Indian/Chagos">GMT+6 Indian/Chagos</option>
                                </optgroup>
                                <optgroup label="GMT+6.5">
                                    <option value="Asia/Rangoon">GMT+6.5 Asia/Rangoon</option>
                                    <option value="Indian/Cocos">GMT+6.5 Indian/Cocos</option>
                                </optgroup>
                                <optgroup label="GMT+7">
                                    <option value="Antarctica/Davis">GMT+7 Antarctica/Davis</option>
                                    <option value="Asia/Bangkok">GMT+7 Asia/Bangkok</option>
                                    <option value="Asia/Ho_Chi_Minh">GMT+7 Asia/Ho_Chi_Minh</option>
                                    <option value="Asia/Hovd">GMT+7 Asia/Hovd</option>
                                    <option value="Asia/Jakarta">GMT+7 Asia/Jakarta</option>
                                    <option value="Asia/Krasnoyarsk">GMT+7 Asia/Krasnoyarsk</option>
                                    <option value="Asia/Novokuznetsk">GMT+7 Asia/Novokuznetsk</option>
                                    <option value="Asia/Phnom_Penh">GMT+7 Asia/Phnom_Penh</option>
                                    <option value="Asia/Pontianak">GMT+7 Asia/Pontianak</option>
                                    <option value="Asia/Saigon">GMT+7 Asia/Saigon</option>
                                    <option value="Asia/Vientiane">GMT+7 Asia/Vientiane</option>
                                    <option value="Indian/Christmas">GMT+7 Indian/Christmas</option>
                                </optgroup>
                                <optgroup label="GMT+8">
                                    <option value="Antarctica/Casey">GMT+8 Antarctica/Casey</option>
                                    <option value="Asia/Brunei">GMT+8 Asia/Brunei</option>
                                    <option value="Asia/Choibalsan">GMT+8 Asia/Choibalsan</option>
                                    <option value="Asia/Chongqing">GMT+8 Asia/Chongqing</option>
                                    <option value="Asia/Chungking">GMT+8 Asia/Chungking</option>
                                    <option value="Asia/Harbin">GMT+8 Asia/Harbin</option>
                                    <option value="Asia/Hong_Kong">GMT+8 Asia/Hong_Kong</option>
                                    <option value="Asia/Irkutsk">GMT+8 Asia/Irkutsk</option>
                                    <option value="Asia/Kuala_Lumpur">GMT+8 Asia/Kuala_Lumpur</option>
                                    <option value="Asia/Kuching">GMT+8 Asia/Kuching</option>
                                    <option value="Asia/Macao">GMT+8 Asia/Macao</option>
                                    <option value="Asia/Macau">GMT+8 Asia/Macau</option>
                                    <option value="Asia/Makassar">GMT+8 Asia/Makassar</option>
                                    <option value="Asia/Manila">GMT+8 Asia/Manila</option>
                                    <option value="Asia/Shanghai">GMT+8 Asia/Shanghai</option>
                                    <option value="Asia/Singapore">GMT+8 Asia/Singapore</option>
                                    <option value="Asia/Taipei">GMT+8 Asia/Taipei</option>
                                    <option value="Asia/Ujung_Pandang">GMT+8 Asia/Ujung_Pandang</option>
                                    <option value="Asia/Ulaanbaatar">GMT+8 Asia/Ulaanbaatar</option>
                                    <option value="Asia/Ulan_Bator">GMT+8 Asia/Ulan_Bator</option>
                                    <option value="Australia/Perth">GMT+8 Australia/Perth</option>
                                    <option value="Australia/West">GMT+8 Australia/West</option>
                                    <option value="Hongkong">GMT+8 Hongkong</option>
                                    <option value="PRC">GMT+8 PRC</option>
                                    <option value="ROC">GMT+8 ROC</option>
                                    <option value="Singapore">GMT+8 Singapore</option>
                                </optgroup>
                                <optgroup label="GMT+8.75">
                                    <option value="Australia/Eucla">GMT+8.75 Australia/Eucla</option>
                                </optgroup>
                                <optgroup label="GMT+9">
                                    <option value="Asia/Dili">GMT+9 Asia/Dili</option>
                                    <option value="Asia/Jayapura">GMT+9 Asia/Jayapura</option>
                                    <option value="Asia/Pyongyang">GMT+9 Asia/Pyongyang</option>
                                    <option value="Asia/Seoul">GMT+9 Asia/Seoul</option>
                                    <option value="Asia/Tokyo">GMT+9 Asia/Tokyo</option>
                                    <option value="Asia/Yakutsk">GMT+9 Asia/Yakutsk</option>
                                    <option value="Japan">GMT+9 Japan</option>
                                    <option value="Pacific/Palau">GMT+9 Pacific/Palau</option>
                                    <option value="ROK">GMT+9 ROK</option>
                                </optgroup>
                                <optgroup label="GMT+9.5">
                                    <option value="Australia/Darwin">GMT+9.5 Australia/Darwin</option>
                                    <option value="Australia/North">GMT+9.5 Australia/North</option>
                                </optgroup>
                                <optgroup label="GMT+10">
                                    <option value="Antarctica/DumontDUrville">GMT+10 Antarctica/DumontDUrville</option>
                                    <option value="Asia/Magadan">GMT+10 Asia/Magadan</option>
                                    <option value="Asia/Sakhalin">GMT+10 Asia/Sakhalin</option>
                                    <option value="Asia/Vladivostok">GMT+10 Asia/Vladivostok</option>
                                    <option value="Australia/Brisbane">GMT+10 Australia/Brisbane</option>
                                    <option value="Australia/Lindeman">GMT+10 Australia/Lindeman</option>
                                    <option value="Australia/Queensland">GMT+10 Australia/Queensland</option>
                                    <option value="Pacific/Chuuk">GMT+10 Pacific/Chuuk</option>
                                    <option value="Pacific/Guam">GMT+10 Pacific/Guam</option>
                                    <option value="Pacific/Port_Moresby">GMT+10 Pacific/Port_Moresby</option>
                                    <option value="Pacific/Saipan">GMT+10 Pacific/Saipan</option>
                                    <option value="Pacific/Truk">GMT+10 Pacific/Truk</option>
                                    <option value="Pacific/Yap">GMT+10 Pacific/Yap</option>
                                </optgroup>
                                <optgroup label="GMT+10.5">
                                    <option value="Australia/Adelaide">GMT+10.5 Australia/Adelaide</option>
                                    <option value="Australia/Broken_Hill">GMT+10.5 Australia/Broken_Hill</option>
                                    <option value="Australia/South">GMT+10.5 Australia/South</option>
                                    <option value="Australia/Yancowinna">GMT+10.5 Australia/Yancowinna</option>
                                </optgroup>
                                <optgroup label="GMT+11">
                                    <option value="Antarctica/Macquarie">GMT+11 Antarctica/Macquarie</option>
                                    <option value="Australia/ACT">GMT+11 Australia/ACT</option>
                                    <option value="Australia/Canberra">GMT+11 Australia/Canberra</option>
                                    <option value="Australia/Currie">GMT+11 Australia/Currie</option>
                                    <option value="Australia/Hobart">GMT+11 Australia/Hobart</option>
                                    <option value="Australia/LHI">GMT+11 Australia/LHI</option>
                                    <option value="Australia/Lord_Howe">GMT+11 Australia/Lord_Howe</option>
                                    <option value="Australia/Melbourne">GMT+11 Australia/Melbourne</option>
                                    <option value="Australia/NSW">GMT+11 Australia/NSW</option>
                                    <option value="Australia/Sydney">GMT+11 Australia/Sydney</option>
                                    <option value="Australia/Tasmania">GMT+11 Australia/Tasmania</option>
                                    <option value="Australia/Victoria">GMT+11 Australia/Victoria</option>
                                    <option value="Pacific/Efate">GMT+11 Pacific/Efate</option>
                                    <option value="Pacific/Guadalcanal">GMT+11 Pacific/Guadalcanal</option>
                                    <option value="Pacific/Kosrae">GMT+11 Pacific/Kosrae</option>
                                    <option value="Pacific/Noumea">GMT+11 Pacific/Noumea</option>
                                    <option value="Pacific/Pohnpei">GMT+11 Pacific/Pohnpei</option>
                                    <option value="Pacific/Ponape">GMT+11 Pacific/Ponape</option>
                                </optgroup>
                                <optgroup label="GMT+11.5">
                                    <option value="Pacific/Norfolk">GMT+11.5 Pacific/Norfolk</option>
                                </optgroup>
                                <optgroup label="GMT+12">
                                    <option value="Asia/Anadyr">GMT+12 Asia/Anadyr</option>
                                    <option value="Asia/Kamchatka">GMT+12 Asia/Kamchatka</option>
                                    <option value="Kwajalein">GMT+12 Kwajalein</option>
                                    <option value="Pacific/Fiji">GMT+12 Pacific/Fiji</option>
                                    <option value="Pacific/Funafuti">GMT+12 Pacific/Funafuti</option>
                                    <option value="Pacific/Kwajalein">GMT+12 Pacific/Kwajalein</option>
                                    <option value="Pacific/Majuro">GMT+12 Pacific/Majuro</option>
                                    <option value="Pacific/Nauru">GMT+12 Pacific/Nauru</option>
                                    <option value="Pacific/Tarawa">GMT+12 Pacific/Tarawa</option>
                                    <option value="Pacific/Wake">GMT+12 Pacific/Wake</option>
                                    <option value="Pacific/Wallis">GMT+12 Pacific/Wallis</option>
                                </optgroup>
                                <optgroup label="GMT+13">
                                    <option value="Antarctica/McMurdo">GMT+13 Antarctica/McMurdo</option>
                                    <option value="Antarctica/South_Pole">GMT+13 Antarctica/South_Pole</option>
                                    <option value="NZ">GMT+13 NZ</option>
                                    <option value="Pacific/Auckland">GMT+13 Pacific/Auckland</option>
                                    <option value="Pacific/Enderbury">GMT+13 Pacific/Enderbury</option>
                                    <option value="Pacific/Fakaofo">GMT+13 Pacific/Fakaofo</option>
                                    <option value="Pacific/Tongatapu">GMT+13 Pacific/Tongatapu</option>
                                </optgroup>
                                <optgroup label="GMT+13.75">
                                    <option value="NZ-CHAT">GMT+13.75 NZ-CHAT</option>
                                    <option value="Pacific/Chatham">GMT+13.75 Pacific/Chatham</option>
                                </optgroup>
                                <optgroup label="GMT+14">
                                    <option value="Pacific/Apia">GMT+14 Pacific/Apia</option>
                                    <option value="Pacific/Kiritimati">GMT+14 Pacific/Kiritimati</option>
                                </optgroup>
                            </select><br/>
                        </div>
                        <div class="col-sm-6">
                            <p>زمان جاری سایت (میلادی) : <span
                                        dir="ltr"><?php echo date("Y/m/d H:i:s", $date); ?></span></p>
                            <p>زمان جاری سایت : <span dir="ltr"><?php echo jdate("Y/m/d H:i:s", $date); ?></span></p>
                            <?php
                            $date = $this->db->query("SELECT UNIX_TIMESTAMP(NOW()) AS T")->result();
                            $date = $date[0]->T;
                            ?>
                            <p>زمان جاری دیتابیس (میلادی) : <span
                                        dir="ltr"><?php echo date("Y/m/d H:i:s", $date); ?></span></p>
                            <p>زمان جاری دیتابیس : <span dir="ltr"><?php echo jdate("Y/m/d H:i:s", $date); ?></span></p>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
        </div>
        <div class="box">
            <div class="box-title"><i class="fa fa-envelope-o"></i> <span>ایمیل</span></div>
            <form class="clearfix">
                <div class="box-content col-sm-6">
                    <p>نوع ارسال ایمیل</p>
                    <select class="form-control" name="data[mail_type]">
                        <option value="mail" label="mail">از طریق Mail</option>
                        <option value="smtp" label="stp">از طریق SMTP</option>
                    </select>
                </div>
                <div class="box-content col-sm-6">
                    <p>آدرس سرور SMTP</p>
                    <input type="text" name="data[smtp_server]" class="form-control en"
                           value="<?php echo @$smtp_server ?>"><br/>
                    <p>پورت SMTP</p>
                    <input type="text" name="data[smtp_port]" class="form-control en" value="<?php echo @$smtp_port ?>"><br/>
                    <p>نام کاربری SMTP</p>
                    <input type="text" name="data[smtp_user]" class="form-control en" value="<?php echo @$smtp_user ?>"><br/>
                    <p>گذرواژه SMTP</p>
                    <input type="text" name="data[smtp_pass]" class="form-control en" value="<?php echo @$smtp_pass ?>"><br/>
                </div>
                <div class="clearfix"></div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
        </div>

        <div class="box">
            <div class="box-title"><i class="fa fa-envelope-o"></i> <span>درگاه پرداخت</span></div>
            <form class="clearfix">
                <div class="box-content col-sm-12">
                    <p>ساختار درگاه پرداخت</p>
                    <select class="form-control" name="data[online]">
                        <option value="0" label="تست" <?php echo @$online == 0 ? "selected" : ""; ?>>تست</option>
                        <option value="1" label="آنلاین" <?php echo @$online == 1 ? "selected" : ""; ?>>آنلاین</option>
                    </select>
                </div>
                <div class="box-content col-sm-12">
                    <p>پرداخت از طریق</p>
                    <select class="form-control" name="data[bank]">
                        <option value="saman" label="saman">بانک سامان</option>
                    </select>
                </div>
                <div class="box-content col-sm-6">
                    <h3>بانک سامان :</h3>
                    <p>کد پذیرنده</p>
                    <input type="text" name="data[saman_id]" class="form-control en"
                           value="<?php echo @$saman_id ?>"><br/>
                    <p>نام کاربری</p>
                    <input type="text" name="data[saman_username]" class="form-control en"
                           value="<?php echo @$saman_username ?>"><br/>
                    <p>رمز عبور</p>
                    <input type="password" name="data[saman_password]" class="form-control en"
                           value="<?php echo @$saman_password ?>"><br/>
                </div>
                <div class="clearfix"></div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
        </div>
        <?php
        $path = BASEPATH;
        $path = str_replace(DIRECTORY_SEPARATOR . 'system', '', $path);
        $files = scandir($path . DIRECTORY_SEPARATOR . 'sms');
        $basesmsType = array();
        foreach ($files as $k => $v) {
            if (strpos($v, '.php') !== FALSE) {
                $v = str_replace('.php', '', $v);
                $basesmsType[] = $v;
            }
        }
        $remaind = 0;
        if (intval(@$smsCheck)) {
            $file = $path . DIRECTORY_SEPARATOR . 'sms' . DIRECTORY_SEPARATOR . $smsType . ".php";
            if (strlen($smsUN) && strlen($smsType) && strlen($smsNumber) && file_exists($file)) {
                include_once($file);
                eval('$Panel = new ' . $smsType . ';');
                $remaind = $Panel->LoadSmsPanel("check", $smsUN, $smsPass, $smsNumber, $smsCenter);
                $remaind = is_numeric($remaind) ? number_format($remaind, 0, '.', ',') : $remaind;
            }
        }
        ?>
        <div class="box">
            <div class="box-title"><i class="fa fa-envelope-o"></i> <span>تنظیمات پیامک</span></div>
            <form class="clearfix">
                <div class="box-content col-sm-12">
                    <p>نام درگاه</p>
                    <select class="form-control" name="data[smsType]">
                        <?php foreach ($basesmsType as $k => $v) { ?>
                            <option value="<?php echo $v ?>"<?php echo $v == @$smsType ? ' selected' : ''; ?>><?php echo $v ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="box-content col-sm-12">
                    <p>
                    <div class="col-sm-4">مشاهده مانده :</div>
                    <div class="col-sm-4">
                        <select class="form-control" name="data[smsCheck]">
                            <option value="0"<?php echo intval(@$smsCheck) ? '' : ' selected'; ?>>خیر</option>
                            <option value="1"<?php echo intval(@$smsCheck) ? ' selected' : ''; ?>>بلی</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        مانده : <?php echo $remaind; ?>
                    </div>
                    </p>
                    <div class="clearfix"></div>
                    <p>آدرس وب سرویس مرکز پیامک</p>
                    <input type="text" name="data[smsCenter]" class="form-control en"
                           value="<?php echo @$smsCenter; ?>">
                    <div class="clearfix"></div>
                    <div class="col-sm-4">
                        <p>نام کاربری</p>
                        <input type="text" name="data[smsUN]" class="form-control en" value="<?php echo @$smsUN; ?>">
                    </div>
                    <div class="col-sm-4">
                        <p>رمز عبور</p>
                        <input type="password" name="data[smsPass]" class="form-control en"
                               value="<?php echo @$smsPass; ?>">
                    </div>
                    <div class="col-sm-4">
                        <p>قالب ارسال</p>
                        <input type="text" name="data[smsTemplate]" class="form-control en"
                               value="<?php echo @$smsTemplate; ?>">
                    </div>
                    <div class="clearfix"></div>
                    <p>شماره پیامک</p>
                    <input type="text" name="data[smsNumber]" class="form-control en"
                           value="<?php echo @$smsNumber; ?>">
                    <div class="clearfix"></div>
                </div>
                <div class="box">
                    <div class="box-title"><i class="fa fa-mobile"></i> <span>پیامک احراز هویت</span></div>
                    <div>
                        <p>مدت تاخیر ارسال احراز هویت پس از اولین ارسال</p>
                        <select class="form-control col-sm-6" name="data[waitTime]">
                            <option value="0"<?php echo intval(@$waitTime) == 0 ? ' selected' : ''; ?>>بدون وقفه
                            </option>
                            <option value="1"<?php echo intval(@$waitTime) == 1 ? ' selected' : ''; ?>>1 دقیقه</option>
                            <option value="2"<?php echo intval(@$waitTime) == 2 ? ' selected' : ''; ?>>2 دقیقه</option>
                            <option value="3"<?php echo intval(@$waitTime) == 3 ? ' selected' : ''; ?>>3 دقیقه</option>
                            <option value="4"<?php echo intval(@$waitTime) == 4 ? ' selected' : ''; ?>>4 دقیقه</option>
                            <option value="5"<?php echo intval(@$waitTime) == 5 ? ' selected' : ''; ?>>5 دقیقه</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <p>متن ایمیل احراز هویت</p>
                    <textarea name="data[smstextpassrec]" id="smstextpassrec" class="form-control"
                              rows="5"><?php echo @$smstextpassrec; ?></textarea>
                    <div class="clearfix"></div>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('smstextpassrec','{gender}');">خانم / آقا</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('smstextpassrec','{name}');">نام</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('smstextpassrec','{family}');">نام خانوادگی</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('smstextpassrec','{code}');">کد احراز هویت</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('smstextpassrec','{displayname}');">نام و نام خانوادگی</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('smstextpassrec','{username}');">نام کاربری</a>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <div class="box">
                    <div class="box-title"><i class="fa fa-envelope-o"></i> <span>ایمیل احراز هویت</span></div>
                    <p>عنوان ایمیل احراز هویت</p>
                    <input type="text" name="data[emailtitlepassrec]" id="emailtitlepassrec" class="form-control"
                           value="<?php echo @$emailtitlepassrec; ?>"/>
                    <p>متن ایمیل احراز هویت</p>
                    <textarea name="data[emailtextpassrec]" id="emailtextpassrec" class="form-control"
                              rows="20"><?php echo @$emailtextpassrec; ?></textarea>
                    <div class="clearfix"></div>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('emailtextpassrec','{gender}');">خانم / آقا</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('emailtextpassrec','{name}');">نام</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('emailtextpassrec','{family}');">نام خانوادگی</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('emailtextpassrec','{code}');">کد احراز هویت</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('emailtextpassrec','{displayname}');">نام و نام خانوادگی</a>
                    <a class="btn btn-primary" href="javascript:void(0);"
                       onclick="javascript:AppendKeysInvate('emailtextpassrec','{username}');">نام کاربری</a>
                </div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
            <script language="javascript">
                //=============================================================================================
                function AppendKeysInvate(field, key_text) {
                    insertAtCursor(document.getElementById(field), key_text);
                }

                //=============================================================================================
                function insertAtCursor(myField, myValue) {
                    //IE support
                    if (document.selection) {
                        myField.focus();
                        sel = document.selection.createRange();
                        sel.text = myValue;
                    }
                    //MOZILLA/NETSCAPE support
                    else if (myField.selectionStart || myField.selectionStart == '0') {
                        var startPos = myField.selectionStart;
                        var endPos = myField.selectionEnd;
                        myField.value = myField.value.substring(0, startPos)
                            + myValue
                            + myField.value.substring(endPos, myField.value.length);
                    } else {
                        myField.value += myValue;
                    }
                }

                //=============================================================================================
            </script>
        </div>

        <form class="clearfix hidden">
            <div class="box">
                <div class="box-title"><i class="fa fa-"></i> <span>قوانین ثبت نام</span></div>
                <textarea class="hidden" name="data[register_rules]"
                          id="siterules"><?php echo $register_rules ?></textarea>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </div>
        </form>

        <form class="clearfix hidden">
            <div class="box">
                <div class="box-title"><i class="fa fa-"></i> <span> متن صفحه اصلی</span></div>
                <div class="box-content">
                    <p>عنوان</p>
                    <input type="text" name="data[home_page_text_title]" class="form-control text-center"
                           value="<?php echo @$home_page_text_title ?>">
                    <p>عنوان کوچک</p>
                    <input type="text" name="data[home_page_text_small]" class="form-control text-center"
                           value="<?php echo @$home_page_text_small ?>">
                    <input type="button" class="w-btn" value="افزودن رسانه" onClick="media('editor')">
                </div>
                <textarea class="hidden" name="data[home_page_text]"
                          id="homepage"><?php echo @$home_page_text ?></textarea>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </div>
        </form>

    </div>

    <div class="col-sm-6">
        <div class="box">
            <form>
                <div class="box-title"><i class="fa fa-info"></i> <span>اطلاعات سایت</span></div>
                <div class="box-content">
                    <div class="clearfix">
                        <p>تلفن تماس</p>
                        <input type="tel" name="data[site_tel]" class="form-control"
                               value="<?php echo @$site_tel ?>"><br/>
                        <p>ایمیل پشتیبانی</p>
                        <input type="text" name="data[site_email]" class="form-control en"
                               value="<?php echo @$site_email ?>"><br/>
                        <p>آدرس </p>
                        <input type="text" name="data[site_address]" class="form-control"
                               value="<?php echo @$site_address ?>"><br/>
                        <p>آدرس در گوگل</p>
                        <input type="text" name="data[site_map_address]" class="form-control en"
                               value="<?php echo @$site_map_address ?>"><br/>

                        <p>درباره</p>
                        <textarea name="data[site_about]"
                                  class="form-control"><?php echo @$site_about ?></textarea><br/>
                        <hr/>
                        <p>لینک فیسبوک</p>
                        <input type="text" name="data[site_facebook]" class="form-control en"
                               value="<?php echo @$site_facebook ?>"><br/>
                        <p>لینک گوگل پلاس</p>
                        <input type="text" name="data[site_google_plus]" class="form-control en"
                               value="<?php echo @$site_google_plus ?>"><br/>
                        <p>لینک تویتر</p>
                        <input type="text" name="data[site_twitter]" class="form-control en"
                               value="<?php echo @$site_twitter ?>"><br/>
                        <p>لینک اینستاگرام</p>
                        <input type="text" name="data[site_instagram]" class="form-control en"
                               value="<?php echo @$site_instagram ?>"><br/>
                        <p>لینک تلگرام</p>
                        <input type="text" name="data[site_pinterest]" class="form-control en"
                               value="<?php echo @$site_pinterest ?>"><br/>
                        <p>لینک لینکداین</p>
                        <input type="text" name="data[site_linkedin]" class="form-control en"
                               value="<?php echo @$site_linkedin ?>"><br/>

                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
        </div>


        <?php
        $picturs = array(
            array('name' => 'site_logo', 'fa' => 'لوگوی سایت'),
            array('name' => 'favicon', 'fa' => 'آیکن Favicon'),
            array('name' => 'default_post_thumb', 'fa' => 'تصویر عمودی'),
            array('name' => 'default_post_icon', 'fa' => 'تصویر افقی'),
            array('name' => 'default_category_pic', 'fa' => 'تصویر دسته بندی'),
            array('name' => 'default_user_avatar', 'fa' => 'آواتار کاربران'),
            //array('name'=>'default_user_cover'   ,'fa'=>'کاور کاربران'),
            //array('name'=>'landing_page_bg'      ,'fa'=>'تصویر پس زمینه صفحه فرود'),
        );
        ?>
        <div class="box">
            <div class="box-title"><i class="fa fa-camera"></i> <span>تصاویر پیش فرض</span></div>
            <form class="clearfix picturts-form">
                <?php foreach ($picturs as $k => $item): ?>
                    <div class="box-content media-ap col-sm-6">
                        <div class="editable-img text-center">
                            <p><?php echo $item['fa'] ?></p>
                            <span class="media-ap-data replace" data-thumb="thumb150" style="display: inline-block">
                            <img class="convert-this img-responsive"
                                 src="<?php echo thumb(@$this->settings->data[$item['name']]); ?>">
                        </span>
                            <div id="add-thumb-img-<?php echo $k ?>" class="plus add-img"
                                 onclick="media('img,1',this,function(){ $('#add-thumb-img-<?php echo $k ?>').hide() })"
                                 style="display:<?php echo trim(@$this->settings->data[$item['name']]) == '' ? 'inline-block' : 'none' ?>;margin:15px">
                            </div>
                            <div class="form-ap-data" style="display:none"></div>
                            <input type="hidden" class="media-ap-input"
                                   value="<?php echo @$this->settings->data[$item['name']] ?>"
                                   name="data[<?php echo $item['name'] ?>]">
                        </div>
                    </div>
                    <?php echo (($k + 1) % 2 == 0 && $k + 1 < count($picturs)) ? '<div class="clearfix"></div><hr/>' : '' ?>
                <?php endforeach ?>
                <div class="clearfix"></div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
        </div>

        <div class="box hidden">
            <form>
                <div class="box-title"><i class="fa fa-info"></i> <span>صفحه فرود</span></div>
                <div class="box-content">
                    <div class="clearfix">
                        <p>عنوان صفحه</p>
                        <input type="text" name="data[landing_page_title]" class="form-control text-center"
                               value="<?php echo @$landing_page_title ?>"><br/>
                        <p>توضیحات برای نمایش در این صفحه</p>
                        <textarea name="data[landing_page_text]"
                                  class="form-control text-center"><?php echo @$landing_page_text ?></textarea><br/>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="box-footer">
                    <button type="button" class="w-btn" onclick="save_setting(this)">ذخیره</button>
                    <span class="ajax-result"></span>
                </div>
            </form>
        </div>

    </div>
</div>


<script type="text/javascript">

    var EDITOR;

    $(document).ready(function () {
        var ckconfig = {
            height: 200,
            contentsCss: [CKEDITOR.basePath + 'contents.css', '<?php echo base_url() ?>style/_master/font.css']
        };

        CKEDITOR.replace('siterules', ckconfig);
        CKEDITOR.instances.siterules.on('change', function (e) {
            CKEDITOR.instances.siterules.updateElement();
            var html = CKEDITOR.instances.siterules.getData();
            var text = $(html).text();
            var el = CKEDITOR.instances.siterules.element.$;
            if ($.trim(text) == '')
                el.value = '';
            else
                el.value = html;
            $(el).trigger('change');
        });


        //CKEDITOR.appendTo('textEditor', config, html);

        EDITOR = CKEDITOR.replace('homepage', ckconfig);
        CKEDITOR.instances.homepage.on('change', function (e) {
            CKEDITOR.instances.homepage.updateElement();
            var html = CKEDITOR.instances.homepage.getData();
            var text = $(html).text();
            var el = CKEDITOR.instances.homepage.element.$;
            if ($.trim(text) == '')
                el.value = '';
            else
                el.value = html;
            $(el).trigger('change');
        });
    });


    $(document).ready(function (e) {
        $('[data-val]').each(function (i, el) {
            $(el).val($(el).attr('data-val'));
        });
    });

    function save_setting(btn) {

        $(btn).addClass('l blue');
        var form = $(btn).closest("form");
        var data = $(form).serialize();
        $(form).find('.ajax-result').html('');
        $.ajax({
            url: "admin/api/saveSetting",
            type: "POST",
            data: data,
            success: function (data) {
                console.log(data);
                $(btn).removeClass('l');
                $(form).find('.ajax-result').html(data.msg);
                /*
                if($.trim(data)==0){

                    login(function(){
                        save_setting(btn);
                    });

                }else
                $(btn).parent().find(".ajax-result").html(data);
                */
            },
            error: function () {
                $(btn).removeClass('l');
            }
        });
    }
</script>
