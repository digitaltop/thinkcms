//列表
$(function(){
    loadData();
});

//载入数据
function loadData(keywords){
    if(!keywords)var keywords = '';
    $('#listing').datagrid({
        onHeaderContextMenu: function(e, field){
            e.preventDefault();
            if (!$('#tmenu').length){
                createListColumnMenu();
            }
            $('#tmenu').menu('show', {
                left:e.pageX,
                top:e.pageY
            });
        },
        queryParams:{
            'keywords':keywords
        },
        url:U('Category/recycle',{
            act:'search'
        })
    });
}

//行删除
function deleteOperation(dataId,rowIndex){
    if(!dataId){
        var ids = [];
        var rows = $('#listing').datagrid('getSelections');
        var num = rows.length;
        if(num  < 1){
            msgShow('提示消息','请至少选择一条记录进行操作!','info');
            return null;
        }
        for(var i=0;i<rows.length;i++){
            ids.push(rows[i].catid);
        }
        dataId = ids.join(',');
    }
    $.messager.confirm('删除确认', '您确定要删除这些记录吗?\n删除后不可恢复，！！', function(r){
        if (r){
            $.ajax({
                type: 'post', 
                cache: false, 
                dataType: 'json',
                url: U('Category/recycle',{
                    act:'delete'
                }),
                data: [{
                    name: 'id',
                    value: dataId
                }],
                success: function (result){
                    if (!result){
                        msgShow('提示消息','删除失败!','info');
                        return;
                    } else if(result.statusCode !== 1){
                        msgShow('警告',result.message,'warning');
                    }else{
                        msgShowRightDiv(result.message,2);
                        $('#listing').datagrid('reload');
                    }
                },
                error: function (){
                    msgShow('错误','发送时遇到系统错误,请与系统管理员联系!','error');
                }
            });
        }
    });
}

//行还原
function recycleOperation(dataId,rowIndex){
    if(!dataId){
        var ids = [];
        var rows = $('#listing').datagrid('getSelections');
        var num = rows.length;
        if(num  < 1){
            msgShow('提示消息','请至少选择一条记录进行操作!','info');
            return null;
        }
        for(var i=0;i<rows.length;i++){
            ids.push(rows[i].catid);
        }
        dataId = ids.join(',');
    }
    $.messager.confirm('还原确认', '您确定要还原这些记录吗?\n还原后，有可能会造成栏目名称冲突或错乱，请谨慎操作引功能！！', function(r){
        if (r){
            $.ajax({
                type: 'post', 
                cache: false, 
                dataType: 'json',
                url: U('Category/recycle',{
                    act:'recycle'
                }),
                data: [{
                    name: 'id',
                    value: dataId
                }],
                success: function (result){
                    if (!result){
                        msgShow('提示消息','还原失败!','info');
                        return;
                    } else if(result.statusCode !== 1){
                        msgShow('警告',result.message,'warning');
                    }else{
                        msgShowRightDiv(result.message,2);
                        $('#listing').datagrid('reload');
                    }
                },
                error: function (){
                    msgShow('错误','发送时遇到系统错误,请与系统管理员联系!','error');
                }
            });
        }
    });
}

//刷新表格
function reloadOperation(){
    $('#listing').datagrid('reload');
}


//格式化导航菜单
function navViewOperation(value,rowIndex){
    switch(value){
        case "0":
            return '不显示';
            break;
        case "1":
            return '头部主导航';
            break;
        case "2":
            return '尾部导航';
            break;
        case "3":
            return '头尾都显示';
            break;
        default:
            return '未设置';
    }
}

//格式化弹窗方式
function targetOperation(value,rowIndex){
    switch(value){
        case "0":
            return '默认打开方式';
            break;
        case "1":
            return '新窗口打开';
            break;
        default:
            return '未设置';
    }
}