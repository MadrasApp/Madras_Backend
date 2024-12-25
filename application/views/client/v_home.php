<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

			<!-- Slider -->
			<section class="slider-holder">
				<div class="flexslider carousel">
                    <?php if(isset($slider) && $slider): ?>
                        <ul class="slides">
                            <?php foreach ($slider as $item): if( ! file_exists($item->thumb) ) continue; ?>
                                <li>
                                    <img src="<?php echo  base_url().$item->thumb ?>" alt="<?php echo  html_escape($item->title) ?>"/>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
					<div class="search-box">
						<div class="container">
							<div class="search-box-inner">
								<h1 class="font-15">جستجوی متخصص</h1>
								<form action="<?php echo  site_url('proficient') ?>" method="GET" role="form" id="filter-form">
									<div class="row">
										<div class="col-md-12 mb-15">
											<div class="form-group">
                                                <div class="select-style border-w">
                                                    <?php echo  $states ?>
                                                </div>
											</div>
										</div>
										<div class="col-md-12 mb-15">
											<div class="form-group">
												<div class="select-style border-w">
													<?php echo  $skills ?>
												</div>
											</div>
										</div>
                                        <div class="col-md-12 mb-15">
                                            <div class="form-group">
                                                <div class="select-style border-w">
                                                    <?php
                                                    $orders = array(
                                                        'username' => 'نام کاربری',
                                                        'rating'   => 'امتیاز',
                                                        'name'     => 'نام',
                                                        'lastseen' => 'آخرین بازدید',
                                                        'online'   => 'آنلاین ها',
                                                    );
                                                    ?>
                                                    <select class="form-control" name="order">
                                                        <option value="">مرتب سازی بر اساس</option>
                                                        <?php foreach ($orders as $value=>$name): ?>
                                                            <?php $selected = $this->input->get('order') == $value ? ' selected':'' ?>
                                                            <option value="<?php echo  $value ?>"<?php echo  $selected ?>><?php echo  $name ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
										<div class="col-md-12">
											<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Slider / End -->

            <div class="container-fluid">
                <div class="section-light section-nomargin row">
                    <div class="section-inner">
                        <div class="">
                            <div class="col-sm-3">
                                <div class="counter-holder counter-dark">
                                    <a href="<?php echo  site_url('tools') ?>" title="ابزارآلات">
                                        <i class="fa fa-3x fa-suitcase"></i>
                                        <span class="counter-wrap">
                                            <span class="counter" data-to="<?php echo  $summary->total_tools ?>" data-speed="1500" data-refresh-interval="50"><?php echo  $summary->total_tools ?></span>
                                        </span>
                                        <span class="counter-info">
                                            <span class="counter-info-inner">ابزارها</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="counter-holder counter-dark">
                                    <i class="fa fa-3x fa-thumbs-o-up"></i>
                                    <span class="counter-wrap">
                                        <span class="counter" data-to="<?php echo  $summary->total_rates ?>" data-speed="1500" data-refresh-interval="50"><?php echo  $summary->total_rates ?></span>
                                    </span>
                                    <span class="counter-info">
                                        <span class="counter-info-inner">نظرات</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="counter-holder counter-dark">
                                    <a href="<?php echo  site_url('proficient') ?>" title="متخصصین">
                                        <i class="fa fa-3x fa-user"></i>
                                        <span class="counter-wrap">
                                            <span class="counter" data-to="<?php echo  $summary->total_experts ?>" data-speed="1500" data-refresh-interval="50"><?php echo  $summary->total_experts ?></span>
                                        </span>
                                        <span class="counter-info">
                                            <span class="counter-info-inner">متخصصین</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="counter-holder counter-dark">
                                    <i class="fa fa-3x fa-check"></i>
                                    <span class="counter-wrap">
                                        <span class="counter" data-to="<?php echo  $summary->done_missions ?>" data-speed="1500" data-refresh-interval="50"><?php echo  $summary->done_missions ?></span>
                                    </span>
                                    <span class="counter-info">
                                        <span class="counter-info-inner">ماموریتهای انجام شده</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

			<!-- Page Content -->
			<section class="page-content">
				<div class="container">

					<!-- Stats -->

					<!-- Stats / End -->

                    <?php if(isset($tools) && !empty($tools)): ?>

                        <div class="spacer-xl"></div>

                        <div class="title-bordered">
                            <h2> ابزارها <small>جدید ترین ها</small></h2>
                        </div>
                        <div class="row tools-container">
                            <?php
                                foreach($tools as $tool)
                                {
                                    echo $this->instrument->htmlTemplate($tool,'col-xs-12 col-sm-4 col-md-3 col-lg-2');
                                }
                            ?>
                        </div>
                        <div class="spacer"></div>
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4">
                                <a title="مشاهده همه بزارها" class="btn btn-default btn-block" href="<?php echo  site_url('tools') ?>">مشاهده همه ابزارها</a>
                            </div>
                        </div>
                    <?php endif ?>

                    <?php if(isset($users) && !empty($users)): ?>

                        <div class="spacer-xl"></div>
                        <!-- Listings -->
                        <div class="title-bordered">
                            <h2> متخصصین <small>برگزیده ها</small></h2>
                        </div>
                        <div class="job_listings">
                            <ul class="job_listings">
                                <?php foreach($users as $user):?>
                                    <li class="job_listing<?php echo  $user->approved ? ' job_position_featured':'' ?>">
                                        <a title="<?php echo  html_escape($user->displayname) ?>" href="<?php echo  site_url("user/{$user->username}") ?>">
                                            <div class="job_img">
                                                <img src="<?php echo  $this->user->getAvatarSrc(NULL,150,$user->avatar)  ?>" alt="<?php echo  html_escape($user->displayname) ?>" class="company_logo">
                                            </div>
                                            <div class="position">
                                                <h3><?php echo  $user->displayname ?></h3>
                                                <div class="company">
                                                    <strong class="date">
                                                        <?php $isonline = $user->is_online ? 'on':'' ?>
                                                        <span title="<?php echo  $isonline ? 'آنلاین':'آفلاین' ?>" class="is-online <?php echo  $isonline ?>" data-id="<?php echo  $user->id ?>">
                                                            <i class="fa fa-globe"></i>
                                                        </span>
                                                        <span title="آخرین بازدید"><?php
                                                            if( $user->last_seen )
                                                            {
                                                                $d = $this->tools->Date($user->last_seen,FALSE);

                                                                echo "<span class=\"relative-date last-seen\" datestr=\"'{$d['datestr']}'\" date=\"{$d['date']}\" data-id=\"{$user->id}\">{$d['date']}</span>";
                                                            }
                                                            else
                                                                echo 'نا مشخص';
                                                            ?></span>
                                                    </strong>
                                                </div>
                                            </div>
                                            <div class="location">
                                                <i class="fa fa-map-marker"></i>
                                                <?php
                                                if( $this->tools->isJson($user->state_json) )
                                                {
                                                    foreach($this->tools->jsonDecode($user->state_json) as $id)
                                                        echo "<span>{$groups[$id]} &nbsp; </span>";
                                                }
                                                ?>
                                            </div>
                                            <div class="rating">
                                                <?php echo  $this->rate->ratingHtml(NULL,NULL,NULL,array(
                                                    'rating' => $user->rating ,
                                                    'sum'    => $user->rating_sum ,
                                                    'total'  => $user->rate_count
                                                ))  ?>
                                                <div class="reviews-num text-muted"><?php echo  $user->rate_count ? :'بدون'  ?> رای </div>
                                            </div>
                                            <ul class="meta">
                                                <li class="job-type">
                                                    <?php
                                                    if( $this->tools->isJson($user->skill_json) )
                                                    {
                                                        foreach($this->tools->jsonDecode($user->skill_json) as $id)
                                                            echo "<span>{$groups[$id]} &nbsp; </span>";
                                                    }
                                                    ?>
                                                </li>
                                            </ul>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                        <div class="spacer"></div>
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4">
                                <a title="مشاهده همه متخصصین" class="btn btn-default btn-block" href="<?php echo  site_url('proficient') ?>">مشاهده همه متخصصین</a>
                            </div>
                        </div>

                        <div class="spacer-xxl"></div>
                    <?php endif ?>


                    <div class="title-bordered">
                        <h2><?php echo  $config['home_page_text_title'] ?><small><?php echo  $config['home_page_text_small'] ?></small></h2>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <?php echo  $config['home_page_text'] ?>
                        </div>
                    </div>
                    <div class="spacer-xl"></div>

                    
                    <?php if($services): ?>
                        <div class="title-bordered">
                            <h2>سرویس های ابزاربر <small>امکانات و ویژگی های سایت</small></h2>
                        </div>
                        <div class="row fix-cols">
                            <?php foreach($services as $service): ?>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <div class="icon-box">
                                    <div class="icon">
                                        <i class="fa fa-<?php echo  $service->icon ?>"></i>
                                    </div>
                                    <div class="icon-box-body">
                                        <h5><?php echo  $service->title ?></h5>
                                        <p><?php echo  nl2br(html($service->excerpt)) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach ?>
                        </div>
					<?php endif ?>

                    <?php if($testimonials): ?>
                        <div class="title-bordered">
                            <h2>نظرات<small>از زبان کاربران</small></h2>
                        </div>
                        <div class="row fix-cols">
                            <?php foreach($testimonials as $post): ?>
                                <div class="col-xs-12 col-sm-6 col-md-3">
                                    <div class="testimonial">
                                        <blockquote>
                                            <p><?php echo  nl2br(html($post->excerpt)) ?></p>
                                        </blockquote>
                                        <div class="bq-author">
                                            <figure class="author-img">
                                                <img src="<?php echo  $this->post->thumb($post->thumb,150) ?>" alt="<?php echo html_escape($post->title) ?>">
                                            </figure>
                                            <h6><?php echo  $post->title ?></h6>
                                            <span class="bq-author-info"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>

				</div>
			</section>
			<!-- Page Content / End -->

	<script>
		jQuery(function($){
			$('body').addClass('loading');
		});
		
		$(window).load(function(){
			$('.flexslider').flexslider({
				animation: "fade",
				controlNav: true,
				directionNav: false,
				prevText: "",
				nextText: "",
				start: function(slider){
					$('body').removeClass('loading');
				}
			});
		});
	</script> 