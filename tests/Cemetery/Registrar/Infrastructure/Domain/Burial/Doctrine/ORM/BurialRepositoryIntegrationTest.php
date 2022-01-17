<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Crk\Scraping\Domain\EventDispatcherInterface;
use Crk\Scraping\Domain\Modules\Finisher\ScraperRunFinished;
use Crk\Scraping\Domain\Modules\Product\AbstractProduct;
use Crk\Scraping\Domain\Modules\Product\ProductCollection;
use Crk\Scraping\Domain\Modules\Product\ProductDuplicatesFound;
use Crk\Scraping\Domain\Modules\Product\ProductId;
use Crk\Scraping\Domain\Modules\Product\ProductNum;
use Crk\Scraping\Domain\Modules\Product\ProductScheduledForRemoving;
use Crk\Scraping\Domain\Modules\Product\ProductScheduledForSaving;
use Crk\Scraping\Domain\Modules\SettingManager\SettingManagerInterface;
use Crk\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\ClassMetadataCache;
use Crk\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\ColumnValuesBuilder;
use Crk\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\ProductRepository as DoctrineOrmProductRepository;
use Crk\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\DuplicateCounter;
use Crk\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\ProductDeleter;
use Crk\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\ProductInserter;
use Crk\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\Query\QueryFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialRepositoryIntegrationTest extends KernelTestCase
{
    private const TEST_SCRAPER_NAME = 'TestScraper';

    private array $entityManagerNameToListOfEntityClasses = [];

    private MockObject|YamlParser $mockYamlParser;

    private ManagerRegistry $managerRegistry;

    private MockObject|SettingManagerInterface $mockSettingManager;

    private ClassMetadataCache $classMetadataCache;

    private ProductInserter $productInserter;

    private ProductDeleter $productDeleter;

    private MockObject|EventDispatcherInterface $mockEventDispatcher;

    private MockObject|LoggerInterface $mockLogger;

    private DoctrineOrmProductRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;

        $this->mockYamlParser      = $this->buildMockYamlParser();
        $this->managerRegistry     = $container->get(ManagerRegistry::class);
        $this->mockSettingManager  = $this->createMock(SettingManagerInterface::class);
        $this->classMetadataCache  = $container->get(ClassMetadataCache::class);
        $queryFactory              = $container->get(QueryFactory::class);
        $this->mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->productInserter     = new ProductInserter(
            $queryFactory,
            $container->get(ColumnValuesBuilder::class),
            $this->mockEventDispatcher,
            $this->createMock(DuplicateCounter::class),
        );
        $this->productDeleter = new ProductDeleter($queryFactory);
        $this->mockLogger     = $this->createMock(LoggerInterface::class);
        $this->repo           = new DoctrineOrmProductRepository(
            'this/filename/is/not/used/for/testing.yaml',
            $this->mockYamlParser,
            $this->managerRegistry,
            $this->mockSettingManager,
            $this->classMetadataCache,
            $this->productInserter,
            $this->productDeleter,
            $this->mockEventDispatcher,
            $this->mockLogger,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewProductButDoesNotFlushEm(): void
    {
        $this->initializeRepoSettings(PHP_INT_MAX, true, false);

        $product = TestProductFactory::createRandomTestFooProduct();
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) use ($product) {
                return $arg instanceof ProductScheduledForSaving && $arg->getProduct() === $product;
            })
        );
        $this->repo->save($product);

        $persistedProduct = $this->repo->findById(TestFooProduct::class, $product->getId());
        $this->assertInstanceOf(TestFooProduct::class, $persistedProduct);
        $this->assertSame((string) $product->getId(), (string) $persistedProduct->getId());
        $this->assertInstanceOf(ProductNum::class, $persistedProduct->getProductNum());
        $this->assertSame((string) $product->getProductNum(), (string) $persistedProduct->getProductNum());
        $this->assertSame(0, $this->getRowCount(TestFooProduct::class));
    }

    public function testItSavesNewProductsAndFlushEmWhenFlushCountValueReached(): void
    {
        $this->initializeRepoSettings(3, true, false);

        $productA = TestProductFactory::createRandomTestBazProduct();
        $productB = TestProductFactory::createRandomTestBazProduct();
        $this->repo->save($productA);
        $this->repo->save($productB);
        $this->assertSame(0, $this->getRowCount(TestBazProduct::class));

        $productC = TestProductFactory::createRandomTestBazProduct();
        $this->repo->save($productC);
        $this->assertSame(3, $this->getRowCount(TestBazProduct::class));
    }

    public function testItSavesACollectionOfNewProductsAndFlushEmWhenFlushCountValueReached(): void
    {
        $this->initializeRepoSettings(3, true, false);

        $productA = TestProductFactory::createRandomTestFooProduct();
        $productB = TestProductFactory::createRandomTestFooProduct();
        $productC = TestProductFactory::createRandomTestFooProduct();
        $productD = TestProductFactory::createRandomTestBazProduct();
        $productE = TestProductFactory::createRandomTestBazProduct();
        $productF = TestProductFactory::createRandomTestBazProduct();
        $this->repo->saveAll(
            new ProductCollection(
                [$productA, $productB, $productC, $productD, $productE, $productF]
            )
        );

        $this->assertSame(3, $this->getRowCount(TestFooProduct::class));
        $this->assertSame(3, $this->getRowCount(TestBazProduct::class));
        $this->assertNotNull($this->repo->findById(TestFooProduct::class, $productA->getId()));
        $this->assertNotNull($this->repo->findById(TestFooProduct::class, $productB->getId()));
        $this->assertNotNull($this->repo->findById(TestFooProduct::class, $productC->getId()));
        $this->assertNotNull($this->repo->findById(TestBazProduct::class, $productD->getId()));
        $this->assertNotNull($this->repo->findById(TestBazProduct::class, $productE->getId()));
        $this->assertNotNull($this->repo->findById(TestBazProduct::class, $productF->getId()));
    }

    public function testItRemovesProductsAndFlushEmWhenFlushCountValueReached(): void
    {
        $this->initializeRepoSettings(1, true, false);

        // Prepare the repo for testing
        $product = TestProductFactory::createRandomTestBazProduct();
        $this->repo->save($product);
        $this->assertSame(1, $this->getRowCount(TestBazProduct::class));

        // Testing itself
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) use ($product) {
                $isEventInstanceOfProductScheduledForRemoving = $arg instanceof ProductScheduledForRemoving;
                /** @var ProductScheduledForRemoving $arg */
                $removedProduct   = $arg->getProduct();
                $isTheSameProduct = $removedProduct->getId()->isEqual($product->getId());

                return $isEventInstanceOfProductScheduledForRemoving && $isTheSameProduct;
            })
        );
        $persistedProduct = $this->repo->findById(TestBazProduct::class, $product->getId());
        $this->repo->remove($persistedProduct);
        $this->assertSame(0, $this->getRowCount(TestBazProduct::class));
    }

    public function testItRemovesACollectionOfProductsAndFlushEmWhenFlushCountValueReached(): void
    {
        $this->initializeRepoSettings(3, true, false);

        // Prepare the repo for testing
        $productA = TestProductFactory::createRandomTestFooProduct();
        $productB = TestProductFactory::createRandomTestFooProduct();
        $productC = TestProductFactory::createRandomTestFooProduct();
        $productD = TestProductFactory::createRandomTestFooProduct();
        $productE = TestProductFactory::createRandomTestFooProduct();
        $productF = TestProductFactory::createRandomTestFooProduct();
        $this->repo->saveAll(
            new ProductCollection(
                [$productA, $productB, $productC, $productD, $productE, $productF]
            )
        );
        $this->assertSame(6, $this->getRowCount(TestFooProduct::class));

        // Testing itself
        $persistedProductB = $this->repo->findById(TestFooProduct::class, $productB->getId());
        $persistedProductC = $this->repo->findById(TestFooProduct::class, $productC->getId());
        $persistedProductD = $this->repo->findById(TestFooProduct::class, $productD->getId());
        $this->repo->removeAll(
            new ProductCollection(
                [$persistedProductB, $persistedProductC, $persistedProductD]
            )
        );
        $this->assertSame(3, $this->getRowCount(TestFooProduct::class));
        $this->assertNotNull($this->repo->findById(TestFooProduct::class, $productA->getId()));
        $this->assertNotNull($this->repo->findById(TestFooProduct::class, $productE->getId()));
        $this->assertNotNull($this->repo->findById(TestFooProduct::class, $productF->getId()));
    }

    public function testItFindsAProductById(): void
    {
        $this->initializeRepoSettings(3, true, false);

        // Prepare the repo for testing
        $productA = TestProductFactory::createRandomTestFooProduct();
        $productB = TestProductFactory::createRandomTestFooProduct();
        $productC = TestProductFactory::createRandomTestFooProduct();
        $this->repo->saveAll(new ProductCollection([$productA, $productB, $productC]));

        // Testing itself
        $persistedProduct = $this->repo->findById(TestFooProduct::class, $productB->getId());
        $this->assertInstanceOf(TestFooProduct::class, $persistedProduct);
        $this->assertSame((string) $productB->getId(), (string) $persistedProduct->getId());
    }

    public function testItReturnsNullIfAProductIsNotFoundById(): void
    {
        $product = $this->repo->findById(TestFooProduct::class, new ProductId('unknown_id'));

        $this->assertNull($product);
    }

    public function testItAddsListeners(): void
    {
        $this->mockEventDispatcher->expects($this->exactly(1))->method('addListener')->withConsecutive(
            [
                $this->equalTo(ScraperRunFinished::class),
                $this->callback(function (callable $arg) {
                    return is_array($arg) && isset($arg[0], $arg[1]) &&
                        $arg[0] instanceof DoctrineOrmProductRepository && $arg[1] === 'onScraperRunFinished';
                })
            ],
        );
        new DoctrineOrmProductRepository(
            'this/filename/is/not/used/for/testing.yaml',
            $this->mockYamlParser,
            $this->managerRegistry,
            $this->mockSettingManager,
            $this->classMetadataCache,
            $this->productInserter,
            $this->productDeleter,
            $this->mockEventDispatcher,
            $this->mockLogger,
        );
    }

    public function testItFailsWhenTryToFindAProductOfInvalidClass(): void
    {
        $fakeProductClass = 'someFakeProductClass';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf('Product class "%s" is not a subclass of "%s"', $fakeProductClass, AbstractProduct::class)
        );
        $this->repo->findById($fakeProductClass, new ProductId('777'));
    }

    public function testItFailsWhenEmMapFileParsingFailed(): void
    {
        $someFilename   = 'some/file.yaml';
        $mockYamlParser = $this->createMock(YamlParser::class);
        $mockYamlParser
            ->method('parseFile')
            ->willThrowException(new ParseException('some exception messages'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            \sprintf('Entity manager map file "%s" could not be read or YAML is invalid.', $someFilename)
        );
        new DoctrineOrmProductRepository(
            $someFilename,
            $mockYamlParser,
            $this->managerRegistry,
            $this->mockSettingManager,
            $this->classMetadataCache,
            $this->productInserter,
            $this->productDeleter,
            $this->mockEventDispatcher,
            $this->mockLogger,
        );
    }

    public function testItFailsWhenEmMapFileContainsNotAnArray(): void
    {
        $someFilename   = 'some/file.yaml';
        $mockYamlParser = $this->createMock(YamlParser::class);
        $mockYamlParser
            ->method('parseFile')
            ->willReturn('some string that is not an array at all');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            \sprintf('Entity manager map file "%s" contains no data.', $someFilename)
        );

        new DoctrineOrmProductRepository(
            $someFilename,
            $mockYamlParser,
            $this->managerRegistry,
            $this->mockSettingManager,
            $this->classMetadataCache,
            $this->productInserter,
            $this->productDeleter,
            $this->mockEventDispatcher,
            $this->mockLogger,
        );
    }

    public function testItFailsWhenNoEntityManagerConfiguredForAProductClass(): void
    {
        $productClass = TestBarProduct::class;
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            \sprintf('No entity manager configured for the product class "%s".', $productClass)
        );

        $this->repo->findById($productClass, new ProductId('777'));
    }

    public function testItFailsToSaveDuplicateProductWhenDuplicatesShouldNotBeHandled(): void
    {
        $this->initializeRepoSettings(1, false, false);

        $productFoo = TestProductFactory::createRandomTestFooProduct();
        $this->repo->save($productFoo);

        $this->expectException(UniqueConstraintViolationException::class);
        $this->repo->save($productFoo);
    }

    private function buildMockYamlParser(): MockObject|YamlParser
    {
        $this->entityManagerNameToListOfEntityClasses = [
            'sqlite_main1' => [
                'Crk\Tests\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\TestFooProduct',
            ],
            'sqlite_main2' => [
                'Crk\Tests\Scraping\Infrastructure\Domain\Modules\Product\Doctrine\ORM\TestBazProduct',
            ],
        ];
        $mockYamlParser = $this->createMock(YamlParser::class);
        $mockYamlParser->method('parseFile')->willReturn($this->entityManagerNameToListOfEntityClasses);

        return $mockYamlParser;
    }

    private function getRowCount(string $productClass): ?int
    {
        $rowCount                = null;
        $targetEntityManagerName = null;
        foreach ($this->entityManagerNameToListOfEntityClasses as $entityManagerName => $listOfEntityClasses) {
            foreach ($listOfEntityClasses as $entityClass) {
                if ($entityClass === $productClass) {
                    $targetEntityManagerName = $entityManagerName;
                    break 2;
                }
            }
        }
        if ($targetEntityManagerName) {
            $rowCount = (int) $this->getEntityManager($targetEntityManagerName)
                ->getRepository($productClass)
                ->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $rowCount;
    }

    private function truncateEntities(): void
    {
        foreach (\array_keys($this->entityManagerNameToListOfEntityClasses) as $entityManagerName) {
            (new ORMPurger($this->getEntityManager($entityManagerName)))->purge();
        }
    }

    private function getEntityManager(string $name): EntityManagerInterface
    {
        return $this->managerRegistry->getManager($name);
    }

    private function initializeRepoSettings(int $flushCount, bool $handleDuplicates, bool $updateIfExists): void
    {
        $this->mockSettingManager->method('get')->willReturnMap([
            ['product', 'flush_count', $flushCount],
            ['product', 'handle_duplicates', $handleDuplicates],
            ['product', 'update_if_exists', $updateIfExists],
        ]);
        $this->repo->configure(self::TEST_SCRAPER_NAME);
    }
}
