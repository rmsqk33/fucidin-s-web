<div id='wrap_signUp'>
    <?=form_open("/user/sign_up", array('autocomplete' => "off"))?>
        <table width='700'>
            <tbody>
                <tr>
                    <td class='leftSide'>* 아이디(<?=1?>~<?=$idMaxLength?>자)</td>
                    <td class='rightSide'>
                        <input type='text' name='id' value='<?=set_value("id")?>'/>
                        <div><?=form_error("id")?></div>
                    </td>
                </tr>
                <tr>
                    <td class='leftSide'>* 닉네임(<?=1?>~<?=$nickNameMaxLength?>자)</td>
                    <td class='rightSide'>
                        <input type='text' name='nickname' value='<?=set_value("nickname")?>'/>
                        <div><?=form_error("nickname")?></div>
                    </td>
                </tr>
                <tr>
                    <td class='leftSide'>* 비밀번호(<?=$passwordMinLength?>~<?=$passwordMaxLength?>자)</td>
                    <td class='rightSide'>
                        <input type='password' name='password' value='<?=set_value("password")?>'/>
                        <div><?=form_error("password")?></div>
                    </td>
                </tr>
                <tr>
                    <td class='leftSide'>* 비밀번호 확인</td>
                    <td class='rightSide'>
                        <input type='password' name='passwordConfirm' value='<?=set_value("passwordConfirm")?>'/>
                        <div><?=form_error("passwordConfirm")?></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class='signUpPageBtn'>
            <input class='anchor_button' type='submit' value='가입' />
            <a class='anchor_button' href='/'>취소</a>
        </div>
    </form>
</div>
