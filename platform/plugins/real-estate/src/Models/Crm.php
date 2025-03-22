<?php

namespace Srapid\RealEstate\Models;

use Srapid\Base\Traits\EnumCastable;
use Srapid\Base\Enums\BaseStatusEnum;
use Srapid\Base\Models\BaseModel;

class Crm extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_crm';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'content',
        'property_value',
        'min_price',
        'max_price',
        'status',
        'category',
        'lead_color',
        'account_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
        'property_value' => 'float',
        'min_price' => 'float',
        'max_price' => 'float',
    ];

    /**
     * Set property_value attribute
     * 
     * @param string|float $value
     * @return void
     */
    public function setPropertyValueAttribute($value)
    {
        if (is_string($value) && !is_numeric($value)) {
            // Remove R$, pontos e espaços, e substitui vírgula por ponto
            $value = str_replace(['R$', '.', ' '], '', $value);
            $value = str_replace(',', '.', $value);
        }
        
        $this->attributes['property_value'] = (float) $value;
    }

    /**
     * Get property_value attribute for form display
     * 
     * @return string
     */
    public function getFormattedPropertyValueAttribute()
    {
        if (!$this->property_value) {
            return null;
        }
        
        return 'R$ ' . number_format($this->property_value, 2, ',', '.');
    }
    
    /**
     * Set min_price attribute
     * 
     * @param string|float $value
     * @return void
     */
    public function setMinPriceAttribute($value)
    {
        if (is_string($value) && !is_numeric($value)) {
            // Remove R$, pontos e espaços, e substitui vírgula por ponto
            $value = str_replace(['R$', '.', ' '], '', $value);
            $value = str_replace(',', '.', $value);
        }
        
        $this->attributes['min_price'] = (float) $value;
    }
    
    /**
     * Set max_price attribute
     * 
     * @param string|float $value
     * @return void
     */
    public function setMaxPriceAttribute($value)
    {
        if (is_string($value) && !is_numeric($value)) {
            // Remove R$, pontos e espaços, e substitui vírgula por ponto
            $value = str_replace(['R$', '.', ' '], '', $value);
            $value = str_replace(',', '.', $value);
        }
        
        $this->attributes['max_price'] = (float) $value;
    }
    
    /**
     * Get formatted min price attribute
     * 
     * @return string|null
     */
    public function getFormattedMinPriceAttribute()
    {
        if (!$this->min_price) {
            return null;
        }
        
        return 'R$ ' . number_format($this->min_price, 2, ',', '.');
    }
    
    /**
     * Get formatted max price attribute
     * 
     * @return string|null
     */
    public function getFormattedMaxPriceAttribute()
    {
        if (!$this->max_price) {
            return null;
        }
        
        return 'R$ ' . number_format($this->max_price, 2, ',', '.');
    }
    
    /**
     * Get price range as string
     * 
     * @return string|null
     */
    public function getPriceRangeAttribute()
    {
        if (!$this->min_price && !$this->max_price) {
            return null;
        }
        
        if ($this->min_price && !$this->max_price) {
            return 'A partir de ' . $this->formatted_min_price;
        }
        
        if (!$this->min_price && $this->max_price) {
            return 'Até ' . $this->formatted_max_price;
        }
        
        return $this->formatted_min_price . ' - ' . $this->formatted_max_price;
    }
    
    /**
     * Relacionamento com a conta de corretor
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}