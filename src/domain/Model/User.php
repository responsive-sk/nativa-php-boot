<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * User Entity
 */
class User
{
    private string $id;
    private string $name;
    private string $email;
    private string $password;
    private string $role;
    private ?string $avatar;
    private string $createdAt;
    private string $updatedAt;

    private function __construct()
    {
    }

    public static function create(
        string $name,
        string $email,
        string $password,
        string $role = 'user',
        ?string $avatar = null,
    ): self {
        $user = new self();
        $user->id = self::generateId();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password; // Should be hashed before calling
        $user->role = $role;
        $user->avatar = $avatar;
        $user->createdAt = self::now();
        $user->updatedAt = self::now();

        return $user;
    }

    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->id = $data['id'];
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = $data['role'] ?? 'user';
        $user->avatar = $data['avatar'] ?? null;
        $user->createdAt = $data['created_at'];
        $user->updatedAt = $data['updated_at'];

        return $user;
    }

    public function update(
        ?string $name = null,
        ?string $email = null,
        ?string $avatar = null,
    ): void {
        if ($name !== null) {
            $this->name = $name;
        }

        if ($email !== null) {
            $this->email = $email;
        }

        if ($avatar !== null) {
            $this->avatar = $avatar;
        }

        $this->updatedAt = self::now();
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = $newPassword; // Should be hashed before calling
        $this->updatedAt = self::now();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Getters
    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function avatar(): ?string
    {
        return $this->avatar;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function updatedAt(): string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    private static function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
