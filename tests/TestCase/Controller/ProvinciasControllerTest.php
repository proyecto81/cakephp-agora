<?php
declare(strict_types=1);

namespace Agora\Test\TestCase\Controller;

use Agora\Controller\ProvinciasController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Agora\Controller\ProvinciasController Test Case
 *
 * @uses \Agora\Controller\ProvinciasController
 */
class ProvinciasControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Agora.Provincias',
        'plugin.Agora.Paises',
        'plugin.Agora.Estados',
        'plugin.Agora.Espacios',
        'plugin.Agora.Localidades',
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \Agora\Controller\ProvinciasController::index()
     */
    public function testIndex(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     * @uses \Agora\Controller\ProvinciasController::view()
     */
    public function testView(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     * @uses \Agora\Controller\ProvinciasController::add()
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     * @uses \Agora\Controller\ProvinciasController::edit()
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     * @uses \Agora\Controller\ProvinciasController::delete()
     */
    public function testDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
