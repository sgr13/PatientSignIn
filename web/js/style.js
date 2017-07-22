$(document).ready(function () {
    var child = $('#monthSelection').attr('month');
    console.log(child);
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

});