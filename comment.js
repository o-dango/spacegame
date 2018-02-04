"use strict";

function scrollBottom() { /*WIP WHYYY:C*/
    $(document).ready(function(){
        var box = $('#commentbox');
        box.animate({
            scrollTop: box[0].scrollHeight}, 2000);
    });
    
}

function submitComment() {
    /*submits comment when submit button is clicked*/
    $(document).ready(function() {
        $("#commentbtn").click(function(event) {
            if($('input[name=player]').length !== 0) { /*check if comment field is empty*/
                sendComment();
            }
        });
    });
}

function enterListener() {
    /*submits comment if enter is pressed*/
    $(document).ready(function() {
        $(document).keypress(function (event) {
            var key = event.which;
            if(key == 13) {
                if($('input[name=comment]').val().length !== 0) {
                    sendComment();
                }
                return false;
            }
        });
    });
}
 
function sendComment() {
    /*send data to php page where comments are uploaded to database
    after submitting comment box will refresh*/
    $.ajax({
      type: 'POST',
      url: 'comment.php',
      data: $('#commentform').serialize(),
      success: function() {
        console.log("Commenting was successful!");
        clearInput();
        $('#commentbox').load('index.php #commentbox', function() {
            scrollBottom(); /*:<*/
        });
        },
      error: function() {
        console.log("Commenting failed!");
      }
    });
}

function clearInput() { /*clear input field after submit*/
    let comment= $("input[name=comment]");
    comment.val("");
}
