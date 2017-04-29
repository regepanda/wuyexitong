<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>物业系统</title>
    <link rel="stylesheet" type="text/css" href="/style/style.css" />
    <script src="/lib/jquery/jquery-1.9.1.min.js"></script>
</head>
<body>
<div class="col-sm-12" style="height:100px">

</div>
<div class="container">
    <section id="content">
        <form action="_admin_login" method="post">
            <h1>管理员登录</h1>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div>
                <input type="text" placeholder="用户名" required="" name="user_username" />
            </div>
            <div>
                <input type="password" placeholder="密码" required="" name="user_password" />
            </div>
            <div>
                <input type="submit" value="登录" />
                <a href="#">忘记密码?</a>
            </div>
        </form><!--
        <div class="button">
            <a href="#">选择登录方式</a>
        </div>-->
    </section>
</div>
@include("lib.component")
</body>
</html>
