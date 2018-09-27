<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 13/08/18
 * Time: 05:02
 */

namespace App\Models;

use \TCPDF;

/**
 * @Entity
 * @Table(name="exitbill")
 */
class ExitBill extends TCPDF
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * One exitbill has One exit.
     * @OneToOne(targetEntity="FinancialExit", inversedBy="exitbill")
     * @JoinColumn(name="exit_id", referencedColumnName="id")
     */
    protected $exit;

    /** @Column(type="integer") */
    protected $user_id;

    protected $user;

    /** @Column(type="datetime",options={"default"="CURRENT_TIMESTAMP"}) */
    protected $creation_date;

    public function initialise(){
        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Darmotics');
        $this->SetTitle('Exit Bill');
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetMargins(0, 0, 0);
    }

    public function init(){
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Darmotics');
        $this->SetTitle('Exit Bill');
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetMargins(0, 0, 0);
    }

    public function writeData(){
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
                font-size: 10px;
                color: #0894e6;
            }
            .recapbloc p label{
                display: inline-block;
                margin-left: 50px;
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
                padding-bottom: 12px;
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
            
            <tr>
            <td colspan="3">Ref: '.$this->getId().'</td>
            <td></td><td></td><td></td><td></td>
            </tr>
        </table>
        
        
    </div>
    
    <h3 class="billname">RECAPITULATIF DE SORTIE BUDGETAIRE</h3>
    
    <div class="recapbloc">
    <p class="titlebloc">Effectué Par</p>
    <table cellpadding="5px">
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Nom:</label></td>
    <td colspan="3"><span class="info">'.$this->user->getLastName().' '.$this->user->getFirstName().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Téléphone:</label></td>
    <td colspan="3"><span class="info">'.$this->user->getPhone().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Email:</label></td>
    <td colspan="3"><span class="info">'.$this->user->getEmail().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Adresse:</label></td>
    <td colspan="3"><span class="info">'.$this->user->getAddress().'</span></td>
    <td></td>
    <td></td>
    </tr>
</table cellpadding="5px">

    </div>

    <div class="recapbloc">
        <p class="titlebloc">Sortie</p>
        <table cellpadding="5px">
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Raison</label></td>
    <td colspan="3"><span class="info">'.$this->exit->getReason().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Montant:</label></td>
    <td  colspan="3"><span class="info">'.$this->exit->getAmount().'</span></td>
    <td></td>
    <td></td>
    </tr>
    
    <tr>
    <td></td>
    <td></td>
    <td><label>Projet:</label></td>
    <td  colspan="3"><span class="info">'.$this->exit->getProject().'</span></td>
    <td></td>
    <td></td>
    </tr>

    <tr>
    <td></td>
    <td></td>
    <td><label>Pièces Jointes:</label></td>
    <td  colspan="3"><span class="info">'.$this->links.'</span></td>
    <td></td>
    <td></td>
    </tr>

    </table>
    </div>


    <div class="footer">
        <p class="footertext">Copyright©Darmotics '.$this->getCreationDate()->format('Y-m-d H:i:s').'</p>
    </div>';

        $this->AddPage();
        $this->writeHTML($html, true, false, true, false, '');
    }

    public function printBill(){
        $this->Output($this->getId().'.pdf', 'I');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getExit()
    {
        return $this->exit;
    }

    /**
     * @param mixed $exit
     */
    public function setExit($exit)
    {
        $this->exit = $exit;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        $this->setUserId($this->user->getId());
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * @param mixed $creation_date
     */
    public function setCreationDate($creation_date)
    {
        $this->creation_date = $creation_date;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    protected $links;

    public function setLinks($value){
        $this->links=$value;
    }

    public function getLinks(){
        return $this->links;
    }
}