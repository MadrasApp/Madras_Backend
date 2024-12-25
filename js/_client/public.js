var settings = {
    windowFocus : true
};

$.ajaxSetup({
    error: function(x, status, error){
        notify('خطا در اتصال');
    },
});
$(document).ready(function(e) {

    var stay = {};
    $('[on-stay]').on("mouseover",function(event){

        var el = this , func = $(el).attr('on-stay').split(',')[0] ,
            timeOut = $(el).attr('on-stay').split(',')[1];

        if( ! $(el).attr('on-stay-id') )
            $(el).attr('on-stay-id','stay-' + ( $.map( stay ,function(n,i){return i}).length + 1 ));

        var key = $(el).attr('on-stay-id');

        clearTimeout(stay[key]);
        stay[key] = false;

        stay[key] = setTimeout(function(){
            window[func](event,el);
        },timeOut);

    }).on("mouseleave",function(){

        var key = $(this).attr('on-stay-id');
        clearTimeout(stay[key]);
        stay[key] = false;
    });

    $(document).on('keydown','.OnlyNum', function (e) {
        var key = e.charCode || e.keyCode || 0;
        return (
        key == 8 ||
        key == 9 ||
        key == 13 ||
        key == 46 ||
        key == 110 ||
        key == 190 ||
        (key >= 35 && key <= 40) ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
    });

    $(document).on("submit","form",function(e){


        var need = $(this).find('.need') , key = true;
        $(need).each(function(index, el) {

            if( $.trim($(el).val()) == "" )
            {
                $(el).focus();
                key = false;
                return false;
            }
        });

        if( ! key )
        {
            e.preventDefault();
            return false;
        }
    });


    $(document).on("click",function(e){
        var el = e.target;

    }).on("click",".table-op .cover",function(){
        var p = this.parentNode;
        $('.table-op').not(p).removeClass('active');
        $(p).toggleClass('active');
    });

    $(document).on("click",".select-all",function(){
        var selector = $(this).attr('select');

        var els = $('.'+selector);

        if( $(this).is(':checked') || $(this).hasClass('checked') )
        {
            $(els).prop('checked',false);
        }
        else
        {
            $(els).prop('checked','checked');
        }

        $(this).toggleClass('checked');
    });

    $(document).on("change",".chk-tg-next",function(){
        var tg = $(this).attr('data-toggle').split('|'),a = tg[0] ,b = tg[1],
            el = $(this).attr('data-el') || $(this).prev() , val = $(el).val();
        var k  = $(this).is(':checked') ? a:b;
        $(el).val(k);
    });
    $(".chk-tg-next").each(function(index, el) {
        var inp = $(el).attr('data-el') || $(el).prev(),val = $(inp).val(),
            tg = $(el).attr('data-toggle').split('|'),a = tg[0] ,b = tg[1];
        $(el).prop('checked',val==a);
    });
    $(document).on("click",".label-click,.checkbox",function(e){
        var chk = $(this).find('input[type=checkbox]')[0];
        $(chk).prop('checked',!$(chk).is(':checked'))
        $(chk).trigger("change");
        e.preventDefault();
        return false;
    }).on("change",".chk-tg-field",function(e){
        toggleField(this,e);
    }).on("click",".toggle-rate",toggle_rate);

    $(window).resize(function(){
        clearTimeout(wResizeTime);
        wResizeTime = setTimeout(set_window_size,100);
    });

    $('[data-toggle="tab"]').on("click",function(){
        //setTimeout(set_window_size,250);
    });

    $(window).on("blur focus", function(e) {
        var prevType = $(this).data("prevType");

        if (prevType != e.type) {
            switch (e.type) {
                case "blur":
                    clearInterval(updateTimeInterval);
                    clearTimeout(updateOnlinesT);
                    settings.windowFocus = false;
                    break;
                case "focus":
                    settings.windowFocus = true;
                    //updateOnlines();
                    //updateTime();
                    break;
            }
        }
        $(this).data("prevType", e.type);
    }).trigger('focus');

    $(window).ready(function(e) {
        set_window_size();
        //setGoTopButton();
        $('[title]').tooltip();
    });

});

var wResizeTime = false,
    wResizeInt  = false;

function set_window_size(action){
    var xs = 480 , sm = 768 , md = 992 , lg = 1200;

    var wh = $(window).height(),
        ww = $(window).width(),
        hh = $('#header').height(),
        fh = $('#footer').outerHeight(true);

    $('#container').css('min-height',wh-hh-fh);
}

var csrf = function(){ return readCookie('csrf_cookie') };

function logout(){

    $.ajax({
        type: "GET",
        url : AURL + "/logout",
        dataType:"json",
        success: function(data){
            $('.main-nav-box.user-panel .body .result-data').html(get_alert(data));
        },
        error: function(){
            notify('خطا در اتصال');
        }
    });
}

function resend_pass(){

    var data = $.trim($('#forget-email').val()),
        loader = $('#Forgot .modal-footer .loader-data');
    if( data == "" )
    {
        $('#forget-email').focus();
        return;
    }

    $(loader).addClass('loader h4').html('');

    $.ajax({
        type: "POST",
        url: AURL + "/sendpass",
        data:{email:data,csrf_token:csrf},
        dataType:"json",
        success: function(data)
        {
            $(loader).removeClass('loader h4').html('');
            $('.append-data').html(get_alert(data));
        },
        error: function(){
            $(loader).removeClass('loader h4').html('خطا در اتصال');
        }
    });
}

function Merge(obj1, obj2) {

    for (var p in obj2) {
        try {
            if ( obj2[p].constructor==Object ) {
                obj1[p] = MergeRecursive(obj1[p], obj2[p]);
            } else {
                obj1[p] = obj2[p];
            }
        } catch(e) {
            obj1[p] = obj2[p];
        }
    }
    return obj1;
}

function dialog_box(options,Class){

    if(typeof(options)=="string")
        options = {body:options};

    var settings = $.extend({},{
        id        : '',
        name      : 'اعلان',
        body      : '',
        style     : {},
        footer    : true,
        buttons   : null,
        onClose   : null,
        onSubmit  : null,
        close     : true
    },options);

    var style = $.extend({
        width   : 'auto',
        height  : 'auto',
        padding : '20px 40px'
    },settings.style);

    var existDialog = $('#'+(settings.id));
    if(existDialog.length){
        $(existDialog).fadeTo(100,.8,function(){
            $(this).fadeTo(100,1,function(){
                $(this).fadeTo(100,.8,function(){
                    $(this).fadeTo(100,1);
                });
            });
        });
        return;
    }

    var $dialog = $('<div/>').addClass("dialog active");
    var $header = $('<div/>').addClass("dialog-header")
        .html('<i class="fa fa-bell-o"></i> &nbsp; '+settings.name);
    var $body   = $('<div/>').addClass("dialog-body").css(style).html(settings.body);

    if(Class) $dialog.addClass(Class);

    if(settings.id)
        $dialog.attr('id',settings.id);

    $dialog.append($header);
    $dialog.append($body);

    $('<button>',{'type':'button','class':'close'}).html('&times;').appendTo($header)
        .prop('disabled',!settings.close);

    if(settings.footer){

        $footer = $('<div/>').addClass("dialog-footer").append(settings.buttons);
        $dialog.append($footer);
        $footer.find('button').addClass('btn btn-sm');
    }else{

        $('<div/>').addClass('hr').css("margin-bottom",0).appendTo($dialog);
        $('<div/>').css({"padding":5}).append(settings.buttons).appendTo($dialog);
    }

    $('.dialog').removeClass("active");

    var $other = $('.dialog');
    $('body').append($dialog);

    setDialogPosition();

    $dialog/*.draggable({

     containment:'window',
     scroll: false,
     handle:'.dialog-header,.dialog-footer'

     })*/.on("mousedown",function(){

        $('.dialog').removeClass("active");
        $(this).addClass("active");

        var hz = Hz(),zx = parseInt($(this).css("z-index"),10);
        if(zx!=hz)
            $(this).css("z-index",hz+1);

    }).find('button').on("click",function(e){
        if($(this).attr("close")!="0"){

            $dialog.fadeOut("fast",function(){$dialog.remove()});
            if(typeof(settings.onClose)=='function')
                settings.onClose(e,this);
        }
        if($(this).attr("action")=="submit" && typeof(settings.onSubmit)=='function')
            settings.onSubmit(e,this);
    });

    $dialog.css({'z-index':(Hz())+1});

    function setDialogPosition()
    {
        var w    = $dialog.width(),
            ww   = $(window).width(),
            left = (ww/2)-(w/2);

        $dialog.css({'left':left});

        if( ! $dialog.find('input:focus').length )
        {
            var h   = $dialog.height(),
                wh  = $(window).height(),
                top = (wh/2)-(h/2);
            $dialog.css({'top':top});
        }
    }

    $(window).resize(function(){
        if( $('.dialog').length ) setDialogPosition();
    });

}

function Hz(){
    var index_highest = 0;
    $("body *").not('.tooltip,.notify').each(function(index ,el) {
        {
            var index_current = parseInt($(this).css("zIndex"), 10);
            if(index_current > index_highest)
                index_highest = index_current;
        }
    });
    return index_highest;
}
function Alert(el,delay,rep){

    var i = 0;
    rep *= 2;

    var int = setInterval(function(){

        if(i == rep)
            return;

        if(i%2 == 1)
            $(el).addClass('cant-empty');
        else
            $(el).removeClass('cant-empty');

        i++;
    },delay);

}

var notifyQueue = {};
function notify(text,status,delay)
{
    if( ! text ) return;
    if( ! delay ) delay = 4000;

    var Class;
    switch( status )
    {
        case 0 : Class = "success"; break;
        case 1 : Class = "danger";  break;
        case 2 : Class = "warning"; break;
    }

    function show(txt,dly)
    {
        if( ! $('.notify-par').length )
        {
            var $p = $('<div>',{'class':'notify-par'}),
                $n = $('<div>',{'class':'notify fade'}).html(txt);
            $p.append($n).appendTo('body');
            setTimeout(function(){
                $n.addClass('in');
            },100);
        }
        else
        {
            var $n = $('.notify');
            $n.html(txt).addClass('in');
        }

        $n.removeClass('success danger warning').addClass(Class);

        setTimeout(function(){
            $n.removeClass('in');
        },dly);
    }
    show(text,delay);
}


function ajax_fail()
{
    notify('خطا در اتصال');
}

function setGoTopButton(){

    var btn = '<div title="رفتن به بالا" class="" onClick="></div>';

    $('<i>',{'class':'go-top-btn fa fa-4x fa-chevron-circle-up'}).on("click",function(){
        $('html,body').animate({scrollTop:0},1000);
    }).appendTo("body");

    var scrl = $(window).scrollTop() , scTimeOute = false;

    $(window).scroll(function(e) {

        var $this = this , sct = $(this).scrollTop();

        clearTimeout(scTimeOute);
        scTimeOute = setTimeout(function(){

            if( sct < scrl && sct > 100)
                $('.go-top-btn').fadeIn();
            else
                $('.go-top-btn').fadeOut();

            scrl = sct;
        },50);

    });
}

$.fn.check = function(){
    this.each(function(index, el) {
        $(el).addClass("checked").fadeTo(150,0,function(){

            $(el).html("&#9745;").fadeTo(150,1);

        })
    });
};
$.fn.uncheck = function(){

    this.each(function(index, el) {
        $(el).removeClass("checked").fadeTo(150,0,function(){

            $(el).html("&#9744;").fadeTo(150,1);

        })
    });
};


function login(callback){

    if($('.jlogin-cover').length) return;

    var $cover = $('<div/>').addClass('cover jlogin-cover').css("z-index",(Hz())+3);
    $('html,body').css("overflow","hidden");
    $('body').append($cover);


    var $button = $('<button/>',{'class':'btn-primary'}).attr({"action":"submit","close":0}).html('ورود');

    var $btndiv = $('<div/>').append($button).append($('<span/>').addClass('ajax-result'));

    var $form = $('<form/>').addClass('jlogin').append("<p>لطفا دوباره وارد شوید</p>");

    var $loginInp =
        $('<input/>').attr({'type':'text','class':'form-control input-sm','placeholder':'شناسه','name':'username','value':''})
            .css({"display":"block","width":200,"margin-bottom":7}).appendTo($form);


    var $passInp =
        $('<input/>').attr({'type':'password','class':'form-control input-sm','placeholder':'گذرواژه','name':'password','value':''})
            .css({"display":"block","width":200,"margin-bottom":7}).appendTo($form);


    $('<div/>',{'class':'text-muted form-group label-click'})
        .append(
            $('<div/>',{'class':'pull-left'})
                .append(
                    $('<input/>',{id:'sldkfjwoie','class':'cmn-toggle cmrf',type:'checkbox',name:'stay'})
                        .val('true')
                ).append(
                $('<label>').attr('for','sldkfjwoie')
            )
        ).append(
        $('<div>',{'class':'pull-right'}).html('مرا به خاطر بسپار')
    ).append(
        $('<div>',{'class':'clearfix'})
    ).appendTo($form);


    var onSubmit = function(e,btn){

        if( $.trim($loginInp.val()) == "")
        {
            $loginInp.focus();
            return;
        }
        if( $.trim($passInp.val()) == "")
        {
            $passInp.focus();
            return;
        }

        $(btn).next().html('').addClass('loader h5 info');

        var data = $('.jlogin').serializeArray();
        data = form_data(data);

        $.ajax({
            url:URL+"/login",
            type:"POST",
            data:{data:data,csrf_token:csrf},
            dataType:"json",
            success: function(data)
            {
                $(btn).next().removeClass('loader h5 info');
                if(data.done == 1)
                {
                    $(btn).attr({'close':1,'action':''}).trigger("click");
                    $cover.remove();
                    $('html,body').css("overflow","");
                    if(typeof(callback)=='function')
                        callback.call();
                }
                $(btn).next().html(data.msg);
            },
            error: function(){
                $(btn).next().removeClass('loader h5 info').html('خطا در اتصال');
            }
        });
    }

    var options = {
        name      : 'ورود به سیستم',
        body      : $form ,
        style     : {padding:'15px 40px 0 40px'},
        buttons   : $btndiv,
        onSubmit  : onSubmit,
        close	  : false
    };
    dialog_box(options);
}

function Confirm(options){

    var options = $.extend({},{
        url         : '',
        data        : {csrf_token:csrf},
        btn         : 'ادامه',
        btn2        : 'لغو',
        success     : null,
        fail        : null,
        loader      : null,
        loadercolor : 'blue',
        Dname       : '',
        Dhtml       : '',
        Did         : '',
        Dimg        : ' <i class="fa fa-exclamation-triangle fa-3x text-danger vm"></i> '
    },options);
    var $buttons = $('<div/>')
        .append($('<button/>').attr("action","submit").html(options.btn))
        .append($('<button/>').html(options.btn2));

    var onSubmit = function()
    {
        if( options.loader ) $(options.loader).addClass('loader '+ options.loadercolor );
        $.ajax({
            url:options.url,
            type:"POST",
            dataType:"json",
            data:options.data,
            success: function(data){

                //console.log(data);

                if(data == 'login')
                {
                    login(onSubmit);
                }
                else
                {
                    if(data.status == 0)
                    {
                        if( typeof(options.success) == 'function' )
                            options.success(data);
                    }
                    else dialog_box(data.msg);
                }
                if( options.loader ) $(options.loader).removeClass('loader '+ options.loadercolor );
            },
            error: function(){
                if( typeof(options.error) == 'function') options.fail();
                if( options.loader ) $(options.loader).removeClass('loader '+ options.loadercolor );
            }
        });
    }

    var $body = $('<p/>').attr("align","center").html(options.Dimg)
    $('<p/>').attr("align","center").html(options.Dhtml).appendTo($body);

    dialog_box({
        id        : options.Did,
        name      : options.Dname,
        body      : $body ,
        buttons   : $buttons,
        onSubmit  : onSubmit
    });
}


var select_menu_bind = false;

function selectMenu(el,icon){
    var id = 0;
    function create_menu(sel){

        //var selected = $(sel).find(':selected');
        var div = jQuery('<div/>', {'class' : 'select-menu'})
            .append($('<i/>').addClass('fa fa-'+icon));
        var span = jQuery('<span/>', {'class' : 'select-menu-span',text:$(sel).val()});
        var ul = jQuery('<ul/>');
        $(sel).find('option').each(function(index, op) {

            var li = jQuery('<li/>',{
                text: $(op).text(),
                value : $(op).attr('value'),
                'class' : $(op).is(":disabled")?'disable-op':'enable-op'
            }).appendTo(ul);
        });
        $(span).appendTo(div);
        $('<i/>').addClass('fa fa-sort-desc').appendTo(div);
        $(ul).appendTo(div);
        $(sel).attr('sel-num','sel-num-'+id).css("display","none");
        $(div).attr('sel-num','sel-num-'+id).insertBefore(sel);
        id ++;
    }

    create_menu(el);

    if( select_menu_bind  ) return;

    select_menu_bind = true;

    $(document).on("click","body",function(e){
        var el = e.target;
        if($(el).hasClass('select-menu')){
            $('.select-menu:visible').not(el).find('ul').hide();
            $(el).find("ul").toggle();
        }else if($(el).hasClass('select-menu-span')){
            var div = $(el).parent();
            $('.select-menu:visible').not(div).find('ul').hide();
            $(el).parent().find("ul").toggle();
        }else
            $('.select-menu').find("ul").hide();
    });

    $(document).on("click",".select-menu > ul > li",function(e){
        if($(this).hasClass('disable-op')){
            e.preventDefault();
            return false;
        }
        var div = $(this).closest('.select-menu');
        var text = $(this).text();
        var val = $(this).attr("value");
        $(div).find('span').html(text);
        var id = $(div).attr('sel-num')
        $('select[sel-num="'+id+'"]').val(val).trigger("change");
    });
}

function ByteToSize(bytes) {
    if(bytes == 0) return '0 Byte';
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

function strTime() {
    return Math.round(new Date().getTime() / 1000);
}

var updateTimeInterval = false;
function updateTime(){
    function a(){
        var date =  new Date(),
            Ctime = Math.floor(date.getTime()/1000),
            Coffset = date.getTimezoneOffset()*(-60),
            client = Ctime - Coffset;

        $('.relative-date').each(function(index, el) {

            var time = client - $(el).attr('datestr');
            if(time>=0 && time <86400)
            {
                if(time>=0 && time <60)
                    re = 'لحظاتی قبل';
                else
                if(time>=60 && time<3600)
                {
                    time = Math.floor(time/60);
                    re = time+' دقیقه قبل';
                }else
                if(time>=3600 && time<86400)
                {
                    var h = Math.floor(time/3600);
                    re = h+' ساعت قبل';
                }
                $(el).text(re);
            }else{
                $(el).text($(el).attr('date'));
                $(el).removeClass("relative-date");
            }
        });
    }
    a();
    clearInterval(updateTimeInterval);
    updateTimeInterval= setInterval(a,15000);
}

Array.prototype.keySort = function(keys) {

    keys = keys || {};

    var obLen = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key))
                size++;
        }
        return size;
    };

    var obIx = function(obj, ix) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (size == ix)
                    return key;
                size++;
            }
        }
        return false;
    };

    var keySort = function(a, b, d) {
        d = d !== null ? d : 1;
        // a = a.toLowerCase(); // this breaks numbers
        // b = b.toLowerCase();
        if (a == b)
            return 0;
        return a > b ? 1 * d : -1 * d;
    };

    var KL = obLen(keys);

    if (!KL)
        return this.sort(keySort);

    for ( var k in keys) {
        keys[k] =
            keys[k] == 'desc' || keys[k] == -1  ? -1
                : (keys[k] == 'skip' || keys[k] === 0 ? 0
                : 1);
    }

    this.sort(function(a, b) {
        var sorted = 0, ix = 0;

        while (sorted === 0 && ix < KL) {
            var k = obIx(keys, ix);
            if (k) {
                var dir = keys[k];
                sorted = keySort(a[k], b[k], dir);
                ix++;
            }
        }
        return sorted;
    });
    return this;
};

function form_data(data){
    var re = {};
    $(data).each(function(index, el) {
        re[el.name] = el.value;
    });
    return re;
}

function get_alert(data){

    var Class;
    switch( data.status )
    {
        case 0 : Class = "success"; break;
        case 1 : Class = "danger";  break;
        case 2 : Class = "warning"; break;
    }
    var $res   = $('<div/>',{'class':"alert alert-dismissible alert-" + Class})
        .css('margin',"10px 0 0")
        .append(data.msg);

    var $close = $('<button/>',{type:'button','class':'close'})
        .attr('data-dismiss','alert')
        .attr('aria-label','Close')
        .prependTo($res);

    $('<span>').attr('aria-hidden','true').html('&times;').appendTo($close);

    return $res;
}

function resetupic(btn,Item){

    var fieldset = $(btn).closest('fieldset'),
        load = $(btn).find('.loader-data'),
        res  = $(fieldset).find('.result-data');

    $(load).addClass('loader h6 w');
    $(res).html('');

    $.ajax({
        type: "GET",
        url : AURL + "/resetpic/"+Item,
        data: {},
        dataType:"json",
        success: function(data){
            $(load).removeClass('loader h6');
            if(data == "login")
            {
                login(function(){
                    resetupic(btn,Item)
                });
                return;
            }
            if( data.status == 0 )
            {
                var url = data.url + '?s=' + Math.random();
                if( Item == 'cover' )
                    $('.mcover').css("background-image","url('" + url + "')");
                else if(Item == 'avatar')
                    $('.mavatar').attr('src',thumb(url,150))
            }
            $(res).html(get_alert(data));
        },
        error: function(){
            $(load).removeClass('loader h6');
            notify('خطا در اتصال');
        }
    });
}

function update_local(btn,url,callback){

    var form = $(btn).closest('form'),
        need = $(form).find('.need'),
        load = $(btn).find('.loader-data'),
        res  = $(form).find('.result-data'),
        rkey = true;

    if( ! $(load).length ) load = btn;

    $(need).each(function(index, el) {

        if( $.trim($(el).val()) == "" )
        {
            rkey = false;
            $(el).focus();
            return false;
        }
    });
    if( ! rkey ) return;

    $(load).addClass('loader h6 w');
    $(res).html('');

    var data = $(form).serializeArray();
    data = form_data(data);

    $.ajax({
        type: "POST",
        url : AURL + "/" +url,
        data: {data:data,csrf_token:csrf},
        dataType:"json",
        success: function(data){
            $(load).removeClass('loader h6');
            if(data == "login")
            {
                login(function(){
                    update_local(btn,url);
                });
                return;
            }

            $(res).html(get_alert(data));

            if( data.status == 0 )
            {
                if(callback) callback(btn,data);
            }
        },
        error: function(){
            $(load).removeClass('loader h6');
            notify('خطا در اتصال');
        }
    });
}

function toggleField(btn){

    var t=$(btn).data('t'),f=$(btn).data('f'),id=$(btn).val(),dchk=!$(btn).prop('checked');

    $(btn).addClass('chk-loading1');

    $.ajax({
        type: "POST",
        url : AURL + "/toggleField",
        data: {table:t,field:f,id:id,csrf_token:csrf},
        dataType:"json",
        success: function(data){

            if(data == "login")
            {
                login(function(){
                    toggleField(btn)
                });
                return;
            }

            $(btn).removeClass('chk-loading1');
            if( data.status != 0 )
                $(btn).prop('checked',dchk);
        },
        error: function(x, status, error){
            $(btn).removeClass('chk-loading1');
            $(btn).prop('checked',dchk);
            notify('خطا در اتصال');
        }
    });
}

function toggle_rate(){

    var btn = this;

    var data = $(btn).data('toggle');
    $(btn).addClass('loading');

    $.ajax({
        type: "POST",
        url : AURL + "/togglerate",
        data: {data:data,'csrf_token':csrf},
        dataType:"json",
        success: function(data){

            $(btn).removeClass('loading');
            if(data == "login")
            {
                login(function(){
                    toggle_rate(btn);
                });
                return;
            }
            if( data.status == 0 )
                $(btn).toggleClass('on').find('span').html(data.msg != 0 ? data.msg:'');
            else
                notify(data.msg);
        },
        error: function(x, status, error){
            $(btn).removeClass('loading');
            notify('خطا در اتصال');
        }
    });
}

var updateOnlinesT = false;
function updateOnlines() {

    var users = {};
    $('.is-online').each(function(index, el) {
        users[index] = $(el).data('id');
    });
    $.ajax({
        url: AURL+"/onlines",
        type:"POST",
        data:{data:users,csrf_token:csrf},
        dataType:"json",
        success: function(data){

            for( var id in data.users )
            {
                var el = $('.is-online[data-id="'+id+'"]') , key = data.users[id];
                if( key === true )
                    $(el).addClass('on');
                else
                    $(el).removeClass('on');
            }

            clearTimeout(updateOnlinesT);
            updateOnlinesT = setTimeout(updateOnlines,60*1000);
        },
        error: function (data,xhr){
            clearTimeout(updateOnlinesT);
            updateOnlinesT = setTimeout(updateOnlines,10*1000)
        }
    });
}

function userPopup(event,el){

    function getInfo(){
        $.ajax({
            url: AURL+"/userinfo/"+id,
            type:"POST",
            data:{csrf_token:csrf},
            dataType:"json",
            success: function(data){
                setInfo(id,data);
            }
        });
    }

    function appendP(el){

        var userLink = '#',
            userName = '...',
            userId = $(el).attr('data-id'),
            id = 'user-preview-'+userId;

        var $parent = $('<div>',{'class':'user-popup-p','id':id});

        var $userP = $('<div>',{'class':'user user-popup disable fd to-top'}),
            $cover = $('<div>',{'class':'cover'}).appendTo($userP);

        $('<a>',{'href':userLink}).append(
            $('<h4>',{'class':'name'}).html(userName)
        ).appendTo($cover);

        $('<div>',{'class':'avatar cricle'}).appendTo($cover);

        var $ul = $('<ul>',{'class':'linear-btns'}).appendTo($cover);

        $('<li>').data('original-title','نقش').append(
            $('<i>',{'class':'fa fa-flag'})
        ).append($('<span>').html('...')).appendTo($ul);

        $('<li>',{'class':'rate-user toggle-rate'}).data('original-title','پسند ها').append(
            $('<i>',{'class':'fa fa-star'})
        ).append($('<span>').html('...')).appendTo($ul);

        $('<li>').data('original-title','آنلاین').append(
            $('<i>',{'class':'fa fa-globe'})
        ).appendTo($ul);

        var $body = $('<div>',{'class':'body'}).appendTo($userP);

        $('<i>',{'class':'loader h5 center gray vm'}).appendTo($body)

        $userP.appendTo($parent);
        $parent.insertAfter(el);

        setPos(el,$parent);

        setTimeout(function(){$userP.addClass('in');},20);
    }

    function setPos(el,par){
        var p   = $(el).position() ,
            t   = p.top , l = p.left;
        var popup = $(par).find('.user-popup'),
            uw    = $(popup).width(),
            uh    = $(popup).height()
        ww    = $(window).width();

        var class1 = 'top' , class2 = 'left' , defr = 0;


        if( uh > t || uh + defr  > $(el).offset().top - $(window).scrollTop() ){
            class1 = 'bottom';
            t = t + $(el).outerHeight() + defr ;
        }else{
            t -= defr;
        }
        if( uw > l || ww < uw + 150 ){
            class2 = 'right';
            (ww < uw + 150) && (l = ( ww - uw )/2);
        }else{
            l = l + $(el).outerWidth()
        }
        $(popup).removeClass('top-right top-left bottom-right bottom-left to-top to-bottom')
            .addClass(class1 + '-' + class2 + ' to-'+ class1 );

        $(par).css({top:t,left:l});
    }

    function setInfo(id,data){

        if( ! data.done ) return;
        data = data.msg;

        var el = $('#user-preview-' + id);

        $(el).find('.cover').css('background-image',"url('"+thumb(data.cover,300)+"')");
        $(el).find('.avatar').append(
            $('<img>',{'class':'fade'}).attr('src',thumb(data.avatar,150)).load(function(e) {
                $(this).addClass('in');
            })
        );
        $(el).find('.cover .name').html(data.displayname).parent().attr('href',data.link);
        $(el).find('.cover .linear-btns li:first span').html(data.role);

        $(el).find('.cover .linear-btns .rate-user').addClass('toggle-rate')
            .attr('data-toggle','{"table":"users","row":'+id+'}');

        if( data.rated == true )
            $(el).find('.cover .linear-btns .rate-user').addClass('on');
        $(el).find('.cover .linear-btns .rate-user span').html(data.rating);

        if( data.isonline == true )
            $(el).find('.cover .linear-btns li:last-child').addClass('on');

        $(el).find('.body').html('');

        $('<span>',{'class':'relative-date'})
            .attr({
                'datestr':data.last_seen.datestr,
                'date':data.last_seen.date,
                'title':data.last_seen.full
            }).html(data.last_seen.date).appendTo($(el).find('.body'));

        updateTime();
    }

    var id = $(el).attr('data-id');

    var ex = $('#user-preview-' + id);

    if( ex.length )
    {
        var pEl = $(ex).prev();
        if( $(pEl).is(el) )
        {
            if( $(ex).find('.user-popup').hasClass('in') ){
                setPos(el,ex);
                return;
            }
            $('.user-popup').removeClass('in');
            setPos(el,ex);
            $(ex).find('.user-popup').addClass('in');
        }
        else
        {
            $('.user-popup').removeClass('in');
            $(ex).insertAfter(el);
            setPos(el,ex);
            setTimeout(function(){$(ex).find('.user-popup').addClass('in')},20);
        }
        getInfo();
        return;
    }
    else
        $('.user-popup').removeClass('in');

    appendP(el);
    getInfo();
}

function thumb(path,size){

    var Split = path.split('.') ,ext   = Split.pop();
    ext = ( ext === Split ) ? '' : '.' + ext;
    return Split.join('.') + '-' + size + ext;
}

function slideShowInit(el){

    if( ! $(el).length ) return;

    var options = {
        sh      : function () { return $(el).offset().top + $(el).outerHeight(true) },
        pused   : false ,
        i       : readCookie('silderCounter') || 0 ,
        delay   : 10000 ,
        isHover : false
    };

    var	items = $(el).find('.item') , timeOut = false;

    $('<div>',{'class':'next-btn'}).on("click",next).appendTo(el);

    $('<div>',{'class':'prev-btn'}).on("click",prev).appendTo(el);

    $(el).on("mouseover",function(){
        options.isHover = true;
    }).on("mouseleave",function(){
        options.isHover = false;
        if( options.pused ) next()
    });

    function show(i)
    {
        function view(){
            $(items).removeClass('active');
            $(itm).addClass('active');
            init();
        }
        var itm = items[i] , img = $(itm).find('.img img')[0];
        createCookie('silderCounter',i,0.1);

        if( ! $(itm).is('[loaded]') )
        {
            if( ! $(el).find('.loader').length )
                $('<div>',{'class':'loader h4 warning'}).appendTo(el);

            $(img).attr('src',$(img).attr('file')).on('load',function(){
                $(el).find('.loader').remove();
                $(itm).attr('loaded','true').removeClass('failed');
                $(this).show();
                view();
            }).on('error', function() {
                $(el).find('.loader').remove();
                $(this).hide();
                $(itm).addClass('failed');
                view();
            });
        }
        else view();
    }

    function puse(){ clearTimeout(timeOut); options.pused = true; }

    function next(){ options.i++; play(); }

    function prev(){ options.i--; play(); }

    function play()
    {
        options.pused = false;

        if( options.i < 0 ) options.i = items.length-1;
        if( options.i >= items.length ) options.i = 0;

        setTimeout(function(){
            show(options.i);
        },$(el).find('.item.active').length ? 0:200);
    }

    function init(t){
        clearTimeout(timeOut);

        timeOut = setTimeout(function(){

            if( $(window).scrollTop() > options.sh() /*|| ! settings.windowFocus*/ )
            {
                init(1000); return;
            }

            if( options.isHover )
            {
                puse(); return;
            }

            next();

        },t || options.delay);
    }
    play();
}
$(function(){
    if( $('.post .body.view-post').length )
    {
        $('.post .body.view-post').css('font-size',(readCookie('post_font_size') || 100) +'%');
        $('.change-font-size').find('.size-text').html( ( readCookie('post_font_size') || 100 ) +' %');
    }
});
function changeFontSize(btn,action){

    var el = $('.post .body.view-post');
    if( ! $(el).length ) return;
    var size = readCookie('post_font_size') || 100 , step = 10;

    switch(action)
    {
        case '+':
            size = size*1 + step;
            if( size > 200 ) size = 200;
            break;
        case '-':
            size = size*1 - step;
            if( size < 100 ) size = 100;
            break;
        case 'reset':
            size = 100;
            break;
    }

    $(btn).closest('.change-font-size').find('.size-text').html(size+' %');
    $(el).css("font-size",size+'%');
    createCookie('post_font_size',size,10);
}

function replyComment(btn){

    var $n   =  $('#new-comment .well').clone(true),
        $b   = $(btn).closest('header').next().find('.content')[0],
        $it  = $(btn).closest('.item.comment')[0];

    $('.comments .reply-to').remove();

    $n.addClass('reply-to').css('margin',0);

    $n.find('input[type=hidden][name=parent]').val( $($it).attr('item-id'));

    $n.find('.btns').append(
        $('<button>',{'class':'btn btn-default','type':'button'}).html('انصراف').on("click",function(){
            $('.comments .reply-to').remove();
        })
    );
    $n.find('.result-data').html('');

    $n.insertAfter($b);
}
function addcmclbck(btn,data) {
    $(btn).closest('form').find('textarea').val('');

    if( ! data.cm ) return;

    function show(cm)
    {
        $(cm).addClass('new');
        setTimeout(function(){
            $(cm).removeClass('new');
        },4000);
        $('html,body').animate({
            scrollTop:$(cm).offset().top - 200
        },1000);
    }

    var cm = $(data.cm) , rply = $(btn).closest('.well.reply-to');

    if( ! $(rply).length )
    {
        $(cm).prependTo('.cm-container');
        show(cm);
    }
    else
    {
        var replycon = $(btn).closest('.body').find('.reply-con')[0];
        $(cm).appendTo( replycon );
        show(cm);
        $(rply).remove();
    }
}

var searchTimeOut = false,
    searchAjax    = false;
function topSearch(el){
    var val = $.trim($(el).val()),
        res = $('.serach-result'),
        ajx = $('.serach-result .ajax');

    if( searchAjax )
    {
        searchAjax.abort();
        searchAjax = false;
    }

    clearTimeout(searchTimeOut);
    if( val == "" )
    {
        $(res).removeClass('in');
        $(ajx).html('');
        $(el).attr('s','');
    }
    else
    {
        $(res).addClass('in');

        if( $(el).attr('s') != val )
            searchTimeOut = setTimeout(function(){
                $(ajx).html('<div class="loader info h5 center vm"></div>').css('height',50);
                searchAjax =
                    $.ajax({
                        type: "POST",
                        url : AURL + "/search",
                        data: {'csrf_token':csrf,search:val},
                        dataType:"json",
                        success: function(data){

                            $(el).attr('s',val);

                            $(ajx).css('height','').html('');

                            if( data ) showResult(data);
                        },
                        error: function(x, status, error){
                            $(ajx).html('<p align="center">خطا در اتصال</p>');
                        }
                    });
            },1000);
    }

    function showResult(data)
    {
        for( var k in data )
        {
            var itm = data[k];
            if( ! itm.text ) continue;
            $('<div>',{'class':'item'}).append(
                $('<a>',{'href':itm.link}).append(
                    $('<i>',{'class':'fa fa-chevron-left'})
                ).append(
                    $('<span>').html(itm.text)
                )
            ).appendTo(ajx);
        }
    }
}

function eth(el) {
    return el[0].outerHTML;
}