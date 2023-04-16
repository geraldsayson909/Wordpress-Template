<?php
//Proweaver Email Sender API V1.1.2
//Do not Edit
//Required Paramter
//* = Required

//body*
//This field is required both direct and OFDB email notification, variable $body, datatype string

//from*
//datatype string

//from name
//datatype string

//to*
//datatype array

//subject*
//variable $subject, datatype string

//sample parameter
// $parameter = array(   
//     'body' => $body,
//     'from' => $from_email,
//     'to' => $to_email,
//     'subject' => $subject
// );

$file_attachment = array();

function send_email($parameter = array(), $success_message = '', $error_message = '')
{
    if (function_exists('curl_version')) {
        if (file_exists('../../../../wp-config.php')) {
            require '../../../../wp-config.php';
        }

        if (file_exists('config')) {
            require 'config.php';
        }

        $parameter['comp_name'] = COMP_NAME;

        $parameter['mail_type'] = (empty($parameter['mail_type'])) ? MAIL_TYPE : $parameter['mail_type'];

        $parameter['dev_mode'] = DEV_MODE;

        if ($parameter['mail_type'] == 2) {
            $parameter['link_url'] = get_home_url() . '/onlineforms/' . $_SESSION['token'];
        }

        if ((!empty($parameter['attachment']) && ($parameter['mail_type'] == 1)) || (!empty($parameter['comb']))) {
            $parameter['attachments'] = implode(',', file_uploader($parameter['attachment'], $parameter['comb']));
        }
        $parameter['to'] = (is_array($parameter['to'])) ? implode(',', $parameter['to']) : $parameter['to'];

        $ch = curl_init();

        $mode = ($parameter['debug'] == true) ? 'test_send_email' : 'send_email';

        $url = "http://proweaveremail.com/email/" . $mode;

        curl_setopt($ch, CURLOPT_URL, $url);
        // Set the result output to be a string.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);

        $response = json_decode(curl_exec($ch), true);

        $info = curl_getinfo($ch);

        if (!empty($parameter['debug']) && $parameter['debug'] == true) {
            echo "<pre>";
            print_r($response);
            exit;
        }

        if ($response['response'] == 'sent') {
            if (!empty($success_message)) {
                return $success_message;
            } else {
                return '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for sending us a message!</span><br/><span>We will be in touch with you soon.</span><p class="close">x</p></div></div>';;
            }
            if (!empty($parameter['confirmation_email'])) {
                confirmation_email($parameter['confirmation_email'], $parameter['from']);
            }
        } else {
            if (!empty($error_message)) {
                return $error_message;
            } else {
                return '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
            }
            unset($_POST);
        }
        curl_close($ch);
    } else {
        return '<div id="error-msg"><div class="message"><span>Failed to send email. Please enable you Curl.</span><br/><p class="error-close">x</p></div></div>';
    }
}

function confirmation_email($message, $from = '')
{
    $confirmation_parameter = array(
        'email_address' => $_POST['Email_Address'],
        'from' => $from,
        'message' => $message,
    );

    $url = "http://proweaveremail.com/email/confirmation_email";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    // Set the result output to be a string.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $confirmation_parameter);

    $response = json_decode(curl_exec($ch), true);
}


function file_uploader($files, $comb = 0)
{

    for ($i = 0; $i < count($files); $i++) {
        $ch = curl_init();
        if ($comb == 0) {
            $post_fields = array(
                'my_file' => new CURLFile($files['attachment']['tmp_name'][$i], get_mime_type($files['attachment']['type'][$i]), basename($files['attachment']['name'][$i]))
            );
        } else {
            $post_fields = array(
                'my_file' => new CURLFile(ABSPATH . 'onlineforms/attachments/' . $files[$i], get_mime_type($files[$i]), basename($files[$i]))
            );
        }

        $url = "http://proweaveremail.com/email/file_uploader";
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set the result output to be a string.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

        $response = json_decode(curl_exec($ch), true);
        $file_attachment[] = $response;
        curl_close($ch);
    }
    return $file_attachment;
}

function get_mime_type($filename)
{
    $idx = explode('.', $filename);
    $count_explode = count($idx);
    $idx = strtolower($idx[$count_explode - 1]);

    $mimet = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',


        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    if (isset($mimet[$idx])) {
        return $mimet[$idx];
    } else {
        return 'application/octet-stream';
    }
}


//Logs

// V1.0.1
// Updated API for single file edit compatibility

// V1.0.2
// Fixed link for notification

// V1.1.0
// Fixed email attachment 

// V1.1.1
// Fixed email notification for multiplication receiver

// V1.1.2
// Added auto reply notification