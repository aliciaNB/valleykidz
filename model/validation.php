<?php
/**
 * This file is used to validate client input
 * @author Valley Kidz Team
 * @date 11/13/2019
 */

/**
 * Validates client number provided is numericall and correct length.
 *
 * @param $data int client number.
 * @return string error message or empty if error-free.
 */
function validateClientNumber($data)
{
    if ($data == "") {
        return "Must fill in field to add/remove client";
    }

    if (!is_numeric($data)) {
        return "Client numbers can not contain letters or special characters";
    }

    if (strlen($data) != 6) {
        return "Invalid entry client numbers must be 6 in length";
    }
    return "";
}

/**
 * Takes an array of inputs and verifies that they are not empty.
 *
 * @param $data array accepts a group of strings representing a group of inputs.
 * @return string|void
 */
function validateInputGroup($data)
{
    if ($data != null) {
        foreach ($data as $input) {
            if (validateInput($input) != "") {
                return validateInput($input);
            }
        }
    }
    return "";
}

/**
 * Check if a field provided is empty.
 *
 * @param $data String representation of a text box.
 * @return string error message.
 */
function validateInput($data)
{
    if ($data != null)
     //TODO Add db connection to escape $data =mysqli_real_escape_string($data);
     if ($data == "") {
         return "Fields can not be empty";
     }
     return "";
}

/**
 * Checks an error array has no set values if so no errors exist.
 *
 * @param $data error array.
 * @return bool true:no errors, false:errors exist.
 */
function checkErrArray($data)
{
    foreach ($data as $key => $value) {
        if ($value != "") {
            return false;
        }
    }
    return true;
}

/**
 * Check if an array contains only empty strings or is null.
 *
 * @param $array an array of strings.
 * @return bool true if null or all empty strings/ false otherwise.
 */
function isEmptyStringOrNUll($array)
{
    if($array === null) {
        return true;
    }
    foreach ($array as $item) {
        if ($item !== "") {
            return false;
        }
    }
    return true;
}

/**
 * Takes a string and returns escaped string to prevent sql injection
 *
 * @param $string string value from a form input.
 * @param $db db connection.
 * @return string escaped string.
 */
function preventSQLInjections($string, $db)
{
    //return mysqli_real_escape_string($db ,$string);
}

/**
 * Takes the Client's post array from their form and the skills array and validates
 * the selects, checkboxes, and the notes.
 *
 * @param $post The post array.
 * @param $skills The skills array.
 * @return bool Whether or not the form data was valid.
 */
function validateForm(&$post, $skills, $targets)
{
    validateNotes($post);
    return validateSelects($post) && validateCheckboxes($post, $skills, $targets);
}

/**
 * Takes the post array from the client's form and makes sure the selects have valid data.
 *
 * @param $post The post array.
 * @return bool Whether or not the data is valid.
 */
function validateSelects($post)
{
    $urges = $post['urges'];
    foreach ($urges as $urge) {
        if (!($urge === "" || in_array($urge, range(0,5)))) {
            return false;
        }
    }

    $intensities = $post['intensity'];
    foreach ($intensities as $intensity) {
        if (!($intensity === "" || in_array($intensity, range(0,5)))) {
            return false;
        }
    }

    $degrees = $post['degree'];
    foreach ($degrees as $degree) {
        if (!($degree === "" || in_array($degree, range(1,5)))) {
            return false;
        }
    }
    return true;
}

/**
 * Takes the post and skills arrays from the client's form and makes sure the checkboxes have valid data.
 *
 * @param $post The post array
 * @param $skills The skills array
 * @return bool Whether or not the data is valid
 */
function validateCheckboxes($post, $skills, $targets)
{
    $actions = $post['actions'];
    $used = $post['coreskills'];

    if($actions) {
        foreach ($targets as $target) {
            if ($actions[$target] != '1' || $actions[$target] != null) {
                return false;
            }
        }
    }

    if ($used) {
        foreach($skills as $coreskill) {
            foreach ($coreskill as $skill) {
                if ($used[$skill['skillName']] != "1" || $used[$skill['skillName']] != null) {
                    var_dump($used[$skill['skillName']]);
                    //return false;
                }
            }
        }
    }
    return true;
}

/**
 * Takes the post array from the client's form and the database object and escapes the client's notes.
 *
 * @param $post The post array
 * @param $db The database object
 */
function validateNotes(&$post)
{
    $badChars = array(';', '(', ')');
    $post['notes'] = str_replace($badChars, '', $post['notes']);
}

// ---------------- Validate Add New Client, Clinician, Update Account Passwords Forms ----------------------------

/**
 * This method validates that the add client form input fields for client id, password,
 * and confirm password are valid.
 *
 * @return boolean Valid add client account form.
 */
function validCreateClientForm()
{
    global $f3;
    $isValid = true;

    if (!validNewClientId($f3->get('clientId'))) {
        $isValid = false;
        $f3->set("errors['newClientId']", 'Client Id must be at least 6 numeric digits.');
    }

    if (!validPassword($f3->get('password'))) {
        $isValid = false;
        $f3->set("errors['password']", 'Password must be 8-20 characters in length, 
        have at least one uppercase letter, one lowercase letter, and one special character. 
        Must not contain spaces.');
    }

    if ($f3->get('password') != $f3->get('password2')) {
        $isValid = false;
        $f3->set("errors['password2']", 'Confirm password must match the password entered.');
    }
    return $isValid;
}

/**
 * This method validates that the add clinician form input fields for clinician id, username,
 * password, and confirm password are valid.
 *
 * @return boolean Valid add clinician account form.
 */
function validCreateClinicianForm()
{
    global $f3;
    $isValid = true;

    if (!validClinicianUsername($f3->get('clnUsername'))) {
        $isValid = false;
        $f3->set("errors['clnUsername']", 'Username must be longer than 3 characters and cannot be blank.');
    }

    if (!validPassword($f3->get('clnPassword'))) {
        $isValid = false;
        $f3->set("errors['clnPassword']", 'Password must be 8-20 characters in length, 
        have at least one uppercase letter, one lowercase letter, and one special character. 
        Must not contain spaces.');
    }

    if ($f3->get('clnPassword') != $f3->get('clnPassword2')) {
        $isValid = false;
        $f3->set("errors['clnPassword2']", 'Confirm password must match the password entered.');
    }

    return $isValid;
}

/**
 * This method validates the change password form input fields for id, password, password confirm
 * are valid.
 *
 * @return boolean Valid change password form.
 */
function validChangeClientPasswordForm()
{
    global $f3;
    $isValid = true;

    if (!validNewClientId($f3->get('chgClientPwId'))) {
        $isValid = false;
        $f3->set("errors['chgClientPwId']", 'Id must be at least 6 numeric digits.');
    }

    if (!validPassword($f3->get('chgPwNewPw'))) {
        $isValid = false;
        $f3->set("errors['chgPwNewPw']", 'Password must be 8-20 characters in length, 
        have at least one uppercase letter, one lowercase letter, and one special character. 
        Must not contain spaces.');
    }

    if ($f3->get('chgPwNewPw') != $f3->get('chgPwNewPw2')) {
        $isValid = false;
        $f3->set("errors['chgPwNewPw2']", 'Confirm password must match the password entered.');
    }
    return $isValid;
}

//TODO php doc
function validChangeClnPasswordForm()
{
    global $f3;
    $isValid = true;

    if (!validClinicianUsername($f3->get('chgPwClnUsername'))) {
        $isValid = false;
        $f3->set("errors['chgPwClnUsername']", 'Username must be longer than 3 characters and cannot be blank.');
    }

    if (!validPassword($f3->get('chgPwClnNewPw'))) {
        $isValid = false;
        $f3->set("errors['chgPwClnNewPw']", 'Password must be 8-20 characters in length, 
        have at least one uppercase letter, one lowercase letter, and one special character. 
        Must not contain spaces.');
    }

    if ($f3->get('chgPwClnNewPw') != $f3->get('chgPwClnNewPw2')) {
        $isValid = false;
        $f3->set("errors['chgPwClnNewPw2']", 'Confirm password must match the password entered.');
    }
    return $isValid;
}

/**
 * This method validates a Client Id is all numeric, not empty, and at least 6 digits.
 *
 * @param $clientId User provided Client Id.
 * @return boolean Valid Client Id.
 */
function validNewClientId($clientId)
{
    return is_numeric($clientId) && !empty($clientId) && strlen($clientId) == 6;
}

/**
 * This method validates a user provided password matches a valid regex pattern.
 *
 * @param $password user provided password to validate.
 * @return boolean Password provided is valid.
 */
function validPassword($password)
{
    /* Regex: Password must be 8-20 chars long, contain no spaces, and have one uppercase,
       one lowercase, and one special character. No spaces allowed. */
    $regexPattern = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d.*)(?=.*\W.*)[a-zA-Z0-9\S]{8,20}$^";
    return preg_match($regexPattern, $password)  && !empty($password);
}

/**
 * This method validates a clinician username is not blank and is at least 4 characters.
 *
 * @param $clnUsername User provided clinician username.
 * @return boolean Valid clinician username.
 */
function validClinicianUsername($clnUsername) {
    return strlen($clnUsername) > 3 && !empty($clnUsername);
}
