
var modal_width = 450;

function disable_form()
{
    $( "#modal_topic [name='topic']" ).prop( "disabled", true );
    $( "#modal_topic [name='topic_body']" ).prop( "disabled", true );
    $( "#modal_topic [name='topicid']" ).prop( "disabled", true );
    $( "#modal_topic button" ).prop( "disabled", true );
}

function enable_form()
{
    $( "#modal_topic [name='topic']" ).prop( "disabled", false );
    $( "#modal_topic [name='topic_body']" ).prop( "disabled", false );
    $( "#modal_topic [name='topicid']" ).prop( "disabled", false );
    $( "#modal_topic button" ).prop( "disabled", false);
}

function clear_form() {
    $(this).closest('form').find("input[type=text], textarea").val("");
}

function modal_center(item){
   $(item).css({"position":"fixed",
                "width":modal_width.toString()+"px",
                "left":((window.innerWidth-modal_width)/2).toString()+"px",
                "top":((window.innerHeight-$(item).height())/2).toString()+"px"
                });
}
function modal_slideToggle(item){
    clear_form() ;
   modal_center(item);
   $(item).slideToggle();
}
$(document).ready(function(){
     $(".modal").hide();
     $(".modal").find("form").prepend("<a href='' class='close_modal'>X</a>");
     $("a.close_modal").click( function(event){
          $(this).parents(".modal").hide();
          event.preventDefault();
     });


    $("#addtopic").click( function(event){
        enable_form();
        modal_slideToggle("#modal_topic");
        event.preventDefault();
    });

    $("div a.topicview").click( function(event){
        $("#modal_topic h2").html("Podgląd notatki ID: <span topicid=\""+$(this).attr("topicid")+"\">"+$(this).attr("topicid")+"</sapn>");
        disable_form();
        $.get("?cmd=gettopic&topicid="+$(this).attr("topicid"),
            function( data, status){
            var topic=JSON.parse(data);
            $("#modal_topic [name='topic']").val(topic.topic).focus(); 
            $("#modal_topic [name='topic_body']").val(topic.topic_body);
            $("#modal_topic [name='topicid']").val(topic.topicid);
        });
        modal_slideToggle("#modal_topic");
        event.preventDefault();
    });

    $("nav a.topicedit").click( function(event){
        $("#modal_topic h2").html("Edycja notatki ID: <span topicid=\""+$(this).attr("topicid")+"\">"+$(this).attr("topicid")+"</sapn>");
        enable_form();
        $.get("?cmd=gettopic&topicid="+$(this).attr("topicid"),
            function( data, status){
            var topic=JSON.parse(data);
            $("#modal_topic [name='topic']").val(topic.topic).focus(); 
            $("#modal_topic [name='topic_body']").val(topic.topic_body);
            $("#modal_topic [name='topicid']").val(topic.topicid);
        });
        modal_slideToggle("#modal_topic");
        event.preventDefault();
    });


    $("#act-userid").click( function(event){
        enable_form();
        modal_slideToggle("#modal_topic");
        event.preventDefault();
    });
});