<?php
namespace Budgetcontrol\Goals\Http\Controller;

use Budgetcontrol\Library\Model\Goal;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;

class GoalController extends Controller {

    /**
     * Retrieves a goal based on the provided workspace ID.
     *
     * This method fetches goal information associated with the specified workspace.
     *
     * @param string|int $wsid The workspace identifier to retrieve goals for
     * @return Response The HTTP response containing goal data
     */
    public function get(Request $request, Response $response, $argv): Response
    {
        $wsid = $argv['wsid'];

        $goals = Goal::where('workspace_id', $wsid)
            ->orderBy('created_at', 'desc')
            ->get();

        return response($goals->toArray());
    }

    /**
     * Retrieves a specific goal based on the workspace ID and UUID.
     *
     * @param string $wsid The workspace ID where the goal is located
     * @return Response Returns the response containing the goal data
     */
    public function find(Request $request, Response $response, $argv): Response
    {
        $wsid = $argv['wsid'];
        $uuid = $argv['uuid'];

        $goal = Goal::where('workspace_id', $wsid)
            ->where('uuid', $uuid)
            ->first();

        if (!$goal) {
            return response(['error' => 'Goal not found'], 404);
        }

        return response($goal->toArray());
    }

    /**
     * Store a new goal.
     *
     * @param Request $request The incoming HTTP request
     * @param string $wdid The wallet dashboard ID
     * @return Response Returns HTTP response
     */
    public function store(Request $request, Response $response, $argv): Response
    {
        $data = $request->getParsedBody();
        $wsid = $argv['wsid'];

        if (empty($data['name']) || empty($data['amount'] || empty($wsid)) || empty($data['due_date'])) {
            return response(['error' => 'Name, amount, due_date and wsid are required'], 400);
        }

        if($data['due_date'] < date('Y-m-d')) {
            return response(['error' => 'Due date cannot be in the past'], 400);
        }

        $goal = new Goal();
        $goal->workspace_id = $wsid;
        $goal->name = $data['name'];
        $goal->amount = $data['amount'];
        $goal->category_icon = $data['icon'] ?? null;
        $goal->description = $data['description'] ?? null;
        $goal->due_date = $data['due_date'] ?? null;
        $goal->status = $data['status'] ?? 'active';
        $goal->category_icon = $data['category_icon'] ?? null;
        $goal->uuid = $data['uuid'] = Uuid::uuid4()->toString();
        $goal->save();

        return response($goal->toArray(), 201);
    }

    /**
     * Update a goal resource.
     *
     * @param Request $request The HTTP request containing goal data to update
     * @return Response The HTTP response after processing the update
     */
    public function update(Request $request, Response $response, $argv): Response
    {
        $data = $request->getParsedBody();
        $wsid = $argv['wsid'];
        $uuid = $argv['uuid'];

        $goal = Goal::where('workspace_id', $wsid)
            ->where('uuid', $uuid)
            ->first();

        if (!$goal) {
            return response(['error' => 'Goal not found'], 404);
        }

        if (isset($data['name'])) {
            $goal->name = $data['name'];
        }
        if (isset($data['amount'])) {
            $goal->amount = $data['amount'];
        }
        if (isset($data['description'])) {
            $goal->description = $data['description'];
        }
        if (isset($data['due_date'])) {
            $goal->due_date = $data['due_date'];
        }
        if (isset($data['status'])) {
            $goal->status = $data['status'];
        }
        if (isset($data['category_icon'])) {
            $goal->category_icon = $data['category_icon'];
        }

        $goal->save();

        return response($goal->toArray());
    }

    /**
     * Deletes a goal based on workspace ID and UUID.
     *
     * @param Request $request The HTTP request object
     * @return Response The HTTP response
     */
    public function delete(Request $request, Response $response, $argv): Response
    {
        $wsid = $argv['wsid'];
        $uuid = $argv['uuid'];

        $goal = Goal::where('workspace_id', $wsid)
            ->where('uuid', $uuid)
            ->first();

        if (!$goal) {
            return response(['error' => 'Goal not found'], 404);
        }

        $goal->delete();

        return response(['message' => 'Goal deleted successfully'], 204);
    }
    
}