//Calls data tables for viewforms



$(document).ready( function(){
    $.post("model/ajax/emotiontable.php", function(results)
        {

        });
});

// Scripts for data tables
//targets table
$(document).ready( function () {
    $('#targets').DataTable();
} );
$('#branchtable').DataTable( {
    responsive: true
});

//feelings table
$(document).ready( function () {
    $('#feelings').DataTable();
} );
$('#branchtable').DataTable( {
    responsive: true
} );

//skills table
$(document).ready( function () {
    $('#skill').DataTable();
} );
$('#branchtable').DataTable( {
    responsive: true
} );
