<?php


class formsplitter
{
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
            $divs.="<li><a href='viewform?form=".$formNum. "&weekStart=".$dates."&weekEnd=".
                $futuredate->format("Y-m-d")."&id=".$id."'>". $displayStart.
                " - ". $displayEnd."</a></li>";

        }
        return $divs;
    }

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