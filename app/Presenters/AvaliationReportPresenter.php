<?php

namespace App\Presenters;

use App\Models\Avaliation;

final class AvaliationReportPresenter
{
    public static function getUserLogoBase64(Avaliation $Avaliation): string
    {
        return $Avaliation->client->user?->info?->getLogoBase64();
    }

    public static function getUserInfoLoopArray(Avaliation $Avaliation): array
    {
        $items = array_filter([
            'title',
            'license_text',
            'whatsapp_phone',
        ], function ($item) use ($Avaliation) {
            return !empty($Avaliation->client->user?->info?->{$item});
        });

        // loop and add key 'value' with the value of the field
        return array_map(function ($item) use ($Avaliation) {
            return [
                'value' => $Avaliation->client->user?->info->{$item}
            ];
        }, $items);
    }

    public static function getSocialLinks(Avaliation $Avaliation): array
    {
        $items = array_filter([
            ['field' => 'link_telegram', 'icon' => 'fab fa-telegram'],
            ['field' => 'link_facebook', 'icon' => 'fab fa-facebook'],
            ['field' => 'link_instagram', 'icon' => 'fab fa-instagram'],
            ['field' => 'link_twitter', 'icon' => 'fab fa-twitter'],
            ['field' => 'link_youtube', 'icon' => 'fab fa-youtube'],
            ['field' => 'link_website', 'icon' => 'fas fa-globe']
        ], function ($item) use ($Avaliation) {
            return !empty($Avaliation->client->user?->info?->{$item['field']});
        });

        // loop and add key 'value' with the value of the field
        return array_map(function ($item) use ($Avaliation) {
            return [
                'icon' => $item['icon'],
                'value' => $Avaliation->client->user?->info->{$item['field']}
            ];
        }, $items);
    }

    public static function getInfoCardsData(Avaliation $Avaliation): array
    {
        $data = [
            [
                'method' => 'getWeightInfo',
                'title' => __('messages.models.Client.fields.weight'),
                'showReference' => true
            ],
            [
                'method' => 'getSkeletalMuscleInfo',
                'title' => __('messages.components.avaliationReport.skeletalMuscle'),
                'showReference' => true
            ],
            [
                'method' => 'getBodyWaterInfo',
                'title' => __('messages.components.avaliationReport.bodyWater'),
                'showReference' => true
            ],
            [
                'method' => 'getBoneMassInfo',
                'title' => __('messages.models.Avaliation.fields.bone_mass_kg'),
                'showReference' => true
            ],
            [
                'method' => 'getBodyAgeInfo',
                'title' => __('messages.models.Avaliation.fields.body_age'),
                'showReference' => false
            ],
            [
                'method' => 'getFFMIInfo',
                'title' => __('messages.components.avaliationReport.FFMI'),
                'showReference' => true
            ],
            [
                'method' => 'getBmiInfo',
                'title' => __('messages.components.avaliationReport.bmi'),
                'showReference' => true
            ],
            [
                'method' => 'getBodyFatInfo',
                'title' => __('messages.components.avaliationReport.bodyFat'),
                'showReference' => true
            ],
            [
                'method' => 'getBAInfo',
                'title' => __('messages.components.avaliationReport.BAI'),
                'showReference' => true
            ],
            [
                'method' => 'getVisceralFatInfo',
                'title' => __('messages.models.Avaliation.fields.visceral_fat_kg'),
                'showReference' => true
            ],
            [
                'method' => 'getBasalMetabolismInfo',
                'title' => __('messages.models.Avaliation.fields.basal_metabolism'),
                'showReference' => true
            ],
            [
                'method' => 'getWaistToHipRatioInfo',
                'title' => __('messages.components.avaliationReport.WaistToHipRatio'),
                'showReference' => true
            ]
        ];

        return array_map(function ($item) use ($Avaliation) {
            return [
                'method' => $item['method'],
                'title' => $item['title'],
                'showReference' => $item['showReference'],
                'info' => $Avaliation->{$item['method']}()
            ];
        }, $data);
    }

    public static function getGraphData(): array
    {
        return [
            ['title' => __('messages.models.Client.fields.weight'), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationWeightGraphHelper'],
            ['title' => __('messages.components.avaliationFatLeanMassGraph.title', [
                'fatMass' => __('messages.models.Avaliation.labelFatMass'),
                'leanMass' => __('messages.models.Avaliation.labelLeanMass'),
            ]), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationFatLeanMassGraphHelper'],
            ['title' => __('messages.components.AvaliationBodyCompositionGraph.title', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationBodyCompositionGraphHelper'],
            ['title' => __('messages.components.AvaliationMuscleFatPercGraph.title', [
                'fatMass' => __('messages.models.Avaliation.labelFatMass'),
                'skeletalMuscle' => __('messages.components.avaliationReport.skeletalMuscle'),
            ]), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationMuscleFatPercGraphHelper'],
            ['title' => __('messages.components.AvaliationFatBySegmentGraph.title', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationFatBySegmentGraphHelper'],
            ['title' => __('messages.components.AvaliationLeanBySegmentGraph.title', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationLeanBySegmentGraphHelper'],
            ['title' => __('messages.components.avaliationReport.bodyWater', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationBodyWaterGraphHelper'],
            ['title' => __('messages.models.Avaliation.fields.body_age', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationBodyAgeGraphHelper'],
            ['title' => __('messages.components.AvaliationUpperLimbsGraph.title'), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationUpperLimbsGraphHelper'],
            ['title' => __('messages.components.AvaliationLowerLimbsGraph.title'), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationLowerLimbsGraphHelper']
        ];
    }

    public static function getImagesData(): array
    {
        return  [
            [
                'fieldName' => 'photo_front_url',
                'inputName' => 'f-photo_front_url',
                'defaultImg' => '/images/photo_front.jpg',
                'imgAlt' => __('messages.models.Avaliation.fields.photo_front_url'),
            ],
            [
                'fieldName' => 'photo_right_url',
                'inputName' => 'f-photo_right_url',
                'defaultImg' => '/images/photo_right.jpg',
                'imgAlt' => __('messages.models.Avaliation.fields.photo_right_url'),
            ],
            [
                'fieldName' => 'photo_rear_url',
                'inputName' => 'f-photo_rear_url',
                'defaultImg' => '/images/photo_rear.jpg',
                'imgAlt' => __('messages.models.Avaliation.fields.photo_rear_url'),
            ],
            [
                'fieldName' => 'photo_left_url',
                'inputName' => 'f-photo_left_url',
                'defaultImg' => '/images/photo_left.jpg',
                'imgAlt' => __('messages.models.Avaliation.fields.photo_left_url'),
            ]
        ];
    }
}
