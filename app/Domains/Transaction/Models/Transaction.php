<?php

namespace App\Domains\Transaction\Models;

use App\Domains\Transaction\Models\Traits\Method\TransactionMethod;
use App\Domains\Transaction\Models\Traits\Relationship\TransactionRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction.
 */
class Transaction extends Model
{
    use HasFactory,
        SoftDeletes,
        TransactionRelationship,
        TransactionMethod;

    /** @var string */
    protected $table = 'transactions';

    /** @var array */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'job_id',
        'escrow_transaction_id',
        'item_escrow_id',
        'status',
        'reference',
        'amount_sender',
        'amount_receiver',
        'currency',
        'is_visible_recipient'
    ];

    public const CANCELED = 'cancel';
    public const CREATE = 'create';
    public const PAYMENT_APPROVED = 'payment_approved';
    public const PAYMENT_REJECTED = 'payment_rejected';
    public const PAYMENT_SENT = 'payment_sent';
    public const PAYMENT_RECEIVED = 'payment_received';
    public const PAYMENT_REFUNDED = 'payment_refunded';
    public const PAYMENT_DISBURSED = 'payment_disbursed';
    public const COMPLETE = 'complete';
    public const ACCEPT = 'accept';
    public const SHIP = 'ship';
    public const RECEIVER = 'receive';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_visible_recipient' => 'boolean',
    ];
}
