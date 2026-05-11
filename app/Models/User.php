<?php

namespace App\Models;

use App\Core\AbstractModel;
use App\Models\Role\Role;
use App\Models\Role\RolePermission;
use http\Exception\InvalidArgumentException;

class User extends AbstractModel
{
    protected string $table = 'users';

    protected string $primaryKey = 'id';

    protected array $fillable = [
        "name",
        "email",
        "password",
        "document",
        "role_id",
        "role",
        "last_login_at",
        "status",
        "reset_token",
        "reset_expires_at"
    ];

    protected array $required = [
        "name" => "O campo NOME é obrigatório ",
        "email" => "O campo EMAIL é obrigatório",
        "password" => "O campo SENHA é obrigatório",
        "role_id" => "o campo PERFIL é obrigatório"
    ];

    public const TECHNICIAN = "tecnico";

    public const  TEACHER = "professor";

    private const ROLES = [
        self::TECHNICIAN,
        self::TEACHER
    ];


    public const  REGISTERED = "registrado";
    public const ACTIVE = "ativo";
    public const  INACTIVE = "inativo";

    private const STATUS = [
        self::REGISTERED,
        self::ACTIVE,
        self::INACTIVE
    ];

    protected bool $timestamps = true;

    protected bool $softDelete = true;


    public function getId()
    {
        return $this->attributes["id"];
    }

    public function setName(string $name): void
    {
        $name = trim(strip_tags($name));

        if (strlen($name) < 3) {
            throw new \InvalidArgumentException("O campo nome deve ter pelo menos de 3 caracteres");
        }

        $this->attributes["name"] = $name;

    }

    public function getName(): ?string
    {
        return $this->attributes["name"];
    }

    public function setEmail(string $email): void
    {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            throw new \InvalidArgumentException("O Email é inválido");
        }

        $this->attributes["email"] = $email;
    }

    public function getEmail(): ?string
    {
        return $this->attributes["email"];
    }

    public function setPassword(string $password): void
    {
        if ($password === null || $password === "") {
            throw new \InvalidArgumentException("A senha não pode ser nula ou vazia");
        }

        if (strlen($password) < 8 || strlen($password) > 16) {
            throw new \InvalidArgumentException("A senha deve ter ente 8 e 16 caracteres");
        }

        $this->attributes["password"] = password_hash($password, PASSWORD_DEFAULT);

    }

    public function getPassword(): ?string
    {
        return $this->attributes["password"];
    }

    public function passwordVerify(string $password): bool
    {
        return password_verify($password, $this->attributes["password"] ?? null);
    }

    public function setDocument(?string $document): void
    {
        if ($document) {
            $document = preg_replace("/[^0-9]/", '', $document);

            if (strlen($document) !== 11) {
                throw new \InvalidArgumentException("O campo documento deve ter exatamente 11 caracteres");
            }
        }

        $this->attributes["document"] = $document;
    }

    public function getDocument(): ?string
    {
        return $this->attributes["document"];
    }

    public function setRoleId(int $roleId): void
    {
        if ($roleId < 1) {
            throw new \InvalidArgumentException("O ID do perfil do usuário é inválido");
        }

        $this->attributes["role_id"] = $roleId;
    }

    public function getRoleId(): int
    {
        return $this->attributes["role_id"];
    }

    public function role(): ?Role
    {
        return $this->getRoleId() ? Role::find($this->getRoleId()) : null;
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->getRoleId()) {
            return false;
        }

        return RolePermission::userHasPermission($this->getRoleId(), $permission);
    }



    public function setRole(?string $role): void
    {
        $role = $role ?? self::TEACHER;

        if (!in_array($role, self::ROLES)) {

            throw new \InvalidArgumentException("O perfil é inválido");

        }
        $this->attributes["role"] = $role;
    }

    public function setLastLoginAt(): void
    {
        $timezone = new \DateTimeZone(APP_TIMEZONE);
        $now = new \DateTimeImmutable("now", $timezone);
        $this->attributes["last_login_at"] = $now->format("Y-m-d h:i:s");
    }

    public function getLastLoginAt(): ?string
    {
        return $this->attributes["last_login_at"];
    }

    public function getRole(): ?string
    {
        return $this->attributes["role"];
    }

    public function setStatus(?string $status): void
    {
        $status = $status ?? self::REGISTERED;

        if (!in_array($status, self::STATUS)) {

            throw new \InvalidArgumentException("O status é inválido");

        }

        $this->attributes["status"] = $status;

    }

    public function getStatus(): ?string
    {
        return $this->attributes["status"];
    }

    public static function findByEmail(?string $email): ?self
    {
        return (new static())->where('email', "=", $email)->first();
    }

    public function schools(): array
    {
        return (new SchoolUser())
            ->where("user_id", "=", $this->getId())
            ->get();
    }

    public function setResetToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->attributes["reset_token"] = hash("sha256", $token);
        $this->setResetExpiresAt();
        return $token;
    }

    public function getResetToken(): ?string
    {
        return $this->attributes["reset_token"] ?? null;
    }

    public function setResetExpiresAt(): void
    {
        $timezone = new \DateTimeZone(APP_TIMEZONE);
        $expiresAt = new \DateTimeImmutable("now", $timezone);
        $this->attributes["reset_expires_at"] = $expiresAt->modify("+2 hours")->format("Y-m-d H:i:s");
    }

    public function getResetExpiresAt(): ?string
    {
        return $this->attributes["reset_expires_at"] ?? null;
    }

    public static function findByResetToken(string $token): ?self
    {
        $hash = hash("sha256", $token);
        return (new static())->where("reset_token", "=", $hash)->first();
    }

    public function findByName(string $name): ?self
    {
        return $this->where("name", "=", $name)
            ->first()
            ->orderBy("name");
    }

    public static function userByRole(string $role): ?array
    {
        return (new static())->where("role", "=", $role)->get();
    }

    public function schoolUserLinks(): ?array
    {
        return (new SchoolUser())->where("user_id", "=", $this->getId())->get();
    }

    public function existsByEmail(string $email, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];

        if ($ignoreId) {
            $sql .= " AND id != :ignore_id";
            $params['ignore_id'] = $ignoreId;
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return (int)$statement->fetchColumn() > 0;
    }

    public function existsByDocument(string $document, ?int $ignoreId = null): bool
    {
        $document = preg_replace('/[^0-9]/', '', $document);

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE document = :document";
        $params = ['document' => $document];

        if ($ignoreId) {
            $sql .= " AND id != :ignore_id";
            $params['ignore_id'] = $ignoreId;
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return (int)$statement->fetchColumn() > 0;
    }

    public function validateBusinessRule(?int $ignoreId = null): array
    {
        $errors = [];

        if ($this->existsByEmail($this->getEmail(), $ignoreId)) {
            $errors[] = "Já existe uma usuário com esse mesmo email.";
        }
        $document = $this->getDocument();

        if ($document) {
            if ($this->existsByDocument($this->getDocument(), $ignoreId)) {
                $errors[] = "Já existe um usuário com esse mesmo documento.";
            }
        }

        return $errors;
    }

    public function totalUsers(): string
    {
        $sql = "SELECT count(*) FROM {$this->table} WHERE deleted_at is null and status != 'inativo'";



        $statement = $this->connection->prepare($sql);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function recentUsers():array
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at is null and status != 'inativo'
                 ORDER BY created_at DESC LIMIT 5";




        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $results = [];
        foreach ($rows as $row) {
            $results[] = static::hydrate($row);
        }

        return $results;
    }
}