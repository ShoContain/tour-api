<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Travel extends Model
{
    use HasFactory, Sluggable, HasUlids;

    // specifying cuz travels is not plural of travel
    protected $table = 'travels';

    protected $fillable = [
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days'
    ];

    protected $casts = [
        'number_of_days' => 'integer',
        'number_of_nights' => 'integer',
    ];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['number_of_days'] - 1,
        );
    }
}
