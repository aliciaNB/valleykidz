jQuery(document).ready(function($) {
    var alterClass = function() {
        var ww = document.body.clientWidth;
        if (ww < 550) {
            $('.smaller').addClass('w-100');
            $("table").addClass('table-sm');
        } else if (ww >= 401) {
            $('.smaller').removeClass('w-100');
            $("table").removeClass('table-sm');
        };
    };
    $(window).resize(function(){
        alterClass();
    });
    //Fire it when the page first loads:
    alterClass();
});