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
    foreach ($data as $input)
    {
        if(validateInput($input)!="")
        {
            return validateInput($input);
        }
    }
    return;

}

/**
 * Check if a field provided is empty
 * @param $data String representation of a text box
 * @return string error message
 */
function validateInput($data)
{
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