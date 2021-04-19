$(document).ready(() => {

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