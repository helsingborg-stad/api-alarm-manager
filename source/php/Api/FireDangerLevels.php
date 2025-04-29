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

use function PHPUnit\Framework\isNull;

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

    public function getFireDangerLevels(\WP_REST_Request $request): array
    {
        $data            = $this->acfService->getField('fire_danger_levels', 'option');
        $dateTimeChanged = $this->wpService->getOption(AdminFireDangerLevels::$dateTimeChangedOptionName, null);
        $places          =  is_array($data) ? array_map([$this, 'convertPlaceIdToName'], $data) : [];
        $input           = $request->get_param('place');

        if (!is_null($input) && !empty($input)) {
            $filter = explode(',', $input);
            $filter = array_map('trim', $filter);
            $filter = array_map('strtolower', $filter);

            $places = array_filter($places, function ($item) use ($filter) {
                return in_array(strtolower($item['place']), $filter);
            });
        }

        if (count($places) === 0) {
            trigger_error('No fire danger levels found', E_USER_WARNING);
            return [];
        }

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
