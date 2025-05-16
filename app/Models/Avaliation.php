<?php

namespace App\Models;

use App\Helpers\Constants;
use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Image;

class Avaliation extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const CALCULATE_PERC_FAT_BY_BIOIMPEDANCE = 'BIOIMPEDANCE';
    public const CALCULATE_PERC_FAT_BY_SKINFOLD = 'SKINFOLD';
    public const CALCULATE_PERC_FAT_BY_MEASURES = 'MEASURES';

    public const SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK = '3_FOLDS_JACKSON_POLLOCK';
    public const SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK = '7_FOLDS_JACKSON_POLLOCK';
    public const SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY = '4_FOLDS_DURNIN_WOMERSLEY';

    public const BASE_PHOTOS_FOLDER = '/avaliations/photos/';

    /** we use this to display the correct fields in the UI */
    public const SKIN_FOLDS_FORMULA_INPUT_CODE = [
        self::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK => '3jp',
        self::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK => '7jp',
        self::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY => '4dw',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'date',
        'weight_kg',
        'calculate_perc_fat_by',

        'body_fat_perc',
        'skeletal_muscle_perc',
        'muscle_mass_perc',
        'visceral_fat_kg',
        'basal_metabolism',
        'body_age',
        'body_water_perc',
        'bone_mass_kg',
        'right_arm_lean_mass_kg',
        'right_arm_lean_mass_perc',
        'right_arm_fat_kg',
        'right_arm_fat_perc',
        'left_arm_lean_mass_kg',
        'left_arm_lean_mass_perc',
        'left_arm_fat_kg',
        'left_arm_fat_perc',
        'trunk_lean_mass_kg',
        'trunk_lean_mass_perc',
        'trunk_fat_kg',
        'trunk_fat_perc',
        'right_leg_lean_mass_kg',
        'right_leg_lean_mass_perc',
        'right_leg_fat_kg',
        'right_leg_fat_perc',
        'left_leg_lean_mass_kg',
        'left_leg_lean_mass_perc',
        'left_leg_fat_kg',
        'left_leg_fat_perc',

        'chest_circ_cm',
        'right_arm_circ_cm',
        'left_arm_circ_cm',
        'waist_circ_cm',
        'right_forearm_circ_cm',
        'left_forearm_circ_cm',
        'abdomen_circ_cm',
        'right_thigh_circ_cm',
        'left_thigh_circ_cm',
        'hip_circ_cm',
        'right_calf_circ_cm',
        'left_calf_circ_cm',
        'neck_circ_cm',

        'skin_folds_formula',
        'skin_folds_chest_cm',
        'skin_folds_abdominal_cm',
        'skin_folds_thigh_cm',
        'skin_folds_tricep_cm',
        'skin_folds_suprailiac_cm',
        'skin_folds_axilla_cm',
        'skin_folds_subscapular_cm',
        'skin_folds_bicep_cm',

        'client_notes',
        'private_notes',

        'photo_front_url',
        'photo_right_url',
        'photo_rear_url',
        'photo_left_url',
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
        'bodyAgeCalc'
    ];

    // relations
    public function client()
    {
        return $this->belongsTo(
            Client::class,
            'client_id',
            'id'
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
        // TODO: check how to validate date unique for the same client
        $validation->addField('date', ['required', 'date', 'date_format:Y-m-d'], __('messages.models.Avaliation.fields.date'));
        $validation->addField('age', ['required', 'integer', 'min:10', 'max:150'], __('messages.models.Avaliation.fields.age'));
        $validation->addField('height_cm', ['required', 'integer', 'min:40', 'max:250'], __('messages.models.Client.fields.height'));
        $validation->addField('weight_kg', ['required', 'numeric', 'min:20', 'max:400'], __('messages.models.Client.fields.weight'));
        $validation->addField('calculate_perc_fat_by', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetCalculatePercFatBy())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.Avaliation.fields.calculate_perc_fat_by')])
                );
            }
        }], __('messages.models.Avaliation.fields.calculate_perc_fat_by'));
        $validation->addField('body_fat_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.body_fat_perc'));
        $validation->addField('skeletal_muscle_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.skeletal_muscle_perc'));
        $validation->addField('muscle_mass_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.muscle_mass_perc'));
        $validation->addField('visceral_fat_kg', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.visceral_fat_kg'));
        $validation->addField('basal_metabolism', ['nullable', 'integer', 'min:1000', 'max:9999'], __('messages.models.Avaliation.fields.basal_metabolism'));
        $validation->addField('body_age', ['nullable', 'integer', 'min:10', 'max:150'], __('messages.models.Avaliation.fields.body_age'));
        $validation->addField('body_water_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.body_water_perc'));
        $validation->addField('bone_mass_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.bone_mass_kg'));
        $validation->addField('right_arm_lean_mass_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_arm_lean_mass_kg'));
        $validation->addField('right_arm_lean_mass_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_arm_lean_mass_perc'));
        $validation->addField('right_arm_fat_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_arm_fat_kg'));
        $validation->addField('right_arm_fat_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_arm_fat_perc'));
        $validation->addField('left_arm_lean_mass_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_arm_lean_mass_kg'));
        $validation->addField('left_arm_lean_mass_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_arm_lean_mass_perc'));
        $validation->addField('left_arm_fat_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_arm_fat_kg'));
        $validation->addField('left_arm_fat_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_arm_fat_perc'));
        $validation->addField('trunk_lean_mass_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.trunk_lean_mass_kg'));
        $validation->addField('trunk_lean_mass_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.trunk_lean_mass_perc'));
        $validation->addField('trunk_fat_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.trunk_fat_kg'));
        $validation->addField('trunk_fat_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.trunk_fat_perc'));
        $validation->addField('right_leg_lean_mass_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_leg_lean_mass_kg'));
        $validation->addField('right_leg_lean_mass_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_leg_lean_mass_perc'));
        $validation->addField('right_leg_fat_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_leg_fat_kg'));
        $validation->addField('right_leg_fat_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.right_leg_fat_perc'));
        $validation->addField('left_leg_lean_mass_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_leg_lean_mass_kg'));
        $validation->addField('left_leg_lean_mass_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_leg_lean_mass_perc'));
        $validation->addField('left_leg_fat_kg', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_leg_fat_kg'));
        $validation->addField('left_leg_fat_perc', ['nullable', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.left_leg_fat_perc'));

        $validation->addField('chest_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.chest_circ_cm'));
        $validation->addField('right_arm_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.right_arm_circ_cm'));
        $validation->addField('left_arm_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.left_arm_circ_cm'));
        $validation->addField('waist_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.waist_circ_cm'));
        $validation->addField('right_forearm_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.right_forearm_circ_cm'));
        $validation->addField('left_forearm_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.left_forearm_circ_cm'));
        $validation->addField('abdomen_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.abdomen_circ_cm'));
        $validation->addField('right_thigh_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.right_thigh_circ_cm'));
        $validation->addField('left_thigh_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.left_thigh_circ_cm'));
        $validation->addField('hip_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.hip_circ_cm'));
        $validation->addField('right_calf_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.right_calf_circ_cm'));
        $validation->addField('left_calf_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.left_calf_circ_cm'));
        $validation->addField('neck_circ_cm', ['nullable', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.neck_circ_cm'));

        $validation->addField('skin_folds_formula', ['nullable', 'string', function ($attribute, $value, $fail) {
            if (null !== $value && false === array_key_exists($value, self::fGetSkinFoldFormulas())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.Avaliation.fields.skin_folds_formula')])
                );
            }
        }], __('messages.models.Avaliation.fields.skin_folds_formula'));
        $validation->addField('skin_folds_chest_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_chest_cm'));
        $validation->addField('skin_folds_abdominal_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_abdominal_cm'));
        $validation->addField('skin_folds_thigh_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_thigh_cm'));
        $validation->addField('skin_folds_tricep_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_tricep_cm'));
        $validation->addField('skin_folds_suprailiac_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_suprailiac_cm'));
        $validation->addField('skin_folds_axilla_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_axilla_cm'));
        $validation->addField('skin_folds_subscapular_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_subscapular_cm'));
        $validation->addField('skin_folds_bicep_cm', ['nullable', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.skin_folds_bicep_cm'));

        $validation->addField('client_notes', ['nullable', 'string'], __('messages.models.Avaliation.fields.client_notes'));
        $validation->addField('private_notes', ['nullable', 'string'], __('messages.models.Avaliation.fields.private_notes'));

        // TODO: check if we need to validate the file URL
        $validation->addField('photo_front_url', ['nullable', 'string'], __('messages.models.Avaliation.fields.photo_front_url'));
        $validation->addField('photo_right_url', ['nullable', 'string'], __('messages.models.Avaliation.fields.photo_right_url'));
        $validation->addField('photo_rear_url', ['nullable', 'string'], __('messages.models.Avaliation.fields.photo_rear_url'));
        $validation->addField('photo_left_url', ['nullable', 'string'], __('messages.models.Avaliation.fields.photo_left_url'));

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

    public function getBodyAgeCalcAttribute()
    {
        return $this->getBodyAge();
    }

    public function getFormattedDate(): string
    {
        return SysUtils::reformatDate($this->date, 'Y-m-d', __('messages.dateFormat'));
    }

    public function getFormattedWeight(): string
    {
        return SysUtils::formatDbToNumber($this->weight_kg, 1) . 'kg';
    }

    public function getFormattedCalculatePercFatBy(): string
    {
        return self::fGetCalculatePercFatBy()[$this->calculate_perc_fat_by] ?? '';
    }

    /** Fat-Free Mass Index */
    public function getFFMI(): float
    {
        $heightM = $this->height_cm / 100;
        $leanMassKg = $this->getLeanMassKg();

        if ($heightM <= 0 || $leanMassKg < 0) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        $ffmi = $leanMassKg / ($heightM ** 2);
        return round($ffmi, 2);
    }

    public function getFormattedFFMI(): string
    {
        return SysUtils::formatDbToNumber($this->getFFMI(), 2);
    }

    /** Body Adiposity Index */
    public function getBAI(): float
    {
        $heightCm = $this->height_cm;
        $hipCircCm = $this->hip_circ_cm;

        if ($heightCm <= 0 || $hipCircCm <= 0) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        $heightM = $heightCm / 100;
        $bai = ($hipCircCm / pow($heightM, 1.5)) - 18;

        return round($bai, 2);
    }

    public function getFormattedBAI(): string
    {
        return SysUtils::formatDbToNumber($this->getBAI(), 2);
    }

    /**
     * Basal Metabolic Rate
     */
    public function getTmb(): float
    {
        // if filled
        if ($this->basal_metabolism) {
            return $this->basal_metabolism;
        }

        // if not, calculate using "Harris-Benedict" formula
        if ($this->client->isMale()) {
            $tmb = number_format(
                88.36 + (13.4 * $this->weight_kg) + (4.8 * $this->height_cm) - (5.7 * $this->age),
                2,
                '.',
                ''
            );
        } else {
            $tmb = number_format(
                447.6 + (9.2 * $this->weight_kg) + (3.1 * $this->height_cm) - (4.3 * $this->age),
                2,
                '.',
                ''
            );
        }

        return $tmb;
    }

    public function getFormattedTmb(): string
    {
        return SysUtils::formatDbToNumber($this->getTmb(), 0) . ' kcal';
    }

    public function getWaistToHipRatio(): float
    {
        if (null === $this->waist_circ_cm || null === $this->hip_circ_cm) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        return round($this->waist_circ_cm / $this->hip_circ_cm, 2);
    }

    public function getFormattedHipToWaistRatio(): string
    {
        return SysUtils::formatDbToNumber($this->getWaistToHipRatio(), 2);
    }

    public function getFatMassKg(): float
    {
        // check if any of the values are null
        if ($this->weight_kg <= 0 || $this->getBodyFatPerc() <= 0) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        return number_format($this->weight_kg * ($this->getBodyFatPerc() / 100), 3, '.', '');
    }

    public function getFormattedFatMass(): string
    {
        return SysUtils::formatDbToNumber($this->getFatMassKg(), 2) . 'kg';
    }

    public function getLeanMassKg(): float
    {
        if ($this->getFatMassKg() <=0) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        return number_format($this->weight_kg - $this->getFatMassKg(), 3, '.', '');
    }

    public function getFormattedLeanMass(): string
    {
        return SysUtils::formatDbToNumber($this->getLeanMassKg(), 2) . 'kg';
    }

    public function getBodyFatPerc(): float
    {
        // bioimpedance
        if (self::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE === $this->calculate_perc_fat_by) {
            return $this->body_fat_perc ?? Constants::RETURN_INT_CANT_CALCULATE;
        }

        if (self::CALCULATE_PERC_FAT_BY_SKINFOLD === $this->calculate_perc_fat_by) {
            return $this->calculateBodyFatPercSkinfold();
        }

        if (self::CALCULATE_PERC_FAT_BY_MEASURES === $this->calculate_perc_fat_by) {
            return $this->calculateBodyFatPercMeasuresUsNavy();
        }

        return Constants::RETURN_INT_CANT_CALCULATE;
    }

    private function calculateBodyFatPercMeasuresUsNavy(): float
    {
        // US Navy (1984) - "Body fat estimation from circumference measurements"
        $waist = $this->waist_circ_cm;
        $neck = $this->neck_circ_cm;
        $height = $this->height_cm;
        $hip = $this->hip_circ_cm;

        if ($this->client->isMale()) {
            // check if any of the values are null
            if (null === $waist || null === $neck || null === $height) {
                return Constants::RETURN_INT_CANT_CALCULATE;
            }

            return round(86.010 * log10($waist - $neck) - 70.041 * log10($height) + 36.76, 2);
        }

        // woman - check if any of the values are null
        if (null === $waist || null === $neck || null === $height || null === $hip) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }
        return 163.205 * log10($waist + $hip - $neck) - 97.684 * log10($height) - 78.387;
    }

    private function calculateBodyFatPercSkinfold(): float
    {
        if (self::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK === $this->skin_folds_formula) {
            return $this->calculateBodyFatPerc3FoldsJacksonPollock();
        }

        if (self::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK === $this->skin_folds_formula) {
            return $this->calculateBodyFatPerc7FoldsJacksonPollock();
        }

        if (self::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY === $this->skin_folds_formula) {
            return $this->calculateBodyFatPerc4FoldsDurninWomersley();
        }

        return Constants::RETURN_INT_CANT_CALCULATE;
    }

    private function calculateBodyFatPerc3FoldsJacksonPollock(): float
    {
        if ($this->client->isMale()) {
            // check if any of the values less than 0
            if (null === $this->skin_folds_chest_cm || null === $this->skin_folds_abdominal_cm || null === $this->skin_folds_thigh_cm) {
                return Constants::RETURN_INT_CANT_CALCULATE;
            }

            $foldsSum = $this->skin_folds_chest_cm + $this->skin_folds_abdominal_cm + $this->skin_folds_thigh_cm;
            $dc = 1.10938 - (0.0008267 * $foldsSum) + (0.0000016 * $foldsSum ** 2) - (0.0002574 * $this->age);
        } else {
            // check if any of the values less than 0
            if (null === $this->skin_folds_tricep_cm || null === $this->skin_folds_thigh_cm || null === $this->skin_folds_suprailiac_cm) {
                return Constants::RETURN_INT_CANT_CALCULATE;
            }

            $foldsSum = $this->skin_folds_tricep_cm + $this->skin_folds_thigh_cm + $this->skin_folds_suprailiac_cm;
            $dc = 1.0994921 - (0.0009929 * $foldsSum) + (0.0000023 * $foldsSum ** 2) - (0.0001392 * $this->age);
        }

        return $this->bodyDensityToBodyFatPerc($dc);
    }

    private function calculateBodyFatPerc7FoldsJacksonPollock(): float
    {
        // check if any of the values less than 0
        if (null === $this->skin_folds_chest_cm || null === $this->skin_folds_abdominal_cm || null === $this->skin_folds_thigh_cm ||
            null === $this->skin_folds_tricep_cm || null === $this->skin_folds_axilla_cm || null === $this->skin_folds_subscapular_cm ||
            null === $this->skin_folds_suprailiac_cm) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        $foldsSum =
            $this->skin_folds_chest_cm +
            $this->skin_folds_abdominal_cm +
            $this->skin_folds_thigh_cm +
            $this->skin_folds_tricep_cm +
            $this->skin_folds_axilla_cm +
            $this->skin_folds_subscapular_cm +
            $this->skin_folds_suprailiac_cm;

        if ($this->client->isMale()) {
            $dc = 1.112 - (0.00043499 * $foldsSum) + (0.00000055 * $foldsSum ** 2) - (0.00028826 * $this->age);
        } else {
            $dc = 1.097 - (0.00046971 * $foldsSum) + (0.00000056 * $foldsSum ** 2) - (0.00012828 * $this->age);
        }

        return $this->bodyDensityToBodyFatPerc($dc);
    }

    private function calculateBodyFatPerc4FoldsDurninWomersley(): float
    {
        // check if any of the values less than 0
        if (null === $this->skin_folds_tricep_cm || null === $this->skin_folds_bicep_cm || null === $this->skin_folds_subscapular_cm ||
            null === $this->skin_folds_suprailiac_cm) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        $foldsSum =
            $this->skin_folds_tricep_cm +
            $this->skin_folds_bicep_cm +
            $this->skin_folds_subscapular_cm +
            $this->skin_folds_suprailiac_cm;
        $dc = $this->calculateDurninWomersleyBodyDensity(
            ($this->client->isMale()) ? 'male': 'female',
            $this->age,
            $foldsSum
        );

        if (null === $dc) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        return $this->bodyDensityToBodyFatPerc($dc);
    }

    private function calculateDurninWomersleyBodyDensity(string $gender, int $age, float $sumOfSkinfolds): float
    {
        $coefficients = [
            'male' => [
                [17, 19, 1.1620, 0.0630],
                [20, 29, 1.1631, 0.0632],
                [30, 39, 1.1422, 0.0544],
                [40, 49, 1.1620, 0.0700],
                [50, 59, 1.1715, 0.0779],
                [60, 72, 1.1632, 0.0806],
            ],
            'female' => [
                [17, 19, 1.1549, 0.0678],
                [20, 29, 1.1599, 0.0717],
                [30, 39, 1.1423, 0.0632],
                [40, 49, 1.1333, 0.0612],
                [50, 59, 1.1339, 0.0645],
                [60, 72, 1.1317, 0.0630],
            ]
        ];

        $gender = strtolower($gender);
        if (!isset($coefficients[$gender])) {
            return Constants::RETURN_INT_CANT_CALCULATE; // Sexo inválido
        }

        foreach ($coefficients[$gender] as [$minAge, $maxAge, $a, $b]) {
            if ($age >= $minAge && $age <= $maxAge) {
                if ($sumOfSkinfolds <= 0) {
                    return Constants::RETURN_INT_CANT_CALCULATE; // Evita log10 negativo/inválido
                }
                $logSum = log10($sumOfSkinfolds);
                $density = $a - ($b * $logSum);
                return round($density, 4); // Retorna densidade corporal (g/cm³)
            }
        }

        return Constants::RETURN_INT_CANT_CALCULATE; // Faixa etária não suportada
    }

    private function bodyDensityToBodyFatPerc(float $bodyDensity): float
    {
        // Fórmula de Siri (mais comum)
        // Fórmula de Brozek (alternativa) -> $bodyFatPerc = (457 / $dc) - 414.2;
        return (495 / $bodyDensity) - 450;
    }

    public function getFormattedBodyFat(): string
    {
        return SysUtils::formatDbToNumber($this->getBodyFatPerc(), 2) . '%';
    }

    public function getBoneMassKg(): float
    {
        if ($this->bone_mass_kg) {
            return $this->bone_mass_kg;
        } else {
            if ($this->getLeanMassKg() <= 0) {
                return Constants::RETURN_INT_CANT_CALCULATE;
            }

            // Heyward & Wagner (2004) — Applied Body Composition Assessment
            // Mcardle, Katch & Katch – Exercise Physiology
            // 15% (man) and 12% (woman) of lean mass is bone mass
            return $this->getLeanMassKg() * ($this->client->isMale() ? 0.15: 0.12);
        }
    }

    public function getFormattedBoneMassKg(): string
    {
        return SysUtils::formatDbToNumber($this->getBoneMassKg(), 1) . 'kg';
    }

    public function getVisceralFatKg(): float
    {
        if ($this->visceral_fat_kg) {
            $visceralFatKg = $this->visceral_fat_kg;
        } else {
            // check if any of the values are null
            if (null === $this->waist_circ_cm || $this->getBmi() < 0) {
                return Constants::RETURN_INT_CANT_CALCULATE;
            }

            // Gordura Visceral (kg)=(0.68×IMC)+(0.03×Circunfereˆncia da Cintura (cm))−16.2
            $visceralFatKg = (0.68 * $this->getBmi()) + (0.03 * $this->waist_circ_cm) - 16.2;
        }

        // if null or negative
        if (null === $visceralFatKg || $visceralFatKg < 0) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        return number_format($visceralFatKg, 3, '.', '');
    }

    public function getFormattedVisceralFatKg(): string
    {
        return SysUtils::formatDbToNumber($this->getVisceralFatKg(), 1) . 'kg';
    }

    public function getBodyWaterPerc(): float
    {
        if ($this->body_water_perc) {
            return $this->body_water_perc;
        } else {
            if ($this->getLeanMassKg() <= 0) {
                return Constants::RETURN_INT_CANT_CALCULATE;
            }

            // Wang et al. (1999) – "Body composition: methods and applications"
            // 73% of lean mass is water
            return $this->getLeanMassKg() * 0.73;
        }
    }

    public function getFormattedBodyWaterPerc(): string
    {
        return SysUtils::formatDbToNumber($this->getBodyWaterPerc(), 1) . '%';
    }

    public function getFormattedBodyWaterKg(): string
    {
        $perc = $this->getBodyWaterPerc();
        return SysUtils::formatDbToNumber(($perc/100) * $this->weight_kg, 1) . 'kg';
    }

    public function getSkeletalMuscleMassPerc(): float
    {
        if ($this->skeletal_muscle_perc) {
            return $this->skeletal_muscle_perc;
        }

        return $this->estimateSkeletalMuscleMassPerc();
    }

    private function estimateSkeletalMuscleMassPerc(): float
    {
        // check if any of the values are null
        if (null === $this->weight_kg || null === $this->height_cm || null === $this->age) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        // if not, calculate using "Lee et al" formula
        /*
            SM = 0.244 × BW + 7.80 × Ht + 6.6 × sex − 0.098 × age + race − 3.3
            ==================================================================
            BW: is body weight in kilograms
            Ht: is height in meters
            sex: is 0 for female and 1 for male
            race: is −2.0 for Asian, 1.1 for African American, and 0 for white and Hispanic in Model 1
            race: is −1.2 for Asian, 1.4 for African American, and 0 for white and Hispanic in Model 2
        */
        $skeletalMuscleMassKg =
            (0.244 * $this->weight_kg)
            + (7.8 * ($this->height_cm / 100))
            + (6.6 * ($this->client->isFemale()) ? 0: 1)
            - (0.098 * $this->age)
            + 0
            - 3.3;
        return round(($skeletalMuscleMassKg / $this->weight_kg) * 100, 2);
    }

    public function getFormattedSkeletalMuscleMassPerc(): string
    {
        return SysUtils::formatDbToNumber($this->getSkeletalMuscleMassPerc(), 1) . '%';
    }

    public function getSkeletalMuscleMassKg(): float
    {
        return number_format(($this->getSkeletalMuscleMassPerc() / 100) * $this->weight_kg, 3, '.', '');
    }

    public function getFormattedSkeletalMuscleMassKg(): string
    {
        return SysUtils::formatDbToNumber($this->getSkeletalMuscleMassKg(), 1) . 'kg';
    }

    public function getMuscleMassKg(): float
    {
        if ($this->muscle_mass_perc) {
            return ($this->muscle_mass_perc / 100) * $this->weight_kg;
        } else {
            // Janssen et al. (2000) formula
            return $this->estimateMuscleMassKg(
                $this->getLeanMassKg(),
                $this->age,
                ($this->client->isMale()) ? 'masculino' : 'feminino',
            );
        }
    }

    private function estimateMuscleMassKg(float $leanMassKg, int $age, bool $isMale): float
    {
        // factor
        $factor = ($isMale) ? 0.538: 0.478;

        // Correção etária: a partir dos 30 anos, considera uma perda de 1% por década
        $factorAge = max(0.95, 1 - (max($age - 30, 0) * 0.001)); // Redução gradual

        // Estima massa muscular esquelética (kg)
        $massaMuscularKg = $leanMassKg * $factor * $factorAge;

        return round($massaMuscularKg, 2);
    }

    public function getBodyAge(): int
    {
        if ($this->body_age) {
            return $this->body_age;
        }

        return $this->estimateBodyAge(
            $this->age,
            ($this->client->isMale()) ? 'male' : 'female',
            $this->getBodyFatPerc(),
            $this->getMuscleMassKg(),
            $this->getTmb()
        );
    }

    private function estimateBodyAge(int $age, string $sex, float $bodyFatPercent, float $muscleMassKg, float $bmr)
    {
        $bodyAge = $age;

        // Valores médios estimados por idade e sexo
        $avg = [
            'male' => [
                'body_fat' => 18,       // % gordura corporal média
                'muscle_mass' => 33.0,  // kg de músculo esquelético médio
                'bmr' => 1650           // kcal/dia médio
            ],
            'female' => [
                'body_fat' => 25,
                'muscle_mass' => 24.0,
                'bmr' => 1400
            ]
        ];

        $group = $sex === 'female' ? 'female' : 'male';

        // Ajuste baseado na gordura corporal
        if ($bodyFatPercent > $avg[$group]['body_fat'] + 5) {
            $bodyAge += 2;
        } elseif ($bodyFatPercent < $avg[$group]['body_fat'] - 5) {
            $bodyAge -= 1;
        }

        // Ajuste baseado na massa muscular
        if ($muscleMassKg < $avg[$group]['muscle_mass'] * 0.9) {
            $bodyAge += 2;
        } elseif ($muscleMassKg > $avg[$group]['muscle_mass'] * 1.1) {
            $bodyAge -= 1;
        }

        // Ajuste baseado no BMR
        if ($bmr < $avg[$group]['bmr'] - 150) {
            $bodyAge += 1;
        } elseif ($bmr > $avg[$group]['bmr'] + 150) {
            $bodyAge -= 1;
        }

        return max($bodyAge, 10); // idade corporal não deve ser menor que 10
    }

    public function getFormattedBodyAge(): string
    {
        return SysUtils::formatDbToNumber($this->getBodyAge(), 0) . ' ' . __('messages.components.avaliationReport.years');
    }

    /**
     * Body Mass Index
     */
    public function getBmi(): float
    {
        return number_format($this->weight_kg / (($this->height_cm / 100) ** 2), 2, '.', '');
    }

    public function getFormattedBmi(): string
    {
        return SysUtils::formatDbToNumber($this->getBmi(), 2) . ' kg/m²';
    }

    // === PHOTO
    public function setPhotoFrontUrl(?UploadedFile $file): void
    {
        $this->setPhotoUrl('photo_front_url', $file);
    }

    public function removePhotoFrontUrl(): void
    {
        $this->removePhotoUrl('photo_front_url');
    }

    public function setPhotoRightUrl(?UploadedFile $file): void
    {
        $this->setPhotoUrl('photo_right_url', $file);
    }

    public function removePhotoRightUrl(): void
    {
        $this->removePhotoUrl('photo_right_url');
    }

    public function setPhotoRearUrl(?UploadedFile $file): void
    {
        $this->setPhotoUrl('photo_rear_url', $file);
    }

    public function removePhotoReartUrl(): void
    {
        $this->removePhotoUrl('photo_rear_url');
    }

    public function setPhotoLeftUrl(?UploadedFile $file): void
    {
        $this->setPhotoUrl('photo_left_url', $file);
    }

    public function removePhotoLeftUrl(): void
    {
        $this->removePhotoUrl('photo_left_url');
    }

    public function getPhotoBase64(string $fieldName): ?string
    {
        if (null === $this->{$fieldName}) {
            return null;
        }

        $filePath = storage_path(self::fGetOsPhotosFolder() . DIRECTORY_SEPARATOR . basename($this->{$fieldName}));
        if (!File::exists($filePath)) {
            return null;
        }

        $mimeType = mime_content_type($filePath);
        $base64 = base64_encode(file_get_contents($filePath));
        return "data:$mimeType;base64,$base64";
    }

    private function setPhotoUrl(string $field, ?UploadedFile $file): void
    {
        // check if file is null
        if (null === $file) {
            return;
        }

        // check folder
        $destinationPath = storage_path(self::fGetOsPhotosFolder());
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // check if $this->{$field} has value, if yes, use remove method
        if ($this->{$field}) {
            $this->removePhotoUrl($field);
        }

        // save image
        $side = str_replace(['photo_', '_url'], '', $field);
        $newFileName = 'pic-' . $side . '-' . $this->id . '.' . $file->extension();
        $saveFilePath = $destinationPath  . DIRECTORY_SEPARATOR  . $newFileName;

        $img = Image::make($file->path());
        $retSave = $img->fit(480)->save($saveFilePath);
        if ($retSave) {
            $this->{$field} = self::fGetDbPhotosFolder() . $newFileName;
            $this->save();
        }
    }

    private function removePhotoUrl(string $field): void
    {
        // remove file
        $fileName = basename($this->{$field});
        $filePath = storage_path(self::fGetOsPhotosFolder() . DIRECTORY_SEPARATOR . $fileName);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // update field
        $this->{$field} = null;
        $this->save();
    }
    // =========

    // =======
    /**
     * Ranks from 1 to 8.
     * Man [1=6-13%, 2=14-24%, 3=25-27%, 4=28-29%, 5=30-32%, 6=33-35%, 7=36-38%, 8=39%+]
     * Woman [1=16-23%, 2=24-31%, 3=32-34%, 4=35-36%, 5=37-39%, 6=40-42%, 7=43-45%, 8=46%+]
     * @return string
     */
    public function getBodyFatInfo(): array
    {
        $BodyFat = new \App\Helpers\Avaliation\BodyFat($this);
        return $BodyFat->getFieldInfo();
    }

    public function getWeightInfo(): array
    {
        $Weight = new \App\Helpers\Avaliation\Weight($this);
        return $Weight->getFieldInfo();
    }

    public function getSkeletalMuscleInfo(): array
    {
        $SkeletalMuscle = new \App\Helpers\Avaliation\SkeletalMuscle($this);
        return $SkeletalMuscle->getFieldInfo();
    }

    public function getBodyWaterInfo(): array
    {
        $BodyWater = new \App\Helpers\Avaliation\BodyWater($this);
        return $BodyWater->getFieldInfo();
    }

    public function getBoneMassInfo(): array
    {
        $BoneMass = new \App\Helpers\Avaliation\BoneMass($this);
        return $BoneMass->getFieldInfo();
    }

    public function getBodyAgeInfo(): array
    {
        $BodyAge = new \App\Helpers\Avaliation\BodyAge($this);
        return $BodyAge->getFieldInfo();
    }

    public function getBmiInfo(): array
    {
        $Bmi = new \App\Helpers\Avaliation\BodyMassIndex($this);
        return $Bmi->getFieldInfo();
    }

    public function getVisceralFatInfo(): array
    {
        $VisceralFat = new \App\Helpers\Avaliation\VisceralFat($this);
        return $VisceralFat->getFieldInfo();
    }

    public function getBasalMetabolismInfo(): array
    {
        $BasalMetabolism = new \App\Helpers\Avaliation\BasalMetabolism($this);
        return $BasalMetabolism->getFieldInfo();
    }

    public function getWaistToHipRatioInfo(): array
    {
        $WaistToHipRatio = new \App\Helpers\Avaliation\WaistToHipRatio($this);
        return $WaistToHipRatio->getFieldInfo();
    }

    /**
     * Body Composition Index (BCI)
     *
     */
    public function getBciInfo(): array
    {
        // TODO: maybe use App\Helpers\Avaliation classes
        $score = 0;
        $isMale = $this->client->isMale();

        // BMI
        $bmi = $this->getBmi();
        if ($bmi < 18.5) {
            $score += 1;
        } elseif ($bmi <= 24.9) {
            $score += 0;
        } elseif ($bmi <= 29.9) {
            $score += 1;
        } elseif ($bmi <= 34.9) {
            $score += 2;
        } else {
            $score += 3;
        }

        // Gordura corporal (%)
        $bodyFatPerc = $this->getBodyFatPerc();
        if ($isMale) {
            if ($bodyFatPerc < 10) {
                $score += 1;
            } elseif ($bodyFatPerc <= 20) {
                $score += 0;
            } elseif ($bodyFatPerc <= 25) {
                $score += 1;
            } else {
                $score += 2;
            }
        } else {
            if ($bodyFatPerc < 20) {
                $score += 1;
            } elseif ($bodyFatPerc <= 30) {
                $score += 0;
            } elseif ($bodyFatPerc <= 35) {
                $score += 1;
            } else {
                $score += 2;
            }
        }

        // Relação cintura-quadril
        $whr = $this->getWaistToHipRatio();
        if ($isMale) {
            if ($whr < 0.90) {
                $score += 0;
            } elseif ($whr <= 0.99) {
                $score += 1;
            } else {
                $score += 2;
            }
        } else {
            if ($whr < 0.80) {
                $score += 0;
            } elseif ($whr <= 0.84) {
                $score += 1;
            } else {
                $score += 2;
            }
        }

        // Gordura visceral (kg)
        $vf = $this->getVisceralFatKg();
        if ($isMale) {
            if ($vf <= 1.0) {
                $score += 0;
            } elseif ($vf <= 2.0) {
                $score += 1;
            } else {
                $score += 2;
            }
        } else {
            if ($vf <= 0.9) {
                $score += 0;
            } elseif ($vf <= 1.8) {
                $score += 1;
            } else {
                $score += 2;
            }
        }

        // classification
        $idx = match(true) {
            $score <= 1 => 0,
            $score === 2 => 1,
            $score === 3 => 2,
            $score === 4 => 3,
            $score === 5 => 4,
            $score === 6 => 5,
            $score === 7 => 6,
            default => 7,
        };

        // return like other "info" methods
        return [
            Constants::FI_FIELD_LABEL => '',
            Constants::FI_FIELD_VALUE => '',
            Constants::FI_FIELD_SUFFIX => '',
            Constants::FI_RANK => ($idx + 1),
            Constants::FI_RANK_IDX => $idx,
            Constants::FI_RANK_LABEL => array_column(Constants::getRankings(), 'label')[$idx],
            Constants::FI_RANK_COLOR => array_column(Constants::getRankings(), 'color')[$idx],
            Constants::FI_IDEAL_MIN => '',
            Constants::FI_IDEAL_MAX => '',
            Constants::FI_IDEAL_LABEL => '',
        ];
    }

    public function getFFMIInfo(): array
    {
        $FFMIInfo = new \App\Helpers\Avaliation\FatFreeMassIndex($this);
        return $FFMIInfo->getFieldInfo();
    }

    public function getBAInfo(): array
    {
        $BAInfo = new \App\Helpers\Avaliation\BodyAdiposityIndex($this);
        return $BAInfo->getFieldInfo();
    }
    // =======
    // ===============

    // static functions
    public static function fGetCalculatePercFatBy(): array
    {
        return [
            self::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE => __('messages.models.Avaliation.calculatePercFatByBioimpedance'),
            self::CALCULATE_PERC_FAT_BY_SKINFOLD => __('messages.models.Avaliation.calculatePercFatBySkinfold'),
            self::CALCULATE_PERC_FAT_BY_MEASURES => __('messages.models.Avaliation.calculatePercFatByMeasures'),
        ];
    }

    public static function fGetSkinFoldFormulas(): array
    {
        return [
            self::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK => __('messages.models.Avaliation.skinFoldFormula3FoldsJacksonPollock'),
            self::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK => __('messages.models.Avaliation.skinFoldFormula7FoldsJacksonPollock'),
            self::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY => __('messages.models.Avaliation.skinFoldFormula4FoldsDurninWomersley'),
        ];
    }

    public static function fGetJsonSkinFoldsInputCode(): string
    {
        return json_encode(self::SKIN_FOLDS_FORMULA_INPUT_CODE);
    }

    public static function fSave(array $form, ?string $codedId = null): ApiResponse
    {
        // get model for insert or update
        if (!empty($codedId)) {
            $Avaliation = self::getModelByCodedId($codedId);
            if ($Avaliation === null) {
                return new ApiResponse(true, __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.Avaliation.name'),
                ]));
            }
        } else {
            $Avaliation = new self();
        }
        $isEdit = ($Avaliation->id > 0);

        // check if user can save
        if (!self::fHasAccess($Avaliation)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => __('messages.models.Avaliation.name'),
            ]));
        }

        // fill model
        $Avaliation->fill($form);

        // fix skin_folds values
        self::fFixSkinFoldsValues($Avaliation);

        // default value for height_cm = Client current height
        if (!$isEdit) {
            $Client = Client::find($Avaliation->client_id);
            $Avaliation->height_cm = $Client?->height_cm;
            $Avaliation->age = $Client?->getAge();
        }

        // validate model
        $validation = $Avaliation->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
            $Avaliation->timestamps = false;
            $Avaliation->save();
            $Avaliation->refresh();
        } catch (\Exception $e) {
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => __('messages.models.Avaliation.name'),
            ]));
        }

        // all good, return success
        $msg = $isEdit ? __('messages.saveModelSuccessEditing', ['modelName' => __('messages.models.Avaliation.name')]) : __('messages.saveModelSuccessAdding', ['modelName' => __('messages.models.Avaliation.name')]);
        return new ApiResponse(false, $msg, [
            'Avaliation' => $Avaliation,
            'isEdit' => $isEdit,
        ]);
    }

    private static function fFixSkinFoldsValues(self &$Avaliation): void
    {
        switch ($Avaliation->skin_folds_formula) {
            case self::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK:
                $arrFieldsToKeep = ['skin_folds_thigh_cm'];

                if ($Avaliation->Client->gender === Client::GENDER_MALE) {
                    $arrFieldsToKeep[] = 'skin_folds_chest_cm';
                    $arrFieldsToKeep[] = 'skin_folds_abdominal_cm';
                }

                if ($Avaliation->Client->gender === Client::GENDER_FEMALE) {
                    $arrFieldsToKeep[] = 'skin_folds_tricep_cm';
                    $arrFieldsToKeep[] = 'skin_folds_suprailiac_cm';
                }
                break;

            case self::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK:
                $arrFieldsToKeep = [
                    'skin_folds_chest_cm',
                    'skin_folds_axilla_cm',
                    'skin_folds_tricep_cm',
                    'skin_folds_subscapular_cm',
                    'skin_folds_abdominal_cm',
                    'skin_folds_suprailiac_cm',
                    'skin_folds_thigh_cm',
                ];
                break;

            case self::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY:
                $arrFieldsToKeep = [
                    'skin_folds_bicep_cm',
                    'skin_folds_tricep_cm',
                    'skin_folds_subscapular_cm',
                    'skin_folds_suprailiac_cm',
                ];
                break;

            default:
                $arrFieldsToKeep = [];
                break;
        }

        foreach ($Avaliation->getAttributes() as $field => $value) {
            if (
                str_starts_with($field, 'skin_folds_') &&
                str_ends_with($field, '_cm') &&
                !in_array($field, $arrFieldsToKeep, true)
            ) {
                $Avaliation->$field = null;
            }
        }
    }

    public static function fHasAccess(self $Avaliation): bool
    {
        // adding user is ok
        if (!$Avaliation->id > 0) {
            return true;
        }

        // check logged user
        $lggdUser = SysUtils::getLoggedInUser();
        if (null === $lggdUser) {
            return false;
        }

        // root can save any client
        if ($lggdUser->role === User::ROLE_ROOT) {
            return true;
        }

        // check if user is trying to edit a goal from a client that is not his
        if ($Avaliation->id > 0 && $Avaliation->client->user_id !== $lggdUser->id) {
            return false;
        }

        return true;
    }

    public static function fGetOsPhotosFolder(): string
    {
        $basePath = str_replace('/', DIRECTORY_SEPARATOR, self::BASE_PHOTOS_FOLDER);
        return 'app'.$basePath;
    }

    public static function fGetDbPhotosFolder(): string
    {
        return 'storage' . self::BASE_PHOTOS_FOLDER;
    }
    // ================
}
