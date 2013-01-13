//列表
$(function(){
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
        onDblClickRow:function(rowIndex,field){
            editOperation(field.menu_id,rowIndex);
        }
    });
    loadData();
});

//载入数据
function loadData(keywords){
    if(!keywords)var keywords = '{$keywords}';
    $('#listing').datagrid({
        queryParams:{
            'keywords':keywords
        },
        url:"{:U('Plugin/listing',array('act'=>'search'))}"
    });
    $('#listing').datagrid('load');
}

//行添加
function addOperation(dataId,rowIndex){
    var p = {
        url:'{:U("Plugin/add")}',
        title:'添加记录',
        iconCls:'icon-add',
        width:'90%',
        height:'90%',
        mini:false,
        max:false,
        close:true,
        button:true
    };
    windowOpen(p);
}

//行编辑
function editOperation(dataId,rowIndex){
    if(!dataId){
        var ids = [];
        var rows = $('#listing').datagrid('getSelections');
        var num = rows.length;
        if(num  != 1){
            msgShow('提示消息','请选择一条记录进行操作!','info');
            return null;
        }else{ 
            dataId = rows[0].menu_id;
        }
    }
    var p = {
        url:'/Plugin/edit/id/' + dataId,
        title:'修改记录',
        iconCls:'icon-edit',
        width:400,
        height:460,
        mini:false,
        max:false,
        close:true,
        button:true
    };
    windowOpen(p);
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
            ids.push(rows[i].menu_id);
        }
        dataId = ids.join(',');
    }
    $.messager.confirm('删除确认', '您确定要删除这些记录吗?\n删除后不可恢复！！', function(r){
        if (r){
            $.ajax({
                type: 'post', 
                cache: false, 
                dataType: 'json',
                url: '/Model/delete',
                data: [
                {
                    name: 'id', 
                    value: dataId
                }
                ],
                success: function (result){
                    if (!result){
                        msgShow('提示消息','删除失败!','info');
                        return;
                    } else if(result.statusCode !== 1){
                        msgShow('警告',result.message,'warning');
                    }else{
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

//保存表单
function saveOperation(windowId){
    $('#dataTableForm').form('submit',{
        url:'/Plugin/save',
        success:function(result){
            result = $.parseJSON(result);
            if (!result){
                msgShow('提示消息','数据返回失败，请重试！','info');
                return;
            } else if(result.statusCode == 1){
                msgShowRightDiv('保存成功！',2);
                $('#' + windowId).dialog('close',true);
                $('#listing').datagrid('reload');
            }else{
                msgShow('警告',result.message,'warning');
            }
        },
        error: function (){
            msgShow('错误','发送时遇到系统错误,请与系统管理员联系!','error');
        }
    });
}

//搜索
function searchOperation(value,name){
    loadData(name,value);
    return;
}

//导出
function exportOperation(){
    location.href='{:U("Plugin/export")}';
}

//刷新表格
function reloadOperation(){
    $('#listing').datagrid('reload');
}
