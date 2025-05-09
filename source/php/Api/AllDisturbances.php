<?php

namespace ApiAlarmManager\Api;

use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetTerm;
use WpService\Contracts\RegisterRestRoute;
use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;

class AllDisturbances extends \ApiAlarmManager\Entity\ApiFields
{
    private $namespace = 'wp/v2';
    private $route     = '/all-disturbances';

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
            'callback' => array($this, 'getApiResponse'),
        ));
    }

    public function getApiResponse(\WP_REST_Request $request): array
    {
        $api_1 = new Disturbances($this->wpService, $this->acfService);
        $api_2 = new FireDangerLevels($this->wpService, $this->acfService);

        return [
            'disturbances'    => $api_1->getDisturbances($request),
            'firedangerlevel' => $api_2->getFireDangerLevels($request),
        ];
    }
}
