$("#submit").on("click", function(){
    let date = $(".active").attr("id");
    $("#date").val(date);
});