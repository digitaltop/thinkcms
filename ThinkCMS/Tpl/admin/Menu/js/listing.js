//左侧栏目
$('#MenuTree').tree({
    date:[{
        "id":0
    }],
    dnd:false,
    url:U('Menu/listing',{
        act:'listTree'
    }),
    onClick:function(node){
        $('#chooseThisParentName').html(node.text);
        $('#chooseThisParentId').val(node.id);
        loadData(node.id);
    },
    onDblClick:function(node){
        $(this).tree('expand',node.target);
    }
});
$(function(){
    loadData();
});

//载入数据
function loadData(parentid,keywords){
    if(!parentid)var parentid = 0;
    if(!keywords)var keywords = '';
    $('#menuListing').datagrid({
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
        },
        queryParams:{
            'parentid':parentid,
            'keywords':keywords
        },
        url:U('Menu/listing',{
            act:'search'
        })
    });
}

//行添加
function addOperation(dataId,rowIndex){
    var p = {
        url:U('Menu/add',{
			  parentId:$('#chooseThisParentId').val()
			  }),
        title:'添加记录',
        iconCls:'icon-add',
        width:400,
        height:460,
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
        var rows = $('#menuListing').datagrid('getSelections');
        var num = rows.length;
        if(num  != 1){
            msgShow('提示消息','请选择一条记录进行操作!','info');
            return null;
        }else{ 
            dataId = rows[0].menu_id;
        }
    }
    var p = {
        url:U('Menu/edit',{
            id:dataId
        }),
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
        var rows = $('#menuListing').datagrid('getSelections');
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
                url: U('Menu/delete'),
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
                        $('#menuListing').datagrid('reload');
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
	ajaxSubFrom(U('Menu/save'),$('#menuDataTableForm',window.parent.document).serializeArray(),function(res){
        window.top.msgShowRightDiv(res.message,2);
        window.parent.closeWindow(windowId,true);
        $('#menuListing').datagrid('reload');
    });
}

//搜索
function searchOperation(value,name){
	if(!name)var name=$('#chooseThisParentId').val();
    loadData(name,value);
    return;
}

//导出
function exportOperation(){
    location.href=U('Menu/export');
}

//刷新表格
function reloadOperation(){
    $('#menuListing').datagrid('reload');
}

//菜单状态
function menuFormatter(value){
    if(value==0){
        return '<font color="#f00">未不生成</font>';
    }else{
        return '生成';
    }
}
//启用状态
function statusFormatter(value){
    if(value==0){
        return '<font color="#f00">未启用</font>';
    }else{
        return '已启用';
    }
}
//权限状态
function permisFormatter(value){
    if(value==0){
        return '<font color="#f00">不检查</font>';
    }else{
        return '需要授权';
    }
}

//一键授权
function oneKeyToMeRole(){
    $.get(U('Menu/oneKeyToMeRole'), function(result){
        if (!result){
            msgShow('提示消息','数据返回失败，请重试！','info');
            return;
        } else if(result.statusCode == 1){
            msgShowRightDiv('授权成功！',2);
        }else{
            msgShow('警告',result.message,'warning');
        }
    });
}