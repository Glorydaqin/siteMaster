<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--360浏览器优先以webkit内核解析-->


    <title>dashboard - 主页</title>

    <link rel="shortcut icon" href="/favicon.ico">
    <link href="/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css?v=4.1.0" rel="stylesheet">


</head>

<body class="gray-bg">
<div class="row  border-bottom white-bg dashboard-header">
    <div class="col-sm-12">
        <blockquote class="text-warning" style="font-size:14px">
            感谢选择Vip For Me,下方是您可访问的服务和对应的实效
            <br>有任何问题联系请联系卖家

            <h4 class="text-danger">全新Vip For Me ,他来了</h4>
        </blockquote>

        <hr>
    </div>
    <div class="row">
        <div class="col-sm-8">

            <div class="panel blank-panel">

                <div class="panel-heading">
                    <div class="panel-title m-b-md">
                        <h4>选择平台</h4>
                    </div>
                    <div class="panel-options">

                        <ul class="nav nav-tabs">
                            <?foreach ($site_list as $key=>$site){?>
                                <li class="<?if($key == 0){?> active <?}?>">
                                    <a data-toggle="tab" href="#tab-<?=$site['id']?>"><i class="fa fa-laptop"></i>
                                    <?=$site['name']?>
                                    </a>
                                </li>
                            <?}?>

                        </ul>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="tab-content">
                        <?foreach ($site_list as $key=>$site){?>

                            <div id="tab-<?=$site['id']?>" class="tab-pane <?if($key == 0){?> active <?}?>">

                                <a class="list-group-item active">
                                    <h3 class="list-group-item-heading"><?=$site['name']?></h3>

                                    <p class="list-group-item-text"><?=$site['desc']?></p>
                                </a>
                                <!--                        <a class="list-group-item">-->
                                <!--                            <h3 class="list-group-item-heading">选择账号</h3>-->
                                <!---->
                                <!--                            <p class="list-group-item-text">沉思十五年中事，才也纵横，泪也纵横，双负箫心与剑名。春来没个关心梦，自忏飘零，不信飘零，请看床头金字经。</p>-->
                                <!--                        </a>-->
                                <div class="faq-item">
                                    <div class="row">
                                        <?foreach ($site['account_list'] as $account){?>
                                            <div class="col-md-8">
                                                <a data-toggle="collapse" class="faq-question" style="color: #f8ac59">账号<?=$account['id']?></a>
                                            </div>
                                            <div class="col-md-4">
                                                <?foreach ($account['target'] as $target){?>
                                                    <span class="btn-sm btn-info"><?=$target['name']?></span>
                                                <?}?>
                                            </div>
                                        <?}?>
                                    </div>
                                </div>
                            </div>

                        <?}?>

                    </div>

                </div>

            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>使用注意</h5>

                </div>
                <div class="ibox-content">
                    <address>
                        <strong>操作：</strong><br>
                        选择账号后即可访问官方服务<br>
                    </address>

                    <address>
                        <strong>加速访问</strong><br>
                        有vpn用户请优先开启全局模式访问，可用保证服务稳定链接
                    </address>
                </div>
            </div>
        </div>

    </div>


    <!-- 全局js -->
    <script src="/js/jquery.min.js?v=2.1.4"></script>
    <script src="/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/js/plugins/layer/layer.min.js"></script>

    <!-- 自定义js -->
    <script src="/js/content.js"></script>

    <!-- 欢迎信息 -->
    <script src="/js/welcome.js"></script>
</body>

</html>
