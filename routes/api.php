<?php
/**
 *  application apps
 */

$app->get('/monitor', \Budgetcontrol\Goals\Http\Controller\GoalController::class . ':monitor');
$app->get('/{wsid}', \Budgetcontrol\Goals\Http\Controller\GoalController::class . ':get');
$app->get('/{wsid}/{uuid}', \Budgetcontrol\Goals\Http\Controller\GoalController::class . ':find');
$app->post('/{wsid}', \Budgetcontrol\Goals\Http\Controller\GoalController::class . ':store');
$app->put('/{wsid}/{uuid}', \Budgetcontrol\Goals\Http\Controller\GoalController::class . ':update');
$app->delete('/{wsid}/{uuid}', \Budgetcontrol\Goals\Http\Controller\GoalController::class . ':delete');
$app->patch('/{wsid}/{uuid}', \Budgetcontrol\Goals\Http\Controller\GoalController::class . ':updateStatus');