<?php
use Firebase\JWT\JWT;

class AuthController {
    private $userRepository;
    private $request;
    private $secretKey;

    public function __construct(DataRepositoryInterface $userRepository, RequestInterface $request, $secretKey) {
        $this->userRepository = $userRepository;
        $this->request = $request;
        $this->secretKey = $secretKey;
    }

    public function register() {
        $data = $this->request->getBody();
        
        // Validate required fields
        if (!isset($data['username']) || !isset($data['password']) || !isset($data['email'])) {
            return new Response(400, json_encode(['error' => 'Username, password and email are required']));
        }
        
        // Check if username already exists
        $existingUser = $this->userRepository->table('users')
            ->where('username', $data['username'])
            ->get();
            
        if (!empty($existingUser)) {
            return new Response(409, json_encode(['error' => 'Username already exists']));
        }
        
        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Create the user
        $this->userRepository->table('users')->insert([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return new Response(201, json_encode(['message' => 'User registered successfully']));
    }

    public function login() {
        $data = $this->request->getBody();
        
        // Validate required fields
        if (!isset($data['username']) || !isset($data['password'])) {
            return new Response(400, json_encode(['error' => 'Username and password are required']));
        }
        
        // Find user by username
        $user = $this->userRepository->table('users')
            ->where('username', $data['username'])
            ->get();
            
        if (empty($user) || !password_verify($data['password'], $user['password'])) {
            return new Response(401, json_encode(['error' => 'Invalid credentials']));
        }
        
        // Generate JWT token
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Token valid for 1 hour
        
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $user['id'],
            'username' => $user['username']
        ];
        
        $token = JWT::encode($payload, $this->secretKey, 'HS256');
        
        // Update user's token in database
        $this->userRepository->table('users')
            ->where('id', $user['id'])
            ->update([
                'token' => $token,
                'token_expires_at' => date('Y-m-d H:i:s', $expirationTime)
            ]);
            
        return new Response(200, json_encode([
            'token' => $token,
            'expires_at' => $expirationTime
        ]));
    }
}