<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--360浏览器优先以webkit内核解析-->


    <title>image - 主页</title>

    <link rel="shortcut icon" href="/favicon.ico">
    <link href="/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css?v=4.1.0" rel="stylesheet">


</head>

<body class="gray-bg">
<div class="row  border-bottom white-bg dashboard-header">

    <div class="row">
        <div class="col-sm-12">

            <div class="panel blank-panel">

                <div class="panel-heading">
                    <div class="panel-title m-b-md">
                        <h4>选择平台</h4>
                    </div>
                    <div class="panel-options">

                        <ul class="nav nav-tabs">
                            <?foreach ($site_list as $key=>$site){?>
                                <li class="<?if($key == 0){?> active <?}?>">
                                    <a data-toggle="tab" href="#tab-<?=$site['type']?>"><i class="fa fa-laptop"></i>
                                    <?=$site['site_name']?>
                                    </a>
                                </li>
                            <?}?>

                        </ul>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="tab-content">
                        <?foreach ($site_list as $key=>$site){?>

                            <div id="tab-<?=$site['type']?>" class="tab-pane <?if($key == 0){?> active <?}?>">
                                <form action="/image/down/" method="get" target="_blank">
                                    <p class="text-danger">例如:<?=$site['example_url']?></p>
                                    <input type="hidden" name="type" value="<?=$site['type']?>">
                                    <div class="input-group">
                                        <input type="url" name="url" class="form-control">
                                        <span class="input-group-btn">
                                        <button type="submit" class="btn btn-primary">下载</button>
                                    </span>
                                    </div>
                                </form>
                            </div>

                        <?}?>

                    </div>

                </div>

            </div>
        </div>
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>查询记录</h5>
                </div>
                <div class="ibox-content">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>平台</th>
                            <th>链接</th>
                            <th>时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?foreach ($record_list as $record){?>
                            <tr>
                                <td><?=$record['site']?></td>
                                <td><?=$record['page_url']?></td>
                                <td><?=$record['created_at']?></td>
                                <td>
                                    <a class="btn-sm btn-info" onclick="openLayerFrame('预览图片','/image/prev/?url=<?=$record['page_url']?>')">预览</a>
                                    <a class="btn-sm btn-success" target="_blank" href="/image/down/?url=<?=urlencode($record['page_url'])?>&type=<?=$record['site']?>">下载</a>
                                </td>
                            </tr>
                        <?}?>
                        </tbody>
                    </table>

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

    <script>
        function go(url) {
            window.open(url);
        }

        function openLayerFrame(title,url){

            //iframe层
            parent.layer.open({
                type: 2,
                title: title,
                shadeClose: true,
                maxmin: true,
                shade: 0.8,
                area: ['90%', '90%'],
                content: url //iframe的url
            });
        }
    </script>
</body>

</html>
