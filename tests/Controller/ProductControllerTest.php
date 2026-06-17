<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $productRepository;
    private string $path = '/products';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->productRepository = $this->manager->getRepository(Product::class);

        foreach ($this->productRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
        $this->manager->clear();
    }

    public function testIndexLoads(): void
    {
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
    }

    public function testNewCreatesProduct(): void
    {
        $this->client->request('GET', sprintf('%s/new', $this->path));
        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Salvar', [
            'product[name]' => 'Testing',
            'product[description]' => 'Testing description',
            'product[image]' => 'https://example.com/img.png',
            'product[price]' => '10.50',
            'product[stockQuantity]' => 5,
        ]);

        self::assertResponseRedirects('/products', 303);
        self::assertGreaterThanOrEqual(1, $this->productRepository->count([]));
    }
}
