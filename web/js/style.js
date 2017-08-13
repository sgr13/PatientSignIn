$(document).ready(function () {
    var child = $('#monthSelection').attr('month');
    if (child == 1) {
        $('#jan').attr('selected', 'selected');
    } else if (child == 2) {
        $('#feb').attr('selected', 'selected');
    }else if (child == 3) {
        $('#mar').attr('selected', 'selected');
    }else if (child == 4) {
        $('#apr').attr('selected', 'selected');
    }else if (child == 5) {
        $('#may').attr('selected', 'selected');
    }else if (child == 6) {
        $('#jun').attr('selected', 'selected');
    }else if (child == 7) {
        $('#jul').attr('selected', 'selected');
    }else if (child == 8) {
        $('#aug').attr('selected', 'selected');
    }else if (child == 9) {
        $('#sep').attr('selected', 'selected');
    }else if (child == 10) {
        $('#oct').attr('selected', 'selected');
    }else if (child == 11) {
        $('#nov').attr('selected', 'selected');
    } else {
        $('#dec').attr('selected', 'selected');
    }

    var year = $('#yearSelection').attr('year');

    if (year == 2017) {
        $('#2017').attr('selected', 'selected');
    } else {
        $('#2018').attr('selected', 'selected');
    }

    $("#radioButtonDiet").click(function() {
        console.log("działa");
        $('#diab').css('display', 'none');
        $('#diet').css('display', 'inline');
    });

    $("#radioButtonDiab").click(function() {
        console.log("działa2");
        $('#diet').css('display', 'none');
        $('#diab').css('display', 'inline');

    });

    //------------------------------------------

    //Przypisanie pozycji sideMenu po załadowaniu strony do zmiennej a następnie przesunięcie menu poza ekran
    var p1 = $("#sideMenu");
    var offset1 = p1.offset();
    $('#sideMenu').animate({right: "+=150"}, 1);

    $('#sideMenu').mouseover(function() {
        //przypisanie do zmiennej pozycji sideMenu po jego schowaniu a po najechaniu myszką na ukryte menu jego wysunięcie
        var p1 = $("#sideMenu");
        var offset2 = p1.offset();
        $("#sideMenu").offset({ top: offset2.top, left: offset1.left});
    })

    $('#sideMenu').mouseleave(function() {
        //ponowne przypianie pozycji do zmiennej i po opuszczeniu kursora ukrycie sideMenu
        var p1 = $("#sideMenu");
        var offset3 = p1.offset();
        $("#sideMenu").offset({ top: offset3.top, left: offset1.left});
        $("#sideMenu").offset({ top: offset3.top, left: -151.916671752929688});
    });

    //dodanie czarnego podświetlenia do aktywnego elementu sideMenu
    $('.nav li').click(function () {
        $('.nav li').removeClass('active');
        $(this).addClass('active');
    })


    setInterval(function(){
        if($('#firstPicture').hasClass('bgnd-1')) {
            $('#firstPicture').removeClass('bgnd-1');
            $('#firstPicture').addClass('bgnd-1-2');
        } else if($('#firstPicture').hasClass('bgnd-1-2')) {
            $('#firstPicture').removeClass('bgnd-1-2');
            $('#firstPicture').addClass('bgnd-1-3');
        } else {
            $('#firstPicture').removeClass('bgnd-1-3');
            $('#firstPicture').addClass('bgnd-1');
        }
    }, 10000)

});