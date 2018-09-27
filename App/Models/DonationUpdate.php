<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 17/08/18
 * Time: 00:31
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="donationupdate")
 */
class DonationUpdate extends Account
{
    public function __construct()
    {
        parent::__construct();
    }
}