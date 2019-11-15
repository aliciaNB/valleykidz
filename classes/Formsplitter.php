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
        foreach ($newDate as $dates)
        {
            $futuredate = new DateTime($dates);//convert date time object to be converted to next saturday
            $displayStart = $futuredate->format("M d,Y");//format date for route get hive
            $futuredate->modify('next saturday');//grab saturday of the weeks monday date provided
            $futuredate->format("Y-m-d");//format for route get hive

            $displayEnd =$futuredate->format("M d,Y");//format for html example (October 31, 2018)
            //put together href utilizing all the information provided
            $divs = "<li class='list-group-item'><a href='viewform?form=".$formNum. "&weekStart=".$dates."&weekEnd=".
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
            $startDate->modify('last monday');
        }
        while($startDate<=$endDate)//while start date is not past end of form date
        {
            $dates[]=$startDate->format("Y-m-d"); //add monday date found to list
            $startDate->modify("+1 week");//increment date by a week to the next monday
        }
        return $dates;
    }


    public static function printDetailedForm($id)
    {
        $db = new database();
        $result = $db->getAllForms($id);


        if(!$result) {//no forms created
            echo '<h2 class="mt-5 text-center">No Forms Created For Client </h2>';
        }else{

            //start first card
            echo'    <div class="row mt-3">
                        <div class="col-md-2 col-1 col-lg-2"></div>
                            <div class="card text-center col-lg-8 col-md-8 col-10 p-0">
                             <h5 class="list-group-item list-group-item-primary bglblue white list-group-flush">Current DBT FORM<h5>
                             <ul class="list-group list-group-flush">';
            if($result[0]['endDate']===null) {//if recent form is open
                echo self::printWeeks($result[0]['startDate'],date("Y-m-d"), $result[0]['formId'], $id);
            } else {
                echo self::printWeeks($result[0]['startDate'],$result[0]['endDate'], $result[0]['formId'], $id);
            }
            self::closeCardGroup();//close first card


            //Iterate over remaining results if any
            for($i=1; $i<count($result);$i++)
            {

                //format datetime
                $start = new DateTime($result[$i]["startDate"]);
                $start = $start->format("M d,Y");
                $end = new DateTime($result[$i]["endDate"]);
                $end =$end->format("M d,Y");
                //start card
                echo'<div class="row mt-3">
                        <div class="col-md-2 col-1 col-lg-2"></div>
                            <div class="card text-center col-lg-8 col-md-8 col-10 p-0">
                             <h5 class="list-group-item list-group-item-primary bglblue white list-group-flush clickable">DBT FORM SUBMITTED: '.$end.
                             ' <span class="swap">+</span></h5>
                             <ul class="list-group list-group-flush expandable">';

                echo self::printWeeks($result[$i]['startDate'],$result[$i]['endDate'], $result[$i]['formId'], $id);

                self::closeCardGroup();
                //close card
            }
        }

    }

    private static function closeCardGroup()
    {
        echo '</div><div class="col-md-2 col-1 col-lg-2"></div></div>';
    }
}