<?php if (!defined('THINK_PATH')) exit();?><div style="padding:10px;">
    <form method="post" action="__URL__/save" id="categoryDataTableForm">
        <table class="easyui-propertygri">
            <tr><td height="28">
                    栏目名称：
                </td><td><input name="catname" class="easyui-validatebox" type="text" size="25" value="<?php echo ($vo["catname"]); ?>" required="true" validType="length[1,10]" invalidMessage="请确认填写1-10个字以内！" missingMessage="填写1-10个字以内" />
                </td></tr>
            <tr><td height="28">目录名称：
                </td><td>
                    <input name="catdir" class="easyui-validatebox" type="text" size="25" value="<?php echo ($vo["catdir"]); ?>" required="true" validType="length[1,50]" invalidMessage="请确认填写1-50个字以内！" missingMessage="填写1-50个字以内" />
                </td></tr>
            <tr><td height="28">上级栏目：
                </td><td>
                    <select name="parentid" id="parentid_view" style="width: 170px">
                        <option value="0" <?php if(($vo["parentid"]) == "0"): ?>selected<?php endif; ?>>最上级</option>
                        <?php if(is_array($categoryTree)): $i = 0; $__LIST__ = $categoryTree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["catid"]); ?>" <?php if(($sub["catid"]) == $vo["parentid"]): ?>selected<?php endif; ?>><?php echo ($sub["fulltitle"]); echo ($sub["catname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td></tr>
                <tr>
              <td height="28">使用模块：</td>
              <td><select name="modelid" id="modelid_view" style="width:170px;">
              <?php if(is_array($modelTree)): $i = 0; $__LIST__ = $modelTree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["modelid"]); ?>" <?php if(($sub["modelid"]) == $vo["modelid"]): ?>selected<?php endif; ?>><?php echo ($sub["fulltitle"]); echo ($sub["modelname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>              </td>
            </tr>
            <tr <?php if(($vo["modelid"]) != "d4abe5e7-525f-11e2-ab1c-f0def10abaa2"): ?>style="display:none"<?php endif; ?> id="url_view">
              <td height="28">完整的链接：</td>
              <td><input name="url" type="text" value="<?php echo ($vo["url"]); ?>" size="25" maxlength="200" minlength="4">
              <span class="unit">不建议修改</span></td>
            </tr>
            <tr>
              <td height="28">显示位置：</td>
              <td><select name="viewlocation" style="width:170px;">
               <option value="1" <?php if(($vo["viewlocation"]) == "1"): ?>selected="selected"<?php endif; ?>>仅头部主导航显示</option>
              <option value="0" <?php if(($vo["viewlocation"]) == "0"): ?>selected="selected"<?php endif; ?>>头尾均不显示</option>             
              <option value="2" <?php if(($vo["viewlocation"]) == "2"): ?>selected="selected"<?php endif; ?>>仅页脚导航显示</option>
              <option value="3" <?php if(($vo["viewlocation"]) == "3"): ?>selected="selected"<?php endif; ?>>头尾均显示</option>
              </select></td>
            </tr>
            <tr>
              <td height="28">打开方式：</td>
              <td><select name="target" style="width:170px;">
              <option value="0" <?php if(($vo["target"]) == "0"): ?>selected="selected"<?php endif; ?>>默认打开方式</option>
              <option value="1" <?php if(($vo["target"]) == "1"): ?>selected="selected"<?php endif; ?>>新窗口打开</option>
              </select>
              </td>
            </tr>
            <tr><td height="28">图   标：
                </td><td>
                    <input name="icon" type="text" value="<?php echo ($vo["icon"]); ?>" minlength="4" maxlength="30" >
                </td></tr>
            <tr><td height="28">排 序 号：
                </td><td>
                    <input name="listorder" class="easyui-numberbox" value="<?php echo ($vo["listorder"]); ?>" type="text" size="25" min="0" max="2147483648 2147483647" alt=""/><span class="unit">越小越靠前</span>
                </td></tr>
            <tr><td height="28">备    注：
                </td><td>
                    <textarea name="description" cols="30" rows="2"><?php echo ($vo["description"]); ?></textarea>
                    <input type="hidden" name="catid" value="<?php echo ($vo["catid"]); ?>" />
                </td></tr>
</table>
  </form>
</div>
<script type="text/javascript">
//模块选择器
$('#modelid_view').change(function(){
	if($(this).val() == 'd4abe5e7-525f-11e2-ab1c-f0def10abaa2'){
		$('#url_view').show();
	}else{
		$('#url_view').hide();
	}
});</script>