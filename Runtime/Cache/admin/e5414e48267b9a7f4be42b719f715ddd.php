<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>欢迎进入<?php echo (C("APP_TITLE")); ?>_后台管理系统</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="../Public/css/default.css" rel="stylesheet" type="text/css" />
        <link href="__PUBLIC__/ui/themes/default/easyui.css" rel="stylesheet" type="text/css"/>
        <link href="__PUBLIC__/ui/themes/default/menu.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="__PUBLIC__/ui/themes/icon.css" />
        <script type="text/javascript" src="__PUBLIC__/ui/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="__PUBLIC__/ui/jquery.easyui.min.js"></script>
        <script type="text/javascript" src='__PUBLIC__/ui/locale/easyui-lang-zh_CN.js'></script>
        <script type="text/javascript" src='__PUBLIC__/ui/plugins/jquery.md5.js'></script>
        <script type="text/javascript" src='__PUBLIC__/ui/plugins/jquery.cookie.js'></script>
        <script type="text/javascript" src="__PUBLIC__/ui/plugins/formValidator-4.1.3.min.js"></script>
        <script type="text/javascript" src="__PUBLIC__/ui/plugins/formValidatorRegex.js"></script>
        <script type="text/javascript" src="__PUBLIC__/ui/plugins/DateTimeMask.js"></script>
        <script type="text/javascript" src="../Public/js/common.js"></script>
    </head>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/edit/editor_all.js"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/edit/editor_config.js"></script>
<body class="easyui-layout">
    <div data-options="region:'west',split:true" title="栏目菜单" style="width:160px;padding1:1px;overflow:auto;">
        <ul id="CategoryListTree"></ul>
    </div>
    <div data-options="region:'center'" style="overflow:auto;">
        <!--表单主体-->
        <table id="categoryListing" toolbar="#listing-toolbar" style="width:auto;height:auto"
               data-options="method: 'post',
               fitColumns: true,
               striped:true,
               nowrap:true,
               rownumbers:true,
               showFooter:true,
               pagination:true,
               pageNumber:<?php echo ($page["currentPage"]); ?>,
               pageSize:<?php echo ($page["numPerPage"]); ?>,
               idField: 'catid',
               sortName: '<?php echo ($page["orderField"]); ?>',
               sortOrder: '<?php echo ($page["orderDirection"]); ?>',
               frozenColumns:[[
               {field:'ck',checkbox:true}
               ]]">
            <thead>
                <tr>
                    <th data-options="field:'catid',width:50,sortable:true,align:'center',hidden:true">ID</th>
                    <th data-options="field:'catname',width:120,sortable:true,align:'center'">栏目名称</th>
                    <th data-options="field:'viewlocation',width:120,sortable:true,align:'center',formatter:function(value,rowData,rowIndex){return navViewOperation(rowData.viewlocation,rowIndex);}">导航栏显示</th>
                    <th data-options="field:'modelname',width:120,sortable:true,align:'center'">所属模块</th>
                    <th data-options="field:'target',width:80,sortable:true,align:'center',formatter:function(value,rowData,rowIndex){return targetOperation(rowData.target,rowIndex);}">打开方式</th>
                    <th data-options="field:'opt',width:180,align:'center',formatter:function(value,rowData,rowIndex){return operation(rowData.catid,rowIndex);}">操作</th>
                </tr>
            </thead>
        </table>
        <!--创建Toolbar-->
        <div id="listing-toolbar" class="datagrid-toolbar">
            <div style="margin:5px; float:left">当前栏目：<span id="chooseThisParentName" style="font-size:14px; color:#FF0000; font-weight:bold">根栏目</span><input id="chooseThisParentId" style="display:none" value="0">
                <input id="searchBtn" class="easyui-searchbox" data-options="searcher:searchOperation,prompt:'请输入关键字'" />
            </div>
            <a href="javascript:void(0)" onClick="javascript:addOperation();" id="curd_addOperation" class="easyui-linkbutton" iconcls="icon-add" plain="true" style="float:left;">添加记录</a>
<a href="javascript:void(0)" onClick="javascript:editOperation();" id="curd_editOperation" class="easyui-linkbutton" iconcls="icon-edit" plain="true" style="float:left;">修改记录</a>
<a href="javascript:void(0)" onClick="javascript:deleteOperation();" id="curd_deleteOperation" class="easyui-linkbutton" iconcls="icon-cancel" plain="true" style="float:left;">删除选中的记录</a>
<div class="datagrid-btn-separator"></div>
<a href="javascript:void(0)" onClick="javascript:reloadOperation();" id="curd_reloadOperation" class="easyui-linkbutton" iconcls="icon-reload" plain="true" style="float:left;">刷新</a>
<div class="datagrid-btn-separator"></div>
<a href="javascript:void(0)" onclick="javascript:exportOperation()" id="curd_exportOperation" class="easyui-linkbutton" iconcls="icon-tabicons154" plain="true" style="float:left;">导出到EXCEL</a>
<a href="javascript:void(0)" onClick="javascript:printOperation()" id="curd_printOperation" class="easyui-linkbutton" iconcls="icon-print" plain="true" style="float:left;">打印当前数据</a>
<div class="datagrid-btn-separator"></div>
<script type="text/javascript">
    var thisUrl = getUrlPatch();
//    if(!uiBottonSecurity(thisUrl.model,'add')){
//        $('#curd_addOperation').linkbutton('disable');
//    }
//    if(!uiBottonSecurity(thisUrl.model,'edit')){
//        $('#curd_editOperation').linkbutton('disable');
//    }
//    if(!uiBottonSecurity(thisUrl.model,'delete')){
//        $('#curd_deleteOperation').linkbutton('disable');
//    }
//    if(!uiBottonSecurity(thisUrl.model,'export')){
//        $('#curd_exportOperation').linkbutton('disable');
//    }
</script>
            <a href="javascript:void(0)" onClick="javascript:importOperation();" id="curd_importOperation" class="easyui-linkbutton" iconcls="icon-tabicons308" plain="true" style="float:left;">Excel导入</a>
            <div class="datagrid-btn-separator"></div>
        </div>
    </div>
<?php echo ($jsfile); ?>
</body>
</html>