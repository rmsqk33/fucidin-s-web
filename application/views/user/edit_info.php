<?php
if(1024 <= $iconMaxSize){
    $iconMaxSize = (floor(($iconMaxSize / 1024)*10) / 10)."MB";
} else{
    $iconMaxSize .= "KB";
}
?>
<div id='wrap_editUserInfo'>
    <?=form_open_multipart("/user/edit_info", array('autocomplete' => "off"))?>
        <table width='700'>
            <tbody>
                <tr>
                    <td class='leftSide'>닉네임 (1~<?=$nickNameMaxLength?>자)</td>
                    <td class='rightSide'>
                        <div><?=$userInfo->nickname?></div>
                        <input type='text' name='nickname' <?=$nickNameMaxLength?>/>
                        <span class='errorMsg'><?=form_error("nickname")?></span>
                    </td>
                </tr>
                <tr>
                    <td class='leftSide'>
                        아이콘
                        <div class='text_font_11'>(최대 <?=$iconMaxSize?>)</div>
                    </td>
                    <td class='rightSide'>
                        <div>
                            <img class='align_middle' src='<?=$userInfo->iconpath?>' width='20' height='20'/>
                            <span class='align_middle'>
                                <input type='file' name='icondata' accept='<?=$iconAllowTypes?>' />
                            </span>
                        </div>
                        <div class='text_font_11'>사용 가능한 이미지(<?=$iconAllowTypes?>)</div>
                        <div class='errorMsg'><?=form_error("icondata")?></div>
                    </td>
                </tr>
                <tr class='profil'>
                    <td class='leftSide'>프로필(최대 <?=$profilMaxLength?>자)</td>
                    <td class='rightSide'>
                        <textarea name='profil' cols='20' rows='10' maxlength='<?=$profilMaxLength?>'><?=$userInfo->profil?></textarea>
                        <span class='errorMsg'><?=form_error("profil")?></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class='editUserInfoBtn'>
            <input class='anchor_button' type='submit' value='확인' />
            <a class='anchor_button' href='/'>취소</a>
        </div>
    </form>
</div>
