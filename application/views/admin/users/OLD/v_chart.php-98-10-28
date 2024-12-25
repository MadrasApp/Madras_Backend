<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="chart">
	<div class="page-title"><i class="fa fa-pie-chart fa-2x"></i> آمار کاربران </div>
	
    
    <div id="users-statistics">
		
        
        <table class="table light2">
          <tr> 
            <th colspan="10"> آمار آنلاین ها </th>
          </tr>
          <tr>
            <td style="width:150px"> نعداد افراد آنلاین </td>
            <td><?php echo  $chart['onlines']['all'] ?></td>
          </tr>          
          <tr>
            <td> نعداد کاربران آنلاین </td>
            <td><?php echo  $chart['onlines']['users_online_num'] ?></td>
          </tr>          
          <tr>
            <td> نعداد مهمان های آنلاین </td>
            <td><?php echo  $chart['onlines']['g_online'] ?></td>
          </tr>          
          <tr>
            <td> بعضی از کاربران آنلاین  </td>
            <td>
			<?php
			
			foreach ( $chart['onlines']['users_online'] as $key => $user )
			
			echo '<a href="'. site_url('admin/users/view/'.$user['username']) .'">'. $user['displayname'] .'</a>';
			
			?>
            </td>
          </tr>          
        </table>
        <div class="clear" style="height:30px"></div>
        <table class="table light2">
          <tr> 
            <th colspan="10"> آمار کاربران </th>
          </tr>
          <tr>
            <td style="width:150px"> نعداد کل کاربران </td>
            <td><?php echo  $chart['users']['all'] ?></td>
          </tr>          
          <tr>
            <td style="width:150px"> نعداد کاربران تایید شده </td>
            <td><?php echo  $chart['users']['done'] ?></td>
          </tr>
          <tr>
            <td> نعداد کاربران معلق </td>
            <td><?php echo  $chart['users']['pending'] ?></td>
          </tr>                             
        </table>        
        <div class="clear" style="height:30px"></div>
        
	</div>
    
</div>

<div class="page-title"><i class="fa fa-pie-chart fa-2x"></i> نمودار عضویت کاربران بر حسب زمان </div>

<div style="margin-top:50px">
	<?php
	
	$dates = $chart['date'];
	if( !empty( $dates ) )
	{
		$this->load->helper('inc');
		$chart = new dateChart;
		$chart->setData($dates,'آمار ثبت نام کاربران ('.count($dates).')');
		$chart->analyze();
		$chart->render();
	}
	else
	{
		echo "<h1> آماری موجود نیست </h1>";
	}
    ?>
</div>