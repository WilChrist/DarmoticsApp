<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 26/07/18
 * Time: 15:04
 */

namespace App\Models;

/**
 * @Entity
 * @Table(name="message")
 */
class Message
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id = null;

    /** @Column(length=255) */
    public $content;
}