<?php
// functions.php

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Validate image file (returns error string or empty)
function validateImage($file) {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ''; // Image optional
    }
    $allowed = ['image/jpeg', 'image/png'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Error uploading file.';
    }
    if (!in_array($file['type'], $allowed)) {
        return 'Only JPG and PNG images are allowed.';
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return 'Image size must be less than 2MB.';
    }
    return '';
}

// Upload image and return filename or false on error
function uploadImage($file) {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $destination = __DIR__ . '/uploads/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    return false;
}

// Shorten text (for description preview)
function shorten($text, $chars = 50) {
    if (strlen($text) <= $chars) {
        return $text;
    }
    return substr($text, 0, $chars) . '...';
}
