
var app = angular.module("mainApp", /*["ngRoute"]*/ []);

app.filter('unsafe', function($sce) { return $sce.trustAsHtml; });

app.controller("mainCtrl", function($scope,$http,$templateCache) {

    $templateCache.removeAll();

    $scope.demo_view = function(type) {
        if( type == 3 )
            return $scope.companytype == 'B2C-Brand' || $scope.companytype == 'Both-B2C-and B2B-Brand' || $scope.companytype == 'Agency';
        if( type == 1 )
            return $scope.companytype == 'Influencer-Blogger';
    };

    $scope.demoRequest = function(e){

        var  btn = $(e.target) , form = $(btn).closest('form');

        if($scope.demorequest.$invalid)
        {
            $scope.demo_request_result = '<p style="color:#bd0000">'+ ln['please complete the form'] +'</p>';
            return;
        }

        $(btn).addClass('l w h4');
        $scope.demo_request_result = '';

        var data = $(form).serializeArray();
        data = form_data(data);
        $.ajax({
            url: AURL+"/demorequest",
            type:"POST",
            data:{data:data,csrf_token:csrf},
            dataType:"json",
            success: function(data){

                $(btn).removeClass('l w h4');
                var html = get_alert(data);
                if( data.done ) {
                    $(btn).remove();
                }
                $scope.demo_request_result = eth(html);
                $scope.$digest();
            },
            error: function (){
                $(btn).removeClass('l w h4');
                $scope.demo_request_result = '';
                notify(ln['connection error'] , 2);
            }
        });

    };

    $scope.localSubmit = function(action,event,callback){
        var $form      = event.target ,
            $res       = $form.find('.ajax-result'),
            $btn       = $form.find('input[type=submit]')[0],
            formName   = $form.attr('name'),
            sc         = $scope[formName] ;

        if(sc.$invalid)
        {
            $res.html(get_alert({
                msg    : ln['please complete the form'],
                status : 2
            }));
            return;
        }

        $btn.addClass('l w h4');
        $res.empty();

        var data = $(form).serializeArray();
            data = form_data(data);

        $.ajax({
            url: action,
            type:"POST",
            data:{data:data,csrf_token:csrf},
            dataType:"json",
            success: function(data){

                $btn.removeClass('l w h4');

                if( data.done )
                {
                    if( callback ) callback(data);
                }
                $res.html(get_alert(data));
            },
            error: function (){
                $btn.removeClass('l w h4');
                notify(ln['connection error'] , 2);
                $res.empty();
            }
        });


    };

    $scope.hasPendingRequests = function () {
        return $http.pendingRequests.length > 0;
    };
});

/*
app.config(function($routeProvider, $locationProvider) {
    $routeProvider
        .when("/:resourceUrl*", {
            controller: 'mainCtrl',
            templateUrl : function($a){
                console.log($a);
                $a.angularmode = 'true';

                var url =  $a.resourceUrl + '?';
                for(var k in $a)
                {
                    if( k == 'resourceUrl' ) continue;
                    url += '&'+ k + '=' + ($a[k]);

                    if( k == 'ln') lang = $a[k];
                }
                console.log(url);
                return url;
            }
        });
    $locationProvider.html5Mode(true);
});
*/