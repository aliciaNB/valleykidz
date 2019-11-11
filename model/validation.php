<?php

/**
 * Validates client number provided is numericall and correct length
 * @param $data int client number
 * @return string error message or empty if error-free
 */

function validateClientNumber($data)
{
    if($data=="")
    {
        return "Must Fill in Field to add/remove client";
    }
    if(!is_numeric($data))
    {
        return "Client number can not contain letters or special characters";
    }

    if(strlen($data)!=6)
    {
        return "Invalid Entry client numbers must be 6 in length";
    }
    return "";
}

/**
 * Takes an array of inputs and verifies that they are not empty
 * @param $data array accepts a group of strings representing a group of inputs
 * @return string|void
 */
function validateInputGroup($data)
{
    if($data !=null) {
        foreach ($data as $input) {
            if (validateInput($input) != "") {
                return validateInput($input);
            }
        }
    }
    return "";
}

/**
 * Check if a field provided is empty
 * @param $data String representation of a text box
 * @return string error message
 */
function validateInput($data)
{
    if($data!=null)
     //TODO Add db connection to escape $data =mysqli_real_escape_string($data);
     if($data=="")
     {
         return "Fields can not be empty";
     }
     return "";
}

/**
 * Checks an error array has no set values if so no errors exist
 * @param $data error array
 * @return bool true:no errors, false:errors exist
 */
function checkErrArray($data)
{
    foreach ($data as $key => $value) {
        if($value!="") {
            return false;
        }
    }
    return true;
}

/**
 * Check if an array contains only empty strings or is null
 * @param $array an array of strings
 * @return bool true if null or all empty strings/ false otherwise
 */
function isEmptyStringOrNUll($array)
{
    if($array===null)
    {
        return true;
    }
    foreach ($array as $item)
    {
        if($item!=="")
        {
            return false;
        }
    }
    return true;
}

/**
 * Takes a string and returns escaped string to provent sql injection
 * @param $string string value from a form input
 * @param $db db connection
 * @return string escaped string.
 */
function preventSQLInjections($string, $db)
{
    //return mysqli_real_escape_string($db ,$string);
}