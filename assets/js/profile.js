$(document).ready(() => {

    $('.btn-outline-secondary').click(function (e) { 
        e.preventDefault();
        window.search_form.submit();
    });


    $('#submit_profile_post').click( e => { 
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "./includes/handlers/submit_profile_post.php",
            data: $('form.profile_post').serialize(),
            success: (response) => {
                $('#post_form').modal('hide');
                location.reload();
            },
            error: () => {
                alert('Failed to add post');
            }
        });
    });

});

$(document).click(function(e) { 
    if(e.target.id!='search_text_input'){
        $('.search_results').html("");
        $('.search_results_footer').html("")
        $('.search_resuls_footer').toggleClass('.search_results_footer_empty');
    }
});

function getUsers(value, user) {
	$.post("./includes/handlers/friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".results").html(data);
	});
}

const getLiveSearchUsers = (value,user) => {
    $.post("./includes/handlers/friend_search.php", {query: value,userLoggedIn: user},
        function (response, textStatus, jqXHR) {
            
            $('.search_results').html(response);

        },
    );
}