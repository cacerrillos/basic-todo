<?php
// Routes

$app->get('/fetch', function($request, $response, $args) {
  $data = array();
  if($stmt = $this->db->prepare("SELECT `id`, `text`, `done`, `hidden` FROM `list`;")) {
    $stmt->execute();
    $stmt->bind_result($id, $text, $done, $hidden);
    while($stmt->fetch()) {
      array_push($data, array("id" => $id, "text" => $text, "done" => $done, "hidden" => $hidden));
    }
    $stmt->close();
  }
  return $response->withJson($data, 200);
});

$app->post('/update', function($request, $response, $args) {
  $result = array("status" => false);
  $http_status =  200;
  $body = $request->getParsedBody();
  $done = $body['done'] == "true";
  if($stmt = $this->db->prepare("UPDATE `list` SET `text` = ?, `done` = ? WHERE `id` = ? LIMIT 1;")) {
    $stmt->bind_param("sii", $body['text'], $done, $body['id']);
    if($stmt->execute()) {
      $result['status'] = $stmt->affected_rows == 1;
    }
    $stmt->close();
  }
  return $response->withJson($result, $http_status);
});

$app->post('/add', function($request, $response, $args) {
  $result = array("status" => false);
  $http_status =  200;
  $body = $request->getParsedBody();
  if($stmt = $this->db->prepare("INSERT INTO `list` (`text`, `done`, `hidden`) VALUES (?, 0, 0);")) {
    $stmt->bind_param("s", $body['text']);
    if($stmt->execute()) {
      $result['status'] = $stmt->affected_rows == 1;
    }
    $stmt->close();
  }
  return $response->withJson($result, $http_status);
});

$app->post('/delete', function($request, $response, $args) {
  $result = array("status" => false);
  $http_status =  200;

  $body = $request->getParsedBody();

  if($stmt = $this->db->prepare("UPDATE `list` SET `hidden` = 1 WHERE `id` = ? LIMIT 1;")) {
    $stmt->bind_param("i", $body['id']);
    $stmt->execute();
    $stmt->close();
  }
  

  return $response->withJson($result, $http_status);
});