<?php if (!defined('THINK_PATH')) exit();?><form method="post" action="__URL__/profile" onsubmit="return validateCallback(this, dialogAjaxDone)">
            <input type="hidden" name="doAction" value="1"/>
            <div class="pageFormContent" layoutH="58">

                <div class="unit">
                    <label>真实姓名：</label>
                    <input type="text" class="textInput disabled" disabled=""  name="nickname" value="<?php echo ($vo["nickname"]); ?>"/>
                </div>

                <div class="unit">
                    <label>原密码：</label>
                    <input type="password" class="alphanumeric" name="oldpassword"/>
                </div>

                <div class="unit">
                    <label>新密码：</label>
                    <input type="password" class="alphanumeric" name="password"  minlength="6" maxlength="20"/>
                </div>

                <div class="unit">
                    <label>确认新密码：</label>
                    <input type="password" class="alphanumeric" name="repassword"  minlength="6" maxlength="20"/>
                </div>

                <div class="unit">
                    <label>检验码：</label>
                    <input type="text" class="required" name="verify"> 
                    <img src="__APP__/Public/verify/" BORDER="0" ALT="click" id="verifyImg" onClick="fleshVerify()" style="cursor:pointer" align="absmiddle">
                </div>

            </div>
            <div class="formBar">
                <ul>
                    <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
                    <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
                </ul>
            </div>
        </form>