<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="dashboard">
    <div class="row">
        <?php
        $colors = array('#E08807', '#CB3712', '#9C2D60', '#486340','#555', '#685177', '#B18B6B');

        global $POST_TYPES;

        $i = -1;
        foreach ($POST_TYPES as $type_name => $type):
            $i++;
            if ($i >= count($colors)) $i = 0;
            if ($this->user->can('_read_' . $type_name)):?>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="item" stylee="background-color:<?php echo  $colors[$i] ?>">
                        <a href="<?php echo  site_url('admin/' . $type_name . '/primary') ?>">
                            <i class="fa fa-<?php echo  $type['icon'] ?> item-icon"></i>
                            <h3 class="item-name"><?php echo  $type['g_name'] ?></h3>
                            <i class="fa fa-angle-double-left"></i>
                            <i class="item-count"><?php echo  $this->db->where('type', $type_name)->count_all_results('posts') ?></i>
                        </a>
                        <div class="clearfix"></div>
                        <hr/>
                        <?php if ($this->user->can('create_' . $type_name)): ?>
                            <a href="<?php echo  site_url("admin/$type_name/add") ?>">
                                <i class="fa fa-plus-circle fa-lg"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (in_array('category', $type['support']) && $this->user->can('caregory_' . $type_name)): ?>
                            <a href="<?php echo  site_url("admin/$type_name/category") ?>"><i class="fa fa-bookmark fa-lg"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach ?>
        <div class="col-xs-12"><hr/></div>
        <?php foreach ($home_data as $data):
            $i++;
            if ($i >= count($colors)) $i = 0; ?>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="item" style="background-color:<?php echo  $colors[$i] ?>">

                        <a href="<?php echo  $data['link'] ?>">
                            <i class="fa fa-<?php echo  $data['icon'] ?> item-icon"></i>
                            <h3 class="item-name"><?php echo  $data['name'] ?></h3>
                            <i class="fa fa-angle-double-left"></i>
                            <i class="item-count"><?php echo  $data['count'] ?></i>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </div>
        <?php endforeach ?>

    </div>
</div>


<?php if($this->user->can('site_visits')): ?>

    <!-- <hr style="margin: 50px 0"/>
    <div class="text-center">
        <i class="fa fa-pie-chart fa-lg"></i> &nbsp;  <span>نمودار بازدید از سایت</span>  &nbsp;  <a href="<?php echo  site_url('admin/home/statistics')?>"> مشاهده کامل اطلاعات </a>
    </div>

    <div style="margin-top:50px">
        <?php
        if(isset($chart))
        {

            $dates = $chart['date'];
            if( !empty( $dates ) )
            {
                $this->load->helper('inc');
                $chart = new dateChart;
                $chart->setData($dates,'بازدید یک ماه اخیر');
                $chart->analyze();
                $chart->render();
            }
            else
                echo "<h1> آمار بازدید موجود نیست </h1>";
        }
        ?>
    </div> -->
<?php endif ?>