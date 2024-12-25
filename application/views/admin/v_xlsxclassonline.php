<?php
//$step = (int)$this->input->get('step');
function Pre($data, $die = 1)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    if ($die) {
        die();
    }
}

if (!isset($post["step"])) {
    $post["step"] = 0;
}
$step = $post["step"];
switch ($step) {
    case 0:
        ?>
        <script src="style/assets/js/jquery.form.min.js"></script>
        <style>
            .progress-bar {
                background-color: #075609;
                height: 20px;
                color: #FFFFFF;
                width: 0;
                -webkit-transition: width .3s;
                -moz-transition: width .3s;
                transition: width .3s;
                line-height: 22px;
            }

            .progress-div {
                border: #075609 1px solid;
                padding: 5px 0;
                margin: 6px 1px;
                border-radius: 4px;
                text-align: center;
            }

            .scroll-x {
                max-width: 100%;
                overflow-x: auto;
                -ms-overflow-x: auto;
                white-space: nowrap;
                max-height: 500px;
            }

            .scrollxyz {
                padding: 20px;
                margin: 20px;
            }

            .scroll-x table th:not(.radif) {
                min-width: 50px;
                max-width: 200px;
                overflow-x: auto;
            }
            .scroll-x table td,.scroll-x table td div {
                max-width: 200px;
                overflow: hidden;
            }
            .scroll-x table th.radif {
                min-width: auto;
            }

            #progress-bar {
                background-color: #075609;
                height: 36px;
                color: #FFFFFF;
                width: 0;
                -webkit-transition: width 1s;
                -moz-transition: width 1s;
                transition: width 1s;
                line-height: 36px;
                min-width: 50px;
            }

            #progress-div {
                border: #075609 1px solid;
                padding: 0 1px;
                margin: 30px 0;
                border-radius: 4px;
                text-align: center;
                height: 50px;
            }

            #targetLayer {
                width: 100%;
                text-align: center;
                position: relative;
            }
            .lightScroll {
                overflow: scroll;
                width: 100%;
                border: 2px solid;
            }
        </style>
        <div class="btn-success h3 p-4 text-center">ورود اطلاعات اکانتهای کلاس آنلاین توسط فایل اکسل</div>
        <form method="post" class="col-md-6 p-2 border rounded mt-2" id="uploadForm" enctype="multipart/form-data">
            <div class="h3 p-3 text-white btn-success">
                <div class="control-label">
                    <label for="xlsxupload" class="h3">انتخاب فایل اکسل</label>
                </div>
            </div>
            <div class="control-group mt-2 border p-3">
                <div class="controls">
                    <input type="file" accept=".xlsx" name="xlsfile" id="xlsxupload" required=""
                           class="required span12">
                </div>
            </div>
            <div class="control-group text-center p-2">
                <input type="submit" value="Submit" class="btn btn-success btn-large submit">
            </div>
            <input type="hidden" name="step" value="1">
            <input type="hidden" name="ajax" value="1">
        </form>
        <div class="clearfix"></div>
        <div class="clearfix"></div>
        <div id="progress-div" class="d-none">
            <div id="progress-bar" class="progress-div progress-bar"></div>
        </div>
        <div id="targetLayer"></div>
        <script>
            function LoadEducationDataSeperate(data) {
                AjaxURL = "<?php echo $_SERVER["QUERY_STRING"];?>";
                jQuery.post(AjaxURL,
                    data,
                    function (response) {
                        if (!isNaN(response.percent) && response.percent < 100) {
                            jQuery("#targetLayer").prepend(response.html);
                            startImport(response.count, response.start, response.limit,[]);
                        } else {
                            jQuery('#progress-div').addClass('d-none');
                        }
                    });
            }
            function startImport(count, start, limit,fieldsdata) {
                var percent = (100 * (start + limit)) / count;
                if(percent > 100){
                    percent = 100;
                }
                var percentComplete = percent.toFixed(2);
                jQuery('#progress-div').removeClass('d-none');
                jQuery("#progress-bar")
                    .css({width: percentComplete + "%"})
                    .html('<div id="progress-status btn-success">' + percentComplete + ' %</div>');
                var data = {step: 2, count: count, start: start, limit: limit,fieldsdata:fieldsdata};
                LoadEducationDataSeperate(data);
                return false;
            }
            function ControlDataSelected(count) {
                var fieldsdata = jQuery("form#startImportData").serializeArray();
                jQuery("#targetLayer").html("").addClass("scroll-x");
                startImport(count, 0, 20,fieldsdata)
            }
            $(function () {
                $('#uploadForm').submit(function (e) {
                    if ($('#xlsxupload').val()) {
                        e.preventDefault();
                        $('#progress-div').removeClass('d-none');
                        $(this).ajaxSubmit({
                            target: '#targetLayer',
                            beforeSubmit: function () {
                                $("#progress-bar").width('0%');
                            },
                            uploadProgress: function (event, position, total, percentComplete) {
                                $("#progress-bar")
                                    .css({width: percentComplete + "%"})
                                    .html('<div id="progress-status btn-success">' + percentComplete + ' %</div>');
                            },
                            success: function (status, target) {
                                $('#progress-div').addClass('d-none');
                                $('#uploadForm').slideUp();
                            },
                            resetForm: true
                        });
                        return false;
                    }
                });
            });
        </script>
        <?php
        break;
    case 1:
        require_once(APPPATH . '/helpers/Excel/SimpleXLSX.php');
        include_once(APPPATH . '/helpers/Excel/ExcelCounter.php');
        $XClass = new ExcelCounter();
        list($status, $html) = $XClass->LoadDynamicExcel($step, "classaccount", $post);
        ?>
        <form method="post" class="p-2 border rounded m-2" id="startImportData" enctype="multipart/form-data"
              onsubmit="ControlDataSelected(<?php echo $status; ?>);return false;">
            <div class="h3 p-3 text-white bg-primary"><?php echo "بروزرسانی اکانتهای کلاس آنلاین"; ?></div>
            <?php echo $html; ?>
            <?php
            if ($status) {
                ?>
                <div class="text-center m-3">
                    <input type="submit" class="btn btn-primary btn-lg"
                           value="تایید و ادامه ثبت اطلاعات">
                </div>
                <?php
            }
            ?>
            <input type="hidden" name="step" value="2"/>
        </form>
        <?php
        die();
        break;
    case 2:
        require_once(APPPATH . '/helpers/Excel/SimpleXLSX.php');
        include_once(APPPATH . '/helpers/Excel/ExcelCounter.php');
        $XClass = new ExcelCounter();
        list($status, $html) = $XClass->LoadDynamicExcel($step, "classaccount", $post);
        die();
        break;
}
?>