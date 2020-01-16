<?php

namespace App\Controllers;

use App\Controllers\Dtos\DomainDto;
use App\Controllers\Params\GetDomainPricesParams;
use App\Domains\Models\Domain;
use App\Domains\Models\Tld;
use App\Domains\Repositories\DomainRepository;
use App\Domains\Repositories\TldRepository;
use App\Utils\Domains;
use Yii;
use yii\base\Module;
use yii\filters\ContentNegotiator;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class DomainsController extends Controller
{
    /**
     * @var DomainRepository
     */
    private $domainRepository;

    /**
     * @var TldRepository
     */
    private $tldRepository;

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    public function __construct(string $id, Module $module, TldRepository $tldRepository, DomainRepository $domainRepository, array $config = [])
    {
        $this->domainRepository = $domainRepository;
        $this->tldRepository = $tldRepository;

        parent::__construct($id, $module, $config);
    }

    /**
     * Get domain with prices
     *
     * @return DomainDto[]
     * @throws BadRequestHttpException
     */
    public function actionCheck(): array
    {
        // проверить домены на корректность имени
        $params = new GetDomainPricesParams();
        if (!$params->load(Yii::$app->request->get(), '') || !$params->validate()) {
            throw new BadRequestHttpException();
        }

        $search = mb_strtolower(trim(Yii::$app->request->get('search')));

        // список tld из таблицы
        /** @var Tld[] $tlds */
        $tlds = $this->tldRepository->findAll();

        $priceTlds = [];
        foreach ($tlds as $tld) {
            $priceTlds[$tld->getTld()] = $tld->getPrice();
        }

        // список доменов
        $searchDomains = Domains::fromNamesAndTlds([$search], array_keys($priceTlds));

        // наличие домена в таблице domain
        /** @var Domain[] $domains */
        $domains = $this->domainRepository->findByDomains($searchDomains);

        $existingDomains = array_map(function ($domain) {
            return $domain->getDomain();
        }, $domains);

        // список dto с ценами для списка доменов
        /** @var DomainDto[] $dtos */
        $dtos = [];
        foreach ($searchDomains as $searchDomain) {
            $tld = Domains::getTld($searchDomain);
            $price = $priceTlds[$tld];
            $available = !in_array($searchDomain, $existingDomains);
            $dtos[] = new DomainDto($tld, $searchDomain, $price, $available);
        }

        return $dtos;
    }
}
