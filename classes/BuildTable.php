<?php

//Class that will build the table for printing
class BuildTable
{

    /**
     * Prints all notes form a form withing a range of dates
     * @param $start start date wanted from form
     * @param $end end dates wanted from form
     * @param $formId form number that is currently being displayed
     * @throws Exception if can not convert to a datetime object
     */
    public static function printNotes($start, $end, $formId)
    {
        $db = new database();
        $result=$db->getNotesBetweenDates($start,$end,$formId);
        //build table head
        echo'<div class="mt-5 data">
                <div class=\'card\'>
                    <a id=\'noteHead\' href=\'#\' class=\'text-center mb-0 card_a card-header bgdkblue\' 
                    data-toggle="collapse" data-target="#notediv" aria-expanded="true" 
                    aria-controls="notediv">Notes</a>
                    
                    <div id="notediv" class="collapse show bgdkblue"
                             aria-labelledby="noteHead">
            <table id="notes" class="table table-bordered cell-border">
            <thead>
                <tr>
                    <th id="noteHeader" class="bglblue white">Days of Week</th>
                    <th class="bglblue white">Notes</th>
                </tr>
            </thead>';

        $dates = array("Mon", "Tues", "Wed", "Thurs", "Fri", "Sat", "Sun");
        $current = new DateTime($start);
        if($current->format("N")!=1)//find out if current date is a monday
        {
            $current=$current->modify('last monday');//grab monday if not
        }
        $index = 0;
        echo '<tbody>';
        foreach ($dates as $date)
        {
            echo '<tr>';//open row
            echo '<td>'.$date.'</td>';

            if($result) {
                if($index<count($result)) {
                    $resultDate = new DateTime($result[$index]['dateSubmitted']);
                    if($resultDate->format("Y-m-d")==$current->format("Y-m-d"))
                    {
                        echo '<td>'. $result[$index]['noteInfo'].'</td>';
                        $index++;
                    }
                    else{
                        echo '<td></td>';
                    }

                }else{
                    echo '<td></td>';
                }
            }else{
                echo '<td></td>';
            }

            $current=$current->modify("+1 day");
            echo '</tr>';//close row
        }
        //close table
        echo '</tbody></table></div></div></div>';
    }
    /**
     * Prints a default table of targets from current form. Adding id's to be targeted by a later ajax/json request.
     * @param $formId form id currently being viewed
     */
    public static function printEmptyTargets($formId)
    {
        $db = new database();
        $result=$db->getTargetsFromForm($formId);
        self::printTargetHeader();

        echo '<tbody>';
        foreach ($result as $item)
        {
            $upper = ucwords($item['targetName']);
            echo"<tr><td rowspan='2'>";
            echo $upper;
            echo'<td>Urge</td>';
            $stripped = str_replace(' ', '', $item['targetName']);
            for($i=1; $i<8; $i++)
            {
                echo"<td class='empty text-center' id='"."tu". $stripped. ($i%7)."'>--</td>";
            }
            echo '</tr>';
            echo '<tr>';
            echo '<td>Action</td>';
            for($i=1; $i<8; $i++)
            {
                echo"<td class='empty text-center' id='"."ta". $stripped. ($i%7)."'>--</td>";
            }

        }
        //close table
        echo '</tbody></table></div></div></div>';//close table and div
    }


    /**
     * Prints a default table of emotions from current form. Adding id's to be targeted by a later ajax/json request.
     * @param $formId current form being worked with.
     */
    public static function printEmptyEmotions($formId)
    {

        $db = new database();
        $result=$db->getEmotionsFromForm($formId);
        self::printEmotionHeader();
        echo '<tbody>';
        foreach ($result as $item) {
            $uppper = ucwords($item['emotionName']);
           echo '<tr><td>'.$uppper.'</td>';
            $stripped = str_replace(' ', '', $item['emotionName']);
           for($i=1; $i<8; $i++)
           {
               echo '<td class="empty text-center" id="'.'e'.$stripped.($i%7).'">--</td>';
           }
           echo '</tr>';
        }

        echo '</tbody></table></div></div></div>';//close table and div
    }

    /**
     * Prints a default table for all skills asssociated with current form. To be updated later by an ajax/json call.
     * Marking each td with an id to be used by the ajax request.
     * @param $formId current form number being viewed
     */
    public static function printEmptySkills($formId)
    {
        $db = new database();
        $result=$db->getSkillsFromForm($formId);

        echo '<h2 class="mt-5 text-center">Skills</h2>';
        foreach ($result as $core => $skills)
        {
            self::printSkillHeader($core);
            echo "<tbody>";
            foreach ($skills as $key => $skill)
            {
                echo '<tr>';
                echo "<td rowspan='2'>" . ucwords($skill['skillName']) . "</td>
                    <td>Degree</td>";

                $stripped = str_replace(' ', '', $skill['skillName']);
                for($i=1; $i<8; $i++)
                {
                    echo '<td class="empty text-center" id="'.'sd'.$stripped.($i%7).'">--</td>';
                }

                echo "</tr><tr><td>Used</td>";
                for($i=1; $i<8; $i++)
                {
                    echo '<td class="empty text-center" id="'.'su'.$stripped.($i%7).'">--</td>';
                }
                echo "</tr>";
            }
            echo "</tbody></table>
            </div></div></div>";
        }
    }

    /**
     * Prints header for emotions table
     */
    private static function printEmotionHeader()
    {
        //build table head
        echo'<div class="mt-5 data pagination">
                <div class=\'card\'>
                    <a id=\'emotionHead\' href=\'#\' class=\'text-center mb-0 card_a card-header bgdkblue\' 
                    data-toggle="collapse" data-target="#emotiondiv" aria-expanded="true" 
                    aria-controls="emotiondiv">Emotions</a>
                    
                    <div id="emotiondiv" class="collapse show bgdkblue"
                    aria-labelledby="emotionHead">
                        <table id="feelings" class="table table-bordered cell-border">
                            <thead>
                                <tr>
                                    <th class="bglblue white">Emotions</th>';

        self::printDateRow();
        echo '</thead>';
    }

    /**
     * Prints header for targets table
     */
    private static function printTargetHeader()
    {
        echo "<div class=\"mt-5 data pagination\">
                <div class='card'>
                    <a id='targetHead' href='#' class='text-center mb-0 card_a card-header bgdkblue' 
                    data-toggle=\"collapse\" data-target=\"#targetdiv\" aria-expanded=\"true\" 
                    aria-controls=\"targetdiv\">Targets</a>
                    
                    <div id=\"targetdiv\" class=\"collapse show bgdkblue\"
                             aria-labelledby=\"targetHead\">
                        <table id=\"targets\" 
                        class=\"table table-bordered cell-border\">
                            <thead>
                                <tr>
                                    <th class=\"bglblue white\" colspan=\"2\">Targets</th>";
        self::printDateRow();
        echo '</thead>';
    }

    /**
     * prints header for skills table
     */
    private static function printSkillHeader($core)
    {
        if($core!="Core Mindfulness")
        {
            echo "<div class=\"mt-5 data pagination\">";
        } else {
            echo '<div class="mt-5 data">';
        }
        $stripped = str_replace(' ', '',$core);
        echo "<div class='card'>
                    <a id='" . $stripped . "Head' href='#' class='skillsHeader text-center mb-0 card_a card-header bgdkblue' 
                    data-toggle=\"collapse\" data-target=\"#" . $stripped . "div\" aria-expanded=\"true\" 
                    aria-controls=\"" . $stripped . "div\">$core</a>
                    
                    <div id=\"" . $stripped . "div\" class=\"collapse show bgdkblue\"
                             aria-labelledby=\"" . $stripped . "Head\">
                <table id=\"$stripped\" class=\"table table-bordered cell-border\">
                    <thead>
                        <tr>
                            <th class=\"bglblue white\" colspan=\"2\">DBT Skills</th>";

        self::printDateRow();
        echo"</thead>";
    }

    /**
     * Prints th for date rows
     */
    private static function printDateRow()
    {
        $dates = array("Mon", "Tues", "Wed", "Thurs", "Fri", "Sat", "Sun");
        foreach ($dates as $date) {//dated in table head
            echo '<th class="bglblue white">'.$date.'</th>';
        }
        echo "</tr>";
    }
}