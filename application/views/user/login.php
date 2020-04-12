<script>
function check_login(){
    if(is_empty_str(document.login.id.value)){
        alert("아이디를 입력하세요.");
        document.login.id.focus();

        return false;
    }

    if(is_empty_str(document.login.password.value)){
        alert("비밀번호를 입력하세요.");
        document.login.password.focus();

        return false;
    }

    return true;
}

<?php
$errorCode = $this->input->get('errorCode');
if(!empty($errorCode)){
    if($errorCode == 1){
        echo "alert('아이디나 비밀번호가 잘못되었습니다.')";
    }
}
?>
</script>

<div id='wrap_login'>
    <div align='center'>
        <a href='/'>
            <img src='/data/image/mainLogo.png' width='184' height='45'/>
        </a>
    </div>
    <img src='/data/image/loginTitleBar.png' width='360' height='29'/>
    <table id='loginBox' width='310' cellspacing='0' cellpadding='0'>
        <?=form_open("/user/login_confirm", array('name' => "login"))?>
            <tbody>
                <tr height='50'>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td align='right'>아이디</td>
                    <td width='130'>
                        <input type='text' name='id' maxlength='30' size='12' tabindex='1'/>
                    </td>
                    <td rowspan='2'>
                        <input type='image' src='/data/image/loginSubmit.png' width='64' height='50' tabindex='3'/>
                    </td>
                </tr>
                <tr>
                    <td align='right'>비밀번호</td>
                    <td>
                        <input type='password' name='password' maxlength='30' size='12' tabindex='2'/>
                    </td>
                </tr>
                <tr height='50'>
                    <td colspan='3' align='center'><a class='anchor_button' href='/user/sign_up'>회원가입</a></td>
                </tr>
            </tbody>
        </form>
    </table>
</div>
