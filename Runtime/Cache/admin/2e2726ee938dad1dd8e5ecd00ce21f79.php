<?php if (!defined('THINK_PATH')) exit();?><div style="padding:10px;">
    <form method="post" action="__URL__/save" id="menuDataTableForm">
        <table class="easyui-propertygri">
            <tr><td height="28">
                    菜单标题：
                </td><td><input name="menu_title" class="easyui-validatebox" type="text" size="25" value="<?php echo ($vo["menu_title"]); ?>" required="true" validType="length[3,10]" invalidMessage="请确认填写3-10个字以内！" missingMessage="填写3-10个字以内" />
                    <span class="unit">用于前台显示</span>
                </td></tr>
            <tr><td height="28">菜单名称：
                </td><td>
                    <input name="menu_name" class="easyui-validatebox" type="text" size="25" value="<?php echo ($vo["menu_name"]); ?>" required="true" validType="length[2,50]" invalidMessage="请确认填写2-50个字以内！" missingMessage="填写2-50个字以内" />
                    <span class="unit">用于后台控制</span>
                </td></tr>
            <tr><td height="28">上级菜单：
                </td><td>
                    <select name="parentid" id="parentid_view">
                        <option value="0">最上级</option>
                        <?php if(is_array($modeltree)): $i = 0; $__LIST__ = $modeltree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub["menu_id"]); ?>" <?php if(($sub["menu_id"]) == $vo["parentid"]): ?>selected<?php endif; ?>><?php echo ($sub["fulltitle"]); echo ($sub["menu_title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td></tr>
            <tr>
              <td height="28">外部URL：</td>
              <td><input name="outurl" type="text" value="<?php echo ($vo["outurl"]); ?>" minlength="4" maxlength="30" ><span class="unit">一般不需要设置</span></td>
            </tr>
            <tr><td height="28">图   标：
                </td><td>
                    <input name="icon" type="text" value="<?php echo ($vo["icon"]); ?>" minlength="4" maxlength="30" >
                </td></tr>
            <tr><td height="28">排 序 号：
                </td><td>
                    <input name="listorder" class="easyui-numberbox" value="<?php echo ($vo["listorder"]); ?>" type="text" size="25" min="0" max="999999" alt=""/><span class="unit">越小越靠前</span>
                </td></tr>
            <tr><td height="28">是否启用：
                </td><td>
                    <select name="status">
                        <option value="1" <?php if(($vo["status"]) == "1"): ?>selected<?php endif; ?>>启用</option>
                        <option value="0" <?php if(($vo["status"]) == "0"): ?>selected<?php endif; ?>>不启用</option>
                    </select>
                </td></tr>
            <tr><td height="28">是否生成菜单：
                </td><td>
                    <select name="menu">
                        <option value="1" <?php if(($vo["menu"]) == "1"): ?>selected<?php endif; ?>>生成菜单</option>
                        <option value="0" <?php if(($vo["menu"]) == "0"): ?>selected<?php endif; ?>>不生成菜单</option>
                    </select>
                </td></tr>
            <tr><td height="28">是否判断权限：
                </td><td>
                    <select name="permis">
                        <option value="1" <?php if(($vo["permis"]) == "1"): ?>selected<?php endif; ?>>要求权限</option>
                        <option value="0" <?php if(($vo["permis"]) == "0"): ?>selected<?php endif; ?>>不要求权限，任何人均可以使用的功能</option>
                    </select>
                </td></tr>
            <tr><td height="28">备    注：
                </td><td>
                    <textarea name="description" cols="30" rows="2"><?php echo ($vo["description"]); ?></textarea>
                    <input type="hidden" name="menu_id" value="<?php echo ($vo["menu_id"]); ?>" />
                </td></tr></table>
  </form>
</div>