<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head id="Head">
        <title>欢迎进入<?php echo (C("APP_TITLE")); ?>_后台管理系统</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="../Public/css/default.css" rel="stylesheet" type="text/css" />
        <link href="__PUBLIC__/ui/themes/default/easyui.css" rel="stylesheet" type="text/css"/>
        <link href="__PUBLIC__/ui/themes/default/menu.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="__PUBLIC__/ui/themes/icon.css" />
        <script type="text/javascript" src="__PUBLIC__/ui/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="__PUBLIC__/ui/jquery.easyui.min.js"></script>
        <script type="text/javascript" src='__PUBLIC__/ui/locale/easyui-lang-zh_CN.js'></script>
        <script type="text/javascript" src="../Public/js/common.js"></script>
        <?php echo ($jsfile); ?>
        <script type="text/javascript">
            $(function () {
                $('#loginOut').click(function () {
                    $.messager.confirm('系统提示', '您确定要退出本次登录吗?', function (r) {
                        if (r) {
                            location.href = U("Public/logout");
                        }
                    });
                });
                $('#profile').click(function (){
                    var p = {
                        url:U("Main/profile"),
                        title:'修改个人资料',
                        iconCls:'icon-edit',
                        width:400,
                        height:420,
                        mini:false,
                        max:false,
                        close:true,
                        button:true
                    };
                    windowOpen(p);
                });
                $('#welcomeUser').css('width',$(this).width()-200);
            });
            //保存表单
            function saveOperation(windowId){
                $('#dataTableForm').form('submit',{
                    url:U('User/save'),
                    success:function(result){
                        result = $.parseJSON(result);
                        if (!result){
                            msgShow('提示消息','数据返回失败，请重试！','info');
                            return;
                        } else if(result.statusCode == 1){
                            msgShowRightDiv('保存成功！',2);
                            closeWindow(windowId,true);
                        }else{
                            msgShow('警告',result.message,'warning');
                        }
                    },
                    error: function (){
                        msgShow('错误','发送时遇到系统错误,请与系统管理员联系!','error');
                    }
                });
            }
            
            function openDialogWindow(windowId,options){
                $('#' + windowId).dialog({
                    width: options.width,
                    height: options.height,
                    modal: true,
                    //zIndex: 15000,
                    collapsible: false,
                    minimizable: options.mini,
                    maximizable: options.max,
                    closable: options.close,
                    closed: false,
                    inline: true,
                    href: options.url,
                    buttons:options.button,
                    onBeforeClose:function(){
                        if (confirm('窗口正在关闭，请确认您当前的操作已保存。\n 是否继续关闭窗口？')) {
                            closeWindow(windowId,true);
                        } else {
                            return false; 
                        }
                    }
                });
            }
            
            function closeWindow(windowId,conf){
                if(!conf){
                    $('#' + windowId).dialog('close');
                }else{
                    $('#' + windowId).dialog('close', true);
                }
            }
        </script>

    </head>
    <body class="easyui-layout" style="overflow-y: hidden" scroll="no">
        <!-- 如果未开启Javascript-->
        <noscript><div style=" position:absolute; z-index:100000; height:2046px;top:0px;left:0px; width:100%; background:white; text-align:center;">
                <img src="../Public/images/noscript.gif" alt='抱歉，请开启脚本支持！' />
            </div></noscript>
        <!--页头-->
        <div region="north" split="true" border="false" style="overflow: hidden; height: 113px; line-height: 20px;color: #000; font-family: Verdana, 微软雅黑,黑体; background-color: #e0ecff">
            <div style="width:100%;height:77px;background:url('../Public/images/top_bg.jpg');"><img src="../Public/images/top.jpg"/></div>
<div><div id="welcomeUser" style="width:300px;text-align: right; margin-top: 6px; float: left">欢迎您：<?php echo (session('USER_NICKNAME')); ?></div><div style="width: 190px; text-align: right; margin: 3px; float: right; display: inline"><a href="#" id="profile" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-tip'">修改资料</a>&nbsp;<a href="#" id="loginOut" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-cancel'">退出系统</a></div></div>
        </div>
        <!-- 页脚  -->
        <div region="south" split="true" style="height: 5px; background: #D2E0F2; ">
            <div class="footer"></div>
        </div>
        <!-- 菜单 -->
        <div region="west" hide="true" split="true" title="导航菜单" style="width:180px;" id="west" data-options="plain:true,iconCls:'icon-tabicons75'">
            <div class="easyui-accordion" fit="true" border="false">
    <!-- 循环菜单 -->
    <?php if(is_array($rootTree)): $k = 0; $__LIST__ = $rootTree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><script language="javascript">
            $(function(){
                $('#tree<?php echo ($vo["model_name"]); echo ($vo["model_id"]); ?>').tree({   
                    checkbox: false,   
                    url: U('Main/getUserMenus',{
                        itemId:<?php echo ($vo["model_id"]); ?>
                    }),
                    onClick:function(node){
                        if(node.attributes['children'] == 0){
                            if(node.outurl){
                                if(node.outurl.toLowerCase().indexOf('javascript')>-1){
                                    node.outurl;
                                    return;
                                }else{
                                    var tabPatch = node.outurl;
                                }
                            }else{
                                var tId = node.id.split(':');
                                var tabPatch = U(tId[0] + '/' + tId[1]);
                            }
                            addTab(node.text,tabPatch,node.iconCls);
                        }else{
                            return;
                        }
                    },
                    onDblClick:function(node){
                        if(node.attributes['children'] == 0){
                            return;
                        }else{
                            $(this).tree('expand',node.target);
                        }
                    },
                    onContextMenu: function(e, node){
                        e.preventDefault();
                    }
                });
            });
        </script>
        <div title="<?php echo ($vo["model_title"]); ?>" <?php if(($vo['iconCls']) != ""): ?>data-options="plain:true,iconCls:'<?php echo ($vo["iconCls"]); ?>'"<?php endif; ?> <?php if(($k) == "1"): ?>selected="true"<?php endif; ?> style="overflow:auto;">
            <div class="nav-item">
                <ul id="tree<?php echo ($vo["model_name"]); echo ($vo["model_id"]); ?>" class="easyui-tree"></ul>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
        </div>
        <!-- 右侧内容 -->
        <div id="mainPanle" region="center" style="background: #eee; overflow-y:hidden">
            <div id="tabs" class="easyui-tabs"  fit="true" border="false" >
                <div title="欢迎使用" style="overflow:hidden;" id="home" data-options="plain:true,iconCls:'icon-tabicons15'">
                    <div style="margin: 10px 0px 0px 15px;">
<?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><?php echo(array_keys($info,$vo)[0]); ?>：<?php echo ($vo); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
                </div>
            </div>
        </div>
        <!-- 右键菜单-->
        <div id="mm" class="easyui-menu" style="width:150px;">
            <div id="mm-tabupdate">刷新</div>
            <div class="menu-sep"></div>
            <div id="mm-tabclose">关闭</div>
            <div id="mm-tabcloseall">全部关闭</div>
            <div id="mm-tabcloseother">除此之外全部关闭</div>
            <div class="menu-sep"></div>
            <div id="mm-tabcloseright">当前页右侧全部关闭</div>
            <div id="mm-tabcloseleft">当前页左侧全部关闭</div>
            <div class="menu-sep"></div>
            <div id="mm-exit">退出</div>
        </div>
        <!-- -->
    </body>
</html>