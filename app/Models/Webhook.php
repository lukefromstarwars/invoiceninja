<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2023. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends BaseModel
{
    use SoftDeletes;
    use Filterable;

    const EVENT_CREATE_CLIENT = 1; //tested

    const EVENT_CREATE_INVOICE = 2; //tested

    const EVENT_CREATE_QUOTE = 3; //tested

    const EVENT_CREATE_PAYMENT = 4; //tested

    const EVENT_CREATE_VENDOR = 5; //tested

    const EVENT_UPDATE_QUOTE = 6; //tested

    const EVENT_DELETE_QUOTE = 7; //tested

    const EVENT_UPDATE_INVOICE = 8; //tested

    const EVENT_DELETE_INVOICE = 9; //tested

    const EVENT_UPDATE_CLIENT = 10; //tested

    const EVENT_DELETE_CLIENT = 11; //tested

    const EVENT_DELETE_PAYMENT = 12; //tested

    const EVENT_UPDATE_VENDOR = 13; //tested

    const EVENT_DELETE_VENDOR = 14; //tested

    const EVENT_CREATE_EXPENSE = 15; //tested

    const EVENT_UPDATE_EXPENSE = 16; //tested

    const EVENT_DELETE_EXPENSE = 17; //tested

    const EVENT_CREATE_TASK = 18; //tested

    const EVENT_UPDATE_TASK = 19; //tested

    const EVENT_DELETE_TASK = 20; //tested

    const EVENT_APPROVE_QUOTE = 21; //tested

    const EVENT_LATE_INVOICE = 22;

    const EVENT_EXPIRED_QUOTE = 23;

    const EVENT_REMIND_INVOICE = 24;

    const EVENT_PROJECT_CREATE = 25;//

    const EVENT_PROJECT_UPDATE = 26;

    const EVENT_CREATE_CREDIT = 27;

    const EVENT_UPDATE_CREDIT = 28;

    const EVENT_DELETE_CREDIT = 29;

    const EVENT_PROJECT_DELETE = 30;

    const EVENT_UPDATE_PAYMENT = 31;

    const EVENT_ARCHIVE_PAYMENT = 32;

    const EVENT_ARCHIVE_INVOICE = 33; //tested

    const EVENT_ARCHIVE_QUOTE = 34;

    const EVENT_ARCHIVE_CREDIT = 35;

    const EVENT_ARCHIVE_TASK = 36;

    const EVENT_ARCHIVE_CLIENT = 37;

    const EVENT_ARCHIVE_PROJECT = 38;

    const EVENT_ARCHIVE_EXPENSE = 39;

    const EVENT_RESTORE_PAYMENT = 40;

    const EVENT_RESTORE_INVOICE = 41;

    const EVENT_RESTORE_QUOTE = 42;

    const EVENT_RESTORE_CREDIT = 43;

    const EVENT_RESTORE_TASK = 44;

    const EVENT_RESTORE_CLIENT = 45;

    const EVENT_RESTORE_PROJECT = 46;

    const EVENT_RESTORE_EXPENSE = 47;

    const EVENT_ARCHIVE_VENDOR = 48;

    const EVENT_RESTORE_VENDOR = 49;

    const EVENT_CREATE_PRODUCT = 50;

    const EVENT_UPDATE_PRODUCT = 51;

    const EVENT_DELETE_PRODUCT = 52;

    const EVENT_RESTORE_PRODUCT = 53;

    const EVENT_ARCHIVE_PRODUCT = 54;

    const EVENT_CREATE_PURCHASE_ORDER = 55;

    const EVENT_UPDATE_PURCHASE_ORDER = 56;

    const EVENT_DELETE_PURCHASE_ORDER = 57;

    const EVENT_RESTORE_PURCHASE_ORDER = 58;

    const EVENT_ARCHIVE_PURCHASE_ORDER = 59;

    public static $valid_events = [
        self::EVENT_CREATE_PURCHASE_ORDER,
        self::EVENT_UPDATE_PURCHASE_ORDER,
        self::EVENT_DELETE_PURCHASE_ORDER,
        self::EVENT_RESTORE_PURCHASE_ORDER,
        self::EVENT_ARCHIVE_PURCHASE_ORDER,
        self::EVENT_UPDATE_PRODUCT,
        self::EVENT_DELETE_PRODUCT,
        self::EVENT_RESTORE_PRODUCT,
        self::EVENT_ARCHIVE_PRODUCT,
        self::EVENT_CREATE_CLIENT,
        self::EVENT_CREATE_INVOICE,
        self::EVENT_CREATE_QUOTE,
        self::EVENT_CREATE_PAYMENT,
        self::EVENT_CREATE_VENDOR,
        self::EVENT_UPDATE_QUOTE,
        self::EVENT_DELETE_QUOTE,
        self::EVENT_UPDATE_INVOICE,
        self::EVENT_DELETE_INVOICE,
        self::EVENT_UPDATE_CLIENT,
        self::EVENT_DELETE_CLIENT,
        self::EVENT_DELETE_PAYMENT,
        self::EVENT_UPDATE_VENDOR,
        self::EVENT_DELETE_VENDOR,
        self::EVENT_CREATE_EXPENSE,
        self::EVENT_UPDATE_EXPENSE,
        self::EVENT_DELETE_EXPENSE,
        self::EVENT_CREATE_TASK,
        self::EVENT_UPDATE_TASK,
        self::EVENT_DELETE_TASK,
        self::EVENT_APPROVE_QUOTE,
        self::EVENT_LATE_INVOICE,
        self::EVENT_EXPIRED_QUOTE,
        self::EVENT_REMIND_INVOICE,
        self::EVENT_PROJECT_CREATE,
        self::EVENT_PROJECT_UPDATE,
        self::EVENT_CREATE_CREDIT,
        self::EVENT_UPDATE_CREDIT,
        self::EVENT_DELETE_CREDIT,
        self::EVENT_PROJECT_DELETE,
        self::EVENT_UPDATE_PAYMENT,
        self::EVENT_ARCHIVE_EXPENSE,
        self::EVENT_ARCHIVE_PROJECT,
        self::EVENT_ARCHIVE_CLIENT,
        self::EVENT_ARCHIVE_TASK,
        self::EVENT_ARCHIVE_CREDIT,
        self::EVENT_ARCHIVE_QUOTE,
        self::EVENT_ARCHIVE_INVOICE,
        self::EVENT_ARCHIVE_PAYMENT,
        self::EVENT_ARCHIVE_VENDOR,
        self::EVENT_RESTORE_EXPENSE,
        self::EVENT_RESTORE_PROJECT,
        self::EVENT_RESTORE_CLIENT,
        self::EVENT_RESTORE_TASK,
        self::EVENT_RESTORE_CREDIT,
        self::EVENT_RESTORE_QUOTE,
        self::EVENT_RESTORE_INVOICE,
        self::EVENT_RESTORE_PAYMENT,
        self::EVENT_RESTORE_VENDOR

    ];

    protected $fillable = [
        'target_url',
        'format',
        'event_id',
        'rest_method',
        'headers',
    ];

    protected $casts = [
        'headers' => 'array',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
