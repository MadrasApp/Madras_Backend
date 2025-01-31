<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script> URL = '<?php echo site_url('admin/api/') ?>';
    BURL = '<?php echo base_url() ?>'; </script>


<div id="header" class="bg-dark border-light">
    <a href="<?php echo site_url() ?>" target="_blank">
        <img src="<?php echo base_url() . $site_logo ?>" style="max-height:50px;float:right">
        <h3 style="float:right;margin:12px 10px"><?php echo $title ?></h3>
    </a>
    <div class="admin-user-logged-in">

        <div class="user-avatar border-light">
            <img src="<?php echo $this->user->getAvatarSrc(NULL, 150, $this->user->data->avatar) ?>"/>
        </div>

        <span class="user-name bg-light" dir="ltr">
			<i class="fa fa-user"></i> &nbsp; 
			<span dir="auto"><?php echo $this->user->data->displayname ?></span>
        </span>

        <a href="<?php echo site_url('admin/logout') ?>" title="Log out" class="user-logout bg-light ">
            <i class="fa fa-power-off"></i>
        </a>

    </div>

    <div class="clear"></div>
</div>

<div id="container">
    <div id="sidebar" class="dark-box">
        <ul>
            <?php
            $newMsgsCount = $this->db->where('visited', 0)->count_all_results('admin_inbox');
            $newMsgs = $newMsgsCount ? '<span class="badge pull-left" style="background-color:#EC4444">' . $newMsgsCount . '</span>' : '';
            $messageLink = $newMsgsCount ? 'messages/index/new' : 'messages';

            /*$newComCount  = $this->db->where('submitted',0)->count_all_results('comments');
            $newComs      = $newComCount ? '<span class="badge pull-left" style="background-color:#EC4444">'.$newComCount.'</span>':'';
            $commentsLink = $newComCount ? 'comments/index/pending':'comments';*/

            $colors = array('F90', 'F30', 'FF409F', 'CF0', '3C0', '0CF', 'CD81FF', 'FF9841', '9F3');

            $pages = array('home' => array('', 'صفحه اصلی', 'home'));
            /*
            $pages['publisher']  = array('manage_post','اعضای کتاب','publisher',
                    array(
                        'publisher' => array('manage_publisher','انتشارات','pencil'),
                        'writer' => array('manage_writer','نویسندگان','pencil'),
                        'translator' => array('manage_translator','مترجمین','pencil')
                        )
            );
            Alireza Balvardi
            */

            global $POST_TYPES;

            if (isset($POST_TYPES)) {
                foreach ($POST_TYPES as $name => $type) {
                    $pages[$name . '/primary'] = array(0 => 'read_' . $name, 1 => $type['g_name'], 2 => $type['icon'],
                        3 => array(
                            $name . '/primary' => array('read_' . $name, 'مشاهده', 'eye'),
                            $name . '/add' => array('creat_' . $name, 'افزودن', 'pencil'),
                        )
                    );
                    if (in_array('category', $type['support']))
                        $pages[$name . '/primary'][3][$name . '/category'] = array('category_' . $name, 'دسته بندی', 'bookmark');

                    if (isset($type['menu']))
                        foreach ($type['menu'] as $menu_name => $menu)
                            $pages[$name . '/primary'][3][$name . '/' . $menu_name] =
                                array($menu_name . '_' . $name, $menu['name'], $menu['icon']);
                }
            }
            $pages['azmoon'] = array('manage_azmoon', 'ثبت آزمون', 'key');

            /*$pages['media']    = array('','رسانه','photo',
                                    array(
                                        'media'       => array('','فایل ها','photo'),
                                        'media/add'   => array('upload_file','افزودن','plus'),
                                        )
                                    );	*/


            //$pages[$commentsLink] = array('read_comment','نظرات'. $newComs ,'commenting-o',);
            $pages['comments'] = array('read_comment', 'نظرات', 'commenting-o',);

            //$pages['group'] = array('read_group','گروه ها' ,'list',);

            $pages['geosection'] = array('manage_geosection', 'بخش جغرافیایی', 'tags',
                array(
                    'geotype' => array('manage_geotype', 'نوع مناطق', 'tags'),
                    'geosection' => array('manage_geosection', 'مناطق جغرافیایی', 'tags')
                )
            );//Alireza Balvardi

            $pages['users'] = array('manage_users', 'کاربران', 'user',
                array(
                    'users' => array('manage_users', 'مشاهده', 'user'),
                    'users/levels' => array('edit_user_levels', 'سطوح دسترسی', 'tasks'),//Alireza Balvardi
                    'users/adduser' => array('', 'کاربر جدید', 'pencil'),//Alireza Balvardi
                    'users/chart' => array('', 'آمار', 'bar-chart'),//Alireza Balvardi
                    'leitner' => array('leitner', 'جعبه لایتنر', 'calendar'),//Alireza Balvardi
                )
            );

            $pages['advertise'] = array('manage_advertise', 'تبلیغات', 'bank');//Alireza Balvardi
            $pages['payamak'] = array('manage_payamak', 'پیامک', 'mobile');//Alireza Balvardi
            $pages['dictionary'] = array('manage_dictionary', 'لغتنامه', 'tags',
                array(
                    'dictionary' => array('manage_dictionary', 'لغتنامه', 'tags'),
                    'diclang' => array('manage_diclang', 'زبانهای ترجمه', 'tags')
                )
            );//Alireza Balvardi
            $pages['supplier'] = array('manage_supplier', 'عرضه کنندگان', 'tags',
                array(
                    'supplier' => array('manage_supplier', 'عرضه کنندگان', 'tags'),
                    'suppliertype' => array('manage_suppliertype', 'نوع', 'tags'),
                )
            );
            $pages['tecat'] = array('is_supplier', 'دسته بندی عنوانی', 'mobile');//Alireza Balvardi
            $pages['mecat'] = array('is_supplier', 'دسته بندی موضوعی', 'mobile');//Alireza Balvardi
            $pages['membership'] = array('is_supplier', 'اشتراک', 'group');//Alireza Balvardi
            $pages['classonline'] = array('is_supplier', 'کلاسهای آنلاین', 'globe',
                array(
                    'classonline' => array('is_supplier', 'کلاسهای آنلاین', 'globe'),
                    'xlsxclassonline' => array('is_supplier', 'ورود اکسل', 'file-excel-o'),
                )
            );//Alireza Balvardi
            $pages['classroom'] = array('is_supplier', 'کلاسها', 'laptop');//Alireza Balvardi
            $pages['doreh'] = array('is_supplier', 'دوره ها', 'tags',
                array(
                    'nezam' => array('is_supplier', 'نظام', 'tags'),
                    'doreh' => array('is_supplier', 'دوره ها', 'tags'),
                    'dorehclass' => array('is_supplier', 'کلاسهای دوره', 'tags'),
                    'jalasat' => array('is_supplier', 'جلسات کلاسهای دوره', 'tags'),
                )
            );//Alireza Balvardi
            //Alireza Balvardi
            /*
            */
            $pages['discount'] = array('manage_discount', 'کد تخفیف', 'key');
            $pages['payment'] = array('manage_payment', 'پرداخت ها', 'credit-card');
            $pages['gozaresh'] = array('manage_gozaresh', 'گزارش مالی', 'diamond');
            $pages['salereport'] = array('manage_salereport', 'گزارش فروش', 'diamond');

            $pages['questions']
                = array('manage_questions', 'پشتیبانی ها', 'question',
                array(
                    'questions' => array('manage_questions', 'مشاهده', 'question'),
                    'questions/editQuestion' => array('', 'پشتیبانی جدید', 'pencil'),//Alireza Balvardi
                    'catquest' => array('manage_catquest', 'گروه بندی', 'bookmark'),
                )
            );

            //$pages[$messageLink] = array('read_msg','پیام ها'.$newMsgs,'envelope-o',);

            $pages['setting'] = array(
                'change_settings', 'تنظیمات ', 'gears',
                /*array(
                    'setting#home' => array('','عمومی',''),
                    'setting#logo' => array('','لوگو',''),
                    )*/
            );

            $uri2 = $this->uri->segment(2);
            $uri3 = $this->uri->segment(3);

            $c = 0;

            foreach ($pages as $page => $name) :

                //$color = ' style="color:#'.$colors[$c].'"';
                //$c++; if( $c == count($colors) ) $c = 0;

                $color = ' style="color:#0BB0E7"';


                $href = site_url('admin/' . $page);
                $li_class = 'sidebar-item item-' . str_replace('/', '-', $page);

                if (isset($name[3]) && is_array($name[3])) {
                    $li_class .= ' has-menu';
                }

                if ($name[0] && !$this->user->can($name[0])) {
                    if (substr($name[0], 0, 4) == 'read' && isset($name[3]) && is_array($name[3])) {
                        foreach ($name[3] as $PAGE => $NAME) {
                            if ($this->user->can($NAME[0])) {
                                $href = '#';
                            }
                        }
                        if ($href != '#') {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
                ?>
                <?php $thisPage = $uri2 == $page ? 'this-page' : ''; ?>
                <li class="<?php echo $li_class ?>" <?php echo $color ?>>
        <span class="option <?php echo $thisPage ?>">
        <a href="<?php echo $href ?>">
         <i class="fa fa-<?php echo $name[2] ?>"></i>
         <span class="name"><?php echo $name[1] ?></span>
         <?php if (isset($name[3]) && is_array($name[3])) : ?>
            <i class="fa fa-angle-double-down fa-lg toggle-sub-menu"></i>
            </a></span>
                    <ul>
                        <?php foreach ($name[3] as $Page => $Name) : ?>
                            <?php if ($Name[0] && !$this->user->can($Name[0])) continue; ?>
                            <?php $thisPage = current_url() == site_url('admin/' . $Page) ? ' this-page' : ''; ?>
                            <li>
                    <span class="option <?php echo $thisPage ?>">
                    <a href="<?php echo site_url('admin/' . $Page) ?>" class="">
                       <i class="fa fa-<?php echo $Name[2] . ' ' . $thisPage ?>"></i>
                       <span class="name"><?php echo $Name[1] ?></span>
                    </a>
                    </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                        </a></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>

        </ul>
    </div>
    <div id="main">