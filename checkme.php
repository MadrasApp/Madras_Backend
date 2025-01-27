<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>API docs</title>
    <link type="text/css" rel="stylesheet" href="style/_master/css/_admin/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="style/_master/css/_admin/bootstrap-rtl.css">
    <link type="text/css" rel="stylesheet" href="style/_master/css/_admin/public.css">
    <script src="js/jquery.min.js"></script>
    <style>
        html, body {
            direction: ltr !important;
        }

        body {
            padding: 50px;
        }

        * {
            text-align: left !important;
        }

        table tr td {
            font-size: 90%;
            color: #c7254e;
            background-color: #f9f2f4;
            border-radius: 0;
            font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
        }

        table tr td:nth-child(3) {
            color: #5125c7;
            background-color: #f3f2f9;
        }

        hr {
            margin-top: 26px;
            margin-bottom: 26px;
            border: 0;
            border-top: 1px solid #717171;
        }
    </style>

</head>

<body>

<h1>Day ClassOnline</h1>
<form method="post" action="api/v2/DayClassOnline" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Day ID</td>
            <td>
                <select name="dayofweek" type="text" value="0" class="form-control">
                    <option value="0">شنبه</option>
                    <option value="1">یکشنبه</option>
                    <option value="2">دوشنبه</option>
                    <option value="3">سه شنبه</option>
                    <option value="4">چهارشنبه</option>
                    <option value="5">پنجشنبه</option>
                    <option value="6">جمعه</option>
                </select>
            </td>
            <td>dayofweek</td>
            <td>required</td>
            <td>Day ID
                <div> 0 : شنبه</div>
                <div>1 : یکشنبه</div>
                <div>2 : دوشنبه</div>
                <div>3 : سه شنبه</div>
                <div>4 : چهارشنبه</div>
                <div>5 : پنجشنبه</div>
                <div>6 : جمعه</div>
            </td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="extlogin">Get Class Online</h1>
<form action="api/v2/getClassOnline" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>ترتیب نمایش</td>
            <td>
                <select name="offer" class="form-control">
                    <option value="0">پیش فرض</option>
                    <option value="1">آخرین دوره‌ها</option>
                    <option value="2">دوره‌های برگزیده</option>
                    <option value="3">دوره‌های پیشنهادی</option>
                </select>
            </td>
            <td>section</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="start" type="text" value="0" class="form-control"></td>
            <td>start</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td>optional</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Account Class Online</h1>
<form action="api/v2/buyAccountClassOnline" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Class Online id</td>
            <td><input name="classonline_id" type="text" value="31" class="form-control"></td>
            <td>classonline_id</td>
            <td>optional|numeric</td>
            <td>category id is required</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Account Class Online Bazar</h1>
<form action="api/v2/buyAccountClassOnlineBazar" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Class Online id</td>
            <td><input name="classonline_id" type="text" value="31" class="form-control"></td>
            <td>classonline_id</td>
            <td>optional|numeric</td>
            <td>category id is required</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Action Code</td>
            <td><input name="action" type="text" value="" class="form-control"></td>
            <td>action</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
        <tr>
            <td>Reference Code</td>
            <td><input name="ref_id" type="text" value="" class="form-control"></td>
            <td>ref_id</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="extlogin">Get Class Online Accounts</h1>
<form action="api/v2/getClassOnlineAccounts" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="start" type="text" value="0" class="form-control"></td>
            <td>start</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="extlogin">Get Categories</h1>
<form action="api/v2/getCategories" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>ترتیب نمایش</td>
            <td>
                <select name="offer" class="form-control">
                    <option value="0">پیش فرض</option>
                    <option value="1">آخرین دوره‌ها</option>
                    <option value="2">دوره‌های برگزیده</option>
                    <option value="3">دوره‌های پیشنهادی</option>
                </select>
            </td>
            <td>section</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="start" type="text" value="0" class="form-control"></td>
            <td>start</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td>optional</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Category</h1>
<form action="api/v2/buyCategory" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Category id</td>
            <td><input name="category_id" type="text" value="1,2,3,4" class="form-control"></td>
            <td>category_id</td>
            <td>optional|string 1,2,...</td>
            <td>category id is required</td>
        </tr>
        <tr>
            <td>MemberShip Plan</td>
            <td><input name="plan_id" type="text" value="3,6,1,12" class="form-control"></td>
            <td>plan_id</td>
            <td>optional|stirng [
                <span dir="ltr">1:یک ماهه</span> /
                <span dir="ltr">3:سه ماهه</span> /
                <span dir="ltr">6:شش ماهه</span> /
                <span dir="ltr">12:یک ساله</span>
                ]
            </td>
            <td>category id is required same as category_id</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Category Bazar</h1>
<form action="api/v2/buyCategoryBazar" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Category id</td>
            <td><input name="category_id" type="text" value="1,2,3,4" class="form-control"></td>
            <td>category_id</td>
            <td>optional|string 1,2,...</td>
            <td>category id is required</td>
        </tr>
        <tr>
            <td>MemberShip Plan</td>
            <td><input name="plan_id" type="text" value="3,6,1,12" class="form-control"></td>
            <td>plan_id</td>
            <td>optional|stirng [
                <span dir="ltr">1:یک ماهه</span> /
                <span dir="ltr">3:سه ماهه</span> /
                <span dir="ltr">6:شش ماهه</span> /
                <span dir="ltr">12:یک ساله</span>
                ]
            </td>
            <td>category id is required same as category_id</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Action Code</td>
            <td><input name="action" type="text" value="" class="form-control"></td>
            <td>action</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
        <tr>
            <td>Reference Code</td>
            <td><input name="ref_id" type="text" value="" class="form-control"></td>
            <td>ref_id</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Bought Category</h1>
<form action="api/v2/BoughtCategory" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="extlogin">Get Membership</h1>
<form action="api/v2/getMembership" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>ترتیب نمایش</td>
            <td>
                <select name="offer" class="form-control">
                    <option value="0">پیش فرض</option>
                    <option value="1">آخرین دوره‌ها</option>
                    <option value="2">دوره‌های برگزیده</option>
                    <option value="3">دوره‌های پیشنهادی</option>
                </select>
            </td>
            <td>section</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="start" type="text" value="0" class="form-control"></td>
            <td>start</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td>optional</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Membership</h1>
<form action="api/v2/buyMembership" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Membership id</td>
            <td><input name="membership_id" type="text" value="31" class="form-control"></td>
            <td>membership_id</td>
            <td>optional|numeric</td>
            <td>membership id is required</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Membership Bazar</h1>
<form action="api/v2/buyMembershipBazar" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Membership id</td>
            <td><input name="membership_id" type="text" value="31" class="form-control"></td>
            <td>membership_id</td>
            <td>optional|numeric</td>
            <td>membership id is required</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Action Code</td>
            <td><input name="action" type="text" value="" class="form-control"></td>
            <td>action</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
        <tr>
            <td>Reference Code</td>
            <td><input name="ref_id" type="text" value="" class="form-control"></td>
            <td>ref_id</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="extlogin">Get Advertise</h1>
<form action="api/v2/getAdvertise" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Priority ID</td>
            <td>
                <select name="priority" class="form-control">
                    <option value="0">پیش فرض</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </td>
            <td>priority</td>
            <td>optional</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1 id="extlogin">Get Doreh</h1>
<form action="api/v2/getDoreh" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>ID عرضه کننده</td>
            <td><input name="supplierid" type="text" value="0" class="form-control"></td>
            <td>supplierid</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>ترتیب نمایش</td>
            <td>
                <select name="offer" class="form-control">
                    <option value="0">پیش فرض</option>
                    <option value="1">آخرین دوره‌ها</option>
                    <option value="2">دوره‌های برگزیده</option>
                    <option value="3">دوره‌های پیشنهادی</option>
                </select>
            </td>
            <td>section</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="start" type="text" value="0" class="form-control"></td>
            <td>start</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td>optional</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1 id="extlogin">Get Supplier</h1>
<form action="api/v2/getSupplier" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Supplier ID</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<h1 id="extlogin">Get Suppliers</h1>
<form action="api/v2/getSuppliers" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>پیشنهادی</td>
            <td>
                <select name="offer" class="form-control">
                    <option value="0">پیش فرض</option>
                    <option value="1">عادی</option>
                    <option value="2">پیشنهادی</option>
                </select>
            </td>
            <td>section</td>
            <td>required</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1 id="extlogin">User Class</h1>
<form action="api/v2/userClasses" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Class</h1>
<form action="api/v2/buyClass" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Doreh Class id</td>
            <td><input name="dorehclassid" type="text" value="31" class="form-control"></td>
            <td>dorehclassid</td>
            <td>optional|numeric</td>
            <td>dorehclass id is required</td>
        </tr>
        <tr>
            <td>Doreh id</td>
            <td><input name="dorehid" type="text" value="31" class="form-control"></td>
            <td>dorehid</td>
            <td>optional|numeric</td>
            <td>doreh id is required</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="buy-book">Buy Class Bazar</h1>
<form action="api/v2/buyClassBazar" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Doreh Class id</td>
            <td><input name="dorehclassid" type="text" value="31" class="form-control"></td>
            <td>dorehclassid</td>
            <td>optional|numeric</td>
            <td>dorehclass id is required</td>
        </tr>
        <tr>
            <td>Doreh id</td>
            <td><input name="dorehid" type="text" value="31" class="form-control"></td>
            <td>dorehid</td>
            <td>optional|numeric</td>
            <td>doreh id is required</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Action Code</td>
            <td><input name="action" type="text" value="" class="form-control"></td>
            <td>action</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
        <tr>
            <td>Reference Code</td>
            <td><input name="ref_id" type="text" value="" class="form-control"></td>
            <td>ref_id</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>
<h1 id="extlogin">New Login</h1>
<form action="api/v2/extlogin" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>کد احراز هویت</td>
            <td><input type="text" name="Message" class="form-control"></textarea></td>
            <td>Message</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getBookDetail</h1>
<form action="api/v2/getBookDetail" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>optional</td>
            <td></td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>optional|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>ID کتاب</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<h1>getClassDetail</h1>
<form action="api/v2/getClassDetail" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>ID کلاس</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<h1>getSubJalasat</h1>
<form action="api/v2/getSubJalasat" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>ID جلسه</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<h1>getJalasat</h1>
<form action="api/v2/getJalasat" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>ID دوره</td>
            <td><input name="dorehid" type="text" value="0" class="form-control"></td>
            <td>dorehid</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>ID کلاس</td>
            <td><input name="classid" type="text" value="0" class="form-control"></td>
            <td>classid</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>ID دوره کلاس</td>
            <td><input name="dorehclassid" type="text" value="0" class="form-control"></td>
            <td>dorehclassid</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getLastClasses</h1>
<form action="api/v2/getLastClasses" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Section</td>
            <td>
                <select name="section" class="form-control">
                    <option value="place">مکانهای برگزاری کلاسها</option>
                    <option value="ostad">لیست اساتید</option>
                    <option value="doreh">لیست دوره ها</option>
                    <option value="classroom">لیست کل کلاسها</option>
                    <option value="favclass">لیست کلاسهای پرمخاطب</option>
                    <option value="dorehclass">لیست کلاسهای دوره ها</option>
                    <option value="book">لیست کتابها</option>
                    <option value="favbook">لیست کتاب‌های پرمخاطب</option>
                    <option value="bookupdate">لیست کتاب‌های به روز شده</option>
                    <option value="supplierbook">لیست آخرین کتاب‌های عرضه کننده</option>
                    <option value="supplierfavbook">لیست کتاب‌های پرمخاطب عرضه کننده</option>
                    <option value="specialbook">لیست کتابها پیشنهادی</option>
                    <option value="samembook">لیست کتاب‌های هم موضوع</option>
                    <option value="sametbook">لیست کتاب‌های هم عنوان [در یک دوره]</option>
                    <option value="choosedclass">کلاس‌های برگزیده عرضه کننده</option>
                    <option value="offerclass">کلاس‌های پیشنهادی عرضه کننده</option>
                    <option value="lastclass">آخرین کلاس‌های عرضه کننده</option>
                    <option value="updatedclass">کلاس‌های به روز شده</option>
                </select>
            </td>
            <td>section</td>
            <td>required</td>
            <td>doreh,classroom,dorehclass,ostad,place</td>
        </tr>
        <tr>
            <td>ID دوره / کلاس</td>
            <td><input name="dorehid" type="text" value="0" class="form-control"></td>
            <td>dorehid</td>
            <td></td>
            <td dir="rtl">
                فقط در حالت لیست کلاسهای دوره ها کاربرد دارد
                <br>
                در حالت لیست کتاب‌های هم موضوع ID کلاس ارجاع می شود
                <br>
                در مواردی که دوره وجود دارد ID دوره ارجاع داده می شود
                <br>
                در بخش کلاس‌های برگزیده عرضه کننده و پیشنهادی , آخرین کلاس‌های عرضه کننده ,کلاس‌های به روز شده ID عرضه
                کننده ارجاع می شود
            </td>
        </tr>
        <tr>
            <td>title</td>
            <td><input name="title" type="text" value="" class="form-control"></td>
            <td>title</td>
            <td></td>
            <td>title search</td>
        </tr>
        <tr>
            <td>Membership</td>
            <td>
                <select name="hasmembership" id="output" class="form-control">
                    <option value="0">All</option>
                    <option value="1">Just Has Membership</option>
                </select>
            </td>
            <td>hasmembership</td>
            <td>
                <div>0 : All</div>
                <div>1 : Just Has Membership</div>
            </td>
            <td>required</td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="start" type="text" value="0" class="form-control"></td>
            <td>start</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getFavorite</h1>
<form action="api/v2/getFavorite" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Action</td>
            <td>
                <select name="action" class="form-control">
                    <option value="add">Add new Favorite</option>
                    <option value="remove">Remove current Favorite</option>
                    <option value="show">Show Favorite</option>
                </select>
            </td>
            <td>action</td>
            <td>required</td>
            <td>add | remove</td>
        </tr>
        <tr>
            <td>section</td>
            <td>
                <select name="section" class="form-control">
                    <option value="book">کتاب</option>
                    <option value="category">سطح</option>
                    <option value="classroom">کلاس</option>
                    <option value="doreh">دوره</option>
                    <option value="dorehclass">کلاس دوره</option>
                    <option value="jalasat">جلسه</option>
                    <option value="subjalasat">زیر جلسه</option>
                    <option value="mecat">دسته بندی موضوعی</option>
                    <option value="tecat">دسته بندی عنوانی</option>
                    <option value="ostad">استاد</option>
                    <option value="questions">پرسش</option>
                    <option value="supplier">عرضه کنندگان</option>
                    <option value="tashrihi">سوالات تشریحی</option>
                    <option value="tests">سوالات تستی</option>
                    <option value="publisher">انتشارات</option>
                    <option value="writer">نویسنده</option>
                    <option value="translator">مترجم</option>
                </select>
            </td>
            <td>section</td>
            <td>required</td>
            <td>custom name you want to controll</td>
        </tr>
        <tr>
            <td>sectionid</td>
            <td><input name="sectionid" type="text" value="0" class="form-control"></td>
            <td>sectionid</td>
            <td>required</td>
            <td>Content ID to save</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getResult</h1>
<form action="api/v2/getResult" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th>نیاز به پارامتر ندارد</th>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getResetLeitner</h1>
<form action="api/v2/getResetLeitner" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Box ID</td>
            <td><input name="lid" type="text" value="0" class="form-control"></td>
            <td>lid</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>سطح</td>
            <td><input name="level" type="text" value="0" class="form-control"></td>
            <td>level</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getExtraBook</h1>
<form action="api/v2/getExtraBook" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Book ID</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>ForceLogOut</h1>
<form action="api/v2/ForceLogOut" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getUserMobile</h1>
<form action="api/v2/getUserMobile" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getPublisher</h1>
<form action="api/v2/getPublisher" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getWriter</h1>
<form action="api/v2/getWriter" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getTranslator</h1>
<form action="api/v2/getTranslator" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getLeitbox</h1>
<form action="api/v2/getLeitbox" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>addLeitbox</h1>
<form action="api/v2/addLeitbox" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره جعبه</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>نام جعبه</td>
            <td><input name="title" type="text" value="" class="form-control"></td>
            <td>title</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>دفعات یادآوری</td>
            <td><input name="remember" type="text" value="" class="form-control"></td>
            <td>remember</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>delLeitbox</h1>
<form action="api/v2/delLeitbox" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره جعبه</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getLeitner</h1>
<form action="api/v2/getLeitner" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره جعبه</td>
            <td><input name="lid" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>addLeitner</h1>
<form action="api/v2/addLeitner" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>ID کتاب</td>
            <td><input name="bookid" type="text" value="" class="form-control"></td>
            <td>bookid</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>شماره لایتنر</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>شماره جعبه</td>
            <td><input name="lid" type="text" value="" class="form-control"></td>
            <td>lid</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>گروه بندی</td>
            <td>
                <select class="form-control" name="catid">
                    <option value="1">یادداشت</option>
                    <option value="2">لغت</option>
                    <option value="3">سوال تستی</option>
                    <option value="4">سوال تشریحی</option>
                </select>
            </td>
            <td>catid</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>سطح</td>
            <td><input name="level" type="text" value="0" class="form-control"></td>
            <td>level</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>عنوان</td>
            <td><input name="title" type="text" value="" class="form-control"></td>
            <td>title</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>توضیحات</td>
            <td><input name="description" type="text" value="" class="form-control"></td>
            <td>description</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>جواب داده شده</td>
            <td>
                <select name="answertrue" class="form-control">
                    <option value="1" selected>درست بود</option>
                    <option value="0">اشتباه بود</option>
                </select>
            </td>
            <td>answertrue</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>تگ</td>
            <td><input name="tag" type="text" value="" class="form-control"></td>
            <td>tag</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>delLeitner</h1>
<form action="api/v2/delLeitner" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره لایتنر</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>supportBooks</h1>
<form action="api/v2/supportBooks" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>sendOffer</h1>
<form action="api/v2/sendOffer" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>کلمه</td>
            <td><input name="kalameh" type="text" class="form-control"></td>
            <td>kalameh</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>ترجمه پیشنهادی</td>
            <td><textarea name="translate" class="form-control"></textarea></td>
            <td>translate</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>زبان اصلی</td>
            <td><input name="fromlang" type="text" value="1" class="form-control"></td>
            <td>fromlang</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>زبان ترجمه</td>
            <td><input name="tolang" type="text" value="3" class="form-control"></td>
            <td>tolang</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getDicLang</h1>
<form action="api/v2/getDicLang" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getKalameh</h1>
<form action="api/v2/getKalameh" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>کلمه</td>
            <td><input name="kalameh" type="text" class="form-control"></td>
            <td>kalameh</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>ترجمه</td>
            <td><textarea name="translate" class="form-control"></textarea></td>
            <td>translate</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>زبان اصلی</td>
            <td><input name="fromlang" type="text" value="1" class="form-control"></td>
            <td>fromlang</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>زبان ترجمه</td>
            <td><input name="tolang" type="text" value="3" class="form-control"></td>
            <td>tolang</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>Limitstart</td>
            <td><input name="Limitstart" type="text" value="0" class="form-control"></td>
            <td>Limitstart</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Limit</td>
            <td><input name="Limit" type="text" value="100" class="form-control"></td>
            <td>Limit</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getUserKalameh</h1>
<form action="api/v2/getUserKalameh" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>کلمه</td>
            <td><input name="kalameh" type="text" class="form-control"></td>
            <td>kalameh</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>ترجمه</td>
            <td><textarea name="translate" class="form-control"></textarea></td>
            <td>translate</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>زبان اصلی</td>
            <td><input name="fromlang" type="text" value="1" class="form-control"></td>
            <td>fromlang</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>زبان ترجمه</td>
            <td><input name="tolang" type="text" value="3" class="form-control"></td>
            <td>tolang</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>Book ID</td>
            <td><input name="bookid" type="text" value="0" class="form-control"></td>
            <td>bookid</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>ID کلمه</td>
            <td><input name="dicid" type="text" value="0" class="form-control"></td>
            <td>dicid</td>
            <td>required|nubmer</td>
            <td>
                0 : ثبت به عنوان کلمه جدید
                <hr>
                n : بروزرسانی کلمه موجود
            </td>
        </tr>
        <tr>
            <td>Limitstart</td>
            <td><input name="Limitstart" type="text" value="0" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Limit</td>
            <td><input name="Limit" type="text" value="100" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>DeleteUserKalameh</h1>
<form action="api/v2/DeleteUserKalameh" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>ID کلمه</td>
            <td><input name="dicid" type="text" value="0" class="form-control"></td>
            <td>dicid</td>
            <td>required|nubmer</td>
            <td></td>
        </tr>
        <tr>
            <td>Book ID</td>
            <td><input name="bookid" type="text" value="0" class="form-control"></td>
            <td>bookid</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>UpdateAppVer</h1>
<form action="api/v2/UpdateAppVer" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>مدل گوشی</td>
            <td><input name="mobilemodel" type="text" value="" class="form-control"></td>
            <td>mobilemodel</td>
            <td>required</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>نسخه اندروید گوشی</td>
            <td><input name="android" type="text" value="" class="form-control"></td>
            <td>android</td>
            <td>required</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>نسخه برنامه</td>
            <td><input name="AppVer" type="text" value="" class="form-control"></td>
            <td>AppVer</td>
            <td>required</td>
            <td>تعیین نسخه برنامه مدرس</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>removeMyBook</h1>
<form action="api/v2/removeMyBook" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Book ID</td>
            <td><input type="text" name="bookid" class="form-control"></textarea></td>
            <td>bookid</td>
            <td>required|int</td>
            <td>required for delete</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getQuestion</h1>
<form action="api/v2/getQuestion" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Output</td>
            <td>
                <select name="type">
                    <option value="json">json</option>
                    <option value="zip">zip</option>
                </select>
            </td>
            <td>type</td>
            <td></td>
            <td>json / zip</td>
        </tr>
        <tr>
            <td>Question ID</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td></td>
            <td>optional</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getCatQuest</h1>
<form action="api/v2/getCatQuest" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getQuestions</h1>
<form action="api/v2/getQuestions" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Output</td>
            <td>
                <select name="type">
                    <option value="json">json</option>
                    <option value="zip">zip</option>
                </select>
            </td>
            <td>type</td>
            <td></td>
            <td>json / zip</td>
        </tr>
        <tr>
            <td>Question Cat ID</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td></td>
            <td>optional</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getGeo</h1>
<form action="api/v2/getGeo" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>section</td>
            <td>
                <select name="section" class="form-control">
                    <option value="country">country</option>
                    <option value="province">province</option>
                    <option value="city">city</option>
                </select>
            </td>
            <td>section</td>
            <td>section</td>
            <td>required</td>
        </tr>
        <tr>
            <td>parent</td>
            <td><input name="parent" type="text" value="0" class="form-control"></td>
            <td>parent</td>
            <td></td>
            <td>required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>ChangeMobile</h1>
<form action="api/v2/ChangeMobile" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه جاری</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>در صورت نیاز به تایید احراز هویت حتما شماره همراه تایید هویت ثبت گردد</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره همراه جدید</td>
            <td><input name="tel" type="text" value="" class="form-control"></td>
            <td>tel</td>
            <td>tel</td>
            <td>required</td>
        </tr>
        <tr>
            <td>کد اعتبار سنجی</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>code</td>
            <td>required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>getPrice</h1>
<form action="api/v2/getPriceMix" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>در صورت نیاز به تایید احراز هویت حتما شماره همراه تایید هویت ثبت گردد</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>code</td>
            <td>required</td>
        </tr>
        <tr>
            <td>case</td>
            <td><input name="case" type="text" value="level" class="form-control"></td>
            <td>level</td>
            <td></td>
            <td>required</td>
        </tr>
        <tr>
            <td>id</td>
            <td><input name="id" type="text" value="44" class="form-control"></td>
            <td>id</td>
            <td></td>
            <td>required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1 id="Tags">Tags</h1>
<form action="api/v2/Tags" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>در صورت نیاز به تایید احراز هویت حتما شماره همراه تایید هویت ثبت گردد</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>
<h1>Highlights</h1>
<form action="api/v2/Highlights" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>در صورت نیاز به تایید احراز هویت حتما شماره همراه تایید هویت ثبت گردد</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>

<h1 id="valid-data">Validate Eitaa Data</h1>
<form action="api/v2/eitaaAutoAuth" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>توکن ایتا / Eitaa Token</td>
            <td><input name="eitaa_token" type="text" value="" class="form-control"></td>
            <td>Eitaa Token</td>
            <td>required</td>
            <td>required for validate data</td>
        </tr>
        <tr>
            <td>ایتا دیتا / Eitaa Data</td>
            <td><input name="eitaa_data" type="text" value="" class="form-control"></td>
            <td>Eitaa Data</td>
            <td>required</td>
            <td>required for validate data</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>

<h1>SendUserSMS</h1>
<form action="api/v2/SendUserSMS" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>در صورت نیاز به تایید احراز هویت حتما شماره همراه تایید هویت ثبت گردد</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>ایمیل</td>
            <td><input name="email" type="text" value="" class="form-control"></td>
            <td>email</td>
            <td>در صورت نیاز به تایید احراز هویت در صورت نیاز به ایمیل توسط این روش تایید می شود</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>نوع پیام</td>
            <td>
                <select name="MessageType" class="form-control">
                    <option value="1">پیام معمولی</option>
                    <option value="2">احراز هویت</option>
                </select>
            </td>
            <td>نوع پیام</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>پیام معمولی</td>
            <td><textarea name="Message" class="form-control"></textarea></td>
            <td>در صورت انتخاب "پیام معمولی" نیاز است</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>مدل گوشی</td>
            <td><input name="mobilemodel" type="text" value="" class="form-control"></td>
            <td>mobilemodel</td>
            <td>required</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>نسخه اندروید گوشی</td>
            <td><input name="android" type="text" value="" class="form-control"></td>
            <td>android</td>
            <td>required</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>نسخه برنامه</td>
            <td><input name="AppVer" type="text" value="" class="form-control"></td>
            <td>AppVer</td>
            <td>required</td>
            <td>تعیین نسخه برنامه مدرس</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>SendSupportSMS</h1>
<form action="api/v2/SendSupportSMS" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه پشتیبان</td>
            <td><input name="support" type="text" value="" class="form-control"></td>
            <td>support</td>
            <td>در صورت نیاز به تایید احراز هویت حتما شماره همراه تایید هویت ثبت گردد</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>در صورت نیاز به تایید احراز هویت حتما شماره همراه تایید هویت ثبت گردد</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>نوع پیام</td>
            <td>
                <select name="MessageType" class="form-control">
                    <option value="1">پیام معمولی</option>
                    <option value="2">احراز هویت</option>
                </select>
            </td>
            <td>نوع پیام</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>پیام معمولی</td>
            <td><textarea name="Message" class="form-control"></textarea></td>
            <td>در صورت انتخاب "پیام معمولی" نیاز است</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>VerifySMS</h1>
<form action="api/v2/VerifySMS" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>کد احراز هویت</td>
            <td><input type="text" name="Message" class="form-control"></textarea></td>
            <td>Message</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>ExpDiscountCode</h1>
<form action="api/v2/ExpDiscountCode" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>Recover Users Books</h1>
<form action="api/v2/RecoverUsersBooks" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>User Books</h1>
<form action="api/v2/bookList" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>show Nenbership books</td>
            <td>
                <select name="hasmembership">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </td>
            <td>hasmembership</td>
            <td>optional : default = 0</td>
            <td>show books have mebership</td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="limitstart" type="text" value="0" class="form-control"></td>
            <td>limitstart</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>Get Book</h1>
<form action="api/v2/getBook" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره ID کتاب</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Output</td>
            <td>
                <select name="type">
                    <option value="json">json</option>
                    <option value="zip">zip</option>
                </select>
            </td>
            <td>type</td>
            <td></td>
            <td>json / zip</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<hr>
<h1>PWA Get Book</h1>
<form action="api/v2/ema_getBook" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره ID کتاب</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Output</td>
            <td>
                <select name="type">
                    <option value="json">json</option>
                    <option value="zip">zip</option>
                </select>
            </td>
            <td>type</td>
            <td></td>
            <td>json / zip</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>Get Book Title</h1>
<form action="api/v2/getBookTitle" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره ID کتاب</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>Check Update Need</h1>
<form action="api/v2/neddUpdate" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>شماره ID کتاب</td>
            <td><input name="id" type="text" value="" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>User Save Azmoon</h1>
<form action="api/v2/SaveAzmoon" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Term</td>
            <td><input name="term" type="text" value="1" class="form-control"></td>
            <td>term</td>
            <td>numeric</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>User name</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>آی دی کتاب</td>
            <td><input name="bookid" type="text" value="" class="form-control"></td>
            <td>bookid</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>مدل آزمون</td>
            <td>
                <select name="azmoon_type">
                    <option value="1">1 : تستی</option>
                    <option value="2">2 : تشریحی</option>
                </select>
            </td>
            <td>azmoon_type</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>نوع آزمون</td>
            <td>
                <select name="azmoon_time">
                    <option value="1">1 : آزمون 1</option>
                    <option value="2">2 : آزمون 2</option>
                </select>
            </td>
            <td>azmoon_time</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>تعداد سوالات آزمون</td>
            <td><input name="azmoon_questions" type="text" value="" class="form-control"></td>
            <td>azmoon_questions</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>تعداد جواب درست</td>
            <td><input name="azmoon_true" type="text" value="" class="form-control"></td>
            <td>azmoon_true</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>تعداد جواب نادرست</td>
            <td><input name="azmoon_false" type="text" value="" class="form-control"></td>
            <td>azmoon_false</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>تعداد سوال بدون پاسخ</td>
            <td><input name="azmoon_none" type="text" value="" class="form-control"></td>
            <td>azmoon_none</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>نمره آزمون</td>
            <td><input name="azmoon_result" type="text" value="" class="form-control"></td>
            <td>azmoon_result</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>نمره کل آزمون</td>
            <td><input name="azmoon_final" type="text" value="" class="form-control"></td>
            <td>azmoon_final</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>محدوده آزمون</td>
            <td><input name="azmoon_mahdoode" type="text" value="" class="form-control"></td>
            <td>azmoon_mahdoode</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>زمان برگزاری آزمون</td>
            <td><input name="azmoon_date" type="text" value="" class="form-control"></td>
            <td>azmoon_date</td>
            <td>required</td>
            <td>به صورت <span dir="ltr"><?php echo date("Y-m-d H:i:s"); ?></span></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>User Azmoons</h1>
<form action="api/v2/GetUserAzmoon" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>User name</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Term</td>
            <td><input name="term" type="text" value="0" class="form-control"></td>
            <td>term</td>
            <td>numeric</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>آی دی کتاب</td>
            <td><input name="bookid" type="text" value="0" class="form-control"></td>
            <td>bookid</td>
            <td>required</td>
            <td>0 : همه کتابها</td>
        </tr>
        <tr>
            <td>مدل آزمون</td>
            <td>
                <select name="azmoon_type">
                    <option value="0">0 : همه موارد</option>
                    <option value="1">1 : تستی</option>
                    <option value="2">2 : تشریحی</option>
                </select>
            </td>
            <td>azmoon_type</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
        <tr>
            <td>نوع آزمون</td>
            <td>
                <select name="azmoon_time">
                    <option value="0">0 : همه موارد</option>
                    <option value="1">1 : آزمون 1</option>
                    <option value="2">2 : آزمون 2</option>
                </select>
            </td>
            <td>azmoon_time</td>
            <td>required</td>
            <td>required for register azmoon</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>
<hr>
<h1>Category Books</h1>
<form id="getCategoryBooks" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Category</td>
            <td><input name="category" id="category" type="text" value="0" class="form-control"></td>
            <td>Category ID</td>
            <td>required</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Output</td>
            <td>
                <select name="output" id="output" class="form-control">
                    <option value="0">Full</option>
                    <option value="1">Simple</option>
                </select>
            </td>
            <td>0 : Full</td>
            <td>1 : Simple</td>
            <td>output</td>
        </tr>
        <tr>
            <td>Membership</td>
            <td>
                <select name="hasmembership" id="output" class="form-control">
                    <option value="0">All</option>
                    <option value="1">Just Has Membership</option>
                </select>
            </td>
            <td>hasmembership</td>
            <td>
                <div>0 : All</div>
                <div>1 : Just Has Membership</div>
            </td>
            <td>required</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="10" class="form-control"></td>
            <td>limit</td>
            <td>required</td>
            <td>Limit show</td>
        </tr>
        <tr>
            <td>start</td>
            <td><input name="limitstart" type="text" value="0" class="form-control"></td>
            <td>limitstart</td>
            <td>required</td>
            <td>start show from record</td>
        </tr>
    </table>
    <p>
        <input type="button" value="Try it !" class="btn btn-primary" onClick="LoadMe();">
    </p>
</form>
<script>
    function LoadMe() {
        category = jQuery("#category").val();
        url = "api/v2/getCategoryBooks/" + category;
        jQuery("#getCategoryBooks").attr("action", url);
        jQuery("#getCategoryBooks").submit();
    }
</script>
<hr>

<hr>
<h1 id="buy-book">Buy Book Bazar</h1>
<form action="api/v2/buyBookBazar" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Book id</td>
            <td><input name="bookid" type="text" value="325" class="form-control"></td>
            <td>bookid</td>
            <td>numeric</td>
            <td>bookid id is required</td>
        </tr>
        <tr>
            <td>Discount Code</td>
            <td><input name="code" type="text" value="" class="form-control"></td>
            <td>code</td>
            <td>string</td>
            <td>code is optional</td>
        </tr>
        <tr>
            <td>Action Code</td>
            <td><input name="action" type="text" value="" class="form-control"></td>
            <td>action</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
        <tr>
            <td>Reference Code</td>
            <td><input name="ref_id" type="text" value="" class="form-control"></td>
            <td>ref_id</td>
            <td>string</td>
            <td>action is required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1>Bazar Accept Pay</h1>

<form action="api/v2/AcceptBazarPay" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Type</td>
            <td>
                <select name="type" class="form-control">
                    <option value="book">کتاب</option>
                    <option value="payeh">پایه</option>
                    <option value="sath">سطح</option>
                </select>
            </td>
            <td>type</td>
            <td>required</td>
            <td>book/payeh/sath</td>
        </tr>
        <tr>
            <td>ID</td>
            <td><input name="id" type="text" value="0" class="form-control"></td>
            <td>id</td>
            <td>required</td>
            <td>Book / Payeh / Sath ID</td>
        </tr>
        <tr>
            <td>Factor</td>
            <td><input name="factor" type="text" value="0" class="form-control"></td>
            <td>factor</td>
            <td>required</td>
            <td>Factor Number</td>
        </tr>
        <tr>
            <td>Price</td>
            <td><input name="price" type="text" value="0" class="form-control"></td>
            <td>Price</td>
            <td>required</td>
            <td>Factor Price</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="register">All Book List</h1>
<form action="api/v2/allbookList" method="post" target="_blank">

    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Price Type</td>
            <td>
                <div><label> همه کتابها : <input name="price" type="radio" value="0" class="form-control"></label></div>
                <div><label> کتابهای رایگان : <input name="price" type="radio" value="1" class="form-control"></label>
                </div>
                <div><label> کتابهای پولی : <input name="price" type="radio" value="2" class="form-control"></label>
                </div>
            </td>
            <td>price</td>
            <td>required|min_length[12]|max_length[17]|valid_mac</td>
            <td> OR <br> 01-23-45-67-89-ab OR <br> 0123456789ab OR <br> 0123.4567.89ab</td>
        </tr>

        <tr>
            <td>Category ID</td>
            <td><input name="catid" type="text" value="0" class="form-control"></td>
            <td>catid</td>
            <td>optional</td>
            <td>0</td>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|min_length[12]|max_length[17]|valid_mac</td>
            <td> OR <br> 01-23-45-67-89-ab OR <br> 0123456789ab OR <br> 0123.4567.89ab</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>مرتب سازی</td>
            <td>
                <select name="order">
                    <option value="">پیش فرض</option>
                    <option value="p.id ASC">قدیمی ها اول</option>
                    <option value="p.id DESC">جدیدی ها اول</option>
                    <option value="p.title ASC">عنوان صعودی</option>
                    <option value="p.title DESC">عنوان نزولی</option>
                </select>
            </td>
            <td>order</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>limit</td>
            <td><input name="limit" type="text" value="50" class="form-control"></td>
            <td>limit</td>
            <td>optional|exact_length[32]</td>
            <td></td>
        </tr>
        <tr>
            <td>limitstart</td>
            <td><input name="limitstart" type="text" value="0" class="form-control"></td>
            <td>limitstart</td>
            <td>optional|exact_length[32]</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>
<h1 id="register">Register</h1>
<form action="api/v2/register" method="post" target="_blank">

    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>

        <tr>
            <td>Username</td>
            <td><input name="username" type="text" value="Mohamad" class="form-control"></td>
            <td>username</td>
            <td>required|alpha_dash|is_unique[users.username]|min_length[4]|max_length[30]</td>
            <td></td>
        </tr>

        <tr>
            <td>Mobile</td>
            <td><input name="mobile" type="text" value="09195690112" class="form-control"></td>
            <td>mobile</td>
            <td>required</td>
            <td>OR +989195690112</td>
        </tr>

        <tr>
            <td>Email</td>
            <td><input name="email" type="text" value="info@gmail.com" class="form-control"></td>
            <td>email</td>
            <td>required|valid_email</td>
            <td></td>
        </tr>

        <tr>
            <td>Password</td>
            <td><input name="password" type="text" value="1234" class="form-control"></td>
            <td>password</td>
            <td>required|min_length[4]|max_length[30]</td>
            <td></td>
        </tr>

        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|min_length[12]|max_length[17]|valid_mac</td>
            <td> OR <br> 01-23-45-67-89-ab OR <br> 0123456789ab OR <br> 0123.4567.89ab</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>


<h1 id="login">Login</h1>
<form action="api/v2/login" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>

        <tr>
            <td>Username</td>
            <td><input name="username" type="text" value="Mohamad" class="form-control"></td>
            <td>username</td>
            <td></td>
        </tr>

        <tr>
            <td>Password</td>
            <td><input name="password" type="text" value="1234" class="form-control"></td>
            <td>password</td>
            <td></td>
        </tr>

        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|min_length[12]|max_length[17]|valid_mac</td>
            <td> OR <br> 01-23-45-67-89-ab OR <br> 0123456789ab OR <br> 0123.4567.89ab</td>
        </tr>

    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="logout">Logout</h1>
<form action="api/v2/logout" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|min_length[12]|max_length[17]|valid_mac</td>
            <td> OR <br> 01-23-45-67-89-ab OR <br> 0123456789ab OR <br> 0123.4567.89ab</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<h1 id="collabrationMessageEitaa">collabrationMessageEitaa</h1>
<form action="api/v2/collabrationMessageEitaa" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>پیام</td>
            <td><input name="text" type="text" value="" class="form-control"></td>
            <td>text</td>
            <td></td>
            <td> OR <br> 01-23-45-67-89-ab OR <br> 0123456789ab OR <br> 0123.4567.89ab</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>


<hr>

<h1 id="reset-password">Reset Password</h1>
<form action="api/v2/resetPassword" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>Email</td>
            <td><input name="email" type="text" value="info@gmail.com" class="form-control"></td>
            <td>email</td>
            <td>required|valid_email|max_length[255]</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="update-profile">Update profile</h1>
<form action="api/v2/updateProfile" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Username</td>
            <td><input name="username" type="text" value="Mohamad" class="form-control"></td>
            <td>username</td>
            <td>required|alpha_dash|is_unique[users.username]|min_length[4]|max_length[30]</td>
            <td></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><input name="email" type="email" value="info@gmail.com" class="form-control"></td>
            <td>email</td>
            <td>required|valid_email|is_unique[users.email]</td>
            <td></td>
        </tr>
        <tr>
            <td>Mobile</td>
            <td><input name="mobile" type="tel" value="09195690112" class="form-control"></td>
            <td>mobile</td>
            <td>required|valid_mobile|is_unique[users.tel]</td>
            <td>09195690112 OR +989195690112</td>
        </tr>
        <tr>
            <td>Avatar</td>
            <td><textarea name="avatar" class="form-control">data:image/png;base64,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</textarea>
            </td>
            <td>avatar</td>
            <td>valid_base64</td>
            <td></td>
        </tr>
        <tr>
            <td>Fullname</td>
            <td><input name="fullname" type="text" value="محمد" class="form-control"></td>
            <td>fullname</td>
            <td>max_length[50]</td>
            <td></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input name="password" type="text" value="1234" class="form-control"></td>
            <td>password</td>
            <td>required|min_length[4]|max_length[30]</td>
            <td></td>
        </tr>
        <tr>
            <td>Gender</td>
            <td><input name="gender" type="text" value="1" class="form-control"></td>
            <td>gender</td>
            <td>in_list[0,1]</td>
            <td>جنسیت : <br> 0 زن <br> 1 مرد</td>
        </tr>
        <tr>
            <td>Age</td>
            <td><input name="age" type="text" value="24" class="form-control"></td>
            <td>age</td>
            <td>numeric|greater_than[0]|less_than[120]</td>
            <td></td>
        </tr>
        <tr>
            <td>National code</td>
            <td><input name="national_code" type="text" value="0123456789" class="form-control"></td>
            <td>national_code</td>
            <td>max_length[20]</td>
            <td>کدملی</td>
        </tr>
        <tr>
            <td>Birthday</td>
            <td><input name="birthday" type="text" value="1/1/1371" class="form-control"></td>
            <td>birthday</td>
            <td>max_length[20]</td>
            <td>تارخ تولد</td>
        </tr>
        <tr>
            <td>Country</td>
            <td><input name="country" type="text" value="ایران" class="form-control"></td>
            <td>country</td>
            <td>max_length[20]</td>
            <td>کشور</td>
        </tr>
        <tr>
            <td>State</td>
            <td><input name="state" type="text" value="تهران" class="form-control"></td>
            <td>state</td>
            <td>max_length[20]</td>
            <td>استان</td>
        </tr>
        <tr>
            <td>City</td>
            <td><input name="city" type="text" value="تهران" class="form-control"></td>
            <td>city</td>
            <td>max_length[20]</td>
            <td>شهر</td>
        </tr>
        <tr>
            <td>Postal code</td>
            <td><input name="postal_code" type="text" value="987654-321" class="form-control"></td>
            <td>postal_code</td>
            <td>max_length[20]</td>
            <td>کدپستی</td>
        </tr>
        <tr>
            <td>address</td>
            <td><input name="address" type="text" value="آدرس" class="form-control"></td>
            <td>address</td>
            <td>max_length[1000]</td>
            <td>آدرس</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>


<hr>

<h1 id="buy-book">Buy book</h1>
<form action="api/v2/buyBook" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Book id</td>
            <td><input name="book_id" type="text" value="31" class="form-control"></td>
            <td>book_id</td>
            <td>optional|numeric</td>
            <td>one of [book or level] id is required</td>
        </tr>
        <tr>
            <td>Level id</td>
            <td><input name="level_id" type="text" value="2" class="form-control"></td>
            <td>level_id</td>
            <td>optional|numeric</td>
            <td>one of [book or level] id is required</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="buy-level">Buy level(discount code)</h1>
<form action="api/v2/buyBook" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Code</td>
            <td><input name="code" type="text" value="abc55" class="form-control"></td>
            <td>code</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>Level id</td>
            <td><input name="level_id" type="text" value="43" class="form-control"></td>
            <td>level_id</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Book id</td>
            <td><input name="book_id" type="text" value="43" class="form-control"></td>
            <td>book_id</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="rate-app">Rate APP</h1>
<form action="api/v2/rateApp" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>برای اینکه اگر قبلا ثبت شده بود آپدیت شود</td>
        </tr>
        <tr>
            <td>Rating</td>
            <td><input name="rating" type="text" value="5" class="form-control"></td>
            <td>rating</td>
            <td>rrequired|numeric|greater_than[0]|less_than[6]</td>
            <td>تعداد ستاره</td>
        </tr>
        <tr>
            <td>text</td>
            <td><input name="text" type="text" value="متن نظر" class="form-control"></td>
            <td>text</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>Name</td>
            <td><input name="name" type="text" value="Name" class="form-control"></td>
            <td>name</td>
            <td></td>
            <td>optional</td>
        </tr>
        <tr>
            <td>Email</td>
            <td><input name="email" type="text" value="" class="form-control"></td>
            <td>email</td>
            <td>valid_email</td>
            <td>optional</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="get-part-data">Get part data</h1>
<form action="api/v2/getPartData" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Part id</td>
            <td><input name="id" type="text" value="532" class="form-control"></td>
            <td>id</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Case</td>
            <td>
                <select name="case" class="form-control">
                    <option value="sound">sound</option>
                    <option value="description">description</option>
                </select>
            </td>
            <td>case</td>
            <td>required|in_list[sound,description]</td>
            <td></td>
        </tr>

    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<?php

$data = [];

$data[] = [
    'id' => '',
    'name' => '',
    'url' => '',
    'fields' => [
        [

        ]
    ]
];

?>


<hr>

<h1 id="add-note">Add Note</h1>
<form action="api/v2/addNote" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Text id</td>
            <td><input name="text_id" type="text" value="513" class="form-control"></td>
            <td>text_id</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Note text</td>
            <td><input name="not_text" type="text" value="" class="form-control"></td>
            <td>not_text</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>Note text user</td>
            <td><input name="not_text_user" type="text" value="" class="form-control"></td>
            <td>not_text_user</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>Title</td>
            <td><input name="title" type="text" value="" class="form-control"></td>
            <td>title</td>
            <td>required|max_length[255]</td>
            <td></td>
        </tr>
        <tr>
            <td>Start</td>
            <td><input name="notstart" type="text" value="" class="form-control"></td>
            <td>notstart</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>End</td>
            <td><input name="end" type="text" value="" class="form-control"></td>
            <td>end</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Sharh</td>
            <td><input name="sharh" type="text" value="0" class="form-control"></td>
            <td>sharh</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>


<hr>

<h1 id="add-highlight">Add Highlight</h1>
<form action="api/v2/addHighlight" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Highlight ID</td>
            <td><input name="highlight_id" type="text" value="0" class="form-control"></td>
            <td>highlight_id</td>
            <td>numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Text id</td>
            <td><input name="text_id" type="text" value="513" class="form-control"></td>
            <td>text_id</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Color</td>
            <td><input name="highlight_color" type="text" value="1" class="form-control"></td>
            <td>highlight_color</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Title</td>
            <td><input name="highlight_title" type="text" value="" class="form-control"></td>
            <td>highlight_title</td>
            <td>optional</td>
            <td></td>
        </tr>
        <tr>
            <td>Text</td>
            <td><input name="highlight_text" type="text" value="" class="form-control"></td>
            <td>highlight_text</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><input name="highlight_description" type="text" value="" class="form-control"></td>
            <td>highlight_description</td>
            <td>optional</td>
            <td></td>
        </tr>
        <tr>
            <td>Start</td>
            <td><input name="highlight_start" type="text" value="10" class="form-control"></td>
            <td>highlight_start</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>End</td>
            <td><input name="highlight_end" type="text" value="20" class="form-control"></td>
            <td>highlight_end</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Sharh</td>
            <td><input name="sharh" type="text" value="0" class="form-control"></td>
            <td>sharh</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="add-highlight">Add Tag For Highlight</h1>
<form action="api/v2/addHighTag" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Tag ID</td>
            <td><input name="hightag_id" type="text" value="0" class="form-control"></td>
            <td>hightag_id</td>
            <td>numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Highlight ID</td>
            <td><input name="highlight_id" type="text" value="0" class="form-control"></td>
            <td>highlight_id</td>
            <td>numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Title</td>
            <td><input name="hightag_title" type="text" value="" class="form-control"></td>
            <td>hightag_title</td>
            <td>required</td>
            <td></td>
        </tr>
        <tr>
            <td>Public</td>
            <td>
                <label> Public : <input name="public" id="public1" type="radio" value="1" class="form-control"
                                        checked="checked"></label>
                <label> Private : <input name="public" id="public0" type="radio" value="0" class="form-control"></label>
            </td>
            <td>public</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>tags</td>
            <td><textarea name="tags" type="text" class="form-control"></textarea></td>
            <td>tags</td>
            <td>json array [{"hightag_id":x,"highlight_id":x,"hightag_title":x,"public":x},...]</td>
            <td>json</td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="add-sound">Add sound to favourite</h1>
<form action="api/v2/addfavSound" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Text id</td>
            <td><input name="text_id" type="text" value="513" class="form-control"></td>
            <td>text_id</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>

<h1 id="add-sound">Delete Item</h1>
<form action="api/v2/deleteItem" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Id</td>
            <td><input name="id" type="text" value="1" class="form-control"></td>
            <td>id</td>
            <td>required|numeric</td>
            <td></td>
        </tr>
        <tr>
            <td>Item</td>
            <td>
                <select name="item" class="form-control">
                    <option value="sound">sound</option>
                    <option value="highlight">highlight</option>
                    <option value="tag">Tag</option>
                    <option value="note">note</option>
                </select>
            </td>
            <td>item</td>
            <td>required|in_list[sound,highlight,note]</td>
            <td></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>

<hr>


<?php

$data = [];

$data['highlights']['added'][] = [
    'highlight_id' => 1,
    'text_id' => 514,
    'highlight_text' => 'text',
    'highlight_color' => 1,
    'highlight_start' => 10,
    'highlight_end' => 20,
    'sharh' => 1
];
$data['highlights']['edited'][] = [
    'highlight_id' => 1234,
    //'text_id'         => 514,
    'highlight_text' => 'text',
    'highlight_color' => 1,
    'highlight_start' => 10,
    'highlight_end' => 20,
    'sharh' => 0
];
$data['highlights']['deleted'] = [1, 2, 3, 4];

//============== notes ===================//
$data['notes']['added'][] = [
    'not_id' => 1,
    'text_id' => 513,
    'not_text' => 'text',
    'not_text_user' => 'user text',
    'title' => 'title',
    'notstart' => 10,
    'end' => 20,
    'sharh' => 1
];
$data['notes']['edited'][] = [
    'not_id' => 12,
    //'text_id'       => 513,
    'not_text' => 'text',
    'not_text_user' => 'user text',
    'title' => 'title',
    'notstart' => 10,
    'end' => 20,
    'sharh' => 0
];
$data['notes']['deleted'] = [5, 6, 7, 8, 9];

//============== sounds ===================//
$data['sounds']['added'][] = [
    'text_id' => 514
];
$data['sounds']['deleted'] = [513];

$json = json_encode($data);

?>

<h1 id="HNS">Highlights - Notes - Sounds</h1>

<form action="api/v2/HNS" method="post" target="_blank">
    <table class="table table-bordered table-hover table-striped">
        <tr>
            <th></th>
            <th></th>
            <th>field name</th>
            <th>filters</th>
            <th>description</th>
        </tr>
        <tr>
            <td>شماره همراه / Mac</td>
            <td><input name="mac" type="text" value="" class="form-control"></td>
            <td>mac</td>
            <td>required|valid_mac</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>Token</td>
            <td><input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX" class="form-control"></td>
            <td>token</td>
            <td>required|exact_length[32]</td>
            <td>required for login</td>
        </tr>
        <tr>
            <td>data</td>
            <td><textarea name="data" class="form-control"><?php echo $json ?></textarea></td>
            <td>data</td>
            <td>required|json</td>
            <td><a href="api/v2/hnsExample">View Example</a></td>
        </tr>
    </table>
    <p>
        <input type="submit" value="Try it !" class="btn btn-primary">
    </p>
</form>


<script>if (window.location.hash) window.location = window.location</script>
</body>
</html>