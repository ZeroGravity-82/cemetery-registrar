<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Delivery\Web\Controller;

use DataFixtures\Burial\BurialFixtures;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumFixtures;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumNicheFixtures;
use DataFixtures\BurialPlace\GraveSite\CemeteryBlockFixtures;
use DataFixtures\BurialPlace\GraveSite\GraveSiteFixtures;
use DataFixtures\BurialPlace\MemorialTree\MemorialTreeFixtures;
use DataFixtures\CauseOfDeath\CauseOfDeathFixtures;
use DataFixtures\FuneralCompany\FuneralCompanyFixtures;
use DataFixtures\NaturalPerson\NaturalPersonFixtures;
use DataFixtures\Organization\JuristicPerson\JuristicPersonFixtures;
use DataFixtures\Organization\SoleProprietor\SoleProprietorFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group browser
 * @group functional
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SmokeFunctionalTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $container    = self::getContainer();

        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);
        $this->databaseTool     = $databaseToolCollection->get();

        $this->loadFixtures();
    }

    /**
     * @dataProvider getPageUrlTestData
     */
    public function testItLoadsPageSuccessfully($url): void
    {
        $this->client->request(Request::METHOD_GET, $url);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    private function getPageUrlTestData(): iterable
    {
        yield ['/'];
        yield ['/burial'];
        yield ['/burial/edit/B001'];
        yield ['/natural-person/list-alive'];
        yield ['/admin/dashboard'];
        yield ['/admin/natural-person'];
        yield ['/admin/organization'];
        yield ['/admin/funeral-company'];
        yield ['/admin/cause-of-death'];
        yield ['/admin/cause-of-death/CD001'];
        yield ['/admin/burial-place/grave-site'];
        yield ['/admin/burial-place/grave-site/GS001'];
        yield ['/admin/burial-place/columbarium-niche'];
        yield ['/admin/burial-place/memorial-tree'];
    }

    private function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            BurialFixtures::class,
            CauseOfDeathFixtures::class,
            CemeteryBlockFixtures::class,
            ColumbariumFixtures::class,
            ColumbariumNicheFixtures::class,
            FuneralCompanyFixtures::class,
            GraveSiteFixtures::class,
            JuristicPersonFixtures::class,
            MemorialTreeFixtures::class,
            NaturalPersonFixtures::class,
            SoleProprietorFixtures::class,
        ]);
    }
}
