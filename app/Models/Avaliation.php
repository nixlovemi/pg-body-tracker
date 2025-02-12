<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
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
        'skeletal_muscle_perc',
        'visceral_fat_kg',
        'waist_circumference_cm'
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
        $validation->addField('body_fat_perc', ['required', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.body_fat_perc'));
        $validation->addField('skeletal_muscle_perc', ['nullable', 'filled', 'numeric', 'min:0', 'max:100'], __('messages.models.Avaliation.fields.skeletal_muscle_perc'));
        $validation->addField('visceral_fat_kg', ['nullable', 'filled', 'numeric', 'min:0', 'max:20'], __('messages.models.Avaliation.fields.visceral_fat_kg'));
        $validation->addField('waist_circumference_cm', ['nullable', 'filled', 'numeric', 'min:0', 'max:200'], __('messages.models.Avaliation.fields.waist_circumference_cm'));

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

    public function getFatMassKg(): float
    {
        return number_format($this->weight_kg * ($this->body_fat_perc / 100), 3, '.', '');
    }

    public function getLeanMassKg(): float
    {
        return number_format($this->weight_kg - $this->getFatMassKg(), 3, '.', '');
    }

    /**
     * Body Mass Index
     */
    public function getImc(): float
    {
        return number_format($this->weight_kg / (($this->height_cm / 100) ** 2), 2, '.', '');
    }

    /**
     * Basal Metabolic Rate
     */
    public function getTmb(): float
    {
        if ($this->client->isMale()) {
            $tmb = number_format(
                88.36 + (13.4 * $this->weight_kg) + (4.8 * $this->height_cm) - (5.7 * $this->client->getAge()),
                2,
                '.',
                ''
            );
        } else {
            $tmb = number_format(
                447.6 + (9.2 * $this->weight_kg) + (3.1 * $this->height_cm) - (4.3 * $this->client->getAge()),
                2,
                '.',
                ''
            );
        }

        return $tmb;
    }

    public function getSkeletalMuscleMassKg(): float
    {
        $skeletalMuscleMass = null;

        if ($this->skeletal_muscle_perc) {
            $skeletalMuscleMass = $this->weight_kg * ($this->skeletal_muscle_perc / 100);
        } else {
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
            $skeletalMuscleMass =
                (0.244 * $this->weight_kg)
                + (7.8 * ($this->height_cm / 100))
                + (6.6 * ($this->client->isFemale()) ? 0: 1)
                - (0.098 * $this->client->getAge())
                + 0
                - 3.3;
        }

        return number_format($skeletalMuscleMass ?? 0, 3, '.', '');
    }

    public function getVisceralFatKg(): float
    {
        if ($this->visceral_fat_kg) {
            $visceralFatKg = $this->visceral_fat_kg;
        } else {
            // Gordura Visceral (kg)=(0.68×IMC)+(0.03×Circunfereˆncia da Cintura (cm))−16.2
            $visceralFatKg =
                (0.68 * $this->getImc())
                + (0.03 * $this->waist_circumference_cm)
                - 16.2;
        }

        return number_format($visceralFatKg, 3, '.', '');
    }

    public function getBodyAge(): int
    {
        // avg PGC and PME
        $media_pgc = ($this->client->isMale()) ? 22 : 28; // Média PGC: 22% (homem), 28% (mulher)
        $media_pme = ($this->client->isMale()) ? 40 : 30; // Média PME: 40% (homem), 30% (mulher)

        // Aplicar fórmula
        $bodyAge = $this->client->getAge() + (round($this->body_fat_perc) - $media_pgc) - (round($this->skeletal_muscle_perc) - $media_pme);

        return round($bodyAge, 1);
    }
    // ===============

    // static functions
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

        // default value for height_cm = Client current height
        if (!$isEdit) {
            $Client = Client::find($Avaliation->client_id);
            $Avaliation->height_cm = $Client?->height_cm;
        }

        // validate model
        $validation = $Avaliation->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
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
    // ================
}
