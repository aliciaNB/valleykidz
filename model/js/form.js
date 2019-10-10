//sanwabe website add remove field function redesigned for this project
$(document).ready(function() {
    var max_fields = 10; //maximum input boxes allowed
    var wrapper = $(".input_fields_wrap"); //Fields wrapper
    var add_button = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="mt-1 mb-1"><input type="text" placeholder="Enter Feeling Here"' +
                ' class="form-control mt-1" name="mytext[]"/><a href="#"' +
                ' class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});

//sanwabe website add remove field function redesigned for this project
$(document).ready(function() {
    var target_max_fields = 10; //maximum input boxes allowed
    var target_wrapper = $(".target_wrapper"); //Fields wrapper
    var target_button = $(".add_target_button"); //Add button ID

    var y = 1; //initlal text box count
    $(target_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(y < target_max_fields){ //max input box allowed
            y++; //text box increment
            $(target_wrapper).append('<div class="mt-1 mb-1"><input type="text" placeholder="Enter Target Here"' +
                ' class="form-control mt-1" name="myTargets[]"/><a href="#"' +
                ' class="remove_field">Remove</a></div>'); //add input box
        }
    });

    $(target_wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); y--;
    })
});