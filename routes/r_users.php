<?php
	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;
	use \Firebase\JWT\JWT;
	use \objects\Users;
	use \utils\Validate;

	//[GET]

	$app->get("/users", function (Request $request, Response $response, array $args) {
		$users = new Users($this->get("db"));
		$resp = $users->getUsers()->getResult();
		$response->getBody()->write(json_encode($resp));
		return $response
			->withHeader("Content-Type", "application/json")
			->withStatus($resp->ok ? 200 : 409);
	});

	$app->get("/user/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
		$users = new Users($this->get("db"));
		$resp = $users->getUser($args["id"])->getResult();
		$response->getBody()->write(json_encode($resp));
		return $response
			->withHeader("Content-Type", "application/json")
			->withStatus($resp->ok ? 200 : 409);
	});

	//[POST]

	$app->post("/user", function (Request $request, Response $response, array $args) {
		$fields = $request->getParsedBody();
		
		$verificar = [
			"email" => [
				"type" => "string",
				"isValidMail" => true,
				"unique" => "users"
			],
			"firstname" => [
				"type" => "string",
				"min" => 3,
				"max" => 50
			],
			"lastname" => [
				"type" => "string",
				"min" => 3,
				"max" => 50
			],
			"password" => [
				"type" => "string",
				"min" => 3,
				"max" => 20
			]
		];

		$validacion = new Validate($this->get("db"));
		$validacion->validar($fields, $verificar);

		$resp = null;

		if($validacion->hasErrors()) {
			$resp = $validacion->getErrors();
		} else {
			$users = new Users($this->get("db"));
			$resp = $users->setUser($fields)->getResult();
		}

		$response->getBody()->write(json_encode($resp));
		return $response
			->withHeader("Content-Type", "application/json")
			->withStatus($resp->ok ? 200 : 409);
	});

	$app->post("/user/login", function(Request $request, Response $response, array $args) {
		$fields = $request->getParsedBody();
		
		$verificar = [
			"email" => [
				"type" => "string",
				"isValidMail" => true
			],
			"password" => [
				"type" => "string",
				"min" => 3,
				"max" => 20
			]
		];

		$validacion = new Validate();
		$validacion->validar($fields, $verificar);

		$resp = new \stdClass();
		$resp->ok = false;
		$resp->msg = "Nombre de usuario o contraseña incorrecto.";
		$resp->data = null;

		if($validacion->hasErrors()) {
			$resp = $validacion->getErrors();
		} else {
			$users = new Users($this->get("db"));
			$existe = $users->userExist($fields["email"]);
			if($existe && password_verify($fields["password"], $existe->password)) {
				$iss = "http://hansjal.com";
				$aud = "http://hansjal.com";
				$iat = time();
				$exp = $iat + (3600 * 2); // Expire (2Hs)
				$nbf = $iat;
				$token = array(
					"iss" => $iss,
					"aud" => $aud,
					"iat" => $iat,
					"exp" => $exp,
					"nbf" => $nbf,
					"data" => array(
						"id" => $existe->id,
						"firstname" => $existe->firstname,
						"lastname" => $existe->lastname,
						"email" => $existe->email
					)
				);
				unset($existe->password);
				$jwt = JWT::encode($token, $_ENV["JWT_SECRET_KEY"], $_ENV["JWT_ALGORITHM"]);
				$existe->jwt = "Bearer " . $jwt;
				$resp->ok = true;
				$resp->msg = "Usuario autorizado.";
				$resp->data = $existe;
			}
		}

		$response->getBody()->write(json_encode($resp));
		return $response
			->withHeader("Content-Type", "application/json")
			->withStatus($resp->ok ? 200 : 401);
    });

	//[PATCH]

	//[DELETE]

	$app->delete("/user/{id:[0-9]+}", function (Request $request, Response $response, array $args) {
		$users = new Users($this->get("db"));
		$resp = $users->deleteUser($args["id"])->getResult();
		$response->getBody()->write(json_encode($resp));
		return $response
			->withHeader("Content-Type", "application/json")
			->withStatus($resp->ok ? 200 : 409);
	});

?>
