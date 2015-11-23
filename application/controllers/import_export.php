<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once('base.php');

class Import_Export extends Base_Controller {

    function importAction() {
        $response = array();

        $user = $this->auth->getUser();

        $error = "";
        $msg = "";
        $fileElementName = 'import';
        if (!empty($_FILES[$fileElementName]['error'])) {
            switch ($_FILES[$fileElementName]['error']) {

                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;

                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }
        } elseif (empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none') {
            $error = 'No file was uploaded..';
        } else {
            $msg .= " File Name: " . $_FILES[$fileElementName]['name'] . ", ";
            $msg .= " File Size: " . @filesize($_FILES[$fileElementName]['tmp_name']);

            $info = pathinfo($_FILES[$fileElementName]['name']);
            $extension = $info['extension'];

            if ($extension == 'xml') {
                move_uploaded_file($_FILES[$fileElementName]["tmp_name"], 'upload/' . $user->id . '.' . $extension);

                $this->load->model('services/feeds');
                $this->feeds->importOPML($user->id, 'upload/' . $user->id . '.' . $extension);
            } else {
                $error = 'You need to upload a XML file, not a ' . $extension . '.';
            }

            //for security reason, we force to remove all uploaded file
            @unlink($_FILES[$fileElementName]);
        }
        $response['error'] = $error;
        $response['msg'] = $msg;
        
        $this->auth->feedsNeedUpdate();

        /*
        if ($response['error'] != '') {
            echo $error;
        } else {
            echo 'SUCCESS';
        }
        exit; */
        if ($response['error'] != '') {
            redirect('/error_importing', 'refresh');
        }else{
            redirect('/', 'refresh');
        }
        exit;
    }
    
    function exportAction(){
        $user = $this->auth->getUser();
        
        $this->load->model('subscription');
        $this->load->helper('download');
        
        $data = array();
        $data['feeds'] = $this->subscription->getExport($user->id);
        $data['username'] = $user->username;
        
        $xml = $this->load->view('xml/export.php',$data,true);
        
        force_download('readbo-subscriptions.xml', $xml);
    }

}