//toggles all showing class divs to hiddent to start
$( document ).ready(function() {
    $('.expandable').first().toggle();
});
//on click of hidshow div swaps between hide show verbage and d-none d-inline
$( ".clickable" ).click(function() {
    $(this).next('.expandable').toggle("slow");
    let symbol = $(this).children('.swap').html();
    if(symbol==='+')
    {
        $(this).children('.swap').html('-');
    }
    else
    {
        $(this).children('.swap').html('+');
    }
});