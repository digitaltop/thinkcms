//打印
(function($){
    $.printBox = function(rel){
        var _printBoxId = 'printBox';
        var $contentBox = rel ? $('#'+rel) : $("body"),
        $printBox = $('#'+_printBoxId);
			
        if ($printBox.size()==0){
            $printBox = $('<div id="'+_printBoxId+'"></div>').appendTo("body");
        }

        //$printBox.html($contentBox.html()).find("[layoutH]").height("auto");
        window.print();

    }

})(jQuery);

function addTab(subtitle, url, icon) {
    if (!$('#tabs').tabs('exists', subtitle)) {
        $('#tabs').tabs('add', {
            title : subtitle,
            content : createFrame(url),
            closable : true,
            icon : icon
        });
    } else {
        $('#tabs').tabs('select', subtitle);
        $('#mm-tabupdate').click();
    }
    tabClose();
}

function createFrame(url) {
    var path = getUrlPatch(url);
    var s = '<iframe name="frame-' + path.model + '" id="frame-' + path.model + '" scrolling="auto" frameborder="0"  src="' + url + '" style="width:100%;height:100%;"></iframe>';
    return s;
}

$(function(){
    tabClose();
    tabCloseEven();
})

function tabClose(){
    /*双击关闭TAB选项卡*/
    $(".tabs-inner").dblclick(function(){
        var subtitle = $(this).children("span").text();
        $('#tabs').tabs('close',subtitle);
    })

    $(".tabs-inner").bind('contextmenu',function(e){
        $('#mm').menu('show', {
            left: e.pageX,
            top: e.pageY
        });
        
        var subtitle =$(this).children("span").text();
        $('#mm').data("currtab",subtitle);
        
        return false;
    });
}

//绑定右键菜单事件
function tabCloseEven(){
    //最大化
    $('#mm-maxwindow').click(function(){
        if($('#mm-maxval').html() == '最大化'){
            $('#mm-maxwindow').attr('iconCls','icons-tabicons235');
            $('#digitaltopSystemLayout').layout('collapse','north');
            $('#digitaltopSystemLayout').layout('collapse','south');
            $('#digitaltopSystemLayout').layout('collapse','west');
            $('#digitaltopSystemLayout').layout('panel', 'center').panel('resize',{
                height:$('#digitaltopSystemLayout').height()
            });
            $('#mm-maxval').html('还原');
        }else{
            $('#mm-maxwindow').attr('iconCls','icons-tabicons36')
            $('#digitaltopSystemLayout').layout('expand','north');
            $('#digitaltopSystemLayout').layout('expand','south');
            $('#digitaltopSystemLayout').layout('expand','west');
            $('#mm-maxval').html('最大化');
        }
    });
    // 刷新
    $('#mm-tabupdate').click(function() {
        var currTab = $('#tabs').tabs('getSelected');
        var url = $(currTab.panel('options').content).attr('src');
        if(url){
            $('#tabs').tabs('update', {
                tab : currTab,
                options : {
                    content : createFrame(url)
                }
            });
        }
    });
    //关闭当前
    $('#mm-tabclose').click(function(){
        var currtab_title = $('#mm').data("currtab");
        $('#tabs').tabs('close',currtab_title);
    })
    //全部关闭
    $('#mm-tabcloseall').click(function(){
        $('.tabs-inner span').each(function(i,n){
            var t = $(n).text();
            $('#tabs').tabs('close',t);
        });    
    });
    //关闭除当前之外的TAB
    $('#mm-tabcloseother').click(function(){
        var currtab_title = $('#mm').data("currtab");
        $('.tabs-inner span').each(function(i,n){
            var t = $(n).text();
            if(t!=currtab_title)
                $('#tabs').tabs('close',t);
        });    
    });
    //关闭当前右侧的TAB
    $('#mm-tabcloseright').click(function(){
        var nextall = $('.tabs-selected').nextAll();
        if(nextall.length==0){
            //msgShow('系统提示','后边没有啦~~','error');
            //alert('后边没有啦~~');
            return false;
        }
        nextall.each(function(i,n){
            var t=$('a:eq(0) span',$(n)).text();
            $('#tabs').tabs('close',t);
        });
        return false;
    });
    //关闭当前左侧的TAB
    $('#mm-tabcloseleft').click(function(){
        var prevall = $('.tabs-selected').prevAll();
        if(prevall.length==0){
            //alert('到头了，前边没有啦~~');
            return false;
        }
        prevall.each(function(i,n){
            var t=$('a:eq(0) span',$(n)).text();
            $('#tabs').tabs('close',t);
        });
        return false;
    });
}

//检测右键菜单
function checkRightClickEven(){
    //除此之外全部关闭
    var tabcount = $('#tabs').tabs('tabs').length; //tab选项卡的个数
    if (tabcount <= 1) {
        $('#mm-tabcloseother').attr("disabled", "disabled").css({
            "cursor": "default", 
            "opacity": "0.4"
        });
    } else {
        $('#mm-tabcloseother').removeAttr("disabled").css({
            "cursor": "pointer", 
            "opacity": "1"
        });
    }
    //当前页右侧全部关闭
    var tabs = $('#tabs').tabs('tabs');     //获得所有的Tab选项卡
    var tabcount = tabs.length;  //Tab选项卡的个数
    var lasttab = tabs[tabcount - 1];  //获得最后一个Tab选项卡
    var lasttitle = lasttab.panel('options').tab.text(); //最后一个Tab选项卡的Title
    var currtab_title = $('#mm').data("currtab");  //当前Tab选项卡的Title

    if (lasttitle == currtab_title) {
        $('#mm-tabcloseright').attr("disabled", "disabled").css({
            "cursor": "default", 
            "opacity": "0.4"
        });
    } else {
        $('#mm-tabcloseright').removeAttr("disabled").css({
            "cursor": "pointer", 
            "opacity": "1"
        });
    }
    //当前页左侧全部关闭
    var onetab = tabs[0];  //第一个Tab选项卡
    var onetitle = onetab.panel('options').tab.text();  //第一个Tab选项卡的Title
    if (onetitle == currtab_title) {
        $('#mm-tabcloseleft').attr("disabled", "disabled").css({
            "cursor": "default", 
            "opacity": "0.4"
        });
    } else {
        $('#mm-tabcloseleft').removeAttr("disabled").css({
            "cursor": "pointer", 
            "opacity": "1"
        });
    }
}
// 弹出信息窗口 title:标题 msgString:提示信息 msgType:信息类型 [error,info,question,warning]
function msgShow(title, msgString, msgType) {
    $.messager.alert(title, msgString, msgType);
}

//检测数组中的某一个数值是否在
function isCon(arr, val){
    for(var i=0; i<arr.length; i++){
        if(arr[i] == val)
            return true;
    }
    return false;
}

//格式化Unix时间戳
function formatUnixTime(format,timestamp){
    var date = new Date(parseInt(timestamp) * 1000); 
    var year = date.getFullYear(); 
    var month = date.getUTCMonth(); 
    var day = date.getDate(); 
    var hour = date.getHours(); 
    var minute = date.getMinutes(); 
    var second = date.getSeconds(); 
    month = strPad(month,2,'0','left'); 
    day = strPad(day,2,'0','left'); 
    hour = strPad(hour,2,'0','left'); 
    minute = strPad(minute,2,'0','left'); 
    second = strPad(second,2,'0','left'); 
    format = format.replace(/y/gi,year); 
    format = format.replace(/m/gi,month); 
    format = format.replace(/d/gi,day); 
    format = format.replace(/h/gi,hour); 
    format = format.replace(/i/gi,minute); 
    format = format.replace(/s/gi,second); 
    return format; 
}

/** 
* 字符串填充 
* @param string str 要进行填充的字符串 
* @param int len 目标字符串长度 
* @param str chr 用于填充的字符 默认为空格 
* @param str dir 填充位置 left|right|both 默认为right 
*/ 
function strPad(str, len, chr, dir){ 
    str = str.toString(); 
    len = (typeof len == 'number') ? len : 0; 
    chr = (typeof chr == 'string') ? chr : ' '; 
    dir = (/left|right|both/i).test(dir) ? dir : 'right'; 
    var repeat = function(c, l) { 
        var repeat = ''; 
        while (repeat.length < l) { 
            repeat += c; 
        } 
        return repeat.substr(0, l); 
    } 
    var diff = len - str.length; 
    if (diff > 0) { 
        switch (dir) { 
            case 'left':
                str = '' + repeat(chr, diff) + str; 
                break; 
            case 'both':
                var half = repeat(chr, Math.ceil(diff / 2)); 
                str = (half + str + half).substr(1, len); 
                break; 
            default:
                str = '' + str + repeat(chr, diff); 
        } 
    } 
    return str; 
} 

//格式化URL链接
function U(ma,p){
    if(!p)p = {};
    var surl = window.location.href;
    var stma = ma.split('/');
    var rURL = '';
    
    if(stma.length !== 2 || stma[0] == ''){
        msgShow('内部错误：','内部错误：请与您的管理员联系。问题描述：输入的URL:' + ma + '有误','error');
        return false;
    }
        
    var r = getUrlPatch();
    switch(r.urlType){
        case 0:
            if(r.g !== ''){
                rURL = '/' + r.indexfile + 'g=' + r.group + '&';
            }else{
                rURL = '/' + r.indexfile;
            }
            rURL+='m=' + stma[0] + '&a=' + stma[1];
            for(var x in p){
                rURL+='&' + x + '=' + p[x];
            }
            break;
        case 1:
            rURL = '/' + r.indexfile + '/' + stma[0] + '/' + stma[1];
            for(var x in p){
                rURL+='/' + x + '/' + p[x];
            }
            break;
        case 2:
            rURL = '/' + stma[0] + '/' + stma[1];
            for(var x in p){
                rURL+='/' + x + '/' + p[x];
            }
            break;
        default:
            msgShow('内部错误：','内部错误：请与您的管理员联系。问题描述：未匹配到原始URL参数:' + surl,'error');
            return false;
    }
    return rURL;
}

/**
 *URL格式  本系统仅支持的格式有：
 *第一种：http://www.xx.com/index.php?g=groupname&m=model&a=action
 *第二种：http://www.xx.com/index.php/model/action
 *第三种：http://www.xx.com/model/action
 */
function getUrlPatch(surl){
    if(!surl)surl = window.location.href;
    var tURL = replaceStr(surl,'http://','');
    var tp = new Array();
    var urlType = 0;
    var indexfile = '';
    var rURL = {
        urlType:'',
        indexfile:'',
        group:'',
        model:'',
        action:''
    }
    var m = '';
    var a = '';
    var g = '';
    if(tURL.indexOf('/') > -1){
        if(tURL.toLowerCase().indexOf('.php') > 0){
            tp = tURL.split('/');
            tURL = tp[1];
            if(tURL.indexOf('?') > -1){
                // tURL=index.php?g=admin&m=model&a=action
                indexfile = tURL.substr(0,tURL.indexOf('?') + 1);
                tURL = tURL.substr(tURL.indexOf('?') + 1);
                tp = tURL.split('&');
                var tv = new Array();
                for(var v in tp){
                    tv = tp[v].split('=');
                    if(tv[0] == 'g'){
                        g = tv[1];
                    }
                    if(tv[0] == 'm'){
                        m = tv[1];
                    }
                    if(tv[0] == 'a'){
                        a = tv[1];
                    }
                }
            }else{
                // tURL=index.php
                urlType = 1;
                indexfile = tURL;
                g = null;
                m = tp[2];
                tp = tp[3].split('.');
                a = tp[0];
            }
        }else{
            tp = tURL.split('/');
            // /model/action
            urlType = 2;
            indexfile = null;
            g = null;
            m = tp[1]
            tp = tp[2].split('.');
            a = tp[0];
        }
        rURL.urlType = urlType,
        rURL.indexfile = indexfile
        rURL.group = g;
        rURL.model = m;
        rURL.action = a;
        return rURL;
    }else{
        msgShow('内部错误：','内部错误：请与您的管理员联系。问题描述：未获取到合适的浏览器URL:' + surl,'error');
        return false;
    }
}
    
// 本地时钟
function clockon() {
    var now = new Date();
    var year = now.getFullYear(); // getFullYear getYear
    var month = now.getMonth();
    var date = now.getDate();
    var day = now.getDay();
    var hour = now.getHours();
    var minu = now.getMinutes();
    var sec = now.getSeconds();
    var week;
    month = month + 1;
    if (month < 10)
        month = "0" + month;
    if (date < 10)
        date = "0" + date;
    if (hour < 10)
        hour = "0" + hour;
    if (minu < 10)
        minu = "0" + minu;
    if (sec < 10)
        sec = "0" + sec;
    var arr_week = new Array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
    week = arr_week[day];
    var time = "";
    time = year + "年" + month + "月" + date + "日" + " " + hour + ":" + minu
    + ":" + sec + " " + week;

    $("#bgclock").html(time);

    var timer = setTimeout("clockon()", 200);
}

/*
 * 封装提示框
 */
function msgShowRightDiv(msg, lev) {
    var p = {
        title : "提示",
        msg : msg,
        timeout:3000,
        showSpeed : 1000,
        width : 200,
        height : 100,
        showType : 'show'
    }
    switch (lev) {
        case 2: {
            p.showType = 'slide'
            $.messager.show(p);
        }
        break;
        case 3: {
            p.showType = 'fade'
            $.messager.show(p);
        }
        break;
        default: {
            $.messager.show(p);
        }
    }
}

/**
 *AJAX提交表单
 *string url 表单提交URL地址
 *object data 表单数据
 */
function ajaxSubFrom(url,data,resultFunction){
    $.ajax({
        type: 'post', 
        cache: false, 
        dataType: 'json',
        url: url,
        data: data,
        success: function (result){
            if (!result){
                window.top.msgShow('提示消息','提交失败!','info');
                return;
            } else if(result.statusCode !== 1){
                window.top.msgShow('警告',result.message,'warning');
                return;
            }else{
                resultFunction(result);
            }
        },
        error: function (){
            window.top.msgShow('错误','发送时遇到系统错误,请与系统管理员联系!','error');
            return;
        }
    });
}
//弹窗
function windowOpen(options){
    if(options.width.toString().indexOf('%')>0){
        var w = options.width.split('%')[0];
        options.width = $(window.top).width() * (w/100);
    }
    if(options.height.toString().indexOf('%')>0){
        var h = options.height.split('%')[0];
        options.height = $(window.top).height() * (h/100);
    }
    if(!options.width || options.width == '')options.width = $(this).width() * 0.95;
    if(!options.height || options.height == '')options.height =  $(this).height() * 0.95;
    if(options.title) options.title = ' title="' + options.title + '"';
    if(!options.mini || options.mini == '')options.mini = false;
    if(!options.max || options.max == '')options.max = false;
    if(!options.close || options.close == '')options.close = true;
    if(options.iconCls) options.iconCls = ' iconCls="' + options.iconCls + '"';
    var path = getUrlPatch(options.url);
    windowId = 'window-' + path.model;
    if($('#' + windowId,window.parent.document).length >0){
        //曾经调用过该链接
        $('#' + windowId,window.parent.document).remove();//清空页面
    }
    //alert(windowId + '|' + options.url);
    if(options.button){
        options.button = [{
            text:'保存',
            iconCls:'icon-tabicons149',
            handler:function(){
                saveOperation(windowId);
            }
        },{
            text:'取消',
            iconCls:'icon-cancel',
            handler:function(){
                window.parent.closeWindow(windowId);
            }
        }];
    }else{
        options.button = false;
    }    
    var boarddiv = '<div id="' + windowId + '"' + options.iconCls + options.title + '></div>';
    
    $(window.parent.document.body).append(boarddiv);
    window.parent.openDialogWindow(windowId,options);
}
//正规替换
function replaceStr(str,oldStr,newStr){
    return str.replace(new RegExp(oldStr,"gm"), newStr);
}

/**
* 行操作
* int dataId 数据列的ID值
* array opt 操作方法，如果为空仅显示修改和删除
* int rowIndex 编辑行的行号
*/
function operation(dataId,rowIndex,opt){
    if(opt == '' || !opt){
        var opt = [['edit','修改'],['delete','删除']];
    }
    var rOper = '';
    var ts = false;
    var hrf = '';
    var lbtn = '';
    var thisUrl = getUrlPatch();
    for(var i=0;i<opt.length;i++){ 
        var ac=new Array(); 
        ac=opt[i];
        ts = uiBottonSecurity(thisUrl.model,ac[0]);
        if(ts){
            hrf = ' onclick="javascript:' + ac[0] + 'Operation(' + dataId + ',' + rowIndex + ');"';
            lbtn = '';
        }else{
            //msgShow('警告','你没有权限操作如下模块：' + thisUrl.model + '/' + ac[0],'warning');
            hrf = '';
            lbtn = ' l-btn-disabled';
        }
        rOper+='<a href="javascript:void(0)"' + hrf + ' class="easyui-linkbutton l-btn' + lbtn + '"><span class="l-btn-left"><span class="l-btn-text icon-' + ac[0] + '" style="padding-left: 20px;">' + ac[1] + '</span></span></a>';
    }
    return rOper;
}
/**
*Toolbar
*/
function operationToolbar(opt){
    if(!opt){
        var opt = [];
    }
    var rOper = new Array();
    var ac = new Array();
    var ts = false;
    var hrf = '';
    var lbtn = '';
    var thisUrl = getUrlPatch();
    for(var i=0;i<opt.length;i++){
        if(opt[i] == '-'){
            rOper[i] = '-';
        }else{
            rOper[i] = new Object();
            ac=opt[i];
            ts = uiBottonSecurity(thisUrl.model,ac[0]);
            rOper[i].text = ac[1];
            rOper[i].iconCls = 'icon-' + ac[0];
            if(ts){
                rOper[i].handler = eval(ac[0] + 'Operation();');
            }
        }
    }
    return rOper;
}

/**
* UI按钮权限判断
*/
function uiBottonSecurity(m,a){
    var c = $.cookie('p_' + $.md5(String.fromCharCode(100,105,103,105,116,97,108,116,111,112,95,101,99,109,111,115,95,116,104,105,110,107,111,97,95) + $.md5(m)));
    var t = c.split($.md5(String.fromCharCode(46,124,46)));
    for(var s in t){
        //alert('要求的=' + $.md5(a) + '|浏览器=' + t[s] + '|s=' + s + '|m=' + m + '|a=' + a + '|c=' + c);
        if($.md5(a) == t[s])return true;
    }
    return false;
}

//DataGrid右键菜单
function createListColumnMenu(domId){
    var tmenu = $('<div id="tmenu" style="width:100px;"></div>').appendTo('body');
    var fields =  $('#'+domId).datagrid('getColumnFields');
    for(var i=0; i<fields.length; i++){
        var opts = $('#'+domId).datagrid('getColumnOption', fields[i]);
        var muit = $('<div iconCls="icon-ok"/>');
        muit.attr('id', fields[i]);
        muit.html(opts.title).appendTo(tmenu);
    }
    tmenu.menu({
        onClick: function(item){
            if (item.iconCls=='icon-ok'){
                $('#'+domId).datagrid('hideColumn', item.id);
                tmenu.menu('setIcon', {
                    target: item.target,
                    iconCls: 'icon-empty'
                });
            } else {
                $('#'+domId).datagrid('showColumn', item.id);
                tmenu.menu('setIcon', {
                    target: item.target,
                    iconCls: 'icon-ok'
                });
            }
        }
    });    
}