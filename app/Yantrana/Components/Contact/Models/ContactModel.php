<?php
/**
* Contact.php - Model file
*
* This file is part of the Contact component.
*-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Contact\Models;

use App\Yantrana\Base\BaseModel;
use App\Yantrana\Components\Auth\Models\AuthModel;
use App\Yantrana\Components\WhatsAppService\Models\WhatsAppMessageLogModel;
use App\Yantrana\Support\Country\Models\Country;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'contacts';

    /**
     * Datatable Result counts also its max result per request.
     *
     * @var string
     *----------------------------------------------------------------------- */
    protected $maxDataTableResultCount = 500;

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
        'messaged_at' => 'datetime',
        'unread_messages_count' => 'integer',
        'disable_ai_bot' => 'integer',
        'wa_id' => 'string',
        '__data' => 'array',
    ];

        /**
     * Let the system knows Text columns treated as JSON
     *
     * @var array
     *----------------------------------------------------------------------- */
    protected $jsonColumns = [
        '__data' => [
            'contact_notes' => 'string',
        ]
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [
    ];

    protected $appends = [
        'full_name',
        'name_initials',
    ];

    protected $hidden = [
    ];

    /**
     * Get the Groups of contact
     *
     * @return HasManyThrough
     */
    public function groups(): HasManyThrough
    {
        return $this->hasManyThrough(ContactGroupModel::class, GroupContactModel::class, 'contacts__id', '_id', '_id', 'contact_groups__id');
    }
    /**
     * Get the labels of contact
     *
     * @return HasManyThrough
     */
    public function labels(): HasManyThrough
    {
        return $this->hasManyThrough(LabelModel::class, ContactLabelModel::class, 'contacts__id', '_id', '_id', 'labels__id');
    }

    /**
     * Get the Value with related field
     *
     * @return hasMany
     */
    public function valueWithField(): hasMany
    {
        return $this->hasMany(ContactCustomFieldValueModel::class, 'contacts__id', '_id', '_id', 'contact_custom_fields__id')->with('customField');
    }

    /**
     * Get Custom Filed Values
     *
     * @return HasMany
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(ContactCustomFieldValueModel::class, 'contacts__id', '_id', '_id', 'contact_custom_fields__id');
    }

    /**
     * prepare and get contact whatsapp number
     */
    protected function whatsappNumber(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['wa_id'],
        );
    }

    /**
     * gravatar
     */
    protected function gravatar(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($attributes['email'] ?? ''))),
        );
    }
    /**
     * Get the initials of the full name
     */
    protected function nameInitials(): Attribute
    {
        return Attribute::make(function(mixed $value, array $attributes) {
            $nameParts = explode(' ', trim(($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? '')));
            return mb_substr(array_shift($nameParts) ?? '',0,1) . mb_substr(array_pop($nameParts) ?? '', 0, 1);
        });
    }

    /**
     * prepare and get contact full name
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? '')),
        );
    }

    /**
     * Get the country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'countries__id', '_id');
    }

    /**
     * Get the country
     */
    public function lastMessage(): HasOne
    {
        return $this->hasOne(WhatsAppMessageLogModel::class, 'contacts__id', '_id')->where([
            // 'wab_phone_number_id' => getVendorSettings('current_phone_number_id'),
        ])->latest('messaged_at'); // OfMany('messaged_at')
    }

    /**
     * Get last incoming message
     */
    public function lastIncomingMessage(): HasOne
    {
        return $this->hasOne(WhatsAppMessageLogModel::class, 'contacts__id', '_id')->where([
            // 'wab_phone_number_id' => getVendorSettings('current_phone_number_id'),
            'is_incoming_message' => 1,
        ])->latest('messaged_at');
    }

    /**
     * Get last unread message
     */
    public function lastUnreadMessage(): HasOne
    {
        return $this->hasOne(WhatsAppMessageLogModel::class, 'contacts__id', '_id')->where([
            'status' => 'received',
            // 'wab_phone_number_id' => getVendorSettings('current_phone_number_id'),
            'is_incoming_message' => 1,
        ])->latestOfMany('messaged_at');
    }

    /**
     * Get last unread message
     */
    public function unreadMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessageLogModel::class, 'contacts__id', '_id')->where([
            'status' => 'received',
            'is_incoming_message' => 1,
            // 'wab_phone_number_id' => getVendorSettings('current_phone_number_id'),
        ]);
    }

    /**
     * Get Assigned User
     */
    public function assignedUser(): HasOne
    {
        return $this->hasOne(AuthModel::class, '_id', 'assigned_users__id');
    }
}
