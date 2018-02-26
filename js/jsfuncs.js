//user role
function callusRole(){
     var userRole = getValues({ p: "getUserRole" });
    if (userRole != '') {
    if (userRole[0].role !== null){
        if (userRole[0].role === "admin") {
             var usRole = "admin";
        }else {
            var usRole = "";
        } 
    } else {
        var usRole = "";
    }
    }else {
        var usRole = "";
    }
    return usRole;
}
usRole = callusRole(); 

//initialize all tooltips on a page (eg.$('#mFileTypeTool').tooltip("show"))
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// check the amazon profiles activity each minute.
checkAmzProfiles("timer");

//to start timer, enter "timer" as input
function checkAmzProfiles(timer) {
    var proAmzData = getValues({ p: "getProfileAmazon" });
    if (proAmzData.length > 0) {
        $('#manageAmz').css('display', 'inline');
        var countActive = 0;
        for (var k = 0; k < proAmzData.length; k++) {
            if (proAmzData[k].status === "running" || proAmzData[k].status === "waiting" || proAmzData[k].status === "initiated") {
                countActive++;
            }
            if (timer === "timer") {
                checkAmazonTimer(proAmzData[k].id);
            }
        }
        if (countActive > 0) {
            $('#amzAmount').css('display', 'inline');
            $('#amzAmount').text(countActive);
        } else {
            $('#amzAmount').text(countActive);
            $('#amzAmount').css('display', 'none');
        }
    }
}



function checkAmazonTimer(proId) {
    window['interval_amzStatus_' + proId] = setInterval(function () {
        checkAmazonStatus(proId)
    }, 60000);
}

function checkAmazonStatus(proId) {

    var checkAmazonStatus = getValues({ p: "checkAmazonStatus", profileId: proId });
    if (checkAmazonStatus.status === "waiting") {
        $('#status-' + proId).html('<i class="fa fa-hourglass-1"></i> Waiting for reply..');
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStart').css('display', 'none');
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStop').attr('disabled', 'disabled');
    } else if (checkAmazonStatus.status === "initiated") {
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStart').css('display', 'none');
        $('#status-' + proId).html('<i class="fa fa-hourglass-half"></i> Initializing..');
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStop').removeAttr('disabled');
    } else if (checkAmazonStatus.status === "running") {
        if (checkAmazonStatus.sshText) {
            var sshText = "(" + checkAmazonStatus.sshText + ")";
        } else {
            var sshText = "";
        }
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStart').css('display', 'none');
        $('#status-' + proId).html('Running <br/>' + sshText);
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStop').removeAttr('disabled');

    } else if (checkAmazonStatus.status === "inactive") {
        $('#status-' + proId).text('Inactive');
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStop').attr('disabled', 'disabled');
    } else if (checkAmazonStatus.status === "terminated") {
        if (checkAmazonStatus.logAmzCloudList) {
            var logText = checkAmazonStatus.logAmzCloudList;
            if (logText.match(/INSTANCE ID ADDRESS STATUS ROLE(.*)/)) {
                var errorText = logText.match(/INSTANCE ID ADDRESS STATUS ROLE(.*)/)[1];
                if (errorText !== "") {
                    errorText = "(" + errorText + ")";
                }
            } else {
                errorText = "(" + logText + ")";
            }
        }else {
            var errorText = "";
        }

        if (errorText === "" && checkAmazonStatus.logAmzStart) {
            var logTextStart = checkAmazonStatus.logAmzStart;
            if (logTextStart.match(/error/i) || logTextStart.match(/denied/i) || logTextStart.match(/missing/i) || logTextStart.match(/couldn't/i) || logTextStart.match(/help/i) || logTextStart.match(/wrong/i))
                errorText = "(" + logTextStart + ")";
        } 
        $('#status-' + proId).html('Terminated <br/>' + errorText);
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStart').css('display', 'inline');
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStop').attr('disabled', 'disabled');
    } else {
        $('#amzTable > thead > #amazon-' + proId + ' > > #amzStop').removeAttr('disabled');
    }

}



$(document).ready(function () {
   
    
    
    function addAmzRow(id, name, default_region, executor, instance_type, image_id, subnet_id) {
        $('#amzTable > thead').append('<tr id="amazon-' + id + '"> <td>' + name + '</td><td>Instance_type: ' + instance_type + '<br>  Image id: ' + image_id + '<br>  Default Region: ' + default_region + '<br>  Subnet Id: ' + subnet_id + '<br> Executor: ' + executor + '<br>  </td><td id="status-' + id + '">Inactive</td><td>' + getButtonsDef('amz', 'Start') + getButtonsDef('amz', 'Stop') + '</td></tr>');
    }

    $('#amzModal').on('show.bs.modal', function (event) {
        $(this).find('form').trigger('reset');
        var proAmzData = getValues({ p: "getProfileAmazon" });

        $.each(proAmzData, function (el) {
            addAmzRow(proAmzData[el].id, proAmzData[el].name, proAmzData[el].default_region, proAmzData[el].executor, proAmzData[el].instance_type, proAmzData[el].image_id, proAmzData[el].subnet_id);
            checkAmazonStatus(proAmzData[el].id);

        });


    });

    //close amzModal
    $('#amzModal').on('hide.bs.modal', function (event) {
        $('#amzTable td ').remove();
        checkAmzProfiles("notimer");
    });

    $('#amzModal').on('click', '#amzStart', function (e) {
        e.preventDefault();

        var clickedRowId = $(this).closest('tr').attr('id'); //local-20
        var patt = /(.*)-(.*)/;
        var proId = clickedRowId.replace(patt, '$2');

        //enter amazon details modal
        $('#addAmzNodeModal').off();
        $('#addAmzNodeModal').on('show.bs.modal', function (event) {
            $(this).find('form').trigger('reset');
        });
        //close addAmzNodeModal
        $('#addAmzNodeModal').on('hide.bs.modal', function (event) {
            $('#autoscaleDiv').attr('class', 'collapse');
        });
        $('#addAmzNodeModal').on('click', '#activateAmz', function (event) {
            event.preventDefault();
            var data = {};
            var numNodes = $('#numNodes').val();
            var autoscale_check = $('#autoscale_check').is(":checked").toString();
            var autoscale_maxIns = $('#autoscale_maxIns').val();
            if (numNodes !== '') {
                data = {
                    "id": proId,
                    "nodes": numNodes,
                    "autoscale_check": autoscale_check,
                    "autoscale_maxIns": autoscale_maxIns,
                    "p": "startProAmazon"
                };

                $.ajax({
                    type: "POST",
                    url: "ajax/ajaxquery.php",
                    data: data,
                    async: true,
                    success: function (s) {
                        if (s.start_cloud) {
                            // check the amazon profiles activity each minute.
                            $('#status-' + proId).html('<i class="fa fa-hourglass-1"></i> Waiting for reply..');
                            $('#amzTable > thead > #amazon-' + proId + ' > > #amzStart').css('display', 'none');
                            $('#amzTable > thead > #amazon-' + proId + ' > > #amzStop').attr('disabled', 'disabled');
                            $('#addAmzNodeModal').modal('hide');
                            //                            checkAmazonTimer(proId);
                        }
                    }
                });
            }
        });
        $('#addAmzNodeModal').modal('show');
    });

    $('#amzModal').on('click', '#amzStop', function (e) {
        e.preventDefault();
        var clickedRowId = $(this).closest('tr').attr('id'); //local-20
        var patt = /(.*)-(.*)/;
        var proId = clickedRowId.replace(patt, '$2');
        var data = { "id": proId, "p": "stopProAmazon" };
        $.ajax({
            type: "POST",
            url: "ajax/ajaxquery.php",
            data: data,
            async: true,
            success: function (s) {

                if (s.stop_cloud) {
                    // check the amazon profiles activity each minute.
                    $('#status-' + proId).html('<i class="fa fa-hourglass-1"></i> Waiting for termination..');
                    setTimeout(function () { checkAmazonStatus(proId); }, 1000);

                }
            }
        });
    });

});


//load filter sidebar menu options
if (usRole === "admin"){
    $("#filterMenu").append('<li><a href="#" data-value="630" tabIndex="-1"><input type="checkbox"/>&nbsp;Waiting Approval</a></li>');
}
var allUserGrp = getValues({ p: "getUserGroups" });
$("#filterMenu").append('<li><a href="#" data-value="3" tabIndex="-1"><input type="checkbox"/>&nbsp;Private</a></li>');
$("#filterMenu").append('<li><a href="#" data-value="63" tabIndex="-1"><input type="checkbox"/>&nbsp;Public</a></li>');
for (var i = 0; i < allUserGrp.length; i++) {
    var param = allUserGrp[i];
    $("#filterMenu").append('<li><a href="#" data-value="group-' + param.id + '" tabIndex="-1"><input type="checkbox"/>&nbsp;' + param.name + '</a></li>');
}

//filter sidebar menu (multiple selection feature)
var optionsFilter = [];
$('.filterM a').on('click', function (event) {
    $('#tags').val("");
    var $target = $(event.currentTarget),
        val = $target.attr('data-value'),
        $inp = $target.find('input'),
        idx;
    if ((idx = optionsFilter.indexOf(val)) > -1) {
        optionsFilter.splice(idx, 1);
        setTimeout(function () { $inp.prop('checked', false) }, 0);
    } else {
        optionsFilter.push(val);
        setTimeout(function () { $inp.prop('checked', true) }, 0);
    }
    $(event.target).blur();
    //filter based on options array
    filterSideBar(optionsFilter)
    return false;
});

function filterSideBar(options) {
    var tagElems = $('#autocompletes1').children()
    if (options.length) {
        $(tagElems).hide();
        var selOptArr = [];
        var group_idArr = [];
        for (var i = 0; i < options.length; i++) {
            console.log(options[i])
            if (options[i] === '3' || options[i] === '63' || options[i] === '630') {
                var selOpt = options[i];
                selOptArr.push(selOpt);
            } else if (options[i].match(/group-(.*)/)) {
                var group_id = options[i].match(/group-(.*)/)[1];
                var selOpt = "15";
                selOptArr.push(selOpt);
                group_idArr.push(group_id);
            }
        }

        for (var i = 0; i < tagElems.length; i++) {
            var tagElems2 = $(tagElems).eq(i).children().eq(1).children()
            $(tagElems2).hide()
            for (var j = 0; j < tagElems2.length; j++) {
                if ($(tagElems2).eq(j).attr('pin') === 'true'){
                    var checkPinText = '63';
                }else {
                    var checkPinText = '630';
                }
                var checkPin = $.inArray(checkPinText, selOptArr) >= 0;
                console.log('checkPin')
                console.log(checkPin)
                var checkPerm = $.inArray($(tagElems2).eq(j).attr('p'), selOptArr) >= 0;
                console.log('checkPerm')
                console.log(checkPerm)
                
                var checkGroup = $.inArray($(tagElems2).eq(j).attr('g'), group_idArr) >= 0;
                console.log('checkGroup')
                console.log(checkGroup)
                
                if (($(tagElems2).eq(j).attr('p') === "15" && checkPerm && checkGroup) || ($(tagElems2).eq(j).attr('p') === "3" && checkPerm) || ($(tagElems2).eq(j).attr('p') === "63" && checkPin) ) {
                    $(tagElems).eq(i).show()
                    if (selOpt !== "") {} else {
                        $(tagElems).show()
                    }
                    $(tagElems2).eq(j).show()
                }
            }
        }
    } else {
        //if nothing is selected show everything
        $(tagElems).show()
        for (var i = 0; i < tagElems.length; i++) {
            var tagElems2 = $(tagElems).eq(i).children().eq(1).children()
            $(tagElems2).show()
        }
    }
    $('#inputs').show();
    $('#outputs').show();
    $('.header').show();
    $('#Pipelines').show();
}

//SideBar menu Search Function
//$('#tags').on('keyup',function(e){
$('.main-sidebar').on('keyup', '#tags', function (e) {
    $('.filterM a >').prop('checked', false);

    var tagElems = $('#autocompletes1').children()
    $(tagElems).hide()
    for (var i = 0; i < tagElems.length; i++) {
        var tagElems2 = $(tagElems).eq(i).children().eq(1).children()

        $(tagElems2).hide()
        $(tagElems).eq(i).closest('li').children('ul.treeview-menu').hide()
        for (var j = 0; j < tagElems2.length; j++) {
            if (($(tagElems2).eq(j).text().toLowerCase()).indexOf($(this).val().toLowerCase()) === 0) {
                $(tagElems).eq(i).show()
                if ($(this).val().toLowerCase() !== "") {
                    $(tagElems).eq(i).closest('li').addClass('menu-open')
                    $(tagElems).eq(i).closest('li').children('ul.treeview-menu').show()
                } else {
                    $(tagElems).eq(i).closest('li').removeClass('menu-open')
                    $(tagElems).show()
                }
                $(tagElems2).eq(j).show()
            }
        }
    }
    $('#inputs').show();
    $('#outputs').show();
    $('.header').show();
    $('#Pipelines').show();

});



//table buttons:
var SELECT = 4; // 1
var EDIT = 2; // 10
var REMOVE = 1; // 100

function getTableButtons(name, buttons) {
    //ser <- 9f 
    var selectButton = '';
    var editButton = '';
    var removeButton = '';


    if (buttons.toString(2) & SELECT) {
        selectButton = '<div style="display: inline-flex"><button type="button" class="btn btn-primary btn-sm" title="Select" id="' + name + 'select">Select</button> &nbsp; '
    }
    if (buttons.toString(2) & EDIT) {
        editButton = '<div style="display: inline-flex"><button type="button" class="btn btn-primary btn-sm" title="Edit" id="' + name + 'edit" data-toggle="modal" data-target="#' + name + 'modal">Edit</button> &nbsp;'
    }
    if (buttons.toString(2) & REMOVE) {
        removeButton = '<button type="button" class="btn btn-primary btn-sm" title="Remove" id="' + name + 'remove">Remove</button></div>'
    }

    return selectButton + editButton + removeButton
}




// eg. name:run buttons:select
function getButtonsModal(name, buttons) {
    var buttonId = buttons.split(' ')[0];
    var button = '<button type="button" class="btn btn-primary btn-sm" title="' + buttons + '" id="' + name + buttonId + '" data-toggle="modal" data-target="#' + name + 'modal">' + buttons + '</button> &nbsp;';
    return button;
}
//Default type of buttons
function getButtonsDef(name, buttons) {
    var buttonId = buttons.split(' ')[0];
    var button = '<button type="submit" class="btn btn-primary btn-sm" title="' + buttons + '" id="' + name + buttonId + '">' + buttons + '</button> &nbsp;';
    return button;
}


function getIconButtonModal(name, buttons, icon) {
    var buttonId = buttons.split(' ')[0];
    var button = '<button type="submit" style="padding:0px;" class="btn" title="' + buttons + '" id="' + name + buttonId + '" data-toggle="modal" data-target="#' + name + 'modal"><a data-toggle="tooltip" data-placement="bottom" data-original-title="' + name + '"><i class="' + icon + '" style="font-size: 17px;"></i></a></button>';
    return button;
}

function getIconButton(name, buttons, icon) {
    var buttonId = buttons.split(' ')[0];
    var button = '<button type="submit" style="padding:0px;" class="btn" title="' + buttons + '" id="' + name + buttonId + '"><a data-toggle="tooltip" data-placement="bottom" data-original-title="' + name + '"><i class="' + icon + '" style="font-size: 17px;"></i></a></button>';
    return button;
}




//checklogin
var loginSuccess = false;
var userProfile = checkLogin();

function checkLogin() {
    var userLog = [];
    var userPro = [];
    userLog.push({ name: "p", value: 'checkLogin' });
    $.ajax({
        type: "POST",
        data: userLog,
        url: "ajax/ajaxquery.php",
        async: false,
        success: function (msg) {
            if (msg.error == 1) {
                loginSuccess = false;
            } else {
                userPro = msg;
                loginSuccess = true;
            }

        }
    });
    return userPro;
};

if (loginSuccess === true && userProfile !== '') {
    var imgUrl = userProfile[0].google_image;
    var userName = userProfile[0].name;
    $('#googleSignIn').css('display', "none");
    $('#userAvatar').css('display', "inline");
    $('#userInfo').css('display', "inline");
    $('#userAvatarImg').attr('src', imgUrl);
    $('#userName').text(userName);
} else {
    $('#googleSignIn').css('display', "inline");
    $('#userAvatar').css('display', "none");
    $('#userInfo').css('display', "none");
}
// google sign-in
function Google_signIn(googleUser) {
    var id_token = googleUser.getAuthResponse().id_token;
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.disconnect();

    var userProfile = [];
    var profile = googleUser.getBasicProfile();
    var emailUser = profile.getEmail();
    var pattEmail = /(.*)@(.*)/; //Map_Tophat2@11
    var username = emailUser.replace(pattEmail, '$1');
    userProfile.push({ name: "google_id", value: profile.getId() });
    userProfile.push({ name: "name", value: profile.getName() });
    userProfile.push({ name: "email", value: profile.getEmail() });
    userProfile.push({ name: "google_image", value: profile.getImageUrl() });
    userProfile.push({ name: "username", value: username });
    userProfile.push({ name: "p", value: 'saveUser' });
    update_user_data(userProfile);
}

function update_user_data(userProfile) {
    $.ajax({
        type: "POST",
        data: userProfile,
        url: "ajax/login.php",
        async: false,
        success: function (msg) {
            if (msg.error == 1) {
                alert('Something Went Wrong!');
            } else {
                var logInSuccess = true;
                window.location.reload('true');

            }

        }
    });
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    var userLog = [];
    userLog.push({ name: "p", value: 'logOutUser' });
    auth2.signOut().then(function () {

        $.ajax({
            type: "POST",
            data: userLog,
            url: "ajax/login.php",
            async: false,
            success: function (msg) {
                if (msg.logOut == 1) {
                    var logInSuccess = false;
                    window.location.reload('true');


                }
            }
        });
    });
}

//use changeVal to trigger change event after using val()
$.fn.changeVal = function (v) {
    return $(this).val(v).trigger("change");
}

//Adjustable textwidth
var $inputText = $('input.width-dynamic');
// Resize based on text if text.length > 0
// Otherwise resize based on the placeholder

$("input.width-dynamic").on("change", function () {
    var namePip = $('input.width-dynamic').val();
    resizeForText.call($inputText, namePip);
});

function resizeForText(text) {
    var $this = $(this);
    if (!text.trim()) {
        text = $this.attr('placeholder').trim();
    }
    var $span = $this.parent().find('span.width-dynamic');
    $span.text(text);
    var $inputSize = $span.width() + 10;
    if ($inputSize < 50) {
        $inputSize = 50;
    }
    $this.css("width", $inputSize);
}
$inputText.keypress(function (e) {
    if (e.which && e.charCode) {
        var c = String.fromCharCode(e.keyCode | e.charCode);
        var $this = $(this);
        resizeForText.call($this, $this.val() + c);
    }
});
// Backspace event only fires for keyup
$inputText.keyup(function (e) {
    if (e.keyCode === 8 || e.keyCode === 46) {
        resizeForText.call($(this), $(this).val());
    }
});
$inputText.each(function () {
    var $this = $(this);
    resizeForText.call($this, $this.val())
});

function getValues(data) {
    var result = null;
    $.ajax({
        url: "ajax/ajaxquery.php",
        data: data,
        async: false,
        cache: false,
        success: function (data) {
            result = data;
        }
    });
    return result;
}

function truncateName(name, type) {
    if (type === 'inOut') {
        var letterLimit = 7;
    } else if (type === 'process') {
        var letterLimit = 12;
    } else if (type === 'newTable') {
        var letterLimit = 120;
    } else if (type === 'processTable') {
        var letterLimit = 300;
    }
    if (name.length > letterLimit)
        return name.substring(0, letterLimit) + '..';
    else
        return name;
}

$('.collapseIcon').on('click', function (e) {
    var textClass = $(this).attr('class');
    if (textClass.includes('fa-plus-square-o')) {
        $(this).removeClass('fa-plus-square-o');
        $(this).addClass('fa-minus-square-o');
    } else if (textClass.includes('fa-minus-square-o')) {
        $(this).removeClass('fa-minus-square-o');
        $(this).addClass('fa-plus-square-o');
    }

});


// fills the from with the object data. find is comma separated string: 'input, p'
//eg.  fillForm('#execNextSettTable','input', exec_next_settings);

function fillForm(formId, find, data) {
    var formValues = $(formId).find(find);

    var keys = Object.keys(data);
    for (var i = 0; i < keys.length; i++) {
        $(formValues[i]).val(data[keys[i]]);

    }
}



$(function () {
    $("#feedback-tab").click(function () {
        $("#feedback-form").toggle("slide right");
    });

    $("#feedback-form form").on('submit', function (event) {
        var $form = $(this);
        var data = $form.serializeArray();
        var url = window.location.href;
        console.log(url);
        data.push({ name: "url", value: url })
        data.push({ name: "p", value: "savefeedback" })
        $.ajax({
            type: "POST",
            url: "ajax/ajaxquery.php",
            data: data,
            success: function () {
                $("#feedback-form").toggle("slide right").find("textarea").val('');
            }
        });
        event.preventDefault();
    });
});


function escapeHtml(str) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return str.replace(/[&<>"']/g, function (m) { return map[m]; });
}

function decodeHtml(str) {
    var map = {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'"
    };
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function (m) { return map[m]; });
}
