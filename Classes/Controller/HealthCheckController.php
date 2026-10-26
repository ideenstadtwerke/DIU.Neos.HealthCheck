<?php
namespace DIU\Neos\HealthCheck\Controller;

use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;
use Neos\Neos\Domain\Repository\SiteRepository;
use Neos\Neos\Domain\Service\SiteNodeUtility;

/**
 * Controller for healthcheck
 *
 * @Flow\Scope("singleton")
 */
class HealthCheckController extends ActionController
{

    #[Flow\Inject]
    protected SiteRepository $siteRepository;

    #[Flow\Inject]
    protected SiteNodeUtility $siteNodeUtility;

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * Resolves the live site node to make sure database and content repository are reachable.
     * Any failure surfaces as an uncaught exception and therefore as HTTP 500.
     */
    public function checkAction(): void
    {
        $site = $this->siteRepository->findDefault()
            ?? throw new \RuntimeException('Health check failed: no site configured', 1761487200);
        $this->siteNodeUtility->findSiteNodeBySite(
            $site,
            WorkspaceName::forLive(),
            $site->getConfiguration()->defaultDimensionSpacePoint
        );
        $this->view->assign('value', ['status' => 'OK']);
    }

}
