//左侧栏目
$('#CategoryListTree').tree({
    date:[{
        "id":0
    }],
    dnd:false,
    url:U('Article/listing',{
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
function loadData(catid,keywords){
    if(!catid)var parentid = 0;
    if(!keywords)var keywords = '';
    $('#articleListing').datagrid({
        onHeaderContextMenu: function(e, field){
            e.preventDefault();
            if (!$('#tmenu').length){
                createListColumnMenu('articleListing');
            }
            $('#tmenu').menu('show', {
                left:e.pageX,
                top:e.pageY
            });
        },
        onDblClickRow:function(rowIndex,field){
            editOperation(field.id,rowIndex);
        },
        queryParams:{
            'catid':catid,
            'keywords':keywords
        },
        url:U('Article/listing',{
            act:'search'
        })
    });
}

//行添加
function addOperation(dataId,rowIndex){
    var p = {
        url:U('Article/add',{
            catid:$('#chooseThisParentId').val()
        }),
        title:'添加新闻资讯',
        iconCls:'icon-add',
        width:1050,
        height:'99%',
        mini:false,
        max:true,
        close:true,
        button:true
    };
    windowOpen(p);
}

//行编辑
function editOperation(dataId,rowIndex){
    if(!dataId){
        var ids = [];
        var rows = $('#articleListing').datagrid('getSelections');
        var num = rows.length;
        if(num  != 1){
            msgShow('提示消息','请选择一条记录进行操作!','info');
            return null;
        }else{
            dataId = rows[0].id;
        }
    }
    var p = {
        url:U('Article/edit',{
            id:dataId
        }),
        title:'修改新闻资讯',
        iconCls:'icon-edit',
        width:1050,
        height:'80%',
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
        var rows = $('#articleListing').datagrid('getSelections');
        var num = rows.length;
        if(num  < 1){
            msgShow('提示消息','请至少选择一条记录进行操作!','info');
            return null;
        }
        for(var i=0;i<rows.length;i++){
            ids.push(rows[i].id);
        }
        dataId = ids.join(',');
    }
    $.messager.confirm('删除确认', '您确定要删除这些记录吗?\n删除后不可恢复！！', function(r){
        if (r){
            ajaxSubFrom(U('Article/delete'),[{
                name: 'id', 
                value: dataId
            }],function(res){
                $('#articleListing').datagrid('reload');
            });
        }
    });
}

//保存表单
function saveOperation(windowId){
    window.top.UE.getEditor('content').sync();
    ajaxSubFrom(U('Article/save'),$('#articleDataTableForm',window.parent.document).serializeArray(),function(res){
        window.top.msgShowRightDiv(res.message,2);
        window.parent.closeWindow(windowId,true);
        $('#articleListing').datagrid('reload');
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
    location.href=U('Article/export');
}

//刷新表格
function reloadOperation(){
    $('#articleListing').datagrid('reload');
}

//菜单状态
function menuFormatter(value){
    if(value==0){
        return '<font color="#f00">未不生成</font>';
    }else{
        return '生成';
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