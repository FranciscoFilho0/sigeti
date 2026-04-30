<?php

namespace App\Models\Departments;

use App\Core\AbstractModel;
use App\Models\User;

class UserDepartment extends AbstractModel
{
    protected string $table = "user_departments";

    protected string $primaryKey = "id";

    protected array $fillable = [
        "user_id",
        "department_id",
        "shift"
    ];

    protected array $required = [
        "school_id" => "O campo DEPARTAMENTO é obrigatória",
        "user_id" => "O campo USUARIO é obrigatorio",
        "shift" => "O campo TURNO é obrigatório"
    ];

    protected bool $timestamps = true;

    protected bool $softDelete = false;

    public const MORNING = "manha";
    public const AFTERNOON = "tarde";

    public const WHOLE = "integral";

    public const  NOT_APPLICABLE = "nao_aplicavel";

    private const SHIFTS = [
        self::MORNING,
        self::AFTERNOON,
        self::WHOLE,
        self::NOT_APPLICABLE
    ];

    public function getId(): ?int
    {
        return $this->attributes["id"];
    }

    public function setUserId(int $userId): void
    {
        $this->attributes["user_id"] = $userId;
    }
    public function getUserId(): int
    {
        return $this->attributes["user_id"];
    }

    public function setDepartmentId(int $schoolId): void
    {
        $this->attributes["department_id"] = $schoolId;
    }

    public function getDepartmentId(): int
    {
        return $this->attributes["department_id"];
    }

    public function setShift(?string $shift): void
    {
        $shift = $shift ?? self::NOT_APPLICABLE;

        if (!in_array($shift, self::SHIFTS)) {
            throw new  \InvalidArgumentException("O Turno é inválido");
        }
        $this->attributes["shift"] = $shift;
    }
    public function getShift(): ?string
    {
        return $this->attributes["shift"];
    }

    public function department(): ?Department
    {
        return Department::find($this->getDepartmentId());
    }

    public function user(): ?User
    {
        return User::find($this->getUserId());
    }

    public static function linksByUser(int $userId): array
    {
        return (new static())
            ->where("user_id", "=", $userId)
            ->get();
    }

    public static function validateDepartments(array $links): array
    {
        $validLinks = [];

        foreach ($links as $link) {
            $departmentId = $link["department_id"] ?? 0;

            if (Department::find((int)$departmentId)) {
                $validLinks[] = $link;
            }
        }

        return $validLinks;
    }

    public static function validateDepartmentLinks(array $links): array
    {
        if (empty($links)) {
            return ["Vincule o usuário a pelo menos um departamento."];
        }

        $links = self::validateDepartments($links);

        if (empty($links)) {
            return ["Nenhum departamento válido foi informado."];
        }

        return [];
    }
}