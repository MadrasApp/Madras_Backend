<style>
body{background-color:#46BEF0;color:#fff;padding:1px;}
</style>

<div class="login-top">
 <h1><?php echo $title ?></h1>
</div>

<div class="login-div">

	<div class="login">
        <p style="color:#333"><b>ورود به حساب کاربری</b></p>
        
        <span style="color:red"><?php echo validation_errors(); ?></span>
        
        <?php echo form_open('admin/login'); ?>

        <input type="text" name="username" value="<?php echo set_value('username') ?>" class="input" placeholder="نام کاربری"/>

        <input type="password" name="password" value="" class="input" placeholder="گذرواژه"/>
        
        <div class="clear"></div>
        
        <label style="float:right;margin:10px 0">
            <input type="checkbox" name="stay" value="true"> مرا به خاطر بسپار
        </label>

        <?php if($cap_protect): ?>
        <div style="float:left"><img src="<?php echo  base_url('captcha') ?>" alt="Captcha" class="captcha-img"></div>
        <div class="clear"></div>
        <input type="text" name="captcha"  class="input en" placeholder="تصویر امنیتی"/>
        <?php endif ?>

        <div><input type="submit" value="ورود" class="button"/></div>
        
        </form>
    </div>
    
</div>

<div>
<?php //var_dump($cap); ?>
</div>

</body>
</html>