<?php

require '../assets/class/database.class.php';
require '../assets/class/function.class.php';

if ($_POST) {
    $post = $_POST;

    if (
        $post['full_name'] && $post['email_id'] && $post['objective'] &&
        $post['mobile_no'] && $post['dob'] && $post['nationality'] &&
        $post['marital_status'] && $post['hobbies'] && $post['languages'] &&
        $post['address'] && isset($_FILES['profile_image'])
    ) {
        $columns = '';
        $values = '';

        // Sanitize and prepare data for insertion
        foreach ($post as $index => $value) {
            $value = $db->real_escape_string($value);
            $columns .= $index . ',';
            $values .= "'$value',";
        }

        $authid = $fn->Auth()['id'];
        $columns .= 'slug,updated_at,user_id,profile_image';
        $values .= "'" . $fn->randomstring() . "'," . time() . "," . $authid . ",";

        // Process the uploaded image
        $image = $_FILES['profile_image'];
        if ($image['error'] === 0) {
            $imageData = file_get_contents($image['tmp_name']); // Get binary data
            $imageData = $db->real_escape_string($imageData); // Escape the binary data
            $values .= "'$imageData'";
        } else {
            $fn->setError('Error uploading image!');
            $fn->redirect('../createresume.php');
        }

        try {
            $query = "INSERT INTO resumes ($columns) VALUES ($values)";
            $db->query($query);

            $fn->setAlert('Resume added with profile image!');
            $fn->redirect('../myresumes.php');
        } catch (Exception $error) {
            $fn->setError($error->getMessage());
            $fn->redirect('../createresume.php');
        }
    } else {
        $fn->setError('Please fill the form and upload an image!');
        $fn->redirect('../createresume.php');
    }
} else {
    $fn->redirect('../createresume.php');
}
