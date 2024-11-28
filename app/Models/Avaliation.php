<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Avaliation extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'date',
        'weight_kg',
        'height_cm',
        'body_fat_perc',
        'skeletal_muscle_mass_kg',
        'muscle_rate_perc',
        'subcutaneous_fat_perc',
        'visceral_fat_perc',
        'body_water_perc',
        'skeletal_muscle_perc',
        'muscle_mass_kg',
        'bone_mass_kg',
        'protein_perc',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    protected $attributes = [];

    protected $appends = [
        'codedId',
    ];

    // relations
    public function client()
    {
        return $this->hasOne(
            Client::class, 'id',
            'client_id'
        );
    }
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, __('messages.models.Avaliation.name'), 'id', 'ID');
        $validation->addIdField(Client::class, __('messages.models.Client.name'), 'id', 'ID');
        $validation->addField('date', ['required', 'date', 'date_format:Y-m-d'], __('messages.models.Avaliation.fields.date'));
        $validation->addField('weight_kg', ['required', 'numeric', 'min:20', 'max:400'], __('messages.models.Client.fields.weight'));
        $validation->addField('height_cm', ['required', 'integer', 'min:40', 'max:250'], __('messages.models.Client.fields.height'));
        $validation->addField('body_fat_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.body_fat_perc'));
        $validation->addField('skeletal_muscle_mass_kg', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.skeletal_muscle_mass_kg'));
        $validation->addField('muscle_rate_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.muscle_rate_perc'));
        $validation->addField('subcutaneous_fat_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.subcutaneous_fat_perc'));
        $validation->addField('visceral_fat_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.visceral_fat_perc'));
        $validation->addField('body_water_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.body_water_perc'));
        $validation->addField('skeletal_muscle_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.skeletal_muscle_perc'));
        $validation->addField('muscle_mass_kg', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.muscle_mass_kg'));
        $validation->addField('bone_mass_kg', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.bone_mass_kg'));
        $validation->addField('protein_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.protein_perc'));

        return $validation->validate();
    }

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function setCreatedAtAttribute($value)
    {
        // to Disable created_at
    }
    // ===============

    // static functions
    // ================
}
