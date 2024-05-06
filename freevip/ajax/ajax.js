async function dell_request_comment(comm_id) {
    NProgress.start();
    var token = $('#token').val();
    $.ajax({
        type: "POST",
        url: "../modules_extra/freevip/ajax/actions.php",
        data: "phpaction=1&token=" + token + "&dell_request_comment=1&comm_id=" + comm_id,
        dataType: "json",
        success: function(result) {
            NProgress.done();
            if (result.status == 1) {
                $("#message_id_" + comm_id).fadeOut();
                setTimeout(show_ok, 500); 
            }
            if (result.status == 2) {
                setTimeout(show_error, 500);
            }
        }
    });
}

async function load_request_comments(req_id) {
    var token = $('#token').val();
    $.ajax({
        type: "POST",
        url: "../modules_extra/freevip/ajax/actions.php",
        data: "phpaction=1&token=" + token + "&load_request_comments=1&req_id=" + req_id,
        success: function(html) {
            $("#comments").html(html);
        } 
    });
} 

async function send_request_comment(req_id) {
    NProgress.start();
    var token = $('#token').val();
    var text = tinymce.get("text").getContent();
    text = $.trim(text);
    text = encodeURIComponent(text); 
    $.ajax({
        type: "POST",
        url: "../modules_extra/freevip/ajax/actions.php",
        data: "phpaction=1&token=" + token + "&send_request_comment=1&text=" + text + "&req_id=" + req_id,
        dataType: "json",
        success: function(result) {
            NProgress.done();
            if (result.status == 1) {
                clean_tiny('text');
                stop_button('#send_btn', 1000);
                load_request_comments(id);
                setTimeout(show_ok, 500);
            } else {
                setTimeout(show_error, 500);
                show_input_error(result.input, result.reply, null);
            }
        }
    });
}

async function requestChangeStatus(req_id, status) {
    const data = {
        'change_status':         1,
        'req_id':                req_id,
        'status':                status
    };

    await $.ajax({
        type: "POST",
        dataType: "JSON", 
        url: "../modules_extra/freevip/ajax/actions.php",
        data: create_material(data),

        success: function (response) {
            window.location.reload();
        },

        error: function () {
            setTimeout(show_error, 500); 
        }
    });
}

async function requestDelete(req_id) {
    const data = {
        'delete_request':        1,
        'req_id':                req_id,
    };

    NProgress.start();

    await $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "../modules_extra/freevip/ajax/actions.php",
        data: create_material(data),

        success: function (response) {
            NProgress.done();

            window.location = '/freevip/';
        }
    });
}

async function create_freevip_request() {
    $('#result').html();

    // auto-upload image request
    let uploadStatus = false; 
    var signa = false;

    var data = {
        'upload_image':         1,  
        'upload_type':          'signa',
        'image':                $('#signa')[0].files[0]
    };
 
    const uploadImageRequest = await $.ajax({
        type: "POST",
        dataType: "JSON",
        contentType: false,
        processData: false,
        url: "../modules_extra/freevip/ajax/actions.php",
        data: create_material(data, 1),

        success: function (response) {
            if(response.status == 2) {
                setTimeout(show_error, 500);
                $("#result").html(response.content);
                return;
            }

            console.log('images success uploaded..');

            uploadStatus = true;
            signa = response.image;
        }
    });

    if(!uploadStatus) {
        console.log('WHAAT THE FUCK..');
        return;
    }

    console.log(signa); 

    // create request
    var data = {
        'create_request':       1,
        'server':               $('#server').val(),
        'real_name':            $('#real_name').val(),
        'real_age':             $('#real_age').val(),
        'game_name':            $('#game_name').val(),
        'soc_vk':               $('#soc_vk').val(),
        'have_mic':             $('#have_mic').val(),
        'signa':                signa
    };

    NProgress.start();

    const createRequest = await $.ajax({
        type: "POST",
        url: "../modules_extra/freevip/ajax/actions.php",
        dataType: "JSON",
        data: create_material(data),

        success: function (response) {
            NProgress.done();

            if(response.status == 2) {
                $("#result").html(response.error);
                return;
            }

            $("#result").html(response.content);
        },

        error: function () {
            setTimeout(show_error, 500);
        }
    });
}
