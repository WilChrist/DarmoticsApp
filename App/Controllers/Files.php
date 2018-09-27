<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 10/08/18
 * Time: 11:02
 */

namespace App\Controllers;

use Core\Controller;
use App\Config;
use App\Models\File;

class Files extends Controller
{
    public function downloadAction($other, $id){

        if($id == null || $this->db->getRepository('App\Models\File')->find($id)==null){
            echo "Ce fichier n\existe pas ou plus";
        }
        else{
            $ff = $this->db->getRepository('App\Models\File')->find($id);
            $name = $ff->getName();
            $originName = $ff->getOriginName();

            header('Content-Disposition: attachment; filename="' . $originName. '"');
            header("Content-Length: " . filesize(Config::FileRacine.$name));
            header("Content-Type: application/octet-stream;");
            readfile(Config::FileRacine.$name);
        }

    }

}