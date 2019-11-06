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
        $newDate = self::splitDates($startDate,$endDate);
        $divs ="";
        foreach ($newDate as $dates)
        {
            $futuredate = new DateTime($dates);
            $displayStart = $futuredate->format("M d,Y");
            $futuredate->modify('next saturday');
            $futuredate->format("Y-m-d");

            $displayEnd =$futuredate->format("M d,Y");
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
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        $dates = [];

        if($startDate>$endDate)
        {
            return $dates;
        }
        if(1 !=$startDate->format('N'))
        {
            $startDate->modify('last monday');
        }
        while($startDate<=$endDate)
        {
            $dates[]=$startDate->format("Y-m-d");
            $startDate->modify("+1 week");
        }
        return $dates;
    }
}