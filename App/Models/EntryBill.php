<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 12/08/18
 * Time: 17:13
 */

namespace App\Models;


use \TCPDF;
class EntryBill extends TCPDF
{
    public function initialise(){
        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Darmotics');
        $this->SetTitle('Entry Bill');
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetMargins(0, 0, 0);
    }

    public function writeData($entry,$giver,$user){
        $html='<style>
            .brand{
                font-weight: bold;
                font-size: 15px;
            }
            td{padding: 5px;}
            .headerbloc{
                display: inline-block;
                width:200px;
            }
            
            .recapbloc{
                border-top: 1px solid  #0894e6;
                padding-left: 150px;
            }
            
            .recapbloc .titlebloc{
                text-align: center;
                font-style: italic;
                font-weight: bolder;
                font-size: 15px;
                color: #0894e6;
            }
            .recapbloc p label{
                display: inline-block;
                margin-left: 50px;
                padding: 5px;
                width: 150px;
            }
            .info{
                display: inline-block;
                font-weight: bold;
                margin-left: 25px;
            }
            
            .billname{
                margin-top: 100px;
                color: #0894e6;
                padding-bottom: 15px;
                text-align: center;
            }
            .header{
                position: absolute;
                top:0;
                right: 0;
                left: 0;
                padding: 5px;
                background-color: #efefef;
            }
            .footer{
                position: absolute;
                bottom:0;
                right: 0;
                left: 0;0
                padding: 0px;
                background-color: #efefef;
                text-align: center;
            }
            
    </style>
    <div class="header">
        <table cellpadding="5px">
            <tr>
            <td>
                
                LOGO
                
            </td>
            <td colspan="2">
                
                <span class="brand">Darmotics</span><br/>
                <span>darmotics@gmail.com</span><br/>
                <span>+212 xxxxxxxxx</span>
                
            </td>
            <td></td>
            <td></td>
            <td></td>
            </tr>
        </table>
        
        
    </div>
    
    <h3 class="billname">RECAPITULATIF ENTREE CAPITAL</h3>
    
    <div class="recapbloc">
    <p class="titlebloc">Actionnaire/Donnateur</p>
    <table cellpadding="5px">
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Nom:</label></td>
    <td colspan="2"><span class="info">'.$giver->getLastName().' '.$giver->getFirstName().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Téléphone:</label></td>
    <td colspan="2"><span class="info">'.$giver->getPhone().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Email:</label></td>
    <td colspan="2"><span class="info">'.$giver->getEmail().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Adresse:</label></td>
    <td colspan="2"><span class="info">'.$giver->getAddress().'</span></td>
    <td></td>
    <td></td>
    </tr>
</table cellpadding="5px">

    </div>

    <div class="recapbloc">
        <p class="titlebloc">Apport</p>
        <table cellpadding="5px">
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Type:</label></td>
    <td colspan="2"><span class="info">'.$entry->getType().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Montant:</label></td>
    <td colspan="2"><span class="info">'.$entry->getAmount().'</span></td>
    <td></td>
    <td></td>
    </tr>
    </table>
    </div>

    <div class="recapbloc">
        <p class="titlebloc">Encaisseur</p>
        <table cellpadding="5px">
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Nom:</label></td>
    <td colspan="2"><span class="info">'.$user->getLastName().' '.$user->getFirstName().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Téléphone:</label></td>
    <td colspan="2"><span class="info">'.$user->getPhone().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Email:</label></td>
    <td colspan="2"><span class="info">'.$user->getEmail().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Adresse:</label></td>
    <td colspan="2"><span class="info">'.$user->getAddress().'</span></td>
    <td></td>
    <td></td>
    </tr>
</table>
    </div>


    <div class="footer">
        <p class="footertext">©Darmotics '.date('Y-M-D H:m:s').'</p>
    </div>';
        $this->AddPage();
        $this->writeHTML($html, true, false, true, false, '');
    }

    public function printBill(){
        $this->Output('facture_apport.pdf', 'I');
    }
}