//Calls data tables for viewforms

$(document).ready( function(){

    //insert into existing table framework emotions data between start and end dates
    $.post(
        "model/ajax/emotiontable.php",
        function(results)
        {
            var result =jQuery.parseJSON(results);
            for (var i = 0; i < result.length; i++){
                var obj = result[i];
                if(obj.intensity !== null)
                {
                    var date = obj.dateSubmitted;//get date
                    var emotion = obj.emotionName.replace(/ /g, "");//emotion name
                    date = new Date(date);//modify to date object
                    date = date.getDay();//retrieve day in values of 0-6
                    var id = '#e' + emotion + date;
                    $(id).html(obj.intensity);
                    $(id).removeClass('empty');
                }
            }
        }
    );

    //insert into existing table framework targets data between start and end dates
    $.post(
        "model/ajax/targettable.php",
        function(results)
        {
            var result =jQuery.parseJSON(results);
            for (var i = 0; i < result.length; i++){
                var obj = result[i];
                if(obj.urge !== null)
                {
                    var date = obj.dateSubmitted;//get date
                    var target = obj.targetName.replace(/ /g, "");//target name
                    date = new Date(date);//modify to date object
                    date = date.getDay();//retrieve day in values of 0-6
                    var id = '#tu' + target + date;
                    $(id).html(obj.urge);
                    $(id).removeClass('empty');
                }
                if(obj.action==1)
                {
                    var date = obj.dateSubmitted;//get date
                    var target = obj.targetName.replace(/ /g, "");//target name
                    date = new Date(date);//modify to date object
                    date = date.getDay();//retrieve day in values of 0-6
                    var id = '#ta' + target + date;
                    $(id).html('&#10003;');
                    $(id).removeClass('empty');
                    $(id).addClass('check');
                }
            }
        }
    );

    //insert into existing table framework targets data between start and end dates
    $.post(
        "model/ajax/skillstable.php",
        function(results)
        {
            var result =jQuery.parseJSON(results);
            for (var i = 0; i < result.length; i++){
                var obj = result[i];
                if(obj.degree !== null)
                {
                    var date = obj.dateSubmitted;//get date
                    var skill = obj.skillName.replace(/ /g, "");//target name
                    date = new Date(date);//modify to date object
                    date = date.getDay();//retrieve day in values of 0-6
                    var id = '#sd' + skill + date;
                    $(id).html(obj.degree);
                    $(id).removeClass('empty');
                }
                if(obj.used==1)
                {
                    var date = obj.dateSubmitted;//get date
                    var skill = obj.skillName.replace(/ /g, "");//target name
                    date = new Date(date);//modify to date object
                    date = date.getDay();//retrieve day in values of 0-6
                    var id = '#su' + skill + date;
                    $(id).html('&#10003;');
                    $(id).removeClass('empty');
                    $(id).addClass('check');
                }
            }
        }
    );
});

$(".print").on("click", function(){
    window.print();
});