<?php

namespace ApiAlarmManager\Api;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use ApiAlarmManager\Admin\FireDangerLevels as AdminFireDangerLevels;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetTerm;
use WpService\Contracts\RegisterRestRoute;
use WpService\WpService;

class FireDangerLevels
{
    private $namespace = 'wp/v2';
    private $route     = '/fire-danger-levels';

    public function __construct(
        private GetOption&AddAction&RegisterRestRoute&GetTerm $wpService,
        private GetField&UpdateField $acfService
    ) {
        $this->wpService->addAction('rest_api_init', array($this, 'registerEndpoint'));
    }

    public function registerEndpoint()
    {
        $this->wpService->registerRestRoute($this->namespace, $this->route, array(
            'methods'  => 'GET',
            'callback' => array($this, 'getFireDangerLevels'),
        ));
    }

    public function filter(array $data, string $property, mixed $value): array
    {
        if (!is_null($value) && !empty($value)) {
            $filter = array_map('trim', explode(',', $value));

            return array_values(
                array_filter($data, function ($item) use ($filter, $property) {
                    return in_array($item[$property], $filter);
                })
            );
        }
        return $data;
    }

    public function getFireDangerLevels(\WP_REST_Request $request): array
    {
        $dateTimeChanged = $this->wpService->getOption(AdminFireDangerLevels::$dateTimeChangedOptionName, null);

        $places = $this->acfService->getField('fire_danger_levels', 'option');
        $places = $this->filter($places, 'place', $request->get_param('place'));
        $places = $this->filter($places, 'level', $request->get_param('level'));

        if (count($places) === 0) {
            return [];
        }
        $places = array_map([$this, 'convertPlaceIdToName'], $places);

        return [
            'dateTimeChanged' => $dateTimeChanged,
            'places'          => $places
        ];
    }

    public function convertPlaceIdToName($fireDangerLevel)
    {
        $fireDangerLevel['place'] = $this->wpService->getTerm($fireDangerLevel['place'], '', 'object')->name;
        return $fireDangerLevel;
    }
}
