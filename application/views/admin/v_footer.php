
    	</div><!-- end #main  -->
        <div class="clear"></div>
    </div><!-- end #container -->
    
    <div class="clear"></div>
    
    <div id="footer" class="dark-box border-light">
        <?php /* ?>
        <span> کاربران آنلاین <?php echo  $this->db->count_all_results('onlines'); ?></span>
        <span> ::: بازدید امروز <?php echo  $this->db->where('datestr >',strtotime('today'))->where('event','view')->group_by('ip')->count_all_results('logs') ?></span>
        <?php */ ?>
        &nbsp;
    </div>
    
    <div id="footer-bottom" class="dark-box-2">
        <div dir="ltr">
            &copy; Copyrights <a href="http://namavaran.xyz/" title="نام آوران" target="_blank">نام آوران</a> <?php echo date("Y");?>. All rights reserved | Version 1.0.1
        </div>
    </div>
  </div>
</div>
</body>
</html>