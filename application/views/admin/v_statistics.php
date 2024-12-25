<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>.ui-datepicker{z-index:1500 !important}</style>

<div class="page-title" style="position:relative">
	<i class="fa fa-pie-chart fa-2x"></i> نمودار بازدید از سایت 
    
    <pre style="direction:ltr;float:left;font-size:10px;font-size:12px;margin-top: 20px;"><?php 
	
    echo jdate('Y-m-d H:i',$from,'','','en') ." -> ".jdate('Y-m-d H:i',$to,'','','en')
	
	?></pre>
    
</div>

<div style="margin:15px 0">
	<form action="" method="get" id="date-form" style="display:inline-block">
    	<select name="date" onChange="$('#date-form').submit()" class="input">
        <?php
			
			$oparr = array(
				'today'=>'امروز',
                '1-m'=>'یک ماه قبل',
                '2-m'=>'دو ماه قبل',
				'3-m'=>'سه ماه قبل',
                '6-m'=>'شش ماه قبل',
                '1-y'=>'یک سال قبل',
				'2-y'=>'دو سال قبل',
                'all'=>'همه'
			);
			
			$cur = $this->input->get('date');
			if( $cur == "") $cur = "1-m";
			foreach( $oparr as $value => $name )
			{
				$sel = $cur == $value ? 'selected':'';
				echo "<option value=\"$value\" $sel> $name</option>";
			}
		 
		?>
        </select>
	</form>
    
    <form action="" method="get" id="date-sel-form" style="display:inline-block;margin-right:20px">
    
		<span class="vm"> فیلتر زمان از : </span>
          
        <input type="text" class="input dateInput-1 vm dateFormat" value="<?php echo jdate('Y-m-d',$from,'','','en')?>" maxlength="10" name="from">
          
        <span class="vm"> تا : </span>
          
          <input type="text" class="input dateInput-2 vm dateFormat" value="<?php echo jdate('Y-m-d',$to,'','','en')?>" maxlength="10" name="to">
          
        <input type="submit" class="small-btn" value="اعمال">
    </form>
</div>




<div style="margin-top:50px">
	<?php

	if( !empty( $view_all ) )
	{
		$this->load->helper('inc');
		$chart = new dateChart;
		$chart->setData( $view_all ,'همه بازدید ها ('.count($view_all).')');
		$chart->analyze();
		$chart->render();
	}
	else
	{
		echo "<h1> آماری موجود نیست </h1>";
	}
	
	
	if( !empty( $view_browser ) )
	{
		$chart = new dateChart;
		$chart->setData($view_browser,'بازدید با کامپیوتر ('.count($view_browser).')');
		$chart->analyze();
		$chart->render();
	}
	
	
	if( !empty( $view_mobile ) )
	{
		$chart = new dateChart;
		$chart->setData($view_mobile,'بازدید با موبایل ('.count($view_mobile).')');
		$chart->analyze();
		$chart->render();
	}	
	
	if( !empty( $view_robot ) )
	{
		$chart = new dateChart;
		$chart->setData($view_robot,'بازدید موتور های جستجو ('.count($view_robot).')');
		$chart->analyze();
		$chart->render();
	}
		
    ?>
</div>

<pre dir="ltr">
<?php echo @$more ?>
</pre>

<script>
	$('.dateInput-1').datepicker({
		dateFormat: "yy-mm-dd",
		defaultDate: new JalaliDate(<?php echo jdate('Y,m,d',$from,'','','en')?>),
	});

	$('.dateInput-2').datepicker({
		dateFormat: "yy-mm-dd",
		defaultDate: new JalaliDate(<?php echo jdate('Y,m,d',$to,'','','en')?>),
	});
</script>

