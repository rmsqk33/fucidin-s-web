var userId = null;
var tablePrefix = null;
var crsf_hash = null;

window.onload = function(){
    userId = document.getElementById('userId').value;
    tablePrefix = document.getElementById('table').value;
    crsf_hash = document.getElementById('csrf_hash').value;

    init_recomment_colspan();
}

function init_recomment_colspan(){
    var length = $('.topicComment').eq(0).children('td').length;
    $('.auto_colspan').each(function(index, item){
        $(item).attr('colspan', length);
    });
}

function check_login_and_show_error(){
    if(!userId){
        alert("로그인을 하셔야합니다.");
        return false;
    }
    return true;
}

function on_comment_submit(formObject){
    if(userId){
        var content = formObject.content.value;
        if(content.replace(/\s/gi, "").length){
             return true;
        }

        alert("댓글을 입력해주세요.");
        return false;
    }

    return true;
}

function on_toggle_recomment_input(id){
    if(!check_login_and_show_error()){
        return;
    }

    var recommentInput = $('#recomment_'+id);
    if(recommentInput){
        if(recommentInput.css('display') == "none"){
            recommentInput.css('display', '');
        } else{
            recommentInput.css('display', 'none');
        }
    }
}

function on_del_comment_submit(commentOwnerId){
    if(!check_login_and_show_error()){
        return false;
    }

    if(commentOwnerId != userId){
        alert("다른 사람의 댓글은 삭제할 수 없습니다.");
        return false;
    }

    return confirm("정말 삭제하시겠습니까?");
}

function on_recommend_comment(commentId){
    if(!check_login_and_show_error()){
        return false;
    }

    $.post(
        "/topic/recommend_comment",
        {
            table: tablePrefix,
            commentId: commentId,
            recommend: 1,
            csrf_t: crsf_hash
        },
        function(data){
            if(data.recommended){
                alert("이미 추천된 댓글입니다.");
                return;
            }
            $('#comment_recommend_' + commentId).html(data.count);
        },
        "json"
    )
    return true;
}

function on_unrecommend_comment(commentId){
    if(!check_login_and_show_error()){
        return false;
    }

    if(confirm("정말 반대하시겠습니까?")){
        $.post(
            "/topic/recommend_comment",
            {
                table: tablePrefix,
                commentId: commentId,
                recommend: 0,
                csrf_t: crsf_hash,
            },
            function(data){
                if(data.recommended === true){
                    alert("이미 반대된 댓글입니다.");
                    return;
                }

                $('#comment_unrecommend_' + commentId).html(data.count);
            },
            "json"
        )
        return true;
    }
    return false;
}


function on_recommend_topic(topicId){
    if(!check_login_and_show_error()){
        return false;
    }

    $.post(
        "/topic/recommend_topic",
        {
            table: tablePrefix,
            topicId: topicId,
            recommend: 1,
            csrf_t: crsf_hash
        },
        function(data){
            if(data.recommended){
                alert("이미 추천된 게시물입니다.");
                return;
            }

            $('#topic_recommend').html(data.count);
        },
        "json"
    )
    return true;
}

function on_unrecommend_topic(topicId){
    if(!check_login_and_show_error()){
        return false;
    }

    if(confirm("정말 반대하시겠습니까?")){
        $.post(
            "/topic/recommend_topic",
            {
                table: tablePrefix,
                topicId: topicId,
                recommend: 0,
                csrf_t: crsf_hash,
            },
            function(data){
                if(data.recommended === true){
                    alert("이미 반대된 게시물입니다.");
                    return;
                }

                $('#topic_unrecommend').html(data.count);
            },
            "json"
        )
        return true;
    }
    return false;
}
