<?php
/**
 * Class formsplitter creates divs for weeks between two given dates. formating divs to be monday-saturday
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
    static function printWeeks($startDate, $endDate, $formNum, $id)
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
            $divs = "<li><a href='viewform?form=".$formNum. "&weekStart=".$dates."&weekEnd=".
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
}