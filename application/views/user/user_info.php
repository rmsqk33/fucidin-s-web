
<div id='wrap_userInfo'>
<?php
if(empty($_SESSION['userId']))
{
?>
    <div>
        <dl>
            <dd class='anchor_button'><a href='/user/login'>로그인</a></dd>

            <dd class='anchor_button'><a href='/user/sign_up'>회원가입</a></dd>
        </dl>
    </div>
<?php
} else {
?>
    <div>
        <dl>
            <dd>
                <p align='center' class='text_underline color_red'><?=$nickname?></p>
                <p align='center'>님 반갑습니다.</p>
            </dd>
        </dl>
        <ul>
            <li><a href='/user/edit_info'>정보수정</a></a></li>
            <li><a href='/user/logout'>로그아웃</a></li>
        </ul>
    </div>
<?php
}
?>
</div>
