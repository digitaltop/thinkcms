//左侧栏目
$('#CategoryListTree').tree({
    date:[{
        "id":0
    }],
    dnd:false,
    url:U('Category/listing',{
        act:'listTree'
    }),
    onClick:function(node){
        $('#chooseThisParentName').html(node.text);
        $('#chooseThisParentId').val(node.id);
        loadData(node.id);
    },
    onDblClick:function(node){
        $(this).tree('expand',node.target);
    },
    onDrop:function(target, source, point){
        if(source.id == 0){
            return false;
        }
        var node = $(this).tree('getNode',target);
        if(node.id == 0 && point == 'top'){
            $(this).tree('toggle',target);
        }
    //alert('target=' + node.id + '|source=' + source.id + '|point=' + point);
    //        $.ajax({
    //            url:'UpdateMenuItemServlet',
    //            data:{
    //                target:next.id,
    //                source:source.id,
    //                point: point
    //            },
    //            dataType:'json',
    //            success:function(r){
    //            }
    //        });
    }
});
//列表
$(function(){
    loadData();
});

//载入数据
function loadData(parentid,keywords){
    if(!parentid)var parentid = 0;
    if(!keywords)var keywords = '';
    $('#categoryListing').datagrid({
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
            editOperation(field.catid,rowIndex);
        },
        queryParams:{
            'parentid':parentid,
            'keywords':keywords
        },
        url:U('Category/listing',{
            act:'search'
        })
    });
}

//行添加
function addOperation(dataId,rowIndex){
    var p = {
        url:U('Category/add',{
            parentid:$('#chooseThisParentId').val()
        }),
        title:'添加记录',
        iconCls:'icon-add',
        width:450,
        height:'80%',
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
        var rows = $('#categoryListing').datagrid('getSelections');
        var num = rows.length;
        if(num  != 1){
            msgShow('提示消息','请选择一条记录进行操作!','info');
            return null;
        }else{ 
            dataId = rows[0].menu_id;
        }
    }
    var p = {
        url:U('Category/edit',{
            id:dataId
        }),
        title:'修改记录',
        iconCls:'icon-edit',
        width:450,
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
        var rows = $('#categoryListing').datagrid('getSelections');
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
    $.messager.confirm('删除确认', '您确定要删除这些记录吗？', function(r){
        if (r){
            $.ajax({
                type: 'post', 
                cache: false, 
                dataType: 'json',
                url: U('Category/delete'),
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
                        $('#categoryListing').datagrid('reload');
                        $('#CategoryListTree').tree('reload');
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
    ajaxSubFrom(U('Category/save'),$('#categoryDataTableForm',window.parent.document).serializeArray(),function(result){
        window.top.msgShowRightDiv('保存成功！',2);
        window.parent.closeWindow(windowId,true);
        $('#categoryListing').datagrid('reload');
        var data = [{
            id:result.attributes.id,
            text:result.attributes.text,
            iconCls:result.attributes.iconCls
        }];
        var node = $('#CategoryListTree').tree('getSelected');
        if (node){
            $('#CategoryListTree').tree('expand',node.target);
            if(result.attributes.act == 'add'){ //添加操作
                $('#CategoryListTree').tree('append',{
                    parent:node.target,
                    data:data
                });
            }else{
                $('#CategoryListTree').tree('update',data);
            }
        }
    });
}

//搜索
function searchOperation(value,name){
	if(!name)var name=$('#chooseThisParentId').val();
    loadData(name,value);
    return;
}

//打印
function printOperation(){
	$.printBox('listing');
}

//导入
function importOperation(){
    /*
     * 缩略图
     */
    var myEditorExcel;
    var d;
    function importExcel(){
        d = myEditorExcel.getDialog("attachment");
        d.render();
        d.open();
    }
    myEditorExcel= new UE.ui.Editor();
    myEditorExcel.render('myEditorExcel');
    myEditorExcel.ready(function(){
        myEditorExcel.setDisabled();
        myEditorExcel.hide();//隐藏UE框体
        myEditorExcel.addListener('beforeInsertImage',function(t,arg){
            $("#thumb").attr("value", arg[0].src);
        });
    });
}

//导出
function exportOperation(){
    location.href=U('Category/export');
}

//刷新表格
function reloadOperation(){
    $('#categoryListing').datagrid('reload');
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