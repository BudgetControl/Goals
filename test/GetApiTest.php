<?php

use Budgetcontrol\Core\Http\Controller\PaymentTypesController;
use Budgetcontrol\Goals\Http\Controller\GoalController;
use Budgetcontrol\Library\Model\Goal;
use MLAB\PHPITest\Entity\Json;
use MLAB\PHPITest\Assertions\JsonAssert;
use Slim\Http\Interfaces\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetApiTest extends \PHPUnit\Framework\TestCase
{

    public function test_get_data()
    {
        /** @var  \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $argv = ['wsid' => "de4f2a9c-9c0a-4ee6-ab46-fb4536fd7461"];

        $controller = new GoalController();
        $result = $controller->get($request, $response, $argv);

        $contentArray = json_decode((string) $result->getBody());
        $this->assertEquals(200, $result->getStatusCode());

        $assertionContent = new JsonAssert(new Json($contentArray));
        $assertionContent->assertJsonStructure(
            file_get_json(__DIR__ . '/../assertions/goals.json')
        );
    }

    protected function mook()
    {
        return [
            'name' => 'Education',
            'amount' => 15000.00,
            'description' => 'College education savings',
            'due_date' => '2026-08-31',
            'status' => 'active',
            'category_icon' => 'school',
        ];
    }

    public function test_store_data()
    {
        /** @var  \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $argv = ['wsid' => "de4f2a9c-9c0a-4ee6-ab46-fb4536fd7461"];
        $request->method('getParsedBody')->willReturn(
            $this->mook()
        );

        $controller = new GoalController();
        $result = $controller->store($request, $response, $argv);

        $contentArray = json_decode((string) $result->getBody());
        $this->assertEquals(201, $result->getStatusCode());

        $assertionContent = new JsonAssert(new Json($contentArray));
        $assertionContent->assertJsonStructure(
            file_get_json(__DIR__ . '/../assertions/goals.json')
        );
    }

    public function test_udpate_data()
    {
        $payload = $this->mook();
        $payload['amount'] = 20000.00; // Update amount for testing
        $payload['name'] = 'Education'; // Update name for testing
        /** @var  \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $argv = ['wsid' => "de4f2a9c-9c0a-4ee6-ab46-fb4536fd7461", 'uuid' => '9f47af2d-8c2b-4812-b164-5e58b731c106'];
        $request->method('getParsedBody')->willReturn(
            $payload
        );

        $controller = new GoalController();
        $result = $controller->update($request, $response, $argv);
        $contentResult = (array) json_decode((string) $result->getBody());

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertTrue($contentResult['amount'] == 20000.00);
        $this->assertTrue($contentResult['name'] == 'Education');
    }

    public function test_delete_data()
    {
        /** @var  \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $argv = ['wsid' => "de4f2a9c-9c0a-4ee6-ab46-fb4536fd7461", 'uuid' => '9f47af2d-8c2b-4812-b164-5e58b731c106'];

        $controller = new GoalController();
        $result = $controller->delete($request, $response, $argv);

        $model = Goal::where('uuid', '9f47af2d-8c2b-4812-b164-5e58b731c106')->first();
        $this->assertNull($model);
        $this->assertEquals(204, $result->getStatusCode());
    }

}
