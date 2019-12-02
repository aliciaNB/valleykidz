<?php
/**
 * Class formsplitter creates divs for weeks between two given dates. formating divs to be monday-saturday
 * @author Valley Kidz team
 * @date 11/13/2019
 */

class Formsplitter
{
    /**
     * This method will take two dates and find all the monday-saturday's containeed between these dates
     * It will then prints these in most recent order and pass form/id to the new route
     * @param $startDate forms start date
     * @param $endDate forms end date
     * @param $formNum number of form to be passed via get request
     * @param $id of client to be provded via get request
     * @return string returns divs that contain hrefs that pass get information
     * @throws Exception Date time error if can not be converted
     */
    private static function printWeeks($startDate, $endDate, $formNum, $id)
    {
        $newDate = self::splitDates($startDate,$endDate);//retrive array of monday dates
        $divs ="";
        $numDates = count($newDate);
        if($numDates===0){//no future mondays meaning end date and start date only range of values
            $futuredate= new DateTime($startDate);
            $startDate= $futuredate->format("Y-m-d");
            $displayStart = $futuredate->format("M d,Y");//format date for route get hive
            $futuredate= new DateTime($endDate);
            $displayEnd= $futuredate->format("M d,Y");
            $divs = "<li class='list-group-item'><a href='viewform?form=".$formNum. "&weekStart=".$startDate."&weekEnd=".
                $futuredate->format("Y-m-d")."&id=".$id."'>". $displayStart.
                " - ". $displayEnd."</a></li>" . $divs;
        } else{//multiple weeks

            //display start day to saturday
            $futuredate= new DateTime($startDate);
            $startDate = new DateTime($startDate);
            $startDate= $startDate->format("Y-m-d");
            if($futuredate->format('N')!=1)// if start date does not fall on a monday create first div
            {
                $displayStart = $futuredate->format("M d,Y");//format date for route get hive
                $futuredate= $futuredate->modify('next sunday');
                $displayEnd= $futuredate->format("M d,Y");
                $divs = "<li class='list-group-item'><a href='viewform?form=".$formNum. "&weekStart=".$startDate."&weekEnd=".
                    $futuredate->format("Y-m-d")."&id=".$id."'>". $displayStart.
                    " - ". $displayEnd."</a></li>" . $divs;
            }
            //display all but last week
            for($i=0; $i<count($newDate)-1; $i++)
            {
                $futuredate = new DateTime($newDate[$i]);//convert date time object to be converted to next saturday
                $displayStart = $futuredate->format("M d,Y");//format date for route get hive
                $futuredate->modify('next sunday');//grab saturday of the weeks monday date provided
                $futuredate->format("Y-m-d");//format for route get hive

                $displayEnd =$futuredate->format("M d,Y");//format for html example (October 31, 2018)
                //put together href utilizing all the information provided
                $divs = "<li class='list-group-item'><a href='viewform?form=".$formNum. "&weekStart=".$newDate[$i]."&weekEnd=".
                    $futuredate->format("Y-m-d")."&id=".$id."'>". $displayStart.
                    " - ". $displayEnd."</a></li>" . $divs;
            }

            //print last week making end date forms end date
            $futuredate = new DateTime($newDate[count($newDate)-1]);//convert date time object to be converted to next saturday
            $displayStart = $futuredate->format("M d,Y");//format date for route get hive
            $futuredate= new DateTime($endDate);//grab saturday of the weeks monday date provided

            $futuredate->format("Y-m-d");//format for route get hive

            $displayEnd =$futuredate->format("M d,Y");//format for html example (October 31, 2018)
            //put together href utilizing all the information provided
            $divs = "<li class='list-group-item'><a href='viewform?form=".$formNum. "&weekStart=".$newDate[count($newDate)-1]."&weekEnd=".
                $futuredate->format("Y-m-d")."&id=".$id."'>". $displayStart.
                " - ". $displayEnd."</a></li>" . $divs;

        }
        return $divs;
    }

    /**
     * Takes two dates in find the monday-saturdays contained between these dates
     * @param $startDate start date of a form
     * @param $endDate end date of af a form
     * @return array start dates of the mondays of each week
     * @throws Exception if datetime can not be converted from the date provided.
     */
    private static function splitDates($startDate, $endDate)
    {
        $startDate = new DateTime($startDate);//convert to date time object
        $endDate = new DateTime($endDate);//convert to date time object
        $dates = [];//this will hold all mondays of the week in format Y-m-d

        if($startDate>$endDate)//if provided bad values
        {
            return $dates;
        }
        if(1 !=$startDate->format('N'))//if start date is not a monday grab previous monday
        {
            $startDate->modify('next monday');
        }
        while($startDate<=$endDate)//while start date is not past end of form date
        {
            $dates[]=$startDate->format("Y-m-d"); //add monday date found to list
            $startDate->modify("+1 week");//increment date by a week to the next monday
        }
        return $dates;
    }


    /**
     * Prints all the current form and prior session information in card format using self split methods to format internal dates withing card groups
     * @param $id client id in db
     * @throws Exception if date can not be formatted into a date time object
     */
    public static function printDetailedForm($id)
    {
        $db = new database();
        $result = $db->getAllForms($id);

        if(!$result) {//no forms created
            echo '<h2 class="mt-5 text-center">No Forms Created For Client </h2>';
        }else{

            //start first card
            echo'<div class="row mt-3 mb-5">
                        <div class="col-md-2 col-1 col-lg-2"></div>
                            <div class="card text-center col-lg-8 col-md-8 col-10 p-0">
                             <h3 id="current" class="list-group-item list-group-item-primary bgdkgold white list-group-flush clickable">CURRENT SESSION FORM
                             <span class="swap">-</span></h3>
                             <ul class="list-group list-group-flush expandable">';
            if($result[0]['endDate']===null) {//if recent form is open
                echo self::printWeeks($result[0]['startDate'],date("Y-m-d"), $result[0]['formId'], $id);
            } else {
                echo self::printWeeks($result[0]['startDate'],$result[0]['endDate'], $result[0]['formId'], $id);
            }
            self::closeCardGroup();//close first card


            //Check if second cards exist and add seperators
            if(count($result)>1)
            {
                echo '
                <div class="row mt-3 mb-5">
                <div class="col-md-2 col-1 col-lg-2"></div>
                <div class="card text-center col-lg-8 col-md-8 col-10 p-0">
                <h3 id="prior" class="list-group-item list-group-item-primary bgdkblue white list-group-flush clickable">PRIOR SESSION FORMS
                      <span class="swap">+</span></h3><div class="expandable">';

                //Iterate over remaining results if any
                for($i=1; $i<count($result);$i++)
                {

                    //format datetime
                    $start = new DateTime($result[$i]["startDate"]);
                    $start = $start->format("M d,Y");
                    $end = new DateTime($result[$i]["endDate"]);
                    $end =$end->format("M d,Y");
                    //start card
                    echo'<div class="row mt-3 mb-3">
                        <div class="col-md-2 col-1 col-lg-2"></div>
                            <div class="card text-center col-lg-8 col-md-8 col-10 p-0">
                             <h3 class="priors list-group-item list-group-item-primary bglblue white list-group-flush clickable">SESSION: '.$start.'-'.$end.
                        ' <span class="swap">+</span></h3>
                             <ul class="list-group list-group-flush expandable">';

                    echo self::printWeeks($result[$i]['startDate'],$result[$i]['endDate'], $result[$i]['formId'], $id);

                    self::closeCardGroup();
                    //close card
                }
                //close expandable div
                echo '</div>';
            }

            echo '</div></div>';
        }

    }

    private static function closeCardGroup()
    {
        echo '</ul></div><div class="col-md-2 col-1 col-lg-2"></div></div>';
    }
}