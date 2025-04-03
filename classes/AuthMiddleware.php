<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class AuthMiddleware {
    private $userRepository;
    private $secretKey;

    public function __construct(DataRepositoryInterface $userRepository, $secretKey) {
        $this->userRepository = $userRepository;
        $this->secretKey = $secretKey;
    }

    public function authenticate(callable $next) {
        return function() use ($next) {
            $headers = getallheaders();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
            
            if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                return new Response(401, json_encode(['error' => 'No token provided or invalid format']));
            }
            
            $token = $matches[1];
            
            try {
                $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
                
                // Check if token is in database and not expired
                $user = $this->userRepository->table('users')
                    ->where('id', $decoded->user_id)
                    ->where('token', $token)
                    ->get();
                    
                if (empty($user)) {
                    return new Response(401, json_encode(['error' => 'Invalid token']));
                }
                
                // Call the original handler with the decoded token info
                return call_user_func($next, $decoded);
                
            } catch (ExpiredException $e) {
                return new Response(401, json_encode(['error' => 'Token expired']));
            } catch (Exception $e) {
                return new Response(401, json_encode(['error' => 'Invalid token: ' . $e->getMessage()]));
            }
        };
    }
}